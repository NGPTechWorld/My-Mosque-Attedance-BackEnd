@extends('layouts.app')

@section('content')
    <h2>تعديل الطالب</h2>

    <form method="POST" action="{{ route('students.update', $student->id) }}">
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
            <label class="form-label">كود الطالب (يُستخدم في الـ QR)</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $student->code) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">الاسم</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">هاتف ولي الأمر</label>
            <input type="text" name="guardian_phone" class="form-control" value="{{ $student->guardian_phone }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">الفترة</label>
            <select name="shift_id" class="form-select" required>
                <option value="">-- اختر الفترة --</option>
                @foreach ($shifts as $shift)
                    <option value="{{ $shift->id }}" {{ $student->shift_id == $shift->id ? 'selected' : '' }}>
                        {{ $shift->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </form>
@endsection
