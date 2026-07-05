@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="max-width: 600px; margin-top: 3rem; margin-bottom: 5rem;">
    <div class="card glass-card" style="padding: 2.75rem; border-radius: var(--radius-lg); border: 1px solid rgba(99, 102, 241, 0.15); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05); transition: max-width 0.3s ease;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.025em;" class="text-gradient">Sign Up</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">สร้างบัญชีผู้ใช้งานใหม่ของระบบ Digital HR</p>
        </div>

        <!-- Role Toggles -->
        <div style="display: flex; background: rgba(226, 232, 240, 0.4); padding: 4px; border-radius: 9999px; margin-bottom: 2rem; border: 1px solid rgba(226, 232, 240, 0.8);">
            <button type="button" id="tab-employee" onclick="setRole('employee')" style="flex: 1; padding: 0.75rem; border-radius: 9999px; border: none; background: white; font-weight: 700; color: var(--primary-color); cursor: pointer; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.08); font-family: inherit; font-size: 0.9rem;">
                <span style="display: inline-flex; align-items: center; gap: 0.5rem; justify-content: center; width: 100%;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    บัญชีพนักงาน
                </span>
            </button>
            <button type="button" id="tab-manager" onclick="setRole('manager')" style="flex: 1; padding: 0.75rem; border-radius: 9999px; border: none; background: transparent; font-weight: 600; color: var(--text-muted); cursor: pointer; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); font-family: inherit; font-size: 0.9rem;">
                <span style="display: inline-flex; align-items: center; gap: 0.5rem; justify-content: center; width: 100%;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                    ผู้จัดการ / HR
                </span>
            </button>
        </div>

        <form action="{{ route('register') }}" method="POST" id="register-form">
            @csrf

            <!-- Hidden input to store role selection -->
            <input type="hidden" name="role" id="role-input" value="employee">

            <!-- Employee Fields Block -->
            <div id="employee-fields" style="transition: opacity 0.25s ease;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="first_name" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">ชื่อจริง (First Name) <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="สมชาย" value="{{ old('first_name') }}" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="last_name" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">นามสกุล (Last Name) <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="มั่นคง" value="{{ old('last_name') }}" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="phone" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">เบอร์โทรศัพท์ (Phone)</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="0812345678" value="{{ old('phone') }}" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="date_of_birth" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">วันเกิด (Date of Birth)</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="department_id" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">แผนก (Department) <span style="color: #ef4444;">*</span></label>
                        <select name="department_id" id="department_id" class="form-control" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem; width: 100%; height: 45px;" required>
                            <option value="">เลือกแผนก</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="position_id" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">ตำแหน่ง (Position) <span style="color: #ef4444;">*</span></label>
                        <select name="position_id" id="position_id" class="form-control" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem; width: 100%; height: 45px;" required>
                            <option value="">เลือกตำแหน่ง</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Manager Fields Block -->
            <div id="manager-fields" style="display: none; transition: opacity 0.25s ease;">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="name" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">ชื่อ-นามสกุล (Full Name) <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="สมชาย มั่นคง" value="{{ old('name') }}" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;">
                </div>
            </div>

            <!-- Shared Fields -->
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="email" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">อีเมล (Email) <span style="color: #ef4444;">*</span></label>
                <input type="email" name="email" id="email" class="form-control" placeholder="somchai@scg.com" value="{{ old('email') }}" style="border-radius: var(--radius-md); padding: 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem;" required>
            </div>

            <!-- Password with Show/Hide toggle -->
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="password" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">รหัสผ่าน (Password) <span style="color: #ef4444;">*</span></label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" placeholder="อย่างน้อย 6 ตัวอักษร" style="border-radius: var(--radius-md); padding: 0.75rem 3rem 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem; width: 100%;" required>
                    <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; padding: 0.25rem;">
                        <svg id="eye-icon-password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
                <!-- Strength Indicator -->
                <div id="password-strength-container" style="margin-top: 0.625rem; display: none;">
                    <div style="display: flex; gap: 4px; height: 5px; background: rgba(226, 232, 240, 0.6); border-radius: 999px; overflow: hidden; margin-bottom: 0.35rem;">
                        <div id="strength-bar-1" style="flex: 1; height: 100%; transition: all 0.3s ease;"></div>
                        <div id="strength-bar-2" style="flex: 1; height: 100%; transition: all 0.3s ease;"></div>
                        <div id="strength-bar-3" style="flex: 1; height: 100%; transition: all 0.3s ease;"></div>
                    </div>
                    <span id="password-strength-text" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">ความแข็งแรงรหัสผ่าน: -</span>
                </div>
            </div>

            <!-- Confirm Password with Show/Hide toggle -->
            <div class="form-group" style="margin-bottom: 2.25rem;">
                <label for="password_confirmation" class="form-label" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; font-size: 0.875rem;">ยืนยันรหัสผ่าน (Confirm Password) <span style="color: #ef4444;">*</span></label>
                <div style="position: relative;">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="ยืนยันรหัสผ่านอีกครั้ง" style="border-radius: var(--radius-md); padding: 0.75rem 3rem 0.75rem 1rem; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.6); outline: none; font-size: 0.9rem; width: 100%;" required>
                    <button type="button" onclick="togglePassword('password_confirmation')" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; padding: 0.25rem;">
                        <svg id="eye-icon-password_confirmation" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
                <span id="password-match-text" style="font-size: 0.75rem; display: none; font-weight: 600; margin-top: 0.35rem;"></span>
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

<script>
    // Positions mapping preloaded from department list database
    const positionsByDept = {
        @foreach($departments as $dept)
            "{{ $dept->id }}": [
                @foreach($dept->positions as $pos)
                    { id: "{{ $pos->id }}", title: "{{ $pos->title }}" },
                @endforeach
            ],
        @endforeach
    };

    function setRole(role) {
        document.getElementById('role-input').value = role;
        
        const tabEmployee = document.getElementById('tab-employee');
        const tabManager = document.getElementById('tab-manager');
        const employeeFields = document.getElementById('employee-fields');
        const managerFields = document.getElementById('manager-fields');
        
        // Input fields references
        const firstName = document.getElementById('first_name');
        const lastName = document.getElementById('last_name');
        const deptSelect = document.getElementById('department_id');
        const posSelect = document.getElementById('position_id');
        const managerName = document.getElementById('name');
        
        if (role === 'employee') {
            // Style active tabs
            tabEmployee.style.background = 'white';
            tabEmployee.style.color = 'var(--primary-color)';
            tabEmployee.style.fontWeight = '700';
            tabEmployee.style.boxShadow = '0 4px 12px rgba(99, 102, 241, 0.08)';
            
            tabManager.style.background = 'transparent';
            tabManager.style.color = 'var(--text-muted)';
            tabManager.style.fontWeight = '600';
            tabManager.style.boxShadow = 'none';
            
            // Toggle form views
            employeeFields.style.display = 'block';
            managerFields.style.display = 'none';
            
            // Adjust validation requirements
            firstName.required = true;
            lastName.required = true;
            deptSelect.required = true;
            posSelect.required = true;
            managerName.required = false;
        } else {
            // Style active tabs
            tabManager.style.background = 'white';
            tabManager.style.color = 'var(--primary-color)';
            tabManager.style.fontWeight = '700';
            tabManager.style.boxShadow = '0 4px 12px rgba(99, 102, 241, 0.08)';
            
            tabEmployee.style.background = 'transparent';
            tabEmployee.style.color = 'var(--text-muted)';
            tabEmployee.style.fontWeight = '600';
            tabEmployee.style.boxShadow = 'none';
            
            // Toggle form views
            employeeFields.style.display = 'none';
            managerFields.style.display = 'block';
            
            // Adjust validation requirements
            firstName.required = false;
            lastName.required = false;
            deptSelect.required = false;
            posSelect.required = false;
            managerName.required = true;
        }
    }

    // Dynamic filtering for position dropdown based on department selection
    document.getElementById('department_id').addEventListener('change', function() {
        const deptId = this.value;
        const positionSelect = document.getElementById('position_id');
        
        // Reset and clear options
        positionSelect.innerHTML = '<option value="">เลือกตำแหน่ง</option>';
        
        if (deptId && positionsByDept[deptId]) {
            positionsByDept[deptId].forEach(pos => {
                const opt = document.createElement('option');
                opt.value = pos.id;
                opt.textContent = pos.title;
                positionSelect.appendChild(opt);
            });
        }
    });

    // Toggle password visibility (show/hide)
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById('eye-icon-' + inputId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            // Change SVG path to eye-off style
            eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            passwordInput.type = 'password';
            // Change SVG path back to normal eye
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }

    // Password strength verification
    const passwordInput = document.getElementById('password');
    const strengthContainer = document.getElementById('password-strength-container');
    const strengthBar1 = document.getElementById('strength-bar-1');
    const strengthBar2 = document.getElementById('strength-bar-2');
    const strengthBar3 = document.getElementById('strength-bar-3');
    const strengthText = document.getElementById('password-strength-text');

    passwordInput.addEventListener('input', function() {
        const val = this.value;
        if (!val) {
            strengthContainer.style.display = 'none';
            return;
        }

        strengthContainer.style.display = 'block';
        
        let score = 0;
        if (val.length >= 6) score++;
        if (val.match(/[0-9]/) || val.match(/[^a-zA-Z0-9]/)) score++;
        if (val.length >= 8 && val.match(/[A-Z]/) && val.match(/[a-z]/)) score++;

        // Reset bars
        strengthBar1.style.backgroundColor = 'rgba(226, 232, 240, 0.6)';
        strengthBar2.style.backgroundColor = 'rgba(226, 232, 240, 0.6)';
        strengthBar3.style.backgroundColor = 'rgba(226, 232, 240, 0.6)';

        if (score === 1 || val.length < 6) {
            strengthBar1.style.backgroundColor = '#ef4444'; // Red
            strengthText.textContent = 'ความแข็งแรงรหัสผ่าน: ง่ายเกินไป (อย่างน้อย 6 ตัวอักษร)';
            strengthText.style.color = '#ef4444';
        } else if (score === 2) {
            strengthBar1.style.backgroundColor = '#f59e0b'; // Orange
            strengthBar2.style.backgroundColor = '#f59e0b';
            strengthText.textContent = 'ความแข็งแรงรหัสผ่าน: ปานกลาง';
            strengthText.style.color = '#f59e0b';
        } else if (score === 3) {
            strengthBar1.style.backgroundColor = '#10b981'; // Green
            strengthBar2.style.backgroundColor = '#10b981';
            strengthBar3.style.backgroundColor = '#10b981';
            strengthText.textContent = 'ความแข็งแรงรหัสผ่าน: ปลอดภัยสูง';
            strengthText.style.color = '#10b981';
        }
    });

    // Real-time password confirmation checker
    const confirmInput = document.getElementById('password_confirmation');
    const matchText = document.getElementById('password-match-text');

    function checkPasswordsMatch() {
        if (!confirmInput.value) {
            matchText.style.display = 'none';
            return;
        }

        matchText.style.display = 'block';
        if (passwordInput.value === confirmInput.value) {
            matchText.textContent = '✓ รหัสผ่านตรงกัน';
            matchText.style.color = '#10b981';
        } else {
            matchText.textContent = '✗ รหัสผ่านไม่ตรงกัน';
            matchText.style.color = '#ef4444';
        }
    }

    passwordInput.addEventListener('input', checkPasswordsMatch);
    confirmInput.addEventListener('input', checkPasswordsMatch);

    // Initial setup on load to check values if form is validation-redirected
    document.addEventListener('DOMContentLoaded', function() {
        const oldRole = "{{ old('role', 'employee') }}";
        setRole(oldRole);
        
        // Trigger position populate if old department exists
        const deptSelect = document.getElementById('department_id');
        if (deptSelect.value) {
            deptSelect.dispatchEvent(new Event('change'));
            // Re-select old position if matches
            const oldPos = "{{ old('position_id') }}";
            if (oldPos) {
                document.getElementById('position_id').value = oldPos;
            }
        }
    });
</script>
@endsection
