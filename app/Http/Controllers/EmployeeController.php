<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $departmentId = $request->input('department_id');
        $status = $request->input('status');

        $query = Employee::with(['department', 'position', 'user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $employees = $query->orderBy('first_name')->get();
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('title')->get();

        return view('employees.index', compact('employees', 'departments', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'join_date' => 'nullable|date',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'status' => 'required|in:active,leave,terminated',
            'performance_score' => 'nullable|integer|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            // Create user
            $user = User::create([
                'name' => $request->first_name.' '.$request->last_name,
                'email' => $request->email,
                'password' => Hash::make('password123'), // Default password
            ]);

            // Create employee profile
            Employee::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'join_date' => $request->join_date,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'status' => $request->status,
                'performance_score' => $request->performance_score ?? 80,
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'เพิ่มข้อมูลพนักงานเรียบร้อยแล้ว');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$employee->user_id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'join_date' => 'nullable|date',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'status' => 'required|in:active,leave,terminated',
            'performance_score' => 'nullable|integer|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $employee) {
            // Update user details
            $employee->user->update([
                'name' => $request->first_name.' '.$request->last_name,
                'email' => $request->email,
            ]);

            // Update employee profile
            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'join_date' => $request->join_date,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'status' => $request->status,
                'performance_score' => $request->performance_score ?? $employee->performance_score,
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'แก้ไขข้อมูลพนักงานเรียบร้อยแล้ว');
    }

    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            // Cascade delete will handle the employee when user is deleted,
            // but we can delete the user explicitly to trigger it.
            $employee->user->delete();
        });

        return redirect()->route('employees.index')->with('success', 'ลบข้อมูลพนักงานเรียบร้อยแล้ว');
    }
}
