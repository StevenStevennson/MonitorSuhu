<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MonitorSuhu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.net/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Open Sans', sans-serif;
        }
        .card-login {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
        }
        .btn-gradient {
            background: linear-gradient(310deg, #7928ca 0%, #cb0c9f 100%);
            color: #fff;
            border: none;
        }
        .btn-gradient:hover { color: #fff; opacity: 0.9; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card card-login p-4">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light p-3 mb-2">
                            <i class="fa-solid fa-microchip text-primary fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-dark m-0">MonitorSuhu</h4>
                        <p class="text-muted small">Sign in to access your dashboard</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="/login">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-value form-control" value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn btn-gradient w-100 py-2 fw-semibold rounded-3">Sign In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>