<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\VerifyEmailException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));

        if (! $token) {
            return false;
        }

        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return false;
        }

        $ip_address = $_SERVER['REMOTE_ADDR'];
        if($user->role == 'auditor' && $user->ip_address != '' && $ip_address != $user->ip_address){
            return false;
        } else {
            $user->update(['last_ip' => $ip_address]);
        }

        $this->guard()->setToken($token);

        return true;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $token = (string) $this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'auth_user' => $this->guard()->user(),
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            throw VerifyEmailException::forUser($user);
        }

        $ip_address = $_SERVER['REMOTE_ADDR'];
        if($user && $user->role == 'auditor' && $user->ip_address != '' && $ip_address != $user->ip_address){
            $this->guard()->logout();
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.not_allowed_pc')],
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
    }

    public function username() {
        return 'username';
    }

    public function checkOTP(Request $request) {
        $request->validate([
            'one_time_password' => 'required|digits:6'
        ]);
        $google2fa = app('pragmarx.google2fa');
        $secret = $request->get('google2fa_secret');
        $token = $request->get('one_time_password');
        $result = $google2fa->verifyKey($secret, $token, config('google2fa.window'));
        if ($result) {
            return $this->sendResponse($result);
        } else {
            return $this->sendErrors(['one_time_password' => [__('auth.wrong_otp')]], '', 422);
        }
    }
}
