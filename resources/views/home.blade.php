<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @auth
        <h1>Logged in</h1>
        <form action="/logout" method="POST">
            @csrf
            <button>Log out</button>
        </form>
    @else
        <h1>Home</h1>
        
        <h2>Register</h2>
        <form action="/register" method="POST">
            @csrf
            <input type="text" name="name" placeholder="name" required>
            <input type="email" name="email" placeholder="email" required>
            <input type="password" name="password" placeholder="password" required>
            <button>Register</button>
        </form>

        <h2>Login</h2>
        <form action="/login" method="POST">
            @csrf
            <input type="email" name="email" placeholder="email" required>
            <input type="password" name="password" placeholder="password" required>
            <button>Login</button>
        </form>
    @endauth
</body>
</html>