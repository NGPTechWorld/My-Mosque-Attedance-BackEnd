@extends('layouts.app')
@section('content')
<h2>تقرير دوام الجمعة</h2>
<form method="GET" class="mb-3">
    <input type="date" name="date" class="form-control w-25 d-inline-block" value="{{ $date }}">
    <button class="btn btn-primary">عرض</button>
</form>

{{-- الطلاب --}}
<h4>الطلاب</h4>
<table class="table table-bordered">
    <thead><tr><th>الطالب</th><th>الحضور</th></tr></thead>
    <tbody>
        @foreach($students as $student)
            <tr>
                <td>{{ $student->name }}</td>
                <td>
                    @if($student->attendances->isNotEmpty())
                        <span class="badge bg-success">حاضر {{ \Carbon\Carbon::parse($student->attendances[0]->check_in_time)->format('h:i A') }}</span>
                    @elseif($student->absences->isNotEmpty() && $student->absences[0]->type === 'excused')
                        <span class="badge bg-warning text-dark">غائب مبرّر</span>
                    @elseif($student->absences->isNotEmpty())
                        <span class="badge bg-danger">غائب غير مبرّر</span>
                    @else
                        <span class="badge bg-secondary">غائب</span>
                    @endif
                </td>
            </tr>
        @endforeach
        @if($students->isEmpty())
            <tr><td colspan="2" class="text-center">لا يوجد طلاب</td></tr>
        @endif
    </tbody>
</table>

{{-- الأساتذة --}}
<h4 class="mt-4">الأساتذة</h4>
<table class="table table-bordered">
    <thead><tr><th>الأستاذ</th><th>المادة</th><th>الحضور</th></tr></thead>
    <tbody>
        @foreach($teachers as $teacher)
            <tr>
                <td>{{ $teacher->name }}</td>
                <td>{{ $teacher->subject }}</td>
                <td>
                    @if($teacher->attendances->isNotEmpty())
                        ✅ {{ \Carbon\Carbon::parse($teacher->attendances[0]->check_in_time)->format('h:i A') }}
                    @else
                        ❌ غائب
                    @endif
                </td>
            </tr>
        @endforeach
        @if($teachers->isEmpty())
            <tr><td colspan="3" class="text-center">لا يوجد أساتذة</td></tr>
        @endif
    </tbody>
</table>
@endsection
