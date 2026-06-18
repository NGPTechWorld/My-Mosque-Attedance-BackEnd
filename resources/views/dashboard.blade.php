@extends('layouts.app')

@section('content')
    <h2>لوحة الإحصائيات العامة</h2>
    <br>

    {{-- بطاقات ملخّص --}}
    <div class="row">
        <div class="col-md-3 col-6">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">عدد الطلاب</h5>
                    <p class="card-text fs-4">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">عدد الفترات</h5>
                    <p class="card-text fs-4">{{ $totalShifts }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">حضور اليوم</h5>
                    <p class="card-text fs-4">{{ $presentToday }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">غياب اليوم</h5>
                    <p class="card-text fs-4">{{ $absentToday }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- المخططات --}}
    <div class="row g-3 mt-1">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">الحضور خلال آخر 14 يوماً</h5>
                    <canvas id="dailyChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">حضور اليوم</h5>
                    <canvas id="todayChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">الحضور حسب الفترة (هذا الشهر)</h5>
                    <canvas id="shiftAttendanceChart" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">توزيع الطلاب على الفترات</h5>
                    <canvas id="shiftStudentsChart" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const palette = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                '#20c997', '#fd7e14', '#0dcaf0', '#d63384', '#6c757d'];

            // 1) الحضور آخر 14 يوماً (خطّي)
            new Chart(document.getElementById('dailyChart'), {
                type: 'line',
                data: {
                    labels: @json($dailyLabels),
                    datasets: [{
                        label: 'عدد الحاضرين',
                        data: @json($dailyData),
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.15)',
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

            // 2) حضور اليوم (دائري)
            new Chart(document.getElementById('todayChart'), {
                type: 'doughnut',
                data: {
                    labels: ['حاضر', 'غائب'],
                    datasets: [{
                        data: [{{ $presentToday }}, {{ $absentToday }}],
                        backgroundColor: ['#198754', '#dc3545'],
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });

            // 3) الحضور حسب الفترة (أعمدة)
            new Chart(document.getElementById('shiftAttendanceChart'), {
                type: 'bar',
                data: {
                    labels: @json($shiftLabels),
                    datasets: [{
                        label: 'عدد مرات الحضور',
                        data: @json($shiftAttendance),
                        backgroundColor: '#0dcaf0',
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

            // 4) توزيع الطلاب على الفترات (دائري)
            new Chart(document.getElementById('shiftStudentsChart'), {
                type: 'pie',
                data: {
                    labels: @json($shiftLabels),
                    datasets: [{
                        data: @json($shiftStudents),
                        backgroundColor: palette,
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });
        });
    </script>
@endsection
