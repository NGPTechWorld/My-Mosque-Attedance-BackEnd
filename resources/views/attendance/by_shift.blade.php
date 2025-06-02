@extends('layouts.app')
@section('content')
    <h2>عرض الحضور حسب الفترة</h2>
    <br>
    <button id="exportBtn" class="btn btn-success mb-3">تصدير CSV</button>

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
                <button class="btn btn-primary">عرض</button>
            </div>
        </div>
    </form>

    @if($students)
        <table class="table table-bordered">
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
                                ✅ {{ \Carbon\Carbon::parse($s->attendances[0]->check_in_time)->format('h:i A') }}
                            @else
                                ❌ غائب
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <script>
        function toArabicNumbers(str) {
            const arabicNums = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str.replace(/\d/g, d => arabicNums[d]);
        }

        function exportTableToCSV(filename) {
            const rows = [];
            const table = document.querySelector('table');
            if (!table) {
                alert('لم يتم العثور على الجدول!');
                return;
            }
            const trs = table.querySelectorAll('tr');

            trs.forEach(tr => {
                const cols = tr.querySelectorAll('th, td');
                const row = [];

                cols.forEach(col => {
                    let text = col.innerText.trim();

                    // استبدال الرموز ✅ و ❌ بالنص العربي
                    if (text === '✅') text = 'حاضر';
                    if (text === '❌') text = 'غائب';

                    // لف النص بين علامات اقتباس لمنع مشاكل الفواصل في النص
                    text = `"${text.replace(/"/g, '""')}"`;

                    row.push(text);
                });

                rows.push(row.join(','));
            });

            const csvString = rows.join('\n');
            const BOM = "\uFEFF"; // لضمان ترميز UTF-8

            const blob = new Blob([BOM + csvString], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        document.getElementById('exportBtn').addEventListener('click', () => {
            const shiftName = document.querySelector('select[name="shift_id"] option:checked').textContent.trim();
            const date = document.querySelector('input[name="date"]').value;
            const filename = `تقرير_الحضور_${shiftName}_${date}.csv`;
            exportTableToCSV(filename);
        });
    </script>
@endsection