@php $editing = isset($user); @endphp

<div class="mb-3">
    <label for="name" class="form-label">الاسم</label>
    <input type="text" class="form-control" id="name" name="name"
        value="{{ old('name', $user->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="username" class="form-label">اسم المستخدم (للدخول)</label>
    <input type="text" class="form-control" id="username" name="username" dir="ltr"
        value="{{ old('username', $user->username ?? '') }}" required autocomplete="off">
</div>

<div class="mb-4">
    <label for="password" class="form-label">
        كلمة المرور
        @if ($editing)
            <span class="text-muted small">(اتركها فارغة للإبقاء على الحالية)</span>
        @endif
    </label>
    <input type="password" class="form-control" id="password" name="password"
        autocomplete="new-password" @required(! $editing)>
</div>

<div class="mb-4">
    <label class="form-label fw-bold">الأقسام المسموحة</label>
    @php $userPerms = old('permissions', $user->permissions ?? []); @endphp
    <div class="row g-2">
        @foreach ($sections as $key => $label)
            <div class="col-md-4 col-6">
                <div class="form-check border rounded p-2">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}"
                        id="perm_{{ $key }}" @checked(in_array($key, $userPerms))>
                    <label class="form-check-label" for="perm_{{ $key }}">{{ $label }}</label>
                </div>
            </div>
        @endforeach
    </div>
    <div class="form-text">يرى المشرف فقط الأقسام المحددة هنا عند تسجيل دخوله.</div>
</div>

<div class="mb-4">
    <label class="form-label fw-bold">الفترات التي يديرها</label>
    @php $userShifts = array_map('intval', old('shift_ids', $user->shift_ids ?? [])); @endphp
    @forelse ($shifts as $shift)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="shift_ids[]" value="{{ $shift->id }}"
                id="shift_{{ $shift->id }}" @checked(in_array((int) $shift->id, $userShifts))>
            <label class="form-check-label" for="shift_{{ $shift->id }}">{{ $shift->name }}</label>
        </div>
    @empty
        <div class="text-muted small">لا توجد فترات بعد.</div>
    @endforelse
    <div class="form-text">في أقسام الطلاب/النقاط/التقارير/الإعلانات يرى المشرف فقط طلاب هذه الفترات.</div>
</div>
