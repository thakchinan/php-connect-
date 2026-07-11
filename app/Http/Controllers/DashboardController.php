<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Position;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $totalDepartments = Department::count();
        
        // Active leaves are pending + approved
        $activeLeaves = Leave::whereIn('status', ['pending', 'approved'])->count();
        
        // Average salary based on active employees' positions
        $avgSalary = $totalEmployees > 0
            ? round(Employee::join('positions', 'employees.position_id', '=', 'positions.id')->avg('positions.base_salary') ?? 0, 2)
            : 0;

        // Recent employees
        $recentEmployees = Employee::with(['department', 'position'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Department distribution for simple CSS charts
        $departments = Department::withCount('employees')->get();

        return view('dashboard', compact(
            'totalEmployees',
            'totalDepartments',
            'activeLeaves',
            'avgSalary',
            'recentEmployees',
            'departments'
        ));
    }
}
