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

<div class="mb-3">
    <label class="form-label">اسم السبب</label>
    <input name="name" class="form-control" value="{{ old('name', $reason->name ?? '') }}"
        placeholder="مثلاً: حفظ سورة، تأخير، سلوك..." required>
</div>

<div class="mb-3">
    <label class="form-label">النوع</label>
    <select name="type" class="form-select" required>
        @php $type = old('type', $reason->type ?? 'add'); @endphp
        <option value="add" @selected($type === 'add')>إضافة نقاط</option>
        <option value="remove" @selected($type === 'remove')>حذف نقاط</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">الكمية</label>
    <input name="amount" type="number" min="1" class="form-control"
        value="{{ old('amount', $reason->amount ?? '') }}" required>
</div>

<div class="form-check mb-4">
    <input type="hidden" name="active" value="0">
    <input class="form-check-input" type="checkbox" name="active" value="1" id="active"
        @checked(old('active', $reason->active ?? true))>
    <label class="form-check-label" for="active">مفعّل</label>
</div>

<button class="btn btn-success">حفظ</button>
<a href="{{ route('point_reasons.index') }}" class="btn btn-secondary">إلغاء</a>
