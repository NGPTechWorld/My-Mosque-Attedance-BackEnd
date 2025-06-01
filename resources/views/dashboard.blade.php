@extends('layouts.app')

@section('content')
    <h2>لوحة الإحصائيات العامة</h2>
    <div class="row">
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">عدد الطلاب</h5>
                    <p class="card-text fs-4">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">عدد الفترات</h5>
                    <p class="card-text fs-4">{{ $totalShifts }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">عدد أيام الحضور</h5>
                    <p class="card-text fs-4">{{ $totalAttendance }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">عدد أيام الغياب</h5>
                    <p class="card-text fs-4">{{ $totalAbsence }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
