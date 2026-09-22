<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Stock System</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            min-height: 100vh;
            margin: 0;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 400px;
            background: white;
            padding: 40px;
            border-radius: 20px;

            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .logo {
            text-align: center;
            font-size: 55px;
            margin-bottom: 5px;
        }

        h2 {
            text-align: center;
            color: #1976d2;
            margin: 0;
            font-size: 28px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-top: 8px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #444;
        }

        input {
            width: 100%;
            padding: 13px;
            margin-bottom: 20px;

            border: 1px solid #ccc;
            border-radius: 9px;

            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: #1976d2;
            box-shadow: 0 0 0 2px #e3f2fd;
        }

        button {
            width: 100%;
            padding: 13px;

            background: #1976d2;
            color: white;

            border: none;
            border-radius: 9px;

            font-size: 16px;
            font-weight: bold;

            cursor: pointer;
        }

        button:hover {
            background: #125aa0;
        }

        .error {
            background: #ffebee;
            color: #c62828;

            padding: 12px;
            border-radius: 8px;

            margin-bottom: 20px;
            text-align: center;
        }

        .footer {
            text-align: center;
            color: #999;
            font-size: 13px;
            margin-top: 25px;
        }
    </style>
</head>

<body>

<div class="login-box">

    <div class="logo">
        📦
    </div>

    <h2>Stock System</h2>

    <p class="subtitle">
        ระบบจัดการสต็อกสินค้า
    </p>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <label>Email</label>

        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="กรอกอีเมล"
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="กรอกรหัสผ่าน"
        >

        <button type="submit">
            🔐 เข้าสู่ระบบ
        </button>

    </form>

    <div class="footer">
        Stock Management System
    </div>
     <div class="footer">
  หากลืม Email หรือ password โปรดติดต่อที่ helpdesk
  </div>  
</div>

</body>
</html>