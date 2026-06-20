<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8" />
    <title>لوحة الإدارة المسجد</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />
</head>

<body dir="rtl">
    <!-- شريط التنقل -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">لوحة الإدارة المسجد</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="تبديل التنقل">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                @php $u = auth()->user(); @endphp
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>

                    @if ($u && ($u->hasSection('students') || $u->hasSection('points') || $u->hasSection('shifts')))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                إدارة الطلاب
                            </a>
                            <ul class="dropdown-menu">
                                @if ($u->hasSection('students'))
                                    <li><a class="dropdown-item" href="{{ route('students.index') }}">الطلاب</a></li>
                                @endif
                                @if ($u->hasSection('points'))
                                    <li><a class="dropdown-item" href="{{ route('points.index') }}">النقاط</a></li>
                                @endif
                                @if ($u->hasSection('shifts'))
                                    <li><a class="dropdown-item" href="{{ route('shifts.index') }}">الفترات</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if ($u && $u->hasSection('absences'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('absences.index') }}">تسجيل الغياب</a>
                        </li>
                    @endif

                    @if ($u && $u->hasSection('teachers'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('teachers.index') }}">الأساتذة</a>
                        </li>
                    @endif

                    @if ($u && $u->hasSection('monitoring'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('monitoring.index') }}">متابعة النظام</a>
                        </li>
                    @endif

                    @if ($u && $u->hasSection('announcements'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('announcements.create') }}">إرسال إعلان</a>
                        </li>
                    @endif

                    @if ($u && $u->hasSection('attendance_points'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('settings.attendanceReward') }}">نقاط الحضور</a>
                        </li>
                    @endif

                    @if ($u && $u->hasSection('reports'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                تقارير الدوام
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('attendance.byShift') }}">دوام حسب الفترة</a></li>
                                <li><a class="dropdown-item" href="{{ route('attendance.monthlyReport') }}">تقرير شهري</a></li>
                                <li><a class="dropdown-item" href="{{ route('attendance.friday') }}">تقرير الجمعة</a></li>
                            </ul>
                        </li>
                    @endif

                    @if ($u && $u->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('supervisors.index') }}">إدارة المشرفين</a>
                        </li>
                    @endif
                </ul>

                <form action="{{ route('logout') }}" method="POST" class="d-flex">
                    @csrf
                    <button class="btn btn-outline-danger" type="submit">تسجيل الخروج</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- محتوى الصفحة -->
    <div class="container">
        {{-- إشعارات النجاح أو الخطأ --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ❌ {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- سكربت Bootstrap لتفعيل زر الإغلاق -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
