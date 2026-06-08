@extends('layouts.app')

@section('content')
    <h2>تقرير حضور الأساتذة</h2>
    <br>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <label class="form-label">من</label>
            <input type="date" name="from" value="{{ $from }}" class="form-control">
        </div>
        <div class="col-auto">
            <label class="form-label">إلى</label>
            <input type="date" name="to" value="{{ $to }}" class="form-control">
        </div>
        <div class="col-auto align-self-end">
            <button class="btn btn-primary">عرض</button>
        </div>
    </form>

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>الأستاذ</th>
                <th>المادة</th>
                <th>عدد أيام الحضور</th>
                <th>التواريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($teachers as $teacher)
                <tr>
                    <td>{{ $teacher->name }}</td>
                    <td>{{ $teacher->subject }}</td>
                    <td class="text-center fw-bold">{{ $teacher->attendances->count() }}</td>
                    <td>
                        @forelse ($teacher->attendances as $att)
                            <span class="badge bg-success me-1">
                                {{ $att->date }} ({{ \Carbon\Carbon::parse($att->check_in_time)->format('g:i A') }})
                            </span>
                        @empty
                            <span class="text-muted">لا يوجد</span>
                        @endforelse
                    </td>
                </tr>
            @endforeach

            @if ($teachers->isEmpty())
                <tr><td colspan="4" class="text-center">لا يوجد أساتذة</td></tr>
            @endif
        </tbody>
    </table>
@endsection
