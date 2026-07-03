@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="padding-top: 5rem; padding-bottom: 5rem;">
    <div style="text-align: center; margin-bottom: 5rem;">
        <h1 style="font-size: 3.75rem; font-weight: 800; letter-spacing: -0.03em; line-height: 1.15; margin-bottom: 1.75rem; color: var(--text-main);">
            Empower Your Workforce with<br/>
            <span class="text-gradient" style="background: linear-gradient(135deg, var(--primary-color) 0%, #a855f7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Digital HR</span>
        </h1>
        <p style="font-size: 1.25rem; color: var(--text-muted); max-width: 640px; margin: 0 auto 3rem; line-height: 1.75;">
            ระบบจัดการทรัพยากรบุคคลยุคใหม่ที่จะช่วยผลักดันประสิทธิภาพองค์กรของคุณอย่างอัจฉริยะ ทำงานง่าย ข้อมูลครบถ้วนในที่เดียว
        </p>
        <div style="display: flex; gap: 1.25rem; justify-content: center; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 0.875rem 2.5rem; font-size: 1rem; border-radius: 9999px;">Get Started</a>
            <a href="{{ route('login') }}" class="btn btn-outline" style="padding: 0.875rem 2.5rem; font-size: 1rem; border-radius: 9999px;">View Demo</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2.5rem; margin-bottom: 3rem;" class="animate-fade-in delay-200">
        <!-- Feature 1 -->
        <a href="{{ Auth::check() ? route('employees.index') : route('login') }}" class="card glass-card" style="border-radius: var(--radius-lg); padding: 2.25rem; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); text-decoration: none; color: inherit; display: block;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(99, 102, 241, 0.08); color: var(--primary-color); display: flex; align-items: center; justify-content: center; margin-bottom: 1.75rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">HRIS Core</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">ระบบจัดการฐานข้อมูลพนักงานศูนย์กลาง พร้อมการแสดงผลโครงสร้างองค์กรที่เข้าใจง่ายและเป็นระเบียบ</p>
        </a>

        <!-- Feature 2 -->
        <a href="{{ Auth::check() ? route('leaves.index') : route('login') }}" class="card glass-card" style="border-radius: var(--radius-lg); padding: 2.25rem; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); text-decoration: none; color: inherit; display: block;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(16, 185, 129, 0.08); color: var(--secondary-color); display: flex; align-items: center; justify-content: center; margin-bottom: 1.75rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">Time & Attendance</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">บันทึกเวลางาน วางแผนกะการทำงาน และระบบการส่งคำขอลาหยุดที่มีกระบวนการอนุมัติแบบอัตโนมัติ</p>
        </a>

        <!-- Feature 3 -->
        <a href="{{ Auth::check() ? route('performance.index') : route('login') }}" class="card glass-card" style="border-radius: var(--radius-lg); padding: 2.25rem; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); text-decoration: none; color: inherit; display: block;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(245, 158, 11, 0.08); color: #f59e0b; display: flex; align-items: center; justify-content: center; margin-bottom: 1.75rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">Performance</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">ติดตามผลการดำเนินงานระดับบุคคลและองค์กรผ่าน OKRs และระบบการประเมินผลที่โปร่งใส</p>
        </a>
    </div>
</div>
@endsection
