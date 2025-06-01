@extends('layouts.app')

@section('content')
    <h2>إدارة النقاط</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>النقاط الحالية</th>
                <th>تعديل</th>
                <th>حذف النقاط</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->points }}</td>
                    <td>
                        <form method="POST" action="{{ route('students.updatePoints', $student->id) }}" class="d-flex">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="points" class="form-control me-2" placeholder="مثلاً 5 أو -2">
                            <button class="btn btn-primary">تحديث</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('students.updatePoints', $student->id) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="remove" value="1">
                            <button class="btn btn-danger">حذف النقاط</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
