@extends('layouts.app')

@section('content')
    <h2>إدارة النقاط</h2>
    <br>

    <!-- شريط البحث -->
    <form method="GET" class="mb-3 row g-2">
        <div class="col-md-4">
            <input 
                type="search" 
                name="search" 
                value="{{ request('search') }}" 
                class="form-control" 
                placeholder="ابحث عن طالب بالاسم..."
                autocomplete="off"
            >
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">بحث</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>الاسم</th>
                <th>النقاط الحالية</th>
                <th>تعديل</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td class="text-center">{{ $student->id }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->points }}</td>
                    <td>
                        <form method="POST" action="{{ route('students.updatePoints', $student->id) }}" class="d-flex">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="points" class="form-control me-2" placeholder="مثلاً 5 أو -2" required>
                            <button class="btn btn-primary">تحديث</button>
                        </form>
                    </td>
                </tr>
            @endforeach

            @if($students->isEmpty())
                <tr>
                    <td colspan="4" class="text-center">لا توجد نتائج للعرض</td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
