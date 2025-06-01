@extends('layouts.app')
@section('content')
<h2>عرض الحضور حسب الفترة</h2>
<br>
<form method="GET" class="mb-4">
    <div class="row">
        <div class="col">
            <select name="shift_id" class="form-control">
                @foreach($shifts as $s)
                    <option value="{{ $s->id }}" {{ request('shift_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <input type="date" name="date" class="form-control" value="{{ request('date', now()->toDateString()) }}">
        </div>
        <div class="col">
            <button class="btn btn-primary">عرض</button>
        </div>
    </div>
</form>

@if($students)
<table class="table table-bordered">
    <thead><tr><th>الطالب</th><th>الحضور</th></tr></thead>
    <tbody>
        @foreach($students as $s)
        <tr>
            <td>{{ $s->name }}</td>
            <td>
                @if($s->attendances->isNotEmpty())
                    ✅ {{ $s->attendances[0]->check_in_time }}
                @else
                    ❌ غائب
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
