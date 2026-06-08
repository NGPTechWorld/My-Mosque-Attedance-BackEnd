<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR - {{ $teacher->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        body { font-family: 'Tahoma', sans-serif; text-align: center; padding: 40px; background: #f5f5f5; }
        .card { background: #fff; display: inline-block; padding: 30px 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.1); }
        h2 { margin: 0 0 6px; }
        .code { color: #555; margin-bottom: 20px; font-size: 18px; }
        #qrcode { display: flex; justify-content: center; }
        .actions { margin-top: 24px; }
        button, a.btn { padding: 8px 20px; border: none; border-radius: 8px; background: #0d6efd; color: #fff; font-size: 15px; cursor: pointer; text-decoration: none; margin: 0 4px; }
        a.btn.secondary { background: #6c757d; }
        @media print { .actions { display: none; } body { background: #fff; } .card { box-shadow: none; } }
    </style>
</head>
<body>
    <div class="card">
        <h2>{{ $teacher->name }}</h2>
        <div class="code">الكود: {{ $teacher->code ?? $teacher->id }}</div>
        <div id="qrcode"></div>
        <div class="actions">
            <button onclick="window.print()">طباعة</button>
            <a class="btn secondary" href="{{ route('teachers.index') }}">رجوع</a>
        </div>
    </div>

    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: @json((string) ($teacher->code ?? $teacher->id)),
            width: 240,
            height: 240,
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
