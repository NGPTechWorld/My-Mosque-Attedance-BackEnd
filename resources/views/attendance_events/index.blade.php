@extends('layouts.app')

@section('content')
@php $dayNames = ['أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت']; @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">مناسبات الحضور</h2>
    <a href="{{ route('attendance_events.create') }}" class="btn btn-success">➕ إضافة مناسبة</a>
</div>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>المناسبة</th>
            <th>الأيام</th>
            <th>الوقت</th>
            <th>النقاط</th>
            <th>الفترات</th>
            <th>الحالة</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($events as $event)
        <tr>
            <td>{{ $event->name }}</td>
            <td>
                @foreach ($event->days ?? [] as $d)
                    <span class="badge bg-primary">{{ $dayNames[$d] ?? $d }}</span>
                @endforeach
            </td>
            <td dir="ltr" class="text-end">{{ substr($event->start_time, 0, 5) }} - {{ substr($event->end_time, 0, 5) }}</td>
            <td class="text-center">{{ $event->points }}</td>
            <td>
                @forelse ($event->shifts as $shift)
                    <span class="badge bg-info text-dark">{{ $shift->name }}</span>
                @empty
                    <span class="text-muted">—</span>
                @endforelse
            </td>
            <td>
                @if ($event->active)
                    <span class="badge bg-success">مفعّلة</span>
                @else
                    <span class="badge bg-secondary">معطّلة</span>
                @endif
            </td>
            <td>
                <a href="{{ route('attendance_events.edit', $event->id) }}" class="btn btn-sm btn-outline-primary">✏️ تعديل</a>
                <form method="POST" action="{{ route('attendance_events.destroy', $event->id) }}" class="d-inline"
                    onsubmit="return confirm('تأكيد حذف المناسبة؟')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">🗑 حذف</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted">لا توجد مناسبات بعد. أضف أول مناسبة.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
