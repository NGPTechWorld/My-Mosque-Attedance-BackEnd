@extends('layouts.app')
@section('content')
<h2>إضافة طالب جديد</h2>
<form method="POST" action="{{ route('students.store') }}">
    @csrf
    <div class="mb-3">
        <label>الاسم:</label>
        <input name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>هاتف ولي الأمر:</label>
        <input name="guardian_phone" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>الفترة:</label>
        <select name="shift_id" class="form-control">
            @foreach($shifts as $shift)
                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">حفظ</button>
</form>
@endsection
