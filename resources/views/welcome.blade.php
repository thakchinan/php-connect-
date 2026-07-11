@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="padding-top: 5rem; padding-bottom: 5rem;">
    <!-- Hero Section -->
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
            <a href="{{ route('register') }}" class="btn btn-outline" style="padding: 0.875rem 2.5rem; font-size: 1rem; border-radius: 9999px;">Register Now</a>
        </div>

        <!-- Mock App Dashboard Showcase -->
        <div class="animate-fade-in delay-100" style="margin: 4.5rem auto 0; max-width: 960px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--card-bg); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); box-shadow: var(--shadow-lg); overflow: hidden;">
            <!-- Window Control Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; background: rgba(150, 150, 150, 0.05); border-bottom: 1px solid var(--border-color);">
                <div style="display: flex; gap: 6px;">
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; font-family: monospace; background: rgba(150, 150, 150, 0.1); padding: 0.25rem 1.5rem; border-radius: 6px; border: 1px solid var(--border-color);">
                    hr-management.system/dashboard
                </div>
                <div style="width: 48px;"></div>
            </div>
            
            <!-- Showcase Content -->
            <div style="padding: 2.25rem; display: grid; grid-template-columns: 220px 1fr; gap: 2rem; text-align: left; background: rgba(150, 150, 150, 0.01);">
                <!-- Mock Sidebar -->
                <div style="display: flex; flex-direction: column; gap: 0.75rem; border-right: 1px solid var(--border-color); padding-right: 1.5rem;">
                    <div style="height: 36px; background: rgba(99, 102, 241, 0.08); border-radius: 8px; display: flex; align-items: center; padding: 0 0.875rem; gap: 0.625rem; color: var(--primary-color); font-weight: 700; font-size: 0.85rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                        Dashboard
                    </div>
                    <div style="height: 36px; border-radius: 8px; display: flex; align-items: center; padding: 0 0.875rem; gap: 0.625rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        Employees
                    </div>
                    <div style="height: 36px; border-radius: 8px; display: flex; align-items: center; padding: 0 0.875rem; gap: 0.625rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Leave & Time
                    </div>
                    <div style="height: 36px; border-radius: 8px; display: flex; align-items: center; padding: 0 0.875rem; gap: 0.625rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        Performance
                    </div>
                </div>
                
                <!-- Mock Dashboard Main -->
                <div>
                    <!-- Top stats -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 1.75rem;">
                        <div style="background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.01);">
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 0.35rem;">พนักงานทั้งหมด</span>
                            <span style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">124 <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">คน</span></span>
                        </div>
                        <div style="background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.01);">
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 0.35rem;">แผนกงาน</span>
                            <span style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">4 <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">แผนก</span></span>
                        </div>
                        <div style="background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.01);">
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 0.35rem;">คำขอวันลา (Active)</span>
                            <span style="font-size: 1.5rem; font-weight: 800; color: #f59e0b;">5 <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">รายการ</span></span>
                        </div>
                    </div>
                    
                    <!-- Table Mock -->
                    <div style="background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.01);">
                        <div style="font-size: 0.875rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem;">รายชื่อพนักงานเข้าใหม่ล่าสุด</div>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <!-- row 1 -->
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; padding-bottom: 0.625rem; border-bottom: 1px solid var(--border-color);">
                                <span style="font-weight: 600; color: var(--text-main);">ณภัทร สมบูรณ์</span>
                                <span style="color: var(--text-muted);">IT & Development</span>
                                <span class="badge badge-success">Active</span>
                            </div>
                            <!-- row 2 -->
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; padding-bottom: 0.625rem; border-bottom: 1px solid var(--border-color);">
                                <span style="font-weight: 600; color: var(--text-main);">สุภัทรา ใจดี</span>
                                <span style="color: var(--text-muted);">HR & OD</span>
                                <span class="badge badge-success">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2.5rem; margin-bottom: 3rem;" class="animate-fade-in delay-200">
        <!-- Feature 1 -->
        <a href="{{ Auth::check() ? route('employees.index') : route('login') }}" class="card glass-card" style="border-radius: var(--radius-lg); padding: 2.25rem; text-decoration: none; color: inherit; display: block;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(99, 102, 241, 0.08); color: var(--primary-color); display: flex; align-items: center; justify-content: center; margin-bottom: 1.75rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">HRIS Core</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">ระบบจัดการฐานข้อมูลพนักงานศูนย์กลาง พร้อมการแสดงผลโครงสร้างองค์กรที่เข้าใจง่ายและเป็นระเบียบ</p>
        </a>

        <!-- Feature 2 -->
        <a href="{{ Auth::check() ? route('leaves.index') : route('login') }}" class="card glass-card" style="border-radius: var(--radius-lg); padding: 2.25rem; text-decoration: none; color: inherit; display: block;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(16, 185, 129, 0.08); color: var(--secondary-color); display: flex; align-items: center; justify-content: center; margin-bottom: 1.75rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">Time & Attendance</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">บันทึกเวลางาน วางแผนกะการทำงาน และระบบการส่งคำขอลาหยุดที่มีกระบวนการอนุมัติแบบอัตโนมัติ</p>
        </a>

        <!-- Feature 3 -->
        <a href="{{ Auth::check() ? route('performance.index') : route('login') }}" class="card glass-card" style="border-radius: var(--radius-lg); padding: 2.25rem; text-decoration: none; color: inherit; display: block;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(245, 158, 11, 0.08); color: #f59e0b; display: flex; align-items: center; justify-content: center; margin-bottom: 1.75rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">Performance</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">ติดตามผลการดำเนินงานระดับบุคคลและองค์กรผ่าน OKRs และระบบการประเมินผลที่โปร่งใส</p>
        </a>
    </div>
</div>
@endsection
