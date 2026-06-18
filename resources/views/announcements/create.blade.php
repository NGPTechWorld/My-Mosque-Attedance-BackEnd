@extends('layouts.app')

@section('content')
    <h2>إرسال إعلان</h2>
    <p class="text-muted">أرسل إعلاناً/إشعاراً لأهالي طلاب دوام مختار. يمكنك اختيار أكثر من دوام.</p>

    @if (session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">❌ {{ session('error') }}</div>
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

    <div class="card shadow-sm" style="max-width: 720px;">
        <div class="card-body">
            <form method="POST" action="{{ route('announcements.send') }}">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">عنوان الإعلان</label>
                    <input type="text" class="form-control" id="title" name="title" maxlength="255"
                        value="{{ old('title') }}" placeholder="مثلاً: تعطيل الدوام يوم الجمعة" required>
                </div>

                <div class="mb-3">
                    <label for="body" class="form-label">نص الإعلان</label>
                    <textarea class="form-control" id="body" name="body" rows="4" maxlength="1000"
                        placeholder="اكتب نص الإشعار الذي سيصل لأهالي الطلاب..." required>{{ old('body') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label d-flex justify-content-between align-items-center">
                        <span>الدوامات المستهدفة</span>
                        <span class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label small" for="selectAll">تحديد الكل</label>
                        </span>
                    </label>

                    @php $oldShifts = old('shift_ids', []); @endphp
                    @forelse ($shifts as $shift)
                        <div class="form-check border rounded p-2 mb-2 d-flex align-items-center">
                            <input class="form-check-input shift-checkbox ms-2" type="checkbox" name="shift_ids[]"
                                value="{{ $shift->id }}" id="shift{{ $shift->id }}"
                                @checked(in_array($shift->id, $oldShifts))>
                            <label class="form-check-label d-flex justify-content-between w-100" for="shift{{ $shift->id }}">
                                <span class="fw-bold">{{ $shift->name }}</span>
                                <span class="badge bg-secondary">{{ $shift->students_count }} طالب</span>
                            </label>
                        </div>
                    @empty
                        <div class="alert alert-warning">لا توجد دوامات. أضف دواماً أولاً من صفحة الفترات.</div>
                    @endforelse
                </div>

                <button class="btn btn-primary" @disabled($shifts->isEmpty())>إرسال الإعلان</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const boxes = Array.from(document.querySelectorAll('.shift-checkbox'));

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    boxes.forEach(b => b.checked = selectAll.checked);
                });
            }
            // مزامنة حالة "تحديد الكل" مع الصناديق
            boxes.forEach(b => b.addEventListener('change', function () {
                if (selectAll) selectAll.checked = boxes.every(x => x.checked);
            }));
        });
    </script>
@endsection
