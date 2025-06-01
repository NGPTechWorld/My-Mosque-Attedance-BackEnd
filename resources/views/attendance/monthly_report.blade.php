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
@endsection
