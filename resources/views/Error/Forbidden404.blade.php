<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>404 - Forbidden</title>
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
    <h2>404 - Not Found</h2>
    <p>Halaman ini tidak tersedia.</p>
    <a href="{{url('/login')}}">Kembali ke Login</a>
</body>
</html>
