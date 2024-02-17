<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;

class AccountController extends Controller
{

    public function search()
    {
        return $this->sendResponse(Account::with('company')->get());
    }


    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);

        $model = Account::create([
            'name' => $request->get('name'),
            'company_id' => $request->get('company_id'),
            'comment' => $request->get('comment'),
        ]);
        return $this->sendResponse($model);
    }

    public function update(Request $request){
        $request->validate([
            'name'=>'required',
        ]);
        $model = Account::find($request->get("id"));
        $model->name = $request->get("name");
        $model->comment = $request->get("comment");
        $model->company_id = $request->get("company_id");
        $model->save();
        return $this->sendResponse($model);
    }

    public function delete($id) {
        if (Transaction::where('from', $id)->orWhere('to', $id)->exists()) {
            return $this->sendErrors(null, 'No puedes eliminar esta cuentas porque tiene transacciones asociadas.');
        }
        Account::destroy($id);
        return $this->sendResponse();
    }

}
