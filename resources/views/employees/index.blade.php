@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;" class="text-gradient">จัดการข้อมูลพนักงาน (Employees)</h1>
            <p style="color: var(--text-muted);">รายชื่อพนักงาน ข้อมูลสังกัด และการจัดการสถานะการทำงาน</p>
        </div>
        <button class="btn btn-primary" onclick="openAddModal()" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.5rem; font-weight: 600;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            เพิ่มข้อมูลพนักงาน
        </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card glass-card" style="margin-bottom: 2rem; padding: 1.25rem;">
        <form action="{{ route('employees.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; margin: 0;">
            <div class="form-group" style="flex: 1; min-width: 240px; margin-bottom: 0;">
                <label for="search" class="form-label">ค้นหารายชื่อ / เบอร์โทร</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาชื่อ พนักงาน..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="width: 200px; margin-bottom: 0;">
                <label for="filter_department" class="form-label">แผนก (Department)</label>
                <select name="department_id" id="filter_department" class="form-control">
                    <option value="">ทั้งหมด</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="width: 150px; margin-bottom: 0;">
                <label for="filter_status" class="form-label">สถานะ (Status)</label>
                <select name="status" id="filter_status" class="form-control">
                    <option value="">ทั้งหมด</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>On Leave</option>
                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.25rem;">ค้นหา</button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline" style="padding: 0.625rem 1.25rem;">ล้างค่า</a>
            </div>
        </form>
    </div>

    <!-- Employees List Card -->
    <div class="card glass-card" style="padding: 1.5rem;">
        @if(count($employees) > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>พนักงาน</th>
                            <th>อีเมล</th>
                            <th>แผนก</th>
                            <th>ตำแหน่ง / เงินเดือน</th>
                            <th>วันที่เริ่มงาน</th>
                            <th style="text-align: center;">สถานะ</th>
                            <th style="text-align: right;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                            <tr>
                                <td style="font-weight: 600;">
                                    <div>{{ $emp->first_name }} {{ $emp->last_name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">โทร: {{ $emp->phone ?? '-' }}</div>
                                </td>
                                <td style="color: var(--text-muted);">{{ $emp->user->email ?? '-' }}</td>
                                <td style="font-weight: 500;">{{ $emp->department->name ?? '-' }}</td>
                                <td>
                                    <div>{{ $emp->position->title ?? '-' }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ number_format($emp->position->base_salary ?? 0) }} ฿/เดือน</div>
                                </td>
                                <td>{{ $emp->join_date ? \Carbon\Carbon::parse($emp->join_date)->format('d M Y') : '-' }}</td>
                                <td style="text-align: center;">
                                    @if($emp->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($emp->status == 'leave')
                                        <span class="badge badge-warning">On Leave</span>
                                    @else
                                        <span class="badge badge-danger">Terminated</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                        <!-- Edit Button -->
                                        <button class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; color: var(--primary-color); border-color: var(--primary-color);" 
                                                onclick="openEditModal({
                                                    id: '{{ $emp->id }}',
                                                    first_name: '{{ $emp->first_name }}',
                                                    last_name: '{{ $emp->last_name }}',
                                                    email: '{{ $emp->user->email ?? '' }}',
                                                    phone: '{{ $emp->phone }}',
                                                    date_of_birth: '{{ $emp->date_of_birth }}',
                                                    join_date: '{{ $emp->join_date }}',
                                                    department_id: '{{ $emp->department_id }}',
                                                    position_id: '{{ $emp->position_id }}',
                                                    status: '{{ $emp->status }}',
                                                    performance_score: '{{ $emp->performance_score }}'
                                                })">
                                            แก้ไข
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจว่าต้องการลบพนักงานรายนี้? บัญชีผู้ใช้จะถูกลบไปด้วย')" style="margin: 0; display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; color: #ef4444; border-color: rgba(239,68,68,0.3); background: none;">
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 3rem 0; color: var(--text-muted);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem; opacity: 0.5;"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                <p style="font-size: 1.1rem; font-weight: 500;">ไม่พบข้อมูลพนักงานในระบบ</p>
            </div>
        @endif
    </div>
</div>

<!-- ================= ADD EMPLOYEE MODAL ================= -->
<div id="add-employee-modal" class="modal-overlay" style="display: none;">
    <div class="card glass-card animate-fade-in" style="width: 100%; max-width: 600px; padding: 2rem; margin: 1.5rem;">
        <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <span>เพิ่มข้อมูลพนักงานใหม่</span>
            <button onclick="closeAddModal()" style="background: none; border: none; font-size: 1.5rem; font-weight: bold; cursor: pointer; color: var(--text-muted);">&times;</button>
        </h3>
        
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="first_name" class="form-label">ชื่อจริง (First Name)</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="ปิยมาศ" required>
                </div>
                <div class="form-group">
                    <label for="last_name" class="form-label">นามสกุล (Last Name)</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="วงษ์สุวรรณ" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="email" class="form-label">อีเมลบริษัท (Email)</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="example@scg.com" required>
                </div>
                <div class="form-group">
                    <label for="phone" class="form-label">เบอร์โทรศัพท์ (Phone)</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="0812345678">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="department_id" class="form-label">แผนก (Department)</label>
                    <select name="department_id" id="department_id" class="form-control" required>
                        <option value="">เลือกแผนก</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="position_id" class="form-label">ตำแหน่ง (Position)</label>
                    <select name="position_id" id="position_id" class="form-control" required>
                        <option value="">เลือกตำแหน่ง</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->title }} ({{ number_format($pos->base_salary) }} ฿)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="date_of_birth" class="form-label">วันเกิด (Date of Birth)</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control">
                </div>
                <div class="form-group">
                    <label for="join_date" class="form-label">วันที่เริ่มงาน (Join Date)</label>
                    <input type="date" name="join_date" id="join_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="status" class="form-label">สถานะพนักงาน (Status)</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" selected>Active</option>
                        <option value="leave">On Leave</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="performance_score" class="form-label">คะแนนตั้งต้น OKR/ผลงาน</label>
                    <input type="number" name="performance_score" id="performance_score" class="form-control" value="80" min="0" max="100">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" onclick="closeAddModal()" style="padding: 0.625rem 1.25rem;">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.5rem;">บันทึกพนักงาน</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= EDIT EMPLOYEE MODAL ================= -->
<div id="edit-employee-modal" class="modal-overlay" style="display: none;">
    <div class="card glass-card animate-fade-in" style="width: 100%; max-width: 600px; padding: 2rem; margin: 1.5rem;">
        <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <span>แก้ไขข้อมูลพนักงาน</span>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.5rem; font-weight: bold; cursor: pointer; color: var(--text-muted);">&times;</button>
        </h3>
        
        <form id="edit-form" action="" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="edit_first_name" class="form-label">ชื่อจริง (First Name)</label>
                    <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_last_name" class="form-label">นามสกุล (Last Name)</label>
                    <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="edit_email" class="form-label">อีเมลบริษัท (Email)</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_phone" class="form-label">เบอร์โทรศัพท์ (Phone)</label>
                    <input type="text" name="phone" id="edit_phone" class="form-control">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="edit_department_id" class="form-label">แผนก (Department)</label>
                    <select name="department_id" id="edit_department_id" class="form-control" required>
                        <option value="">เลือกแผนก</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_position_id" class="form-label">ตำแหน่ง (Position)</label>
                    <select name="position_id" id="edit_position_id" class="form-control" required>
                        <option value="">เลือกตำแหน่ง</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->title }} ({{ number_format($pos->base_salary) }} ฿)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="edit_date_of_birth" class="form-label">วันเกิด (Date of Birth)</label>
                    <input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control">
                </div>
                <div class="form-group">
                    <label for="edit_join_date" class="form-label">วันที่เริ่มงาน (Join Date)</label>
                    <input type="date" name="join_date" id="edit_join_date" class="form-control">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="edit_status" class="form-label">สถานะพนักงาน (Status)</label>
                    <select name="status" id="edit_status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="leave">On Leave</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_performance_score" class="form-label">คะแนน OKR/ผลงาน</label>
                    <input type="number" name="performance_score" id="edit_performance_score" class="form-control" min="0" max="100">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()" style="padding: 0.625rem 1.25rem;">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.5rem;">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle Add Modal
    function openAddModal() {
        document.getElementById('add-employee-modal').style.display = 'flex';
    }
    
    function closeAddModal() {
        document.getElementById('add-employee-modal').style.display = 'none';
    }

    // Toggle Edit Modal and Prefill data
    function openEditModal(emp) {
        // Set action URL dynamically
        document.getElementById('edit-form').action = '/employees/' + emp.id;
        
        // Prefill inputs
        document.getElementById('edit_first_name').value = emp.first_name;
        document.getElementById('edit_last_name').value = emp.last_name;
        document.getElementById('edit_email').value = emp.email;
        document.getElementById('edit_phone').value = emp.phone || '';
        document.getElementById('edit_date_of_birth').value = emp.date_of_birth || '';
        document.getElementById('edit_join_date').value = emp.join_date || '';
        document.getElementById('edit_department_id').value = emp.department_id;
        document.getElementById('edit_position_id').value = emp.position_id;
        document.getElementById('edit_status').value = emp.status;
        document.getElementById('edit_performance_score').value = emp.performance_score;
        
        document.getElementById('edit-employee-modal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('edit-employee-modal').style.display = 'none';
    }

    // Close modals on clicking outside the modal box
    window.onclick = function(event) {
        let addModal = document.getElementById('add-employee-modal');
        let editModal = document.getElementById('edit-employee-modal');
        if (event.target == addModal) {
            closeAddModal();
        }
        if (event.target == editModal) {
            closeEditModal();
        }
    }
</script>
@endsection
