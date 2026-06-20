@extends('layouts.app')
@section('content')
<h2>إضافة طالب جديد</h2>
<form method="POST" action="{{ route('students.store') }}">
    @csrf
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
        <label>المعرّف (ID):</label>
        <input type="number" name="id" class="form-control" value="{{ old('id', $defaultId) }}" min="1" required>
        <small class="text-muted">قيمة افتراضية = الرقم التالي المتاح، يمكنك تغييرها.</small>
    </div>
    <div class="mb-3">
        <label>كود الطالب (يُستخدم في الـ QR):</label>
        <input name="code" class="form-control" value="{{ old('code') }}" required>
    </div>
    <div class="mb-3">
        <label>الاسم:</label>
        <input name="name" class="form-control" value="{{ old('name') }}" required>
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
