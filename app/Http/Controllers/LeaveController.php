<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('employee.user')->orderBy('created_at', 'desc')->get();

        // Find if the logged-in user has an associated employee profile
        $userEmployee = Auth::user()->employee;

        return view('leaves.index', compact('leaves', 'userEmployee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        Leave::create([
            'employee_id' => $request->employee_id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('leaves.index')->with('success', 'ส่งใบลาเรียบร้อยแล้ว รอการอนุมัติ');
    }

    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $leave->update([
            'status' => $request->status,
        ]);

        return redirect()->route('leaves.index')->with('success', 'อัปเดตสถานะการลาเรียบร้อยแล้ว');
    }
}
