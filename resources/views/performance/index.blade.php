@extends('layouts.app')

@section('content')
<div class="container animate-fade-in" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;" class="text-gradient">การประเมินผลงาน (Performance)</h1>
        <p style="color: var(--text-muted);">วิเคราะห์คะแนนความสำเร็จตามวัตถุประสงค์และผลลัพธ์หลัก (OKRs) และคะแนนประเมินการทำงานประจำปี</p>
    </div>

    <!-- Performance Grid -->
    <div class="card glass-card" style="padding: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--text-main);">คะแนนประเมินพนักงานรายบุคคล</h3>

        @if(count($employees) > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>พนักงาน</th>
                            <th>แผนกงาน</th>
                            <th>ตำแหน่ง</th>
                            <th>คะแนนผลงานปัจจุบัน</th>
                            <th>ระดับประเมิน (Grade)</th>
                            <th style="text-align: right;">การประเมินใหม่</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                            @php
                                $score = $emp->performance_score;
                                $grade = 'F';
                                $gradeClass = 'badge-danger';
                                if ($score >= 90) { $grade = 'A (Excellent)'; $gradeClass = 'badge-success'; }
                                elseif ($score >= 80) { $grade = 'B (Good)'; $gradeClass = 'badge-success'; }
                                elseif ($score >= 70) { $grade = 'C (Average)'; $gradeClass = 'badge-warning'; }
                                elseif ($score >= 60) { $grade = 'D (Below Avg)'; $gradeClass = 'badge-warning'; }
                            @endphp
                            <tr>
                                <td style="font-weight: 600;">
                                    {{ $emp->first_name }} {{ $emp->last_name }}
                                </td>
                                <td style="color: var(--text-muted);">{{ $emp->department->name ?? '-' }}</td>
                                <td style="color: var(--text-muted);">{{ $emp->position->title ?? '-' }}</td>
                                <td style="font-weight: 700;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 60px; height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden;">
                                            <div style="width: {{ $score }}%; height: 100%;" class="bg-gradient-primary"></div>
                                        </div>
                                        <span>{{ $score }} / 100</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $gradeClass }}">{{ $grade }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <form action="{{ route('performance.update', $emp->id) }}" method="POST" style="margin: 0; display: inline-flex; align-items: center; gap: 0.5rem;">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="performance_score" value="{{ $score }}" min="0" max="100" class="form-control" style="width: 80px; padding: 0.35rem 0.5rem; text-align: center;" required>
                                        <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">อัปเดต</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 3rem 0; color: var(--text-muted);">
                <p style="font-size: 1rem; font-weight: 500;">ไม่พบรายชื่อพนักงานที่ต้องประเมินผล</p>
            </div>
        @endif
    </div>
</div>
@endsection
