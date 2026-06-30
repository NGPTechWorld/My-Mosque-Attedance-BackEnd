@csrf
@if (($mode ?? 'create') === 'edit')
    @method('PUT')
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $dayNames = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
    $selectedDays = collect(old('days', isset($event) ? $event->days : []));
    $selectedShifts = collect(old('shift_ids', isset($event) ? $event->shifts->pluck('id')->all() : []));
@endphp

<div class="mb-3">
    <label class="form-label">اسم المناسبة</label>
    <input name="name" class="form-control" value="{{ old('name', $event->name ?? '') }}"
        placeholder="مثلاً: مجلس الصلاة على النبي" required>
</div>

<div class="mb-3">
    <label class="form-label d-block">أيام التكرار (أسبوعياً)</label>
    @foreach ($dayNames as $i => $dayName)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="days[]" value="{{ $i }}"
                id="day{{ $i }}" {{ $selectedDays->contains($i) ? 'checked' : '' }}>
            <label class="form-check-label" for="day{{ $i }}">{{ $dayName }}</label>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <label class="form-label">وقت البداية</label>
        <input type="time" name="start_time" class="form-control"
            value="{{ old('start_time', isset($event) ? substr($event->start_time, 0, 5) : '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">وقت النهاية</label>
        <input type="time" name="end_time" class="form-control"
            value="{{ old('end_time', isset($event) ? substr($event->end_time, 0, 5) : '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">نقاط الحضور</label>
        <input type="number" name="points" min="0" max="100000" class="form-control"
            value="{{ old('points', $event->points ?? 0) }}" required>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">نص الإشعار / سبب النقاط</label>
    <input name="message" class="form-control" maxlength="255"
        value="{{ old('message', $event->message ?? '') }}"
        placeholder="يظهر في إشعار الأهل ومحفظة النقاط (اختياري)">
</div>

<div class="mb-3">
    <label class="form-label">الفترات المسموح لها التسجيل (واحدة على الأقل):</label>
    <select name="shift_ids[]" class="form-select" multiple size="5" required>
        @foreach ($shifts as $shift)
            <option value="{{ $shift->id }}" {{ $selectedShifts->contains($shift->id) ? 'selected' : '' }}>{{ $shift->name }}</option>
        @endforeach
    </select>
    <small class="text-muted">اضغط Ctrl (أو Cmd) لاختيار أكثر من فترة.</small>
</div>

<div class="form-check mb-4">
    <input type="hidden" name="active" value="0">
    <input class="form-check-input" type="checkbox" name="active" value="1" id="active"
        @checked(old('active', $event->active ?? true))>
    <label class="form-check-label" for="active">مفعّلة</label>
</div>

<button class="btn btn-success">حفظ</button>
<a href="{{ route('attendance_events.index') }}" class="btn btn-secondary">إلغاء</a>
