<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #b5c6e1 100%); min-height: 100vh; display: flex; align-items: center; }
        .welcome-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 3rem; }
    </style>
</head>
<body>
    <div class="container text-center">
        <div class="welcome-card mx-auto" style="max-width: 600px;">
            <h1 class="display-5 fw-bold text-primary mb-3">Student Management System</h1>
            <p class="lead text-secondary mb-5">Manage student records, track analytics, and handle administrative tasks with ease.</p>
            
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 fw-bold shadow-sm">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-5 fw-bold">Sign Up</a>
            </div>
            
            <p class="mt-5 text-muted small">© 2026 USM Student System Portfolio</p>
        </div>
    </div>
</body>
</html>