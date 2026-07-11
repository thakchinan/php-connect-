<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'position'])->orderBy('performance_score', 'desc')->get();

        return view('performance.index', compact('employees'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'performance_score' => 'required|integer|min:0|max:100',
        ]);

        $employee->update([
            'performance_score' => $request->performance_score,
        ]);

        return redirect()->route('performance.index')->with('success', "อัปเดตคะแนนผลงานของ {$employee->first_name} เรียบร้อยแล้ว");
    }
}
