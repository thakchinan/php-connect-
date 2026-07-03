@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="margin-top: 3rem; margin-bottom: 5rem;">
    <!-- Welcome Header -->
    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -0.025em; color: var(--text-main); margin-bottom: 0.5rem;" class="text-gradient">แดชบอร์ดข้อมูล (Dashboard)</h1>
            <p style="color: var(--text-muted); font-size: 1rem;">ยินดีต้อนรับสู่ระบบ Digital HR สำหรับวิเคราะห์และจัดการกำลังพลภายในองค์กร</p>
        </div>
        <div style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(8px); padding: 0.75rem 1.5rem; border-radius: 9999px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); font-weight: 600; font-size: 0.875rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            วันที่ปัจจุบัน: {{ date('d M Y') }}
        </div>
    </div>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <!-- Card 1: Total Employees -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); display: flex; align-items: center; gap: 1.25rem; border: 1px solid rgba(99, 102, 241, 0.15);">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(99, 102, 241, 0.08); color: var(--primary-color); display: flex; align-items: center; justify-content: center; shrink-0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">พนักงานทั้งหมด</p>
                <h3 style="font-size: 1.85rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $totalEmployees }} <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">คน</span></h3>
            </div>
        </div>

        <!-- Card 2: Total Departments -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); display: flex; align-items: center; gap: 1.25rem; border: 1px solid rgba(16, 185, 129, 0.15);">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(16, 185, 129, 0.08); color: var(--secondary-color); display: flex; align-items: center; justify-content: center; shrink-0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">แผนกงาน</p>
                <h3 style="font-size: 1.85rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $totalDepartments }} <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">แผนก</span></h3>
            </div>
        </div>

        <!-- Card 3: Average Salary -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); display: flex; align-items: center; gap: 1.25rem; border: 1px solid rgba(245, 158, 11, 0.15);">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(245, 158, 11, 0.08); color: #f59e0b; display: flex; align-items: center; justify-content: center; shrink-0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">ฐานเงินเดือนเฉลี่ย</p>
                <h3 style="font-size: 1.85rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ number_format($avgSalary) }} <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">฿</span></h3>
            </div>
        </div>

        <!-- Card 4: Active Leaves -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); display: flex; align-items: center; gap: 1.25rem; border: 1px solid rgba(239, 68, 68, 0.15);">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(239, 68, 68, 0.08); color: #ef4444; display: flex; align-items: center; justify-content: center; shrink-0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">คำขอวันลา (Active)</p>
                <h3 style="font-size: 1.85rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $activeLeaves }} <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">รายการ</span></h3>
            </div>
        </div>
    </div>

    <!-- Dashboard Content Sections -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(480px, 1fr)); gap: 2.5rem; align-items: start;">
        @if(count($recentEmployees) > 0)
        <!-- Recent Employees Table -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); padding: 2rem;">
            <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                <span style="color: var(--text-main);">รายชื่อพนักงานเข้าใหม่ล่าสุด</span>
                <a href="{{ route('employees.index') }}" style="font-size: 0.875rem; color: var(--primary-color); font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">ดูทั้งหมด <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
            </h3>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="color: var(--text-muted); font-weight: 600;">
                            <th style="padding: 0.875rem 1rem;">ชื่อ-นามสกุล</th>
                            <th style="padding: 0.875rem 1rem;">แผนก</th>
                            <th style="padding: 0.875rem 1rem;">ตำแหน่ง</th>
                            <th style="padding: 0.875rem 1rem;">วันที่เข้าร่วม</th>
                            <th style="padding: 0.875rem 1rem; text-align: center;">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEmployees as $emp)
                            <tr>
                                <td style="padding: 1rem; font-weight: 600; color: var(--text-main);">{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                <td style="padding: 1rem; color: var(--text-muted);">{{ $emp->department->name ?? '-' }}</td>
                                <td style="padding: 1rem; color: var(--text-muted);">{{ $emp->position->title ?? '-' }}</td>
                                <td style="padding: 1rem;">{{ \Carbon\Carbon::parse($emp->join_date)->format('d M Y') }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($emp->status == 'active')
                                        <span class="badge badge-success" style="border: 1px solid rgba(16, 185, 129, 0.25);">Active</span>
                                    @elseif($emp->status == 'leave')
                                        <span class="badge badge-warning" style="border: 1px solid rgba(245, 158, 11, 0.25);">On Leave</span>
                                    @else
                                        <span class="badge badge-danger" style="border: 1px solid rgba(239, 68, 68, 0.25);">Terminated</span>
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
        <div class="card glass-card" style="border-radius: var(--radius-lg); padding: 2rem;">
            <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; color: var(--text-main);">สัดส่วนจำนวนพนักงานแต่ละแผนก</h3>
            
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                @php
                    $maxCount = $departments->max('employees_count') ?: 1;
                @endphp
                @foreach($departments as $dept)
                    @php
                        $percentage = round(($dept->employees_count / $maxCount) * 100);
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.625rem;">
                            <span style="color: var(--text-main);">{{ $dept->name }}</span>
                            <span style="color: var(--primary-color);">{{ $dept->employees_count }} คน</span>
                        </div>
                        <div style="width: 100%; height: 10px; background: rgba(226, 232, 240, 0.5); border-radius: 9999px; overflow: hidden; border: 1px solid rgba(226, 232, 240, 0.8);">
                            <div style="width: {{ $percentage }}%; height: 100%; border-radius: 9999px; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);" class="bg-gradient-primary"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
