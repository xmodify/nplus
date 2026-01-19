<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
      // แสดงหน้า Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

// ตรวจสอบและเข้าสู่ระบบ-------------------------------------------------------------
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 🔥 กลับหน้าเดิม
            return redirect(
                $request->input('redirect', url('/'))
            );
        }

        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ])->withInput();
    }

// ออกจากระบบ----------------------------------------------------------------
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
