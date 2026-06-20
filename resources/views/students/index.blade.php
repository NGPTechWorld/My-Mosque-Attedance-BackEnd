@extends('layouts.app')

@section('content')
    <h2>قائمة الطلاب</h2>
    <br>

    @if (session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">❌ {{ session('error') }}</div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
        @if (auth()->user()->hasSection('students_create'))
            <a href="{{ route('students.create') }}" class="btn btn-success">إضافة طالب</a>
        @else
            <span></span>
        @endif

        {{-- بحث + فلتر حسب الفترة --}}
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <input type="search" name="search" value="{{ $search ?? '' }}" class="form-control"
                placeholder="بحث بالاسم أو الكود أو الهاتف..." style="min-width: 240px;">
            <label for="shift_id" class="form-label m-0">الفترة:</label>
            <select name="shift_id" id="shift_id" class="form-select" style="width:auto;" onchange="this.form.submit()">
                <option value="">كل الفترات</option>
                @foreach ($shifts as $shift)
                    <option value="{{ $shift->id }}" @selected((string) ($selectedShift ?? '') === (string) $shift->id)>
                        {{ $shift->name }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-primary">بحث</button>
            @if (!empty($selectedShift) || !empty($search))
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            @endif
        </form>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width: 60px; max-width: 60px;">الرقم </th>
                <th>الكود</th>
                <th>الاسم</th>
                <th>الهاتف</th>
                <th>الفترة</th>
                <th>النقاط</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td style="width: 60px; max-width: 60px; text-align: center;">{{ $student->id }}</td>
                    <td class="text-center">{{ $student->code }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->guardian_phone }}</td>
                    <td>{{ $student->shift->name ?? 'غير محددة' }}</td>
                    <td>{{ $student->points }}</td>
                    <td>
                        <form method="POST" action="{{ route('students.checkin', $student->id) }}"
                            style="display:inline-block;">
                            @csrf
                            <button class="btn btn-success btn-sm">تسجيل حضور</button>
                        </form>

                        <form method="POST" action="{{ route('students.absent', $student->id) }}"
                            style="display:inline-block;"
                            onsubmit="return confirm('تأكيد تسجيل غياب الطالب وإرسال إشعار لأهله؟');">
                            @csrf
                            <button class="btn btn-secondary btn-sm">تسجيل غياب</button>
                        </form>

                        <a href="{{ route('students.qr', $student->id) }}" class="btn btn-info btn-sm" target="_blank">QR</a>

                        @if (auth()->user()->hasSection('students_edit'))
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                        @endif

                        @if (auth()->user()->hasSection('students_delete'))
                            <form method="POST" action="{{ route('students.destroy', $student->id) }}" style="display:inline-block;"
                                onsubmit="return confirm('هل أنت متأكد من حذف الطالب؟');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">حذف</button>
                            </form>
                        @endif
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
@endsection



{{-- <div class="d-flex flex-wrap gap-2">
    <!-- أزرار ملونة -->
    <button class="btn btn-primary">Primary</button>
    <button class="btn btn-secondary">Secondary</button>
    <button class="btn btn-success">Success</button>
    <button class="btn btn-danger">Danger</button>
    <button class="btn btn-warning">Warning</button>
    <button class="btn btn-info">Info</button>
    <button class="btn btn-light">Light</button>
    <button class="btn btn-dark">Dark</button>
    <button class="btn btn-link">Link</button>

    <!-- أزرار بإطار فقط -->
    <button class="btn btn-outline-primary">Outline Primary</button>
    <button class="btn btn-outline-secondary">Outline Secondary</button>
    <button class="btn btn-outline-success">Outline Success</button>
    <button class="btn btn-outline-danger">Outline Danger</button>
    <button class="btn btn-outline-warning">Outline Warning</button>
    <button class="btn btn-outline-info">Outline Info</button>
    <button class="btn btn-outline-light">Outline Light</button>
    <button class="btn btn-outline-dark">Outline Dark</button>
</div> --}}