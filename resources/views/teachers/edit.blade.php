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
        <label>الفترات (يمكن اختيار أكثر من فترة — فترة واحدة على الأقل):</label>
        @php $selectedShifts = old('shift_ids', $teacher->shifts->pluck('id')->all()); @endphp
        <select name="shift_ids[]" class="form-select" multiple size="5" required>
            @foreach($shifts as $shift)
                <option value="{{ $shift->id }}" {{ collect($selectedShifts)->contains($shift->id) ? 'selected' : '' }}>{{ $shift->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">اضغط Ctrl (أو Cmd) لاختيار أكثر من فترة.</small>
    </div>
    <button class="btn btn-primary">حفظ التعديلات</button>
    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">رجوع</a>
</form>
@endsection
