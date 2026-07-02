@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="max-width: 480px; margin-top: 3rem; margin-bottom: 4rem;">
    <div class="card glass-card" style="padding: 2.5rem; border-radius: var(--radius-lg);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;" class="text-gradient">Sign Up</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem;">สร้างบัญชีผู้ใช้งานใหม่ของระบบ Digital HR</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">ชื่อ-นามสกุล (Full Name)</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="สมชาย มั่นคง" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">อีเมล (Email)</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="somchai@scg.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">รหัสผ่าน (Password)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="อย่างน้อย 6 ตัวอักษร" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน (Confirm Password)</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="ยืนยันรหัสผ่านอีกครั้ง" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem; font-weight: 600; margin-top: 1rem;">
                สมัครสมาชิก
            </button>
        </form>

        <div style="text-align: center; font-size: 0.875rem; color: var(--text-muted); margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            มีบัญชีผู้ใช้งานแล้วใช่ไหม? 
            <a href="{{ route('login') }}" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">เข้าสู่ระบบ</a>
        </div>
    </div>
</div>
@endsection
