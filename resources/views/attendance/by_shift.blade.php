@extends('layouts.app')
@section('content')
    <h2>عرض الحضور حسب الفترة</h2>
    <br>

    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col">
                <select name="shift_id" class="form-control">
                    @foreach($shifts as $s)
                        <option value="{{ $s->id }}" {{ request('shift_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <input type="date" name="date" class="form-control" value="{{ request('date', now()->toDateString()) }}">
            </div>
            <div class="col">
                <select name="status" class="form-control">
                    <option value="" {{ request('status') === '' || request('status') === null ? 'selected' : '' }}>الكل</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>الحاضرون فقط</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>الغائبون فقط</option>
                    <option value="excused" {{ request('status') === 'excused' ? 'selected' : '' }}>غياب مبرّر</option>
                    <option value="unexcused" {{ request('status') === 'unexcused' ? 'selected' : '' }}>غياب غير مبرّر</option>
                </select>
            </div>
            <div class="col">
                <button class="btn btn-primary">عرض</button>
            </div>
        </div>
    </form>

    @if($students !== null)
        {{-- جدول الطلاب --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">الطلاب</h4>
            <button class="btn btn-success btn-sm export-btn" data-target="studentsTable" data-prefix="الطلاب">تصدير CSV</button>
        </div>
        <table id="studentsTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>الطالب</th>
                    <th>الحضور</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $s)
                    <tr>
                        <td>{{ $s->name }}</td>
                        <td>
                            @if($s->attendances->isNotEmpty())
                                <span class="badge bg-success">حاضر {{ \Carbon\Carbon::parse($s->attendances[0]->check_in_time)->format('h:i A') }}</span>
                            @elseif($s->absences->isNotEmpty() && $s->absences[0]->type === 'excused')
                                <span class="badge bg-warning text-dark">غائب مبرّر</span>
                            @elseif($s->absences->isNotEmpty())
                                <span class="badge bg-danger">غائب غير مبرّر</span>
                            @else
                                <span class="badge bg-secondary">غائب</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if($students->isEmpty())
                    <tr><td colspan="2" class="text-center">لا يوجد طلاب في هذه الفترة</td></tr>
                @endif
            </tbody>
        </table>

        {{-- جدول الأساتذة --}}
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
            <h4 class="mb-0">الأساتذة</h4>
            <button class="btn btn-success btn-sm export-btn" data-target="teachersTable" data-prefix="الأساتذة">تصدير CSV</button>
        </div>
        <table id="teachersTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>الأستاذ</th>
                    <th>المادة</th>
                    <th>الحضور</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $t)
                    <tr>
                        <td>{{ $t->name }}</td>
                        <td>{{ $t->subject }}</td>
                        <td>
                            @if($t->attendances->isNotEmpty())
                                ✅ {{ \Carbon\Carbon::parse($t->attendances[0]->check_in_time)->format('h:i A') }}
                            @else
                                ❌ غائب
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if($teachers->isEmpty())
                    <tr><td colspan="3" class="text-center">لا يوجد أساتذة في هذه الفترة</td></tr>
                @endif
            </tbody>
        </table>
    @endif

    <script>
        function exportTableToCSV(table, filename) {
            const rows = [];
            table.querySelectorAll('tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('th, td').forEach(col => {
                    let text = col.innerText.trim();
                    if (text.includes('✅')) text = text.replace('✅', 'حاضر');
                    if (text.includes('❌')) text = text.replace('❌', 'غائب');
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
                const shiftName = document.querySelector('select[name="shift_id"] option:checked').textContent.trim();
                const date = document.querySelector('input[name="date"]').value;
                exportTableToCSV(table, `${btn.dataset.prefix}_${shiftName}_${date}.csv`);
            });
        });
    </script>
@endsection
