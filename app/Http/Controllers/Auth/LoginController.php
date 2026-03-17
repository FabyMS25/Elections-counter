<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => 'Inicio de sesión exitoso',
            'performed_at' => now(),
        ]);
    }
}