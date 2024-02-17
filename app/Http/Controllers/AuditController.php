<?php

namespace App\Http\Controllers;

use App\Mail\ReportMail;
use App\Models\Audit;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditController extends Controller
{
    public function search(Request $request)
    {
        $mod = new Audit();
        $mod = $mod->with('user', 'company');
        if (auth()->check() && auth()->user()->role == 'auditor') {
            $mod = $mod->where('company_id', auth()->user()->company_id);
        }
        if($request->get('user_id') != '') {
            $mod = $mod->where('user_id', $request->get('user_id'));
        }
        if($request->get('company_id') != '') {
            $mod = $mod->where('company_id', $request->get('company_id'));
        }

        if ($request->get('startDate') != '' && $request->get('endDate') != '') {
            if ($request->get('startDate') == $request->get('endDate')) {
                $mod = $mod->whereDate('date', $request->get('startDate'));
            } else {
                $mod = $mod->whereBetween('date', [$request->get('startDate'), $request->get('endDate')]);
            }
        }

        $per_page = $request->get('per_page');
        $data = $mod->orderBy('created_at', 'desc')->paginate($per_page);

        return $this->sendResponse($data);
    }

    public function save(Request $request) {
        $model = new Audit();
        if ($request->get('id') != '') {
            $model = Audit::find($request->get('id'));
        }
        if (auth()->user()->role === 'auditor' && auth()->user()->company_id != $request->get('company_id')) {
            return $this->sendErrors(null, __('auth.permission_denied'), 403);
        }
        $model->user_id = auth()->id();
        $model->company_id = $request->get('company_id');
        $model->date = Carbon::parse($request->get('date'))->format('Y-m-d');
        $model->balance = $request->get('balance');

        // Send Report Email to Admin
        $date = Carbon::parse($request->get('date'));
        $company = Company::find($request->get('company_id'));
        $latest_audit_date = $company->getLatestAuditDate();
        if (!$latest_audit_date) {
            $startDate = Carbon::parse($company->transactions()->withTrashed()->orderBy('timestamp')->first()->timestamp);
        } else {
            $startDate = $latest_audit_date->addDay();
        }
        $adminEmail = User::where('role', 'super_admin')->first()->email;

        try {
            if (filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                $file_name = 'AuditReport-' . auth()->user()->username . '.pdf';
                $path = $this->generatePdf($company, $startDate, $date);
                $emailData = [
                    'subject' => 'Nuevo Informe de auditoria Para ' . $company->name,
                    'attachment' => storage_path('app/public/'.$path),
                    'file_name' => $file_name,
                ];
                Mail::to($adminEmail)->send(new ReportMail($emailData));
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        $model->save();
        return $this->sendResponse($model);
    }

    public function delete($id) {
        $model = Audit::find($id);
        if (auth()->user()->role === 'auditor' && auth()->user()->company_id != $model->company_id) {
            return $this->sendErrors(null, __('auth.permission_denied'), 403);
        }
        Audit::destroy($id);
        return $this->sendResponse();
    }

    public function getLatestAuditDate() {
        $user = auth()->user();
        if (!$user->company) return $this->sendResponse();
        $latest_date = $user->company->getLatestAuditDate();
        return $this->sendResponse($latest_date);
    }

    private function getAuditReport(Company $company, Carbon $startDate, Carbon $endDate) {
        $category_ids = $company->transactions()
                        ->withTrashed()
                        ->whereBetween('timestamp', [$startDate, $endDate])
                        ->orderBy('category_id')
                        ->distinct()->pluck('category_id');
        $categories = Category::whereIn('id', $category_ids)->get();
        $categoryData = [];
        foreach ($categories as $category) {
            $categoryData[] = [
                'name' => $category->name,
                'total_amount' => $company->transactions()
                                ->withTrashed()
                                ->whereBetween('timestamp', [$startDate, $endDate])
                                ->where('category_id', $category->id)
                                ->sum('amount'),
                'type' => $category->type,
            ];
        }

        $total_incoming = $company->transactions()
                                ->withTrashed()
                                ->whereBetween('timestamp', [$startDate, $endDate])
                                ->where('type', 'incoming')
                                ->sum('amount');

        $total_expense = $company->transactions()
                                ->withTrashed()
                                ->whereBetween('timestamp', [$startDate, $endDate])
                                ->where('type', 'expense')
                                ->sum('amount');
        $latest_audit = $company->audits()->orderBy('date', 'desc')->first();
        $latest_audit_balance = $latest_audit->balance ?? 0;
        return [
            'total_incoming' => $total_incoming,
            'total_expense' => $total_expense,
            'category_data' => $categoryData,
            'latest_audit_balance' => $latest_audit_balance,
            'company_name' => $company->name,
            'start_date' => $startDate->format('m/d/Y'),
            'end_date' => $endDate->format('m/d/Y'),
        ];
    }

    public function generatePdf(Company $company, Carbon $startDate, Carbon $endDate) {
        $data = $this->getAuditReport($company, $startDate, $endDate);
        $pdf = Pdf::loadView('reports.audit', compact('data'));
        $path = "generated_pdf/AuditReport.PDF";

        if(!Storage::disk('public')->exists('generated_pdf')){
            Storage::disk('public')->makeDirectory('generated_pdf', 0777, true, true);
        }
        // return $pdf->stream();
        $pdf->save(storage_path("app/public/$path"));
        return $path;
    }
}
