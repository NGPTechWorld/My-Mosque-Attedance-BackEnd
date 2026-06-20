@extends('layouts.app')

@section('content')
    <h2>تسجيل الغياب</h2>
    <p class="text-muted">اختر الفترة والتاريخ، ثم حدّد الطلاب الغائبين ونوع الغياب (مبرّر / غير مبرّر).</p>

    {{-- اختيار الفترة والتاريخ --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="shift_id" class="form-select" required>
                <option value="">اختر الفترة...</option>
                @foreach ($shifts as $shift)
                    <option value="{{ $shift->id }}" @selected((string) ($selectedShift ?? '') === (string) $shift->id)>
                        {{ $shift->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" value="{{ $date }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">عرض</button>
        </div>
    </form>

    @if ($selectedShift)
        @if ($students->isEmpty())
            <div class="alert alert-warning">لا يوجد طلاب في هذه الفترة.</div>
        @else
            <form method="POST" action="{{ route('absences.store') }}">
                @csrf
                <input type="hidden" name="shift_id" value="{{ $selectedShift }}">
                <input type="hidden" name="date" value="{{ $date }}">

                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>الطالب</th>
                            <th>الكود</th>
                            <th style="width: 180px;">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $s)
                            @php
                                $badge = match ($s->status) {
                                    'present' => ['success', 'حاضر'],
                                    'excused' => ['warning', 'غائب مبرّر'],
                                    'unexcused' => ['secondary', 'غائب غير مبرّر'],
                                    default => ['light text-dark border', 'لم يُسجّل'],
                                };
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if ($s->status !== 'present')
                                        <input type="checkbox" name="student_ids[]" value="{{ $s->id }}"
                                            class="form-check-input row-check">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $s->name }}</td>
                                <td class="text-center">{{ $s->code }}</td>
                                <td><span class="badge bg-{{ $badge[0] }}">{{ $badge[1] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card border-0 bg-light p-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">نوع الغياب</label>
                            <select name="type" class="form-select" required>
                                <option value="unexcused">غير مبرّر (يُخصم نقاط)</option>
                                <option value="excused">مبرّر (بدون خصم)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-danger w-100"
                                onclick="return confirm('تأكيد تسجيل الغياب للطلاب المحدّدين وإرسال إشعار لأهلهم؟');">
                                تسجيل الغياب للمحدّدين
                            </button>
                        </div>
                    </div>
                    <small class="text-muted mt-2">
                        الغياب غير المبرّر يُخصم منه عدد النقاط المحدّد في
                        <a href="{{ route('settings.attendanceReward') }}">إعدادات النقاط</a>،
                        أما المبرّر فلا يُخصم. ويُرسل إشعار للأهل في الحالتين.
                    </small>
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const selectAll = document.getElementById('selectAll');
                    const boxes = Array.from(document.querySelectorAll('.row-check'));
                    if (selectAll) {
                        selectAll.addEventListener('change', () => boxes.forEach(b => b.checked = selectAll.checked));
                    }
                    boxes.forEach(b => b.addEventListener('change', () => {
                        if (selectAll) selectAll.checked = boxes.length > 0 && boxes.every(x => x.checked);
                    }));
                });
            </script>
        @endif
    @endif
@endsection
