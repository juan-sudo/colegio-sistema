<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.login");
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->validated(), $request->boolean("remember"))) {
            $request->session()->regenerate();

            return match (Auth::user()->role) {
                "admin"   => redirect()->intended(route("admin.dashboard")),
                "teacher" => redirect()->intended(route("teacher.dashboard")),
                "parent"  => redirect()->intended(route("parent.dashboard")),
                "student" => redirect()->intended(route("student.dashboard")),
                default   => redirect()->intended("/"),
            };
        }

        return back()->withErrors([
            "email" => "Las credenciales no coinciden con nuestros registros.",
        ])->onlyInput("email");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/login");
    }
}
