@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">إدارة المشرفين</h2>
        <a href="{{ route('supervisors.create') }}" class="btn btn-primary">+ إضافة مشرف</a>
    </div>
    <p class="text-muted">امنح كل مشرف صلاحيات على أقسام معيّنة وفترات محددة — فيرى عند دخوله الأقسام الممنوحة له فقط.</p>

    @if (session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">❌ {{ session('error') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>الاسم</th>
                <th>اسم المستخدم</th>
                <th>الأقسام المسموحة</th>
                <th>الفترات</th>
                <th style="width: 160px;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($supervisors as $supervisor)
                <tr>
                    <td>{{ $supervisor->name }}</td>
                    <td dir="ltr" class="text-end">{{ $supervisor->username }}</td>
                    <td>
                        @forelse ($supervisor->permissions ?? [] as $perm)
                            <span class="badge bg-info text-dark mb-1">{{ $sections[$perm] ?? $perm }}</span>
                        @empty
                            <span class="text-muted small">لا صلاحيات</span>
                        @endforelse
                    </td>
                    <td>
                        @forelse ($supervisor->shift_ids ?? [] as $sid)
                            <span class="badge bg-secondary mb-1">{{ $shifts[$sid]->name ?? ('#' . $sid) }}</span>
                        @empty
                            <span class="text-muted small">لا فترات</span>
                        @endforelse
                    </td>
                    <td>
                        <a href="{{ route('supervisors.edit', $supervisor->id) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                        <form action="{{ route('supervisors.destroy', $supervisor->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('تأكيد حذف المشرف؟');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">لا يوجد مشرفون بعد</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
