<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function enableSiteStatus(Request $request) {
        $user = User::where('username', $request->get('username'))->first();
        if ($user && Hash::check($request->get('password'), $user->password)) {
            Setting::set('site_status', 'active');
            return $this->sendResponse('Success');
        } else {
            return $this->sendErrors(['username' => [__('page.invalid_credential')]], '', 422);
        }
    }

    public function disableSiteStatus(Request $request) {
        // if (auth()->user()->role != 'super_admin') {
        //     return $this->sendErrors(['error' => __('page.you_are_not_admin')]);
        // }
        Setting::set('site_status', 'disabled');
        return $this->sendResponse('Success');
    }

    public function set(Request $request) {
        Setting::set($request->get('key'), $request->get('value'));
        return $this->sendResponse('Success');
    }

    public function get(Request $request) {
        return $this->sendResponse(Setting::get($request->get('key')));
    }
}
