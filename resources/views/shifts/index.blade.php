@extends('layouts.app')

@section('content')
<h2>إدارة الفترات</h2>

<form action="{{ route('shifts.store') }}" method="POST" class="mb-4">
    @csrf
    <div class="row">
        <div class="col">
            <input name="name" class="form-control" placeholder="اسم الفترة" required>
        </div>
        <div class="col">
            <input name="start_time" type="time" class="form-control" required>
        </div>
        <div class="col">
            <input name="end_time" type="time" class="form-control" required>
        </div>
        <div class="col">
            <select name="days[]" multiple class="form-control" required>
                <option value="0">الأحد</option>
                <option value="1">الاثنين</option>
                <option value="2">الثلاثاء</option>
                <option value="3">الأربعاء</option>
                <option value="4">الخميس</option>
                <option value="5">الجمعة</option>
                <option value="6">السبت</option>
            </select>
        </div>
        <div class="col">
            <button class="btn btn-success">إضافة</button>
        </div>
    </div>
</form>

<table class="table table-bordered">
    <thead><tr><th>الاسم</th><th>الوقت</th><th>الأيام</th><th>حذف</th></tr></thead>
    <tbody>
        @foreach ($shifts as $shift)
        <tr>
            <td>{{ $shift->name }}</td>
            <td>{{ $shift->start_time }} - {{ $shift->end_time }}</td>
            <td>
                @foreach ($shift->days as $day)
                    <span class="badge bg-primary">{{ ["أحد","اثنين","ثلاثاء","أربعاء","خميس","جمعة","سبت"][$day] }}</span>
                @endforeach
            </td>
            <td>
                <form method="POST" action="{{ route('shifts.destroy', $shift->id) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">🗑 حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
