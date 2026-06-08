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

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>الاسم</th>
                <th>النقاط الحالية</th>
                <th style="width: 45%;">تعديل النقاط</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td class="text-center">{{ $student->id }}</td>
                    <td>{{ $student->name }}</td>
                    <td class="text-center fw-bold">{{ $student->points }}</td>
                    <td>
                        <form method="POST" action="{{ route('students.updatePoints', $student->id) }}" class="row g-2">
                            @csrf
                            @method('PATCH')
                            <div class="col-3">
                                <input type="number" name="points" class="form-control" placeholder="مثلاً 5 أو -2" required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="reason" class="form-control" placeholder="سبب الإضافة / الحذف">
                            </div>
                            <div class="col-3">
                                <button class="btn btn-primary w-100">تحديث</button>
                            </div>
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
    <small class="text-muted">يظهر السبب والكمية في محفظة الأهل داخل التطبيق.</small>
@endsection
