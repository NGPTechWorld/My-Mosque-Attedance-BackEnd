@extends('layouts.app')

@section('content')
    <h2>تقرير الدوام الشهري</h2>
    <br>
    <button id="exportBtn" class="btn btn-success mb-3">تصدير Excel بالعربي</button>
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

    @if(count($students))
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>الطالب</th>
                    @foreach($dates as $date)
                        {{-- عرض رقم اليوم فقط --}}
                        <th style="font-size: 12px">{{ $date->format('d') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        @foreach($dates as $date)
                            @php
                                $attended = $student->attendances->firstWhere('date', $date->toDateString());
                            @endphp
                            <td class="text-center">
                                @if($attended)
                                    ✅
                                @else
                                    ❌
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <script>
        // لتحويل أرقام الشهر للأرقام العربية (اختياري)
        function toArabicNumbers(str) {
            const arabicNums = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
            return str.replace(/\d/g, d => arabicNums[d]);
        }

        const monthInput = document.getElementById('monthInput');
        const monthDisplay = document.getElementById('monthDisplay');

        function updateMonthDisplay() {
            if (!monthInput.value) {
                monthDisplay.textContent = '';
                return;
            }
            const monthNumber = monthInput.value.split('-')[1]; // رقم الشهر 2 خانات

            // لتغيير هنا حسب ما تريد: عرض عربي أو إنجليزي
            // عربي:
            // monthDisplay.textContent = 'شهر: ' + toArabicNumbers(monthNumber);

            // أو أرقام عادية:
            monthDisplay.textContent = 'شهر: ' + monthNumber;
        }

        monthInput.addEventListener('input', updateMonthDisplay);
        updateMonthDisplay(); // عرض القيمة عند التحميل
    </script>



<script>
  // دالة لتحويل الأرقام العربية
  function toArabicNumbers(str) {
    const arabicNums = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
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

    cols.forEach((col, index) => {
      let text = col.innerText.trim();

      if (tr.querySelectorAll('th').length > 0 && col.tagName === 'TH' && index > 0) {
        text = toArabicNumbers(text);
      }

      if (text === '✅') text = 'حاضر';
      if (text === '❌') text = 'غائب';

      // لف النص بين علامات اقتباس لمنع مشاكل الفواصل في النص
      text = `"${text.replace(/"/g, '""')}"`;

      row.push(text);
    });

    rows.push(row.join(','));
  });

  const csvString = rows.join('\n');

  const BOM = "\uFEFF"; // إضافة BOM

  const blob = new Blob([BOM + csvString], {type: 'text/csv;charset=utf-8;'});

  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = filename;
  link.style.display = 'none';

  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}


  document.getElementById('exportBtn').addEventListener('click', () => {
    // اسم الملف مع الشهر الحالي أو يمكنك تعديله ليأخذ من قيمة input الشهر
    let monthValue = document.getElementById('monthInput').value || '';
    if (!monthValue) monthValue = new Date().toISOString().slice(0,7);
    const arabicMonth = toArabicNumbers(monthValue);

    exportTableToCSV(`تقرير_الدوام_${arabicMonth}.csv`);
  });
</script>
@endsection