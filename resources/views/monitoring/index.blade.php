@extends('layouts.app')

@section('content')
    <h2>متابعة النظام</h2>
    <p class="text-muted">تفاعل أهالي الطلاب مع التطبيق: مين سجّل دخول، فتح التطبيق، عرض النقاط أو الإشعارات.</p>

    {{-- بطاقات ملخّص --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold text-primary">{{ $stats['devices'] }}</div>
                    <div class="text-muted">أجهزة مسجّلة</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold text-success">{{ $stats['logins_today'] }}</div>
                    <div class="text-muted">دخول اليوم</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold text-info">{{ $stats['today'] }}</div>
                    <div class="text-muted">عمليات اليوم</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold text-secondary">{{ $stats['notifications'] }}</div>
                    <div class="text-muted">إشعارات مرسلة</div>
                </div>
            </div>
        </div>
    </div>

    {{-- فلاتر --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                placeholder="بحث برقم الأهل أو اسم الطالب..." autocomplete="off">
        </div>
        <div class="col-md-3">
            <select name="action" class="form-select">
                <option value="">كل العمليات</option>
                @foreach ($actions as $key => $label)
                    <option value="{{ $key }}" @selected(request('action') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="date" value="{{ request('date') }}" class="form-control">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1">تصفية</button>
            <a href="{{ route('monitoring.index') }}" class="btn btn-outline-secondary">إعادة</a>
        </div>
    </form>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 60px;">#</th>
                <th>العملية</th>
                <th>رقم الأهل</th>
                <th>الطالب</th>
                <th>التفاصيل</th>
                <th>المنصّة</th>
                <th>الوقت</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($activities as $activity)
                <tr>
                    <td class="text-center text-muted">{{ $activity->id }}</td>
                    <td>
                        @php
                            $badge = match ($activity->action) {
                                'login' => 'success',
                                'open_app' => 'primary',
                                'view_points' => 'info',
                                'view_notifications' => 'warning',
                                'device' => 'secondary',
                                default => 'dark',
                            };
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ $activity->action_label }}</span>
                    </td>
                    <td dir="ltr" class="text-end">{{ $activity->guardian_phone }}</td>
                    <td>{{ $activity->student_name ?? ($activity->student->name ?? '—') }}</td>
                    <td>{{ $activity->description ?? '—' }}</td>
                    <td>{{ $activity->platform ?? '—' }}</td>
                    <td dir="ltr" class="text-end">
                        <span title="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">لا توجد عمليات للعرض</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $activities->links() }}
@endsection
