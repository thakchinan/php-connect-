@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="max-width: 480px; margin-top: 4rem; margin-bottom: 5rem;">
    <div class="card glass-card" style="padding: 2.75rem; border-radius: var(--radius-lg); border: 1px solid rgba(99, 102, 241, 0.15); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);">
        <div style="text-align: center; margin-bottom: 2.25rem;">
            <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.025em;" class="text-gradient">Sign Up</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">สร้างบัญชีผู้ใช้งานใหม่ของระบบ Digital HR</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="name" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">ชื่อ-นามสกุล (Full Name)</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="สมชาย มั่นคง" value="{{ old('name') }}" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;" required autofocus>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="email" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">อีเมล (Email)</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="somchai@scg.com" value="{{ old('email') }}" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="password" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">รหัสผ่าน (Password)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="อย่างน้อย 6 ตัวอักษร" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;" required>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="password_confirmation" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">ยืนยันรหัสผ่าน (Confirm Password)</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="ยืนยันรหัสผ่านอีกครั้ง" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.875rem; font-size: 1rem; font-weight: 700; border-radius: var(--radius-md); margin-bottom: 1rem;">
                สมัครสมาชิก
            </button>
        </form>

        <div style="text-align: center; font-size: 0.9rem; color: var(--text-muted); margin-top: 1.75rem; border-top: 1px solid var(--border-color); padding-top: 1.75rem;">
            มีบัญชีผู้ใช้งานแล้วใช่ไหม? 
            <a href="{{ route('login') }}" style="color: var(--primary-color); font-weight: 700; text-decoration: none; hover: text-decoration: underline;">เข้าสู่ระบบ</a>
        </div>
    </div>
</div>
@endsection
