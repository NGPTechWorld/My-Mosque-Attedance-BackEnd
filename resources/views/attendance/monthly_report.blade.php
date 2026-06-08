@extends('layouts.app')

@section('content')
    <h2>تقرير الدوام الشهري</h2>
    <br>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <select name="shift_id" class="form-control">
                <option value="">اختر الفترة</option>
                @foreach($shifts as $s)
                    <option value="{{ $s->id }}" {{ $shiftId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="month" id="monthInput" name="month" class="form-control" value="{{ $month }}">
            <small id="monthDisplay" class="text-muted mt-1"></small>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary">عرض التقرير</button>
        </div>
    </form>

    @if($shiftId)
        <p class="text-muted">الأيام التي لا دوام فيها ضمن الفترة تظهر بعلامة «—» (لا تُحتسب غياباً).</p>

        {{-- ===== جدول الطلاب ===== --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">الطلاب</h4>
            <button class="btn btn-success btn-sm export-btn" data-target="studentsTable" data-prefix="الطلاب">تصدير CSV</button>
        </div>
        <div class="table-responsive">
            <table id="studentsTable" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>الطالب</th>
                        @foreach($dates as $date)
                            <th style="font-size: 12px">{{ $date->format('d') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            @foreach($dates as $date)
                                @php
                                    $isWork = in_array($date->dayOfWeek, $shiftDays);
                                    $attended = $isWork ? $student->attendances->firstWhere('date', $date->toDateString()) : null;
                                @endphp
                                <td class="text-center">
                                    @if(!$isWork)
                                        <span class="text-muted">—</span>
                                    @elseif($attended)
                                        ✅
                                    @else
                                        ❌
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($dates) + 1 }}" class="text-center">لا يوجد طلاب في هذه الفترة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ===== جدول الأساتذة ===== --}}
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
            <h4 class="mb-0">الأساتذة</h4>
            <button class="btn btn-success btn-sm export-btn" data-target="teachersTable" data-prefix="الأساتذة">تصدير CSV</button>
        </div>
        <div class="table-responsive">
            <table id="teachersTable" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>الأستاذ</th>
                        @foreach($dates as $date)
                            <th style="font-size: 12px">{{ $date->format('d') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td>{{ $teacher->name }}</td>
                            @foreach($dates as $date)
                                @php
                                    $isWork = in_array($date->dayOfWeek, $shiftDays);
                                    $attended = $isWork ? $teacher->attendances->firstWhere('date', $date->toDateString()) : null;
                                @endphp
                                <td class="text-center">
                                    @if(!$isWork)
                                        <span class="text-muted">—</span>
                                    @elseif($attended)
                                        ✅
                                    @else
                                        ❌
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($dates) + 1 }}" class="text-center">لا يوجد أساتذة في هذه الفترة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <script>
        const monthInput = document.getElementById('monthInput');
        const monthDisplay = document.getElementById('monthDisplay');
        function updateMonthDisplay() {
            monthDisplay.textContent = monthInput.value ? ('شهر: ' + monthInput.value.split('-')[1]) : '';
        }
        monthInput.addEventListener('input', updateMonthDisplay);
        updateMonthDisplay();

        function exportTableToCSV(table, filename) {
            const rows = [];
            table.querySelectorAll('tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('th, td').forEach(col => {
                    let text = col.innerText.trim();
                    if (text === '✅') text = 'حاضر';
                    if (text === '❌') text = 'غائب';
                    text = `"${text.replace(/"/g, '""')}"`;
                    row.push(text);
                });
                rows.push(row.join(','));
            });
            const BOM = "﻿";
            const blob = new Blob([BOM + rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        document.querySelectorAll('.export-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const table = document.getElementById(btn.dataset.target);
                if (!table) { alert('لم يتم العثور على الجدول!'); return; }
                const month = (document.getElementById('monthInput').value) || '';
                exportTableToCSV(table, `${btn.dataset.prefix}_${month}.csv`);
            });
        });
    </script>
@endsection
