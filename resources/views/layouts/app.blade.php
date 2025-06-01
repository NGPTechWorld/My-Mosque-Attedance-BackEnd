<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>لوحة الإدارة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>

<body dir="rtl">
    <!-- شريط التنقل -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">لوحة الإدارة</a>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-light me-2">الرئيسية</a>
                <a href="{{ route('students.index') }}" class="btn btn-outline-light me-2">الطلاب</a>
                <a href="{{ route('points.index') }}" class="btn btn-outline-light me-2">النقاط</a>
                <a href="{{ route('shifts.index') }}" class="btn btn-outline-light me-2">الفترات</a>
                <a href="{{ route('attendance.byShift') }}" class="btn btn-outline-light">دوام حسب الفترة</a>
                <a href="{{ route('attendance.monthlyReport') }}" class="btn btn-outline-light me-2">تقرير شهري</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-danger">تسجيل الخروج</button>
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