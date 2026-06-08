@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">قائمة الأساتذة</h2>
        <div>
            <a href="{{ route('teachers.report') }}" class="btn btn-outline-primary">تقرير الحضور</a>
            <a href="{{ route('teachers.create') }}" class="btn btn-success">إضافة أستاذ</a>
        </div>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th style="width: 60px;">الرقم</th>
                <th>الكود</th>
                <th>الاسم</th>
                <th>الهاتف</th>
                <th>المادة</th>
                <th>الفترة</th>
                <th>حضور اليوم</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($teachers as $teacher)
                <tr>
                    <td class="text-center">{{ $teacher->id }}</td>
                    <td class="text-center">{{ $teacher->code }}</td>
                    <td>{{ $teacher->name }}</td>
                    <td>{{ $teacher->phone }}</td>
                    <td>{{ $teacher->subject }}</td>
                    <td>{{ $teacher->shift->name ?? '—' }}</td>
                    <td class="text-center">
                        @if ($teacher->attendances->isNotEmpty())
                            <span class="badge bg-success">
                                ✅ {{ \Carbon\Carbon::parse($teacher->attendances[0]->check_in_time)->format('g:i A') }}
                            </span>
                        @else
                            <span class="badge bg-secondary">لم يسجّل</span>
                        @endif
                    </td>
                    <td>
                        @if ($teacher->attendances->isEmpty())
                            <form method="POST" action="{{ route('teachers.checkin', $teacher->id) }}" style="display:inline-block;">
                                @csrf
                                <button class="btn btn-success btn-sm">تسجيل حضور</button>
                            </form>
                        @endif
                        <a href="{{ route('teachers.qr', $teacher->id) }}" class="btn btn-info btn-sm" target="_blank">QR</a>
                        <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                        <form method="POST" action="{{ route('teachers.destroy', $teacher->id) }}" style="display:inline-block;"
                            onsubmit="return confirm('هل أنت متأكد من حذف الأستاذ؟');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach

            @if ($teachers->isEmpty())
                <tr><td colspan="8" class="text-center">لا يوجد أساتذة بعد</td></tr>
            @endif
        </tbody>
    </table>
@endsection
