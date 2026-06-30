@extends('layouts.app')

@section('content')
    <h2>إعدادات النقاط التلقائية</h2>
    <p class="text-muted">نقاط الحضور، وخصم التأخير والغياب، مع تحديد وقت التأخير لكل فترة.</p>

    @if (session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
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

    <form method="POST" action="{{ route('settings.attendanceReward.update') }}">
        @csrf

        {{-- نقاط الحضور --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white fw-bold">نقاط الحضور (إضافة)</div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="enabled" name="enabled" value="1"
                        @checked($reward['enabled'])>
                    <label class="form-check-label fw-bold" for="enabled">تفعيل منح نقاط عند الحضور في الوقت</label>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">عدد النقاط المضافة</label>
                        <input type="number" class="form-control" name="points" min="0" max="100000"
                            value="{{ old('points', $reward['points']) }}" placeholder="مثلاً: 100" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">رسالة سجل النقاط</label>
                        <input type="text" class="form-control" name="message" maxlength="255"
                            value="{{ old('message', $reward['message']) }}" placeholder="مثلاً: مكافأة الحضور">
                    </div>
                </div>
            </div>
        </div>

        {{-- نقاط التأخير --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning fw-bold">نقاط التأخير (خصم)</div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="late_enabled" name="late_enabled"
                        value="1" @checked($late['enabled'])>
                    <label class="form-check-label fw-bold" for="late_enabled">تفعيل خصم النقاط عند التأخير</label>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">عدد النقاط المخصومة</label>
                        <input type="number" class="form-control" name="late_points" min="0" max="100000"
                            value="{{ old('late_points', $late['points']) }}" placeholder="مثلاً: 20" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">رسالة سجل النقاط</label>
                        <input type="text" class="form-control" name="late_message" maxlength="255"
                            value="{{ old('late_message', $late['message']) }}" placeholder="مثلاً: خصم تأخير">
                    </div>
                </div>
                <div class="form-text mt-2">يُطبَّق الخصم عند تسجيل الحضور بعد وقت التأخير المحدّد للفترة بالأسفل.</div>
            </div>
        </div>

        {{-- نقاط الغياب --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white fw-bold">نقاط الغياب (خصم)</div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="absence_enabled"
                        name="absence_enabled" value="1" @checked($absence['enabled'])>
                    <label class="form-check-label fw-bold" for="absence_enabled">تفعيل خصم النقاط عند تسجيل الغياب</label>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">عدد النقاط المخصومة</label>
                        <input type="number" class="form-control" name="absence_points" min="0" max="100000"
                            value="{{ old('absence_points', $absence['points']) }}" placeholder="مثلاً: 50" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">رسالة سجل النقاط</label>
                        <input type="text" class="form-control" name="absence_message" maxlength="255"
                            value="{{ old('absence_message', $absence['message']) }}" placeholder="مثلاً: خصم غياب">
                    </div>
                </div>
            </div>
        </div>

        {{-- نقاط حضور الجمعة --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">نقاط حضور الجمعة (إضافة)</div>
            <div class="card-body">
                <p class="text-muted small">يوم الجمعة يُسمح بتسجيل حضور طلاب كل الفترات (بدون التقيّد بأيام/أوقات الفترة)، وتُمنح هذه النقاط بدل نقاط الحضور العادية ودون احتساب تأخير.</p>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="friday_enabled"
                        name="friday_enabled" value="1" @checked($friday['enabled'])>
                    <label class="form-check-label fw-bold" for="friday_enabled">تفعيل منح نقاط عند حضور يوم الجمعة</label>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">عدد النقاط المضافة</label>
                        <input type="number" class="form-control" name="friday_points" min="0" max="100000"
                            value="{{ old('friday_points', $friday['points']) }}" placeholder="مثلاً: 150" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">رسالة سجل النقاط</label>
                        <input type="text" class="form-control" name="friday_message" maxlength="255"
                            value="{{ old('friday_message', $friday['message']) }}" placeholder="مثلاً: نقاط حضور الجمعة">
                    </div>
                </div>
            </div>
        </div>

        {{-- وقت التأخير لكل فترة --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white fw-bold">وقت التأخير حسب الفترة</div>
            <div class="card-body">
                <p class="text-muted small">أي حضور بعد هذا الوقت يُعتبر تأخيراً ويُخصم منه نقاط التأخير. اتركه فارغاً لإلغاء التأخير لتلك الفترة.</p>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>الفترة</th>
                            <th>الدوام</th>
                            <th style="width: 200px;">وقت التأخير</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shifts as $shift)
                            <tr>
                                <td class="fw-bold">{{ $shift->name }}</td>
                                <td dir="ltr" class="text-end">{{ $shift->start_time }} - {{ $shift->end_time }}</td>
                                <td>
                                    <input type="time" class="form-control"
                                        name="late_times[{{ $shift->id }}]"
                                        value="{{ old('late_times.' . $shift->id, $shift->late_time ? substr($shift->late_time, 0, 5) : '') }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">لا توجد فترات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <button class="btn btn-primary btn-lg">حفظ كل الإعدادات</button>
    </form>
@endsection
