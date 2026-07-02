@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="max-width: 480px; margin-top: 3rem; margin-bottom: 4rem;">
    <div class="card glass-card" style="padding: 2.5rem; border-radius: var(--radius-lg);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;" class="text-gradient">Sign In</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem;">ยินดีต้อนรับกลับเข้าสู่ระบบจัดการงานบุคคล</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">อีเมลผู้ใช้งาน (Email)</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="example@scg.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">รหัสผ่าน (Password)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); cursor: pointer;">
                    <input type="checkbox" name="remember" style="accent-color: var(--primary-color);"> จดจำฉันไว้
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">
                เข้าสู่ระบบ
            </button>
        </form>

        <div style="text-align: center; font-size: 0.875rem; color: var(--text-muted); margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            ยังไม่มีบัญชีผู้ใช้งานใช่ไหม? 
            <a href="{{ route('register') }}" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">สมัครสมาชิกใหม่</a>
        </div>
        
        <div style="margin-top: 1.5rem; background: rgba(79, 70, 229, 0.05); padding: 1rem; border-radius: var(--radius-md); font-size: 0.825rem; border: 1px dashed rgba(79, 70, 229, 0.2);">
            <strong style="color: var(--primary-color);">ข้อมูลเข้าสู่ระบบสำหรับทดสอบ (Demo Credentials):</strong><br/>
            อีเมล: <code style="font-weight: 600;">manager@scg.com</code><br/>
            รหัสผ่าน: <code style="font-weight: 600;">password123</code>
        </div>
    </div>
</div>
@endsection
