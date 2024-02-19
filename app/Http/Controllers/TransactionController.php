<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Account;
use App\Models\Image;
use App\Models\Transaction;
use App\Traits\UploadAble;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Events\ImageProcessed;

class TransactionController extends Controller
{
    use UploadAble;

    public function search(Request $request)
    {
        $auth_user = Auth::user();

        if ($auth_user->role === 'auditor' && !$auth_user->company) {
            return $this->sendErrors(null, __('auth.permission_denied'), 403);
        }

        $mod = new Transaction();
        $mod = $mod->with(['user', 'account', 'target', 'company', 'category', 'images']);

        if ($auth_user->company_id != '') {
            $mod = $mod->where('company_id', $auth_user->company_id);
        }
        $mod = $this->filterRequest($request, $mod);
        
        $per_page = $request->get('per_page');
        
        $data = $mod->orderBy('created_at', 'desc')->paginate($per_page);

        if ($request->get('date') != '') {
            foreach ($data as $item) {
                $item->balance = $this->getBalance($item);
            }
        }

        return $this->sendResponse($data);
    }

    public function getAccounts(Request $request){
        $auth_user = Auth::user();
        $mod = Account::where('company_id', $auth_user->company_id)->get();
        return $this->sendResponse($mod);
    }

    public function getTotal(Request $request)
    {
        $auth_user = Auth::user();

        // $mod = Transaction::withTrashed();
        $mod = new Transaction();
        if ($auth_user->company_id != '') {
            $mod = $mod->where('company_id', $auth_user->company_id);
        }

        $mod = $this->filterRequest($request, $mod);
        

        $expenses = $mod->clone()->where('type', 'expense')->sum('amount');
        $incomes = $mod->clone()->where('type', 'incoming')->sum('amount');

        return $this->sendResponse([
            'expenses' => $expenses,
            'incomes' => $incomes,
        ]);
    }

    private function filterRequest(Request $request, $mod)
    {
        $auth_user = Auth::user();
        if ($auth_user->hasRole('sub_admin')) {
            $mod = $mod->whereNull('authorized_by_id')->where('user_id', $auth_user->id);
        }

        if ($auth_user->hasRole('super_admin')) {
            if ($request->get('is_approved_by_super_admin') != '') {
                $isApprovedBySuperAdmin = intval($request->get('is_approved_by_super_admin'));
                $mod = $mod->whereHas('category', function ($query) {
                    return $query->where('must_be_approved_by_super_admin', 1)
                        ->whereNotNull('must_be_approved_from')
                        ->whereColumn('must_be_approved_from', '<=', 'transactions.created_at');
                })
                    ->where('is_approved_by_super_admin', $isApprovedBySuperAdmin);
            }
        }

        if ($request->get('type') != "") {
            $type = $request->get('type');
            $mod = $mod->where('type', $type);
        }
        if ($request->get('company_id') != "") {
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('category_id') != "") {
            $category_id = $request->get('category_id');
            $mod = $mod->where('category_id', $category_id);
        }
        if ($request->get('is_authorized') != "") {
            $is_authorized = $request->get('is_authorized');
            $mod = $mod->where('authorized_by_id', $is_authorized === 'yes' ? '!=' : '=', NULL)
                ->whereHas('user', function ($query) {
                    return $query->where('role', 'sub_admin');
                });
        }
        if ($request->get('keyword') != "") {
            $keyword = $request->get('keyword');
            $mod = $mod->where(function ($query) use ($keyword) {
                $category_array = Category::where('name', 'like', "%$keyword%")->orWhere('comment', 'like', "%$keyword%")->pluck('id');
                $account_array = Account::where('name', 'like', "%$keyword%")->pluck('id');
                return $query->where('description', 'like', "%$keyword%")
                    ->orWhere('amount', $keyword)
                    ->orWhereIn('from', $account_array)
                    ->orWhereIn('to', $account_array)
                    ->orWhereIn('category_id', $category_array);
            });
        }

        if ($request->get('account_id') != ""){
            $account_id = $request->get('account_id');
            $mod->where(function ($query) use ($account_id) {
                return $query->orWhere('from', $account_id)
                        ->orWhere('to', $account_id);
            });
        }

        if ($request->get('startDate') != '' && $request->get('endDate') != '') {
            if ($request->get('startDate') == $request->get('endDate')) {
                $mod = $mod->whereDate('timestamp', $request->get('startDate'));
            } else {
                $mod = $mod->whereBetween('timestamp', [$request->get('startDate'), $request->get('endDate')]);
            }
        }

        if ($request->get('date') != '') {
            $timestamp = Carbon::parse($request->get('date'))->format('Y-m-d');
            $mod = $mod->whereDate('timestamp', $timestamp);
        }
        return $mod;
    }

    public function getLastTransactionDate()
    {
        $last_transaction = Transaction::orderBy('timestamp', 'desc')->first();
        if (Auth::user()->company) {
            $company = Auth::user()->company;
            $last_transaction = $company->transactions()->orderBy('timestamp', 'desc')->first();
        }
        return $this->sendResponse($last_transaction ? Carbon::parse($last_transaction->timestamp)->format('Y-m-d') : now()->format('Y-m-d'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required',
        ]);

        $category = Category::find($request->get('category'));
        $type = $category->type;

        $user = Auth::user();
        $company = null;
        if ($user->company) {
            $company = $user->company;
        } else if ($category->user->company) {
            $company = $category->user->company;
        } else {
            return $this->sendErrors(null, 'Company not found');
        }

        $model = new Transaction();
        $model->type = $type;
        $model->user_id = $user->id;
        $model->company_id = $company->id;
        $model->category_id = $request->get('category');

        $model->from = $type == 'incoming' ? null : $category->from_account_id;
        $model->to = $type == 'expense' ? null : $category->to_account_id;

        $model->amount = $request->get('amount');
        $model->description = $request->get('description');
        $model->timestamp = Carbon::parse($request->get('date'))->format('Y-m-d H:i:s');
        if ($request->get('has_images')) {
            $model->is_uploading = 1;
        }

        $model->save();

        return $this->sendResponse($model->load(['user', 'account', 'target', 'company', 'category', 'images']));
    }

    public function update(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required',
        ]);
        $category = Category::find($request->get('category'));
        $type = $category->type;

        $user = Auth::user();

        $model = Transaction::find($request->get('id'));
        if ($model->is_audited) {
            return $this->sendErrors(null, 'Already audited', 400);
        }
        if (!$model->is_editable) {
            return $this->sendErrors(null, 'This transaction can not be editable.');
        }
        $model->type = $type;
        $model->category_id = $request->get('category');

        $model->from = $type == 'incoming' ? null : $category->from_account_id;
        $model->to = $type == 'expense' ? null : $category->to_account_id;

        $model->amount = $request->get('amount');
        $model->description = $request->get('description');
        $model->timestamp = Carbon::parse($request->get('date'))->format('Y-m-d H:i:s');
        if ($request->get('has_images')) {
            $model->is_uploading = 1;
        }
        $model->save();

        return $this->sendResponse($model->load(['user', 'account', 'target', 'company', 'category', 'images']));
    }

    public function uploadTransactionImage(Request $request)
    {
        $model = Transaction::find($request->get('id'));
        $company_name = auth()->user()->company->name ?? '';
        $description = substr($model->description, 0, 50);
        $category = $model->category->name ?? '';
        $file_name_string = $company_name . "_" . $category . "_" . $description . "_" . date('YmdHis');
        $file_name_string = str_replace(" ", "_", $file_name_string);

        // $jobIds = [];

        if ($request->file('images') != null) {
            try {
                foreach ($request->file('images') as $key => $image) {

                    // $jobId = (string) Str::uuid();
                    // Cache::put("job-status-{$jobId}", 'queued', now()->addMinutes(10));
                    // $jobIds[] = $jobId;

                    $disk = config('filesystems.image_storage_disk');
                    $path = $this->uploadImage($image, 'transactions', $disk, $file_name_string . '_' . $key);
                    $model->images()->create([
                        'disk' => $disk,
                        'path' => $path,
                    ]);
                }
            } catch (\Throwable $th) {
                $model->update(['is_uploading' => 0]);
                throw $th;
            }
        }
        $model->update(['is_uploading' => 0]);
        return $this->sendResponse($model->load(['user', 'account', 'target', 'company', 'category', 'images']));
    }

    public function delete($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction->is_editable) {
            return $this->sendErrors(null, 'This transaction can not be deleted');
        }
        if ($transaction->is_audited) {
            return $this->sendErrors(null, 'Already audited', 400);
        }
        foreach ($transaction->images as $image) {
            $this->deleteFile($image->path, $image->disk);
            $image->delete();
        }
        $transaction->forceDelete();
        return $this->sendResponse();
    }

    public function audit(Request $request)
    {
        $model = Transaction::find($request->get('id'));
        if (auth()->user()->company_id != $model->company_id) {
            return $this->sendErrors(null, __('auth.permission_denied'), 403);
        }
        $model->update(['is_audited' => !$model->is_audited]);
        return $this->sendResponse($model->is_audited);
    }

    public function getDetail(Request $request)
    {
        $model = Transaction::find($request->get('id'));
        return $this->sendResponse($model);
    }

    public function authorizeTransaction($id)
    {
        $model = Transaction::find($id);
        $model->update(['authorized_by_id' => auth()->id()]);
        return $this->sendResponse();
    }

    public function superAdminApprove($id)
    {
        $model = Transaction::find($id);
        $model->update(['is_approved_by_super_admin' => !$model->is_approved_by_super_admin]);
        return $this->sendResponse();
    }

    public function getBalance(Transaction $item)
    {
        $timestamp = date('Y-m-d', strtotime($item->timestamp));
        if (Auth::user()->role == 'super_admin') {
            $before_expenses = Transaction::withTrashed()->where('type', 'expense')->where('timestamp', '<', $timestamp)->sum('amount');
            $before_incoming = Transaction::withTrashed()->where('type', 'incoming')->where('timestamp', '<', $timestamp)->sum('amount');

            $equal_expenses = Transaction::withTrashed()->where('type', 'expense')->whereDate('timestamp', $timestamp)->where('created_at', '<=', $item->created_at)->sum('amount');
            $equal_incoming = Transaction::withTrashed()->where('type', 'incoming')->whereDate('timestamp', $timestamp)->where('created_at', '<=', $item->created_at)->sum('amount');
        } else if (Auth::user()->company) {
            $company = Auth::user()->company;
            $before_expenses = $company->expenses()->withTrashed()->where('timestamp', '<', $timestamp)->sum('amount');
            $before_incoming = $company->incomings()->withTrashed()->where('timestamp', '<', $timestamp)->sum('amount');

            $equal_expenses = $company->expenses()->withTrashed()->whereDate('timestamp', $timestamp)->where('created_at', '<=', $item->created_at)->sum('amount');
            $equal_incoming = $company->incomings()->withTrashed()->whereDate('timestamp', $timestamp)->where('created_at', '<=', $item->created_at)->sum('amount');
        }

        $total_expenses = $before_expenses + $equal_expenses;
        $total_incoming = $before_incoming + $equal_incoming;

        $current_balance = $total_incoming - $total_expenses;
        return $current_balance;
    }

    public function changeImagePath()
    {
        ini_set('max_execution_time', '0');
        foreach (Transaction::all() as $item) {
            $path = $item->attachment;
            if (strpos($path, 'uploaded/transaction_attachments') !== false) {
                $new_path = str_replace('uploaded/transaction_attachments', 'transactions', $path);
                $item->update(['attachment' => $new_path]);
            }
        }
        dump('ok');
    }

    public function organize(Request $request)
    {
        ini_set('max_execution_time', '0');
        $max_id = Transaction::max('id');
        for ($id = 1; $id <= $max_id; $id++) {
            $item = Transaction::find($id);
            if (!$item)
                continue;
            $diff = Carbon::parse($item->timestamp)->diffInDays($item->created_at);
            if ($diff >= 2) {
                $created_at_time = Carbon::parse($item->created_at)->format('H:i:s');
                $transaction_date = Carbon::parse($item->timestamp)->format('Y-m-d');
                $new_created_at = $transaction_date . ' ' . $created_at_time;
                $item->update(['created_at' => $new_created_at]);
            }
        }
        return $this->sendResponse();
    }

    public function deleteImage($id)
    {
        $model = Image::find($id);
        $this->deleteFile($model->path, $model->disk);
        $model->delete();
        return $this->sendResponse();
    }

}
