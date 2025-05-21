<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <title>لوحة التحكم - الحضور</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --main-color: #085e4d;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #eef6f4, #ffffff);
            padding: 40px 20px;
        }

        h2,
        h4 {
            color: var(--main-color);
        }

        .card {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .table {
            background-color: #fff;
        }

        .btn-outline-primary {
            border-radius: 20px;
            border-color: var(--main-color);
            color: var(--main-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--main-color);
            color: white;
        }

        .btn-outline-success {
            border-radius: 20px;
            border-color: #28a745;
            color: #28a745;
            margin-bottom: 15px;
        }

        .btn-outline-success:hover {
            background-color: #28a745;
            color: white;
        }

        .search-box {
            margin-bottom: 15px;
        }

        .filter-dates {
            margin-bottom: 15px;
            gap: 15px;
        }

        #attendanceContainer {
            display: none;
        }

        .sortable {
            cursor: pointer;
            user-select: none;
        }

        .sortable:after {
            content: ' ⇅';
            font-size: 0.8em;
            color: #777;
        }

        .sorted-asc:after {
            content: ' ↑';
            color: var(--main-color);
        }

        .sorted-desc:after {
            content: ' ↓';
            color: var(--main-color);
        }

        /* أزرار التصفح للبايجنيت */
        .pagination {
            justify-content: center;
            margin-top: 15px;
        }

        /* تحديد عرض العمود على حجم الأزرار فقط */
        /* عمود الرقم صغير حسب محتوى الرقم */
        .id-column {
            width: 2%;
            white-space: nowrap;
        }

        /* عمود عرض الحضور صغير حسب الأزرار */
        .attendance-column {
            width: 1%;
            white-space: nowrap;
        }

        /* رأس الجدول لعمود الرقم */
        thead .id-column {
            width: auto;
            white-space: normal;
            min-width: 100px;
            /* ممكن تضبط حسب حجم الرقم */
            text-align: center;
        }

        /* رأس الجدول لعمود عرض الحضور */
        thead .attendance-column {
            width: auto;
            white-space: normal;
            min-width: 110px;
            /* حسب طول النص */
            text-align: center;
        }
    </style>
</head>

<body class="container">

    <div class="card p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="mb-0">قائمة الطلاب</h2>
            <img src="{{ asset('images/logo.png') }}" alt="الشعار" style="height: 80px;" />
        </div>

        <input type="text" id="searchInput" class="form-control search-box" placeholder="ابحث عن طالب بالاسم..."
            oninput="filterStudents()" />
        <div class="d-flex align-items-center justify-content-between mb-3">
            <button class="btn btn-outline-primary mb-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                + إضافة طالب
            </button>
            <button class="btn btn-outline-success" onclick="exportStudentsCSV()">استخراج بيانات الطلاب CSV</button>
        </div>
        <!-- زر فتح المودال -->


        <!-- نافذة إضافة الطالب -->
        <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">إضافة طالب جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addStudentForm">
                            <div class="mb-3">
                                <label for="studentNameInput" class="form-label">اسم الطالب</label>
                                <input type="text" class="form-control" id="studentNameInput" required />
                            </div>
                            <div id="addStudentError" class="text-danger small" style="display: none;"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="button" class="btn btn-primary" onclick="submitAddStudent()">حفظ</button>

                    </div>
                </div>
            </div>
        </div>
        <!-- مودال عرض الباركود -->
        <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content p-3 text-center">
                    <div class="modal-header">
                        <h5 class="modal-title" id="qrModalLabel">رمز الاستجابة السريعة (QR Code)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        <div id="qrcode"></div>
                        <p id="qrText" class="mt-2"></p>
                    </div>
                </div>
            </div>
        </div>




        <table class="table table-hover table-bordered align-middle text-center " id="studentsTable">
            <thead class="table-success">
                <tr>
                    <th class="id-column sortable" onclick="sortTable('id')">الرقم</th>

                    <th class="sortable" onclick="sortTable('name')">الاسم</th>
                    <th class="attendance-column">العمليات</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    <tr data-id="{{ $student->id }}" data-name="{{ $student->name }}">
                        <td class="id-column">{{ $student->id }}</td>

                        <td>{{ $student->name }}</td>
                        <td class="attendance-column">
                            <button class="btn btn-outline-primary btn-sm"
                                onclick="loadAttendance({{ $student->id }}, '{{ $student->name }}')">عرض</button>
                            <button class="btn btn-outline-danger btn-sm ms-2"
                                onclick="deleteStudent({{ $student->id }}, '{{ $student->name }}')">حذف</button>
                            <button class="btn btn-outline-primary btn-sm ms-2"
                                onclick="showQR({{ $student->id }}, '{{ $student->name }}')">عرض QR Code</button>


                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="attendanceContainer" class="card p-4">
        <h4>
            تفاصيل الدوام لـ <span id="studentName"></span>
            <button class="btn btn-sm btn-outline-danger" style="float:left;" onclick="closeAttendance()">إغلاق</button>
        </h4>


        <p><strong>عدد أيام الحضور: <span id="attendanceCount" class="text-success"></span></strong></p>

        <div class="d-flex filter-dates">
            <label for="dateFrom" class="form-label">من تاريخ:</label>
            <input type="date" id="dateFrom" class="form-control" onchange="changePageAttendance(1)" />

            <label for="dateTo" class="form-label">إلى تاريخ:</label>
            <input type="date" id="dateTo" class="form-control" onchange="changePageAttendance(1)" />
        </div>

        <table class="table table-striped table-bordered align-middle text-center" id="attendanceTable">
            <thead class="table-info">
                <tr>
                    <th>التاريخ</th>
                    <th>اليوم</th>
                    <th>الساعة</th>
                </tr>
            </thead>
            <tbody id="attendanceBody"></tbody>
        </table>

        <!-- أزرار التصفح -->
        <nav>
            <ul class="pagination" id="paginationControls"></ul>
        </nav>
    </div>

    <script>
        let students = [];
        let currentSort = { column: null, direction: 'asc' };
        const studentsTableBody = document.querySelector('#studentsTable tbody');

        // بيانات الحضور المحملة كاملة
        let attendanceData = [];
        let attendancePerPage = 5;
        let currentAttendancePage = 1;

        window.onload = () => {
            students = Array.from(studentsTableBody.querySelectorAll('tr')).map(row => {
                return {
                    id: row.getAttribute('data-id'),
                    name: row.getAttribute('data-name'),
                    rowElement: row
                };
            });
            sortTable('name');  // ترتيب ابتدائي حسب الاسم أبجدياً
        };

        function filterStudents() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();

            students.forEach(student => {
                const match = student.name.toLowerCase().includes(searchValue);
                student.rowElement.style.display = match ? '' : 'none';
            });
        }

        function sortTable(column) {
            if (currentSort.column === column) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.column = column;
                currentSort.direction = 'asc';
            }

            document.querySelectorAll('.sortable').forEach(th => {
                th.classList.remove('sorted-asc', 'sorted-desc');
            });

            const th = [...document.querySelectorAll('th')].find(th => th.textContent.trim() === (column === 'id' ? 'الرقم' : 'الاسم'));
            if (th) th.classList.add(currentSort.direction === 'asc' ? 'sorted-asc' : 'sorted-desc');

            students.sort((a, b) => {
                let valA = a[column];
                let valB = b[column];

                if (column === 'id') {
                    valA = parseInt(valA);
                    valB = parseInt(valB);
                } else {
                    valA = valA.toLowerCase();
                    valB = valB.toLowerCase();
                }

                if (valA < valB) return currentSort.direction === 'asc' ? -1 : 1;
                if (valA > valB) return currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            });

            students.forEach(student => studentsTableBody.appendChild(student.rowElement));
        }

        async function loadAttendance(studentId, studentName) {
            // إعادة تعيين الصفحة عند تحميل جديد
            currentAttendancePage = 1;

            // تخزين اسم الطالب
            document.getElementById('studentName').textContent = studentName;

            // جلب البيانات من السيرفر بدون فلتر التاريخ (سيتم الفلترة محلياً)
            const url = `/dashboard/student/${studentId}/attendance`;
            const response = await fetch(url);
            const data = await response.json();

            attendanceData = data.map(record => {
                const date = new Date(record.attended_at);
                return {
                    dateObj: date,
                    fullDate: date.toLocaleDateString('ar-EG'),
                    day: date.toLocaleDateString('ar-EG', { weekday: 'long' }),
                    time: date.toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' }),
                };
            });

            renderAttendanceTable();
            document.getElementById('attendanceContainer').style.display = 'block';
            document.getElementById('attendanceContainer').scrollIntoView({ behavior: 'smooth' });
        }

        // ترشيح البيانات حسب التاريخ
        function getFilteredAttendance() {
            const from = document.getElementById('dateFrom').value;
            const to = document.getElementById('dateTo').value;

            let filtered = attendanceData;

            if (from) {
                const fromDate = new Date(from);
                filtered = filtered.filter(rec => rec.dateObj >= fromDate);
            }
            if (to) {
                const toDate = new Date(to);
                filtered = filtered.filter(rec => rec.dateObj <= toDate);
            }
            return filtered;
        }

        // عرض البيانات مع بايجنيت
        function renderAttendanceTable() {
            const tbody = document.getElementById('attendanceBody');
            const filteredData = getFilteredAttendance();

            // تحديث عدد الحضور المعروض
            document.getElementById('attendanceCount').textContent = filteredData.length;

            if (filteredData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3">لا يوجد تسجيل حضور في هذه الفترة</td></tr>';
                renderPagination(0);
                return;
            }

            const start = (currentAttendancePage - 1) * attendancePerPage;
            const end = start + attendancePerPage;
            const pageData = filteredData.slice(start, end);

            tbody.innerHTML = '';
            pageData.forEach(rec => {
                tbody.innerHTML += `
                <tr>
                    <td>${rec.fullDate}</td>
                    <td>${rec.day}</td>
                    <td>${rec.time}</td>
                </tr>`;
            });

            // تحديث أزرار البايجنيت
            renderPagination(filteredData.length);
        }

        // التحكم بصفحة البايجنيت
        function renderPagination(totalItems) {
            const totalPages = Math.ceil(totalItems / attendancePerPage);
            const pagination = document.getElementById('paginationControls');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentAttendancePage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="changePageAttendance(${i});return false;">${i}</a>`;
                pagination.appendChild(li);
            }
        }

        function changePageAttendance(page) {
            currentAttendancePage = page;
            renderAttendanceTable();
        }

        // استخراج بيانات الطلاب كملف CSV
        function exportStudentsCSV() {
            // إضافة BOM (Byte Order Mark) لتشفير UTF-8 لكي يتعرف Excel على الترميز العربي
            const BOM = '\uFEFF';

            let csvContent = "data:text/csv;charset=utf-8," + encodeURIComponent(BOM);
            csvContent += encodeURIComponent("الرقم,الاسم\n");

            students.forEach(student => {
                if (student.rowElement.style.display !== 'none') { // فقط الطلاب الظاهرين حسب الفلتر
                    const line = `${student.id},"${student.name.replace(/"/g, '""')}"\n`;
                    csvContent += encodeURIComponent(line);
                }
            });

            const link = document.createElement("a");
            link.setAttribute("href", csvContent);
            link.setAttribute("download", "students_data.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        async function submitAddStudent() {
            const studentNameInput = document.getElementById('studentNameInput');
            const name = studentNameInput.value.trim();

            if (!name) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى إدخال اسم الطالب',
                });
                return;
            }

            try {
                const response = await fetch('http://192.168.1.3:8000/api/addStudent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت الإضافة',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // اغلاق المودال بعد ثانيتين
                    setTimeout(() => {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('addStudentModal'));
                        modal.hide();
                        location.reload();  // تحديث الصفحة
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: data.message || 'حدث خطأ أثناء الإضافة',
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'فشل الاتصال بالسيرفر',
                });
            }
        }
        async function deleteStudent(id) {
            const result = await Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذه العملية!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`http://192.168.1.3:8000/api/deleteStudent/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحذف',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        // تحديث الصفحة بعد الحذف
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: data.message || 'حدث خطأ أثناء الحذف',
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'فشل الاتصال بالسيرفر',
                    });
                }
            }
        }
        function closeAttendance() {
            document.getElementById('attendanceContainer').style.display = 'none';
        }

        function showQR(id, name) {
            // نظف محتوى الـ div قبل إنشاء QR جديد
            document.getElementById("qrcode").innerHTML = "";

            // إنشاء QR Code جديد
            new QRCode(document.getElementById("qrcode"), {
                text: id.toString(),
                width: 160,
                height: 160,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            document.getElementById("qrText").textContent = `الطالب: ${name} - الرقم: ${id}`;

            // فتح المودال
            var qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
            qrModal.show();
        }


    </script>
    <!-- سكريبتات جافاسكريبت -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>