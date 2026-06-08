@extends('layouts.app')

@section('content')
    <h2>تقرير حضور وغياب الأساتذة</h2>
    <p class="text-muted">يُحسب الغياب حسب أيام دوام فترة الأستاذ ضمن الفترة الزمنية المختارة.</p>

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
                <th>الفترة</th>
                <th class="text-center">أيام الدوام</th>
                <th class="text-center">حضور</th>
                <th class="text-center">غياب</th>
                <th>التفصيل</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                @php($teacher = $row['teacher'])
                <tr>
                    <td>
                        {{ $teacher->name }}
                        @if ($teacher->subject)
                            <small class="text-muted d-block">{{ $teacher->subject }}</small>
                        @endif
                    </td>
                    <td>{{ $teacher->shift->name ?? '—' }}</td>
                    <td class="text-center">{{ $row['expected'] }}</td>
                    <td class="text-center"><span class="badge bg-success">{{ $row['present'] }}</span></td>
                    <td class="text-center"><span class="badge bg-danger">{{ $row['absent'] }}</span></td>
                    <td>
                        @if (! $teacher->shift)
                            <span class="text-muted">لا توجد فترة محدّدة لهذا الأستاذ</span>
                        @elseif (empty($row['details']))
                            <span class="text-muted">لا أيام دوام ضمن المدة</span>
                        @else
                            @foreach ($row['details'] as $d)
                                @if ($d['present'])
                                    <span class="badge bg-success me-1 mb-1">
                                        ✅ {{ $d['date'] }} ({{ \Carbon\Carbon::parse($d['time'])->format('g:i A') }})
                                    </span>
                                @else
                                    <span class="badge bg-danger me-1 mb-1">❌ {{ $d['date'] }}</span>
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endforeach

            @if ($rows->isEmpty())
                <tr><td colspan="6" class="text-center">لا يوجد أساتذة</td></tr>
            @endif
        </tbody>
    </table>
@endsection
