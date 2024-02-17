<?php

namespace App\Http\Controllers;

use App\Mail\DeleteVerification;
use App\Models\Account;
use App\Models\Company;
use App\Models\CompanyIp;
use App\Models\Transaction;
use App\Traits\UploadAble;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    use UploadAble;

    public function getDashboardData(Request $request) {
        // company_id and date range must be required
        $company_id = $request->get('company_id');
        $company = Company::find($company_id);
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $data = [];

        $chart_start = Carbon::parse($startDate);
        $chart_end = Carbon::parse($endDate);

        $key_array = $date_array = $total_expense_array = $total_incoming_array = array();

        for ($dt=$chart_start; $dt < $chart_end; $dt->addDay()) {
            $date = $dt->format('Y-m-d');
            $date_array[] = $date;
            $key_array[] = $dt->format('M/d');

            // Overview Chart
            $daily_expense = $company->transactions()->where('type', 'expense')->whereDate('timestamp', $date)->get()->sum('amount');
            $daily_incoming = $company->transactions()->where('type', 'incoming')->whereDate('timestamp', $date)->get()->sum('amount');
            $total_expense_array[] = $daily_expense;
            $total_incoming_array[] = $daily_incoming;
        }

        $data['key_array'] = $key_array;
        $data['total_expense_array'] = $total_expense_array;
        $data['total_incoming_array'] = $total_incoming_array;

        if (Auth::user()->role == 'super_admin') {
            // Company Incoming Chart
            $company_incoming = $company_array = $company_balance = [];
            foreach (Company::all() as $item) {
                $company_array[] = $item->name;
                $daily_incoming = [];
                foreach ($date_array as $date) {
                    $daily_incoming[] = $item->transactions()->where('type', 'incoming')->whereDate('timestamp', $date)->get()->sum('amount');
                }
                $company_incoming[] = $daily_incoming;

                $company_total_incoming = $item->transactions()->where('type', 'incoming')->get()->sum('amount');
                $company_total_expense = $item->transactions()->where('type', 'expense')->get()->sum('amount');
                $company_balance[] = $company_total_incoming - $company_total_expense;
            }
            $data['company_array'] = $company_array;
            $data['company_incoming'] = $company_incoming;
            $data['company_balance'] = $company_balance;
        }

        // User Chart
        $user_array = $users_expense = $users_incoming = [];
        foreach ($company->users as $user) {
            $user_array[] = $user->username;
            $user_expense = $user->transactions()->where('type', 'expense')->whereBetween('timestamp', [$startDate, $endDate])->get()->sum('amount');
            $user_incoming = $user->transactions()->where('type', 'incoming')->whereBetween('timestamp', [$startDate, $endDate])->get()->sum('amount');
            $users_expense[] = $user_expense;
            $users_incoming[] = $user_incoming;
        }
        $data['user_array'] = $user_array;
        $data['users_expense'] = $users_expense;
        $data['users_incoming'] = $users_incoming;

        // Account Chart

        $account_array = $accounts_expense = $accounts_incoming = $accounts_balance = [];
        foreach($company->accounts as $account) {
            $account_array[] = $account->name;
            $account_expense = $account->expenses()->whereBetween('timestamp', [$startDate, $endDate])->get()->sum('amount');
            $account_incoming = $account->incomings()->whereBetween('timestamp', [$startDate, $endDate])->get()->sum('amount');
            // $account_expense = $account_incoming = 0;
            $accounts_expense[] = $account_expense;
            $accounts_incoming[] = $account_incoming;
            $accounts_balance[] = $account_incoming - $account_expense;
        }
        $data['account_array'] = $account_array;
        $data['accounts_expense'] = $accounts_expense;
        $data['accounts_incoming'] = $accounts_incoming;
        $data['accounts_balance'] = $accounts_balance;

        return $this->sendResponse($data);
    }

    public function getBalance() {
        $accounts = Account::orderBy('company_id')->get();
        if (Auth::user()->company) {
            $accounts = Account::where('company_id', Auth::user()->company_id)->get();
        }
        $data = [];
        foreach ($accounts as $account) {
            $account_expense = $account->expenses()->withTrashed()->sum('amount');
            $account_incoming = $account->incomings()->withTrashed()->sum('amount');
            $data[] = [
                'id' => $account->id,
                'account' => $account->name,
                'balance' => $account_incoming - $account_expense,
            ];
        }
        return $this->sendResponse($data);
    }

    public function requestAdvancedDelete(Request $request)
    {
        $request_data = $request->all();
        $request_data['verification_code'] = Str::random(8);
        Cache::put('advanced_delete_request_data', $request_data);
        try {
            if (filter_var(Auth::user()->email, FILTER_VALIDATE_EMAIL)) {
                $to_email = Auth::user()->email;
                Mail::to($to_email)->send(new DeleteVerification($request_data));
            } else {
                return $this->sendErrors(['startDate' => [__('page.invalid_email_address')]], __('page.invalid_email_address'), 422);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $this->sendResponse(['verification_code' => $request_data['verification_code']]);
    }

    public function verifyAdvancedDelete(Request $request)
    {
        ini_set('max_execution_time', 0);
        $request->validate(['code' => 'required|size:8']);
        $request_data = Cache::get('advanced_delete_request_data');
        $verification_code = $request->get('code');
        if ($verification_code != $request_data['verification_code']) {
            Cache::forget('advanced_delete_request_data');
            return $this->sendErrors(['code' => [__('page.incorrect_verification_code')]], '', 422);
        } else {
            $mod = new Transaction();

            if ($request_data['startDate'] != '' && $request_data['endDate'] != '') {
                if ($request_data['startDate'] == $request_data['endDate']) {
                    $mod = $mod->whereDate('timestamp', $request_data['startDate']);
                } else {
                    $mod = $mod->whereBetween('timestamp', [$request_data['startDate'], $request_data['endDate']]);
                }
            } else {
                return $this->sendErrors(['code' => [__('page.something_went_wrong')]], '', 422);
            }

            if ($request_data['company_id']) {
                $mod = $mod->where('company_id', $request_data['company_id']);
            }
            $transactions = $mod->get();
            foreach ($transactions as $item) {
                foreach ($item->images as $image) {
                    if ($this->fileExists($image->path, 'public')) {
                        $this->deleteFile($image->path, 'public');
                    }
                }
                $item->delete();
            }
            Cache::forget('advanced_delete_request_data');
            return $this->sendResponse();
        }
    }

    public function getClientIp(Request $request) {
        return $this->sendResponse(['ip' => $request->ip()]);
    }

    public function getCompanyIps() {
        return $this->sendResponse(CompanyIp::with('company')->get());
    }

    public function saveCompanyIp(Request $request) {
        $request->validate([
            'ip_address' => 'required|ip',
        ]);
        $model = CompanyIp::where('ip_address', $request->get('ip_address'))->first();
        if (!$model) {
            $model = new CompanyIp();
            $model->ip_address = $request->get('ip_address');
        }
        $model->company_id = $request->get('company_id');
        $model->save();
        return $this->sendResponse($model);
    }

    public function deleteCompanyIp($id) {
        CompanyIp::destroy($id);
        return $this->sendResponse();
    }
}
