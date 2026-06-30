@extends('layouts.app')

@section('content')
<h2 class="mb-3">تقرير حضور المناسبات</h2>

<form method="GET" class="row g-2 mb-4">
    <div class="col-md-5">
        <select name="event_id" class="form-select" required>
            <option value="">— اختر المناسبة —</option>
            @foreach ($events as $ev)
                <option value="{{ $ev->id }}" {{ (string) request('event_id') === (string) $ev->id ? 'selected' : '' }}>{{ $ev->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <input type="date" name="date" class="form-control" value="{{ $date }}">
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary w-100">عرض</button>
    </div>
</form>

@if ($event)
    <h4 class="mb-3">{{ $event->name }} — {{ $date }}</h4>
    @php $present = $rows->where('present', true)->count(); @endphp
    <p class="text-muted">الحاضرون: {{ $present }} من {{ $rows->count() }}</p>

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>الطالب</th>
                <th>الفترة</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
            <tr>
                <td>{{ $row['student']->name }}</td>
                <td>{{ $row['student']->shift->name ?? '—' }}</td>
                <td>
                    @if ($row['present'])
                        <span class="badge bg-success">حاضر {{ \Carbon\Carbon::parse($row['time'])->format('h:i A') }}</span>
                    @else
                        <span class="badge bg-secondary">غائب</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center text-muted">لا يوجد طلاب في الفترات المسموح لها.</td></tr>
            @endforelse
        </tbody>
    </table>
@endif
@endsection
