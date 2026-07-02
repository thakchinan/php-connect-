@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Welcome Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">แดชบอร์ดข้อมูล (Dashboard)</h1>
            <p style="color: var(--text-muted);">ยินดีต้อนรับสู่ระบบ Digital HR สำหรับวิเคราะห์และจัดการกำลังพลภายในองค์กร</p>
        </div>
        <div style="background: white; padding: 0.75rem 1.25rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); font-weight: 600; font-size: 0.875rem;">
            วันที่ปัจจุบัน: {{ date('d M Y') }}
        </div>
    </div>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <!-- Card 1: Total Employees -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(79, 70, 229, 0.1); color: var(--primary-color); display: flex; align-items: center; justify-content: center; shrink-0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">พนักงานทั้งหมด</p>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $totalEmployees }} คน</h3>
            </div>
        </div>

        <!-- Card 2: Total Departments -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(16, 185, 129, 0.1); color: var(--secondary-color); display: flex; align-items: center; justify-content: center; shrink-0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">แผนกงาน</p>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $totalDepartments }} แผนก</h3>
            </div>
        </div>

        <!-- Card 3: Average Salary -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; shrink-0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">ฐานเงินเดือนเฉลี่ย</p>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ number_format($avgSalary) }} ฿</h3>
            </div>
        </div>

        <!-- Card 4: Active Leaves -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; shrink-0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">คำขอวันลา (Active)</p>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $activeLeaves }} รายการ</h3>
            </div>
        </div>
    </div>

    <!-- Dashboard Content Sections -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; align-items: start;">
        @if(count($recentEmployees) > 0)
        <!-- Recent Employees Table -->
        <div class="card glass-card" style="border-radius: var(--radius-lg);">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span>รายชื่อพนักงานเข้าใหม่ล่าสุด</span>
                <a href="{{ route('employees.index') }}" style="font-size: 0.875rem; color: var(--primary-color); font-weight: 600; text-decoration: none;">ดูทั้งหมด &rarr;</a>
            </h3>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 600;">
                            <th style="padding: 0.75rem 1rem;">ชื่อ-นามสกุล</th>
                            <th style="padding: 0.75rem 1rem;">แผนก</th>
                            <th style="padding: 0.75rem 1rem;">ตำแหน่ง</th>
                            <th style="padding: 0.75rem 1rem;">วันที่เข้าร่วม</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEmployees as $emp)
                            <tr style="border-bottom: 1px solid var(--border-color); hover: background-color: rgba(0,0,0,0.02);">
                                <td style="padding: 1rem; font-weight: 600;">{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                <td style="padding: 1rem; color: var(--text-muted);">{{ $emp->department->name ?? '-' }}</td>
                                <td style="padding: 1rem; color: var(--text-muted);">{{ $emp->position->title ?? '-' }}</td>
                                <td style="padding: 1rem;">{{ \Carbon\Carbon::parse($emp->join_date)->format('d M Y') }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($emp->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($emp->status == 'leave')
                                        <span class="badge badge-warning">On Leave</span>
                                    @else
                                        <span class="badge badge-danger">Terminated</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Department Distribution Simple CSS Chart -->
        <div class="card glass-card" style="border-radius: var(--radius-lg);">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">สัดส่วนจำนวนพนักงานแต่ละแผนก</h3>
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @php
                    $maxCount = $departments->max('employees_count') ?: 1;
                @endphp
                @foreach($departments as $dept)
                    @php
                        $percentage = round(($dept->employees_count / $maxCount) * 100);
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <span>{{ $dept->name }}</span>
                            <span style="color: var(--primary-color);">{{ $dept->employees_count }} คน</span>
                        </div>
                        <div style="width: 100%; height: 12px; background: #f1f5f9; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color);">
                            <div style="width: {{ $percentage }}%; height: 100%; border-radius: 6px; transition: width 1s ease-in-out;" class="bg-gradient-primary"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
