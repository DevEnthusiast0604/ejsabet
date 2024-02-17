<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function search(Request $request)
    {
        $mod = new Category();
        $mod = $mod->with('user', 'from_account', 'to_account');
        if($request->get('user_id') != '') {
            $user_id = $request->get('user_id');
            $user = User::find($user_id);
            if ($company_id = $user->company_id) {
                $mod = $mod->where('company_id', $company_id);
            } else {
                $mod = $mod->where('user_id', $request->get('user_id'));
            }
        }

        if($request->get('keyword') != ''){
            $keyword = $request->get('keyword');
            $mod = $mod->where(function($query) use($keyword) {
                $account_array = Account::where('name', 'like', "%$keyword%")->pluck('id');
                return $query->where('name', 'like', "%$keyword%")
                    ->orWhereIn('from_account_id', $account_array)
                    ->orWhereIn('to_account_id', $account_array)
                    ->orWhere('comment', 'like', "%$keyword%");
            });
        }

        if($request->get('type') != '') {
            $mod = $mod->where('type', $request->get('type'));
        }

        if($request->get('company_id') != ''){
            $company_id = $request->get('company_id');
            $company_users = User::where('company_id', $company_id)->pluck('id');
            $mod = $mod->whereIn('user_id', $company_users);
        }

        if ($request->get('from') != '') {
            if ($request->get('from') === 'transaction_form') {
                $mod = $mod->where('status', '!=', 'inactive');
            }
        }

        if ($request->get('page') != '') {
            $per_page = $request->get('per_page');
            $data = $mod->orderBy('created_at', 'desc')->paginate($per_page);
        } else {
            $data = $mod->orderBy('created_at', 'desc')->get();
        }

        return $this->sendResponse($data);
    }

    public function create(Request $request){
        $validation_rule = [
            'name'=>'required|string',
            'type' => 'required',
        ];
        $type = $request->get('type');
        if($type == 'expense' || $type == 'transfer') {
            $validation_rule['account'] = 'required';
        }
        if($type == 'incoming' || $type == 'transfer') {
            $validation_rule['target'] = 'required';
        }
        $request->validate($validation_rule);

        $model = new Category();
        $model->user_id = Auth::id();
        $model->name = $request->get('name');
        $model->type = $request->get('type');
        $user = User::find(Auth::id());
        $model->company_id = $user->company_id;

        $model->from_account_id = $type == 'incoming' ? null : $request->get('account');
        $model->to_account_id = $type == 'expense' ? null : $request->get('target');

        $model->comment = $request->get('comment');
        $model->save();

        return $this->sendResponse($model);
    }

    public function update(Request $request){
        $validation_rule = [
            'name'=>'required|string',
            'type' => 'required',
        ];
        $type = $request->get('type');
        if($type == 'expense' || $type == 'transfer') {
            $validation_rule['account'] = 'required';
        }
        if($type == 'incoming' || $type == 'transfer') {
            $validation_rule['target'] = 'required';
        }
        $request->validate($validation_rule);

        $model = Category::find($request->get("id"));

        $old_type = $model->type;
        $old_from_account_id = $model->from_account_id;
        $old_to_account_id = $model->to_account_id;

        $model->name = $request->get('name');
        $model->type = $request->get('type');

        $model->from_account_id = $type == 'incoming' ? null : $request->get('account');
        $model->to_account_id = $type == 'expense' ? null : $request->get('target');

        $model->comment = $request->get('comment');
        $model->save();

        if ($old_type != $model->type || $old_from_account_id != $model->from_account_id || $old_to_account_id != $model->to_account_id) {
            Transaction::where('category_id', $model->id)->update([
                'type' => $model->type,
                'from' => $model->from_account_id,
                'to' => $model->to_account_id,
            ]);
        }

        return $this->sendResponse($model);
    }

    public function delete($id) {
        if (Transaction::where('category_id', $id)->exists()) {
            return $this->sendErrors(null, 'No puedes eliminar esta categoría porque tiene transacciones asociadas.');
        }
        Category::destroy($id);
        return $this->sendResponse();
    }

    public function updateCategories() {
        $categories = Category::all();
        foreach ($categories as $item) {
            $transaction = Transaction::where('category_id', $item->id)->first();
            if ($transaction) {
                $item->update([
                    'type' => $transaction->type,
                    'from_account_id' => $transaction->from,
                    'to_account_id' => $transaction->to,
                ]);
            }
        }
        dump('ok');
    }

    public function changeStatus(Request $request) {
        $model = Category::find($request->get('id'));
        $model->status = $request->get('status');
        $model->save();
        return $this->sendResponse();
    }

    public function superAdminApprove($id)
    {
        $model = Category::find($id);
        $model->must_be_approved_from = $model->must_be_approved_by_super_admin ? null : Carbon::now();
        $model->must_be_approved_by_super_admin = !$model->must_be_approved_by_super_admin;
        $model->save();
        return $this->sendResponse();
    }

}
