@extends('layouts.app')

@section('content')
<div class="container animate-fade-in">
    <div style="text-align: center; margin-top: 4rem; margin-bottom: 4rem;">
        <h1 style="font-size: 3.5rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 1.5rem;">
            Empower Your Workforce with<br/>
            <span class="text-gradient">Digital HR</span>
        </h1>
        <p style="font-size: 1.25rem; color: var(--text-muted); max-width: 600px; margin: 0 auto 2.5rem;">
            Streamline your HR processes, manage employee data, and build a high-performance culture with our modern platform.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">Get Started</a>
            <a href="{{ route('login') }}" class="btn btn-outline" style="padding: 0.75rem 2rem; font-size: 1rem;">View Demo</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 4rem;" class="animate-fade-in delay-200">
        <!-- Feature 1 -->
        <div class="card glass-panel" style="border-radius: var(--radius-lg);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(79, 70, 229, 0.1); color: var(--primary-color); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">HRIS Core</h3>
            <p style="color: var(--text-muted);">Centralized employee database with rich profiles and organizational structure management.</p>
        </div>

        <!-- Feature 2 -->
        <div class="card glass-panel" style="border-radius: var(--radius-lg);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: var(--secondary-color); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">Time & Attendance</h3>
            <p style="color: var(--text-muted);">Automated time tracking, shift scheduling, and streamlined leave request workflows.</p>
        </div>

        <!-- Feature 3 -->
        <div class="card glass-panel" style="border-radius: var(--radius-lg);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">Performance</h3>
            <p style="color: var(--text-muted);">Track OKRs, manage 360-degree feedback, and empower continuous employee learning.</p>
        </div>
    </div>
</div>
@endsection
