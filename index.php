<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}
if (isset($_POST['logout'])) {
    $recaptcha_secret = "6LeME7ooAAAAAG579sCUnM2GEFrAp9ESG648QQON";
    $recaptcha_response = $_POST['g-recaptcha-response'];

    $recaptcha = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $recaptcha = json_decode($recaptcha);

    if ($recaptcha->success) {
        // reCAPTCHA hợp lệ, hủy phiên làm việc và chuyển người dùng về trang đăng nhập
        session_destroy();
        header("Location: login.php");
        exit;
    } else {
        // reCAPTCHA không hợp lệ, giữ nguyên trang User Dashboard
        echo "reCAPTCHA verification failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>User Dashboard</title>
</head>

<body>
    <div class="container">
        <h1>Welcome to Dashboard</h1>
        <form method="post">
            <div class="g-recaptcha" data-sitekey="6LeME7ooAAAAAMLN24nYqNzxkYdm-JXcZpC6Wvs5"></div>
            <input type="submit" name="logout" value="Logout">
        </form>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>

</html>