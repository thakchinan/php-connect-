<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))->with('success', 'เข้าสู่ระบบสำเร็จแล้ว!');
        }

        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $departments = Department::with('positions')->orderBy('name')->get();

        return view('auth.register', compact('departments'));
    }

    public function register(Request $request)
    {
        $role = $request->input('role', 'employee');

        if ($role === 'employee') {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'required|exists:positions,id',
                'join_date' => 'nullable|date',
            ]);

            $user = DB::transaction(function () use ($request) {
                // Create user
                $user = User::create([
                    'name' => $request->first_name.' '.$request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'employee',
                ]);

                // Create associated employee profile
                Employee::create([
                    'user_id' => $user->id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'date_of_birth' => $request->date_of_birth,
                    'join_date' => $request->join_date ?? now()->toDateString(),
                    'department_id' => $request->department_id,
                    'position_id' => $request->position_id,
                    'status' => 'active',
                    'performance_score' => 80, // Default entry score
                ]);

                return $user;
            });
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'manager',
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จแล้ว!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
}
