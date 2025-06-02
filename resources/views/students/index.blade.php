@extends('layouts.app')

@section('content')
    <h2>قائمة الطلاب</h2>
    <br>
    <a href="{{ route('students.create') }}" class="btn btn-success mb-3">إضافة طالب</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width: 60px; max-width: 60px;">الرقم </th>
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

                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">تعديل</a>

                        <form method="POST" action="{{ route('students.destroy', $student->id) }}" style="display:inline-block;"
                            onsubmit="return confirm('هل أنت متأكد من حذف الطالب؟');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">حذف</button>
                        </form>
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