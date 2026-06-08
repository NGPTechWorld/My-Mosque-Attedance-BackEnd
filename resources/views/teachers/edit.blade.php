@extends('layouts.app')
@section('content')
<h2>تعديل الأستاذ</h2>
<form method="POST" action="{{ route('teachers.update', $teacher->id) }}">
    @csrf
    @method('PUT')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="mb-3">
        <label>كود الأستاذ (للـ QR):</label>
        <input name="code" class="form-control" value="{{ old('code', $teacher->code) }}">
    </div>
    <div class="mb-3">
        <label>الاسم:</label>
        <input name="name" class="form-control" value="{{ old('name', $teacher->name) }}" required>
    </div>
    <div class="mb-3">
        <label>الهاتف:</label>
        <input name="phone" class="form-control" value="{{ old('phone', $teacher->phone) }}">
    </div>
    <div class="mb-3">
        <label>المادة / الحلقة:</label>
        <input name="subject" class="form-control" value="{{ old('subject', $teacher->subject) }}">
    </div>
    <div class="mb-3">
        <label>الفترة:</label>
        <select name="shift_id" class="form-select">
            <option value="">-- بدون فترة --</option>
            @foreach($shifts as $shift)
                <option value="{{ $shift->id }}" {{ old('shift_id', $teacher->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">حفظ التعديلات</button>
    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">رجوع</a>
</form>
@endsection
