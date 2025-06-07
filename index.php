<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - Project1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="bg-primary text-white">
<div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 text-center">
            <h1 class="mb-4 fw-bold text-primary">Welcome to the Admin Portal</h1>
            <a href="auth/login.php" class="btn btn-primary btn-lg mb-3 w-100">Admin Login</a>
            <div class="text-center mt-4">
                <a href="secretary/login.php" class="btn btn-outline-primary">Secretary Login</a>
            </div>
        </div>
    </div>
    <footer class="mt-4 text-white-50 small text-center">
        &copy; <?php echo date('Y'); ?> Admin Dashboard
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
