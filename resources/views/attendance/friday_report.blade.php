@extends('layouts.app')
@section('content')
<h2>تقرير دوام الجمعة</h2>
<form method="GET" class="mb-3">
    <input type="date" name="date" class="form-control w-25 d-inline-block" value="{{ $date }}">
    <button class="btn btn-primary">عرض</button>
</form>

<table class="table table-bordered">
    <thead><tr><th>الطالب</th><th>الحضور</th></tr></thead>
    <tbody>
        @foreach($students as $student)
            <tr>
                <td>{{ $student->name }}</td>
                <td>
                    @if($student->attendances->isNotEmpty())
                        ✅ {{ \Carbon\Carbon::parse($student->attendances[0]->check_in_time)->format('h:i A') }}
                    @else
                        ❌ غائب
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
