@extends('layouts.app')
@section('content')
<h2>دوام الطالب: {{ $student->name }}</h2>
<br>
<table class="table table-bordered">
    <thead>
        <tr><th>التاريخ</th><th>ساعة الحضور</th></tr>
    </thead>
    <tbody>
        @foreach($student->attendances as $a)
        <tr>
            <td>{{ $a->date }}</td>
            <td>{{ $a->check_in_time }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
