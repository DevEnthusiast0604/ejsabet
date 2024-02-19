<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'username' => 'required|string|unique:users,username,'.$user->id,
            'phone_number' => 'required',
        ]);

        return tap($user)->update([
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
        ]);
    }

    public function generate_2fa_code(Request $request) {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        $google2fa_secret = $user->google2fa_secret;
        if ($request->get('regenerate') != '') {
            $google2fa_secret = $google2fa->generateSecretKey();
            $user->update([
                'google2fa_secret' => $google2fa_secret
            ]);
        }
        $QR_Image = null;
        if ($google2fa_secret) {
            $QR_Image = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->username,
                $google2fa_secret
            );
        }
        return $this->sendResponse($QR_Image);
    }
}
