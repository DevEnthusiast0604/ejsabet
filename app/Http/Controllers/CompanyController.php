<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class CompanyController extends Controller
{
    public function search()
    {
        return $this->sendResponse(Company::all());
    }

    public function update(Request $request){
        $request->validate([
            'name'=>'required',
        ]);
        $model = Company::find($request->get("id"));
        $model->update([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ]);
        return $this->sendResponse($model);
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $model = Company::create($request->all());
        return $this->sendResponse($model);
    }

    public function delete($id) {
        if (Transaction::where('company_id', $id)->exists() || User::where('company_id', $id)->exists()) {
            return $this->sendErrors(null, 'No puedes eliminar esta empresa porque tiene transacciones o usuarios asociados.');
        }
        Company::destroy($id);
        return $this->sendResponse();
    }

    public function getBalance(Request $request) {
        $company = Company::find($request->get('company_id'));
        $date = Carbon::parse($request->get('date'));

        if ($company->transactions()->withTrashed()->whereDate('timestamp', $date)->exists()) {
            return $this->sendResponse([
                'balance' => $company->getBalance($request->get('date')),
            ]);
        } else {
            return $this->sendErrors(null, 'no_transaction');
        }
    }
}
