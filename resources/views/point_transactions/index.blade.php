@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">سجل تحويلات النقاط</h2>
</div>

{{-- فلاتر --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input name="q" value="{{ request('q') }}" class="form-control" placeholder="بحث باسم الطالب">
    </div>
    <div class="col-md-3">
        <select name="type" class="form-select">
            <option value="">كل العمليات</option>
            <option value="add" @selected(request('type') === 'add')>إضافة</option>
            <option value="remove" @selected(request('type') === 'remove')>حذف</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary w-100">تصفية</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('point_transactions.index') }}" class="btn btn-outline-secondary w-100">إلغاء</a>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>الطالب</th>
            <th>النوع</th>
            <th>الكمية</th>
            <th>السبب</th>
            <th>ملاحظة الأستاذ</th>
            <th>الأستاذ</th>
            <th>الرصيد بعدها</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions as $tx)
        <tr>
            <td>{{ \Carbon\Carbon::parse($tx->created_at)->format('Y-m-d g:i A') }}</td>
            <td>{{ $tx->student->name ?? '—' }}</td>
            <td>
                @if ($tx->type === 'add')
                    <span class="badge bg-success">إضافة</span>
                @else
                    <span class="badge bg-danger">حذف</span>
                @endif
            </td>
            <td class="text-center">{{ $tx->type === 'add' ? '+' : '-' }}{{ $tx->amount }}</td>
            <td>{{ $tx->pointReason->name ?? $tx->reason ?? '—' }}</td>
            <td>{{ $tx->note ?? '—' }}</td>
            <td>{{ $tx->teacher->name ?? '—' }}</td>
            <td class="text-center">{{ $tx->balance_after }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted">لا توجد عمليات.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $transactions->links('pagination::bootstrap-5') }}
</div>
@endsection
