@extends('layouts.app')

@section('content')
    <h2>قائمة الطلاب</h2>
    <a href="{{ route('students.create') }}" class="btn btn-success mb-3">➕ إضافة طالب</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
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
                <td>{{ $student->name }}</td>
                <td>{{ $student->guardian_phone }}</td>
                <td>{{ $student->shift->name ?? 'غير محددة' }}</td>
                <td>{{ $student->points }}</td>
                <td>
                    <form method="POST" action="{{ route('students.checkin', $student->id) }}">
                        @csrf
                        <button class="btn btn-success btn-sm">تسجيل حضور</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
