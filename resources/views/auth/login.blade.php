@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="max-width: 480px; margin-top: 4rem; margin-bottom: 5rem;">
    <div class="card glass-card" style="padding: 2.75rem; border-radius: var(--radius-lg); border: 1px solid rgba(99, 102, 241, 0.15); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);">
        <div style="text-align: center; margin-bottom: 2.25rem;">
            <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.025em;" class="text-gradient">Sign In</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">ยินดีต้อนรับกลับเข้าสู่ระบบจัดการงานบุคคล</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="email" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">อีเมลผู้ใช้งาน (Email)</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="example@scg.com" value="{{ old('email') }}" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;" required autofocus>
            </div>

            <div class="form-group" style="margin-bottom: 1.75rem;">
                <label for="password" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">รหัสผ่าน (Password)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;" required>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.625rem; font-size: 0.875rem; color: var(--text-muted); cursor: pointer; user-select: none;">
                    <input type="checkbox" name="remember" style="accent-color: var(--primary-color); width: 16px; height: 16px; border-radius: 4px;"> จดจำฉันไว้
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.875rem; font-size: 1rem; font-weight: 700; border-radius: var(--radius-md); margin-bottom: 1rem;">
                เข้าสู่ระบบ
            </button>
        </form>

        <div style="text-align: center; font-size: 0.9rem; color: var(--text-muted); margin-top: 1.75rem; border-top: 1px solid var(--border-color); padding-top: 1.75rem;">
            ยังไม่มีบัญชีผู้ใช้งานใช่ไหม? 
            <a href="{{ route('register') }}" style="color: var(--primary-color); font-weight: 700; text-decoration: none; hover: text-decoration: underline;">สมัครสมาชิกใหม่</a>
        </div>
    </div>
</div>
@endsection
