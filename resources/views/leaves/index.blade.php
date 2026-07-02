@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">ระบบลางาน (Leave & Time)</h1>
        <p style="color: var(--text-muted);">ยื่นคำขอการลาป่วย ลากิจ หรือลาพักร้อน และตรวจสอบประวัติสถานะการอนุมัติ</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; align-items: start;" class="lg:flex-row">
        <!-- Leave Application Form -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); flex: 1;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--primary-color);">เขียนใบลาใหม่</h3>
            
            @if($userEmployee)
                <form action="{{ route('leaves.store') }}" method="POST">
                    @csrf
                    <!-- Hidden field linking to the logged in employee -->
                    <input type="hidden" name="employee_id" value="{{ $userEmployee->id }}">
                    
                    <div class="form-group">
                        <label for="employee_name_display" class="form-label">ผู้ขอลา</label>
                        <input type="text" id="employee_name_display" class="form-control" value="{{ $userEmployee->first_name }} {{ $userEmployee->last_name }} ({{ $userEmployee->position->title ?? '-' }})" disabled style="background-color: #f1f5f9; color: var(--text-muted);">
                    </div>

                    <div class="form-group">
                        <label for="type" class="form-label">ประเภทการลา (Leave Type)</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">เลือกประเภทการลา</option>
                            <option value="ลาป่วย (Sick Leave)">ลาป่วย (Sick Leave)</option>
                            <option value="ลากิจ (Personal Leave)">ลากิจ (Personal Leave)</option>
                            <option value="ลาพักร้อน (Annual Leave)">ลาพักร้อน (Annual Leave)</option>
                            <option value="ลาคลอด / ลาบวช (Other Leaves)">ลาอื่น ๆ (Other Leaves)</option>
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="start_date" class="form-label">เริ่มวันที่</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label for="end_date" class="form-label">ถึงวันที่ (สิ้นสุด)</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason" class="form-label">เหตุผลการลางาน</label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="ระบุรายละเอียดหรือเหตุผลจำเป็นการลางาน..." style="resize: none;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem; font-weight: 600; margin-top: 1rem;">
                        ส่งใบคำขอลาพัก
                    </button>
                </form>
            @else
                <div style="background: rgba(245, 158, 11, 0.05); padding: 1.25rem; border-radius: var(--radius-md); border: 1px dashed rgba(245,158,11,0.3); text-align: center; color: #92400e;">
                    <p style="font-weight: 600; margin-bottom: 0.5rem;">ไม่พบบัญชีพนักงานผูกกับบัญชีผู้ใช้ปัจจุบันของคุณ</p>
                    <p style="font-size: 0.825rem;">(เนื่องจากเป็นบัญชี Manager/Admin ของบริษัท ในระบบตัวอย่างคุณสามารถจัดการอนุมัติใบลาพักของพนักงานท่านอื่น ๆ ได้ที่ตารางประวัติข้างใต้ได้เลยครับ)</p>
                </div>
            @endif
        </div>

        <!-- Leaves History list -->
        <div class="card glass-card" style="border-radius: var(--radius-lg); flex: 2;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">ประวัติคำขออนุมัติลางาน</h3>
            
            @if(count($leaves) > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">
                                <th style="padding: 1rem;">พนักงานผู้ลา</th>
                                <th style="padding: 1rem;">ประเภท</th>
                                <th style="padding: 1rem;">ช่วงเวลาลา</th>
                                <th style="padding: 1rem;">เหตุผลจำเป็น</th>
                                <th style="padding: 1rem; text-align: center;">สถานะ</th>
                                <th style="padding: 1rem; text-align: right;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $lv)
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="padding: 1rem; font-weight: 600;">
                                        {{ $lv->employee->first_name }} {{ $lv->employee->last_name }}
                                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">
                                            {{ $lv->employee->department->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td style="padding: 1rem;">{{ $lv->type }}</td>
                                    <td style="padding: 1rem; font-weight: 500;">
                                        {{ \Carbon\Carbon::parse($lv->start_date)->format('d M Y') }} -<br/>
                                        {{ \Carbon\Carbon::parse($lv->end_date)->format('d M Y') }}
                                    </td>
                                    <td style="padding: 1rem; color: var(--text-muted); max-width: 200px;">{{ $lv->reason ?? '-' }}</td>
                                    <td style="padding: 1rem; text-align: center;">
                                        @if($lv->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($lv->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem; text-align: right;">
                                        @if($lv->status == 'pending')
                                            <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                                <!-- Approve form -->
                                                <form action="{{ route('leaves.update', $lv->id) }}" method="POST" style="margin:0;">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; background: var(--secondary-color);">อนุมัติ</button>
                                                </form>

                                                <!-- Reject form -->
                                                <form action="{{ route('leaves.update', $lv->id) }}" method="POST" style="margin:0;">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; color: #ef4444; border-color: rgba(239,68,68,0.3);">ไม่อนุมัติ</button>
                                                </form>
                                            </div>
                                        @else
                                            <span style="font-size: 0.825rem; color: var(--text-muted);">จัดการแล้ว</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 3rem 0; color: var(--text-muted);">
                    <p style="font-size: 1rem; font-weight: 500;">ยังไม่มีรายการยื่นใบลาในระบบ</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
