<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>403 - Forbidden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8f9;
            color: #d8000c;
            text-align: center;
            padding-top: 100px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #d8000c;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background-color: #a30000;
        }
    </style>
</head>
<body>
    <h2>403 - Forbidden</h2>
    <p>Anda tidak memiliki akses ke halaman ini.</p>
    <a href="{{url('/login')}}">Kembali ke Login</a>
</body>
</html>
