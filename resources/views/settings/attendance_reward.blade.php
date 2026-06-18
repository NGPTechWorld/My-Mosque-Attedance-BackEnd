@extends('layouts.app')

@section('content')
    <h2>نقاط الحضور التلقائية</h2>
    <p class="text-muted">حدّد عدد النقاط والرسالة التي تُضاف تلقائياً لرصيد الطالب عند تسجيل حضوره.</p>

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

    <div class="card shadow-sm" style="max-width: 640px;">
        <div class="card-body">
            <form method="POST" action="{{ route('settings.attendanceReward.update') }}">
                @csrf

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="enabled" name="enabled" value="1"
                        @checked($reward['enabled'])>
                    <label class="form-check-label fw-bold" for="enabled">تفعيل منح النقاط عند الحضور</label>
                </div>

                <div class="mb-3">
                    <label for="points" class="form-label">عدد النقاط لكل حضور</label>
                    <input type="number" class="form-control" id="points" name="points" min="0" max="100000"
                        value="{{ old('points', $reward['points']) }}" placeholder="مثلاً: 100" required>
                    <div class="form-text">الرقم الذي يُضاف لرصيد الطالب عند تسجيل حضوره (مرة واحدة في اليوم).</div>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">رسالة سجل النقاط</label>
                    <input type="text" class="form-control" id="message" name="message" maxlength="255"
                        value="{{ old('message', $reward['message']) }}" placeholder="مثلاً: مكافأة الحضور">
                    <div class="form-text">تظهر هذه الرسالة كسبب العملية في محفظة الأهل داخل التطبيق.</div>
                </div>

                <div class="alert alert-light border small">
                    <strong>مثال:</strong> تسجيل الحضور =
                    <span class="text-success fw-bold">+{{ $reward['points'] ?: 100 }}</span> نقطة، مع الرسالة
                    "<span class="fw-bold">{{ $reward['message'] ?: 'مكافأة الحضور' }}</span>".
                </div>

                <button class="btn btn-primary">حفظ الإعدادات</button>
            </form>
        </div>
    </div>
@endsection
