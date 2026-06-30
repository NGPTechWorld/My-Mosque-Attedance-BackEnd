@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>برنامج النقاط — الأسباب</h2>
    <a href="{{ route('point_reasons.create') }}" class="btn btn-success">➕ إضافة سبب</a>
</div>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>السبب</th>
            <th>النوع</th>
            <th>الكمية</th>
            <th>الفترات</th>
            <th>الحالة</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($reasons as $reason)
        <tr>
            <td>{{ $reason->name }}</td>
            <td>
                @if ($reason->type === 'add')
                    <span class="badge bg-success">إضافة</span>
                @else
                    <span class="badge bg-danger">حذف</span>
                @endif
            </td>
            <td>{{ $reason->amount }}</td>
            <td>
                @forelse ($reason->shifts as $shift)
                    <span class="badge bg-info text-dark">{{ $shift->name }}</span>
                @empty
                    <span class="text-muted">—</span>
                @endforelse
            </td>
            <td>
                @if ($reason->active)
                    <span class="badge bg-primary">مفعّل</span>
                @else
                    <span class="badge bg-secondary">معطّل</span>
                @endif
            </td>
            <td>
                <a href="{{ route('point_reasons.edit', $reason->id) }}" class="btn btn-sm btn-outline-primary">✏️ تعديل</a>
                <form method="POST" action="{{ route('point_reasons.destroy', $reason->id) }}" class="d-inline"
                    onsubmit="return confirm('تأكيد حذف السبب؟')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">🗑 حذف</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted">لا توجد أسباب بعد. أضف أول سبب.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
