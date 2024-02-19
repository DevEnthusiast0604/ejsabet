<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function current(Request $request)
    {
        return response()->json($request->user()->load('company'));
    }

    public function search(Request $request)
    {
        $mod = new User();
        $mod = $mod->with('company');
        if ($request->get('role') != '') {
            $mod = $mod->where('role', $request->get('role'));
        }
        $data = $mod->get();
        return $this->sendResponse($data);
    }

    public function create(Request $request){
        $validation_rules = [
            'username' => 'required|string|unique:users',
            'phone_number' => 'required',
            'role' => 'required',
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
        ];
        if ($request->get('role') == 'admin' || $request->get('role') == 'sub_admin') {
            $validation_rules['company_id'] ='required';
        };
        $request->validate($validation_rules);

        $model = User::create([
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'company_id' => $request->get('company_id'),
            'phone_number' => $request->get('phone_number'),
            'role' => $request->get('role'),
            'password' => Hash::make($request->get('password')),
            'ip_address' => $request->get('ip_address'),
        ]);
        return $this->sendResponse($model);
    }

    public function update(Request $request){
        $validation_rules = [
            'username'=>'required',
            'phone_number'=>'required',
        ];
        if ($request->get('password') != '') {
            $validation_rules['password'] = [
                'confirmed',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[@$!%*#?&]/', // must contain a special character
            ];
        }
        if ($request->get('role') == 'admin' || $request->get('role') == 'sub_admin') {
            $validation_rules['company_id'] ='required';
        };
        $request->validate($validation_rules);
        $model = User::find($request->get("id"));
        $model->username = $request->get("username");
        $model->email = $request->get("email");
        $model->company_id = $request->get("company_id");
        $model->phone_number = $request->get("phone_number");
        $model->ip_address = $request->get("ip_address");
        $model->enable_google2fa = $request->get("enable_google2fa");

        if($request->get('password') != ''){
            $model->password = Hash::make($request->get('password'));
        }
        $model->save();
        return $this->sendResponse($model);
    }

    public function delete($id){
        User::destroy($id);
        return $this->sendResponse();
    }

    public function changeRoles() {
        User::where('role', '1')->update(['role' => 'admin']);
        User::where('role', '2')->update(['role' => 'user']);
    }

    public function assignCompany(Request $request) {
        $model = User::find($request->get('user_id'));
        $model->update(['company_id' => $request->get('company_id')]);
        return $this->sendResponse();
    }

}
