<?php
session_start();
if (isset($_POST["captchaCompleted"])) {
    $_SESSION["captchaCompleted"] = $_POST["captchaCompleted"];
}
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}
$captchaCompleted = isset($_SESSION["captchaCompleted"]) ? $_SESSION["captchaCompleted"] : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link href="disk/slidercaptcha.css" rel="stylesheet" />
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    .slidercaptcha {
        margin: 0 auto;
        width: 314px;
        height: 286px;
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.125);
        margin-top: 40px;
    }

    .slidercaptcha .card-body {
        padding: 1rem;
    }

    .slidercaptcha canvas:first-child {
        border-radius: 4px;
        border: 1px solid #e6e8eb;
    }

    .slidercaptcha.card .card-header {
        background-image: none;
        background-color: rgba(0, 0, 0, 0.03);
    }

    .refreshIcon {
        top: -54px;
    }
    </style>
</head>

<body>
    <div class="container">
        <?php
        if (isset($_POST["login"])) {
            $email = $_POST["email"];
            $password = $_POST["password"];

            // Kiểm tra checkbox đã được chọn hay chưa
            if (isset($_POST["checkbox"])) {
                // Checkbox đã được chọn, tiếp tục xử lý đăng nhập
                if ($captchaCompleted == '1') { // Kiểm tra biến $captchaCompleted
                    require_once "database.php";
                    $sql = "SELECT * FROM users WHERE email = '$email'";
                    $result = mysqli_query($conn, $sql);
                    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    if ($user) {
                        if (password_verify($password, $user["password"])) {
                            session_start();
                            $_SESSION["user"] = "yes";
                            header("Location: index.php");
                            die();
                        } else {
                            echo "<div class='alert alert-danger'>Password does not match</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Email does not match</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Please complete the security verification</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Please complete the security verification</div>";
            }
        }
        ?>
        <form action="login.php" method="post" id="loginForm">
            <div class="form-group">
                <input type="email" placeholder="Enter Email:" name="email" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password:" name="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="checkbox">I'm not robot: </label>
                <input type="checkbox" name="checkbox" id="checkbox">
            </div>
            <input type="hidden" name="captchaCompleted" id="captchaCompleted" value="0">
            <div class="container-fluid" style="display: none;">
                <div class="form-row">
                    <div class="col-12">
                        <div class="slidercaptcha card">
                            <div class="card-header">
                                <span>Vui lòng hoàn tất xác minh bảo mật!</span>
                            </div>
                            <div class="card-body">
                                <div id="captcha"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="disk/longbow.slidercaptcha.js"></script>
            <script>
            var captcha = null;

            document.getElementById("checkbox").addEventListener("change", function() {
                var container = document.querySelector(".container-fluid");
                var captchaCompletedField = document.getElementById("captchaCompleted"); // Thêm dòng này
                if (this.checked) {
                    container.style.display = "block";
                    if (captcha === null) {
                        captcha = sliderCaptcha({
                            id: "captcha",
                            setSrc: function() {
                                return "images/Pic" + Math.round(Math.random() * 4) + ".jpg";
                            },
                            onSuccess: function() {
                                // Gửi giá trị captchaCompleted bằng AJAX
                                var xhr = new XMLHttpRequest();
                                xhr.open("POST", "login.php", true);
                                xhr.setRequestHeader("Content-type",
                                    "application/x-www-form-urlencoded");
                                xhr.onreadystatechange = function() {
                                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status ===
                                        200) {
                                        // Xử lý phản hồi từ server (nếu cần)
                                    }
                                };
                                captchaCompletedField.value = "1"; // Thêm dòng này
                                xhr.send("captchaCompleted=1");
                            }
                        });
                    } else {
                        captcha.setSrc("images/Pic" + Math.round(Math.random() * 4) + ".jpg");
                        captcha.refresh();
                    }
                } else {
                    container.style.display = "none";
                    captchaCompletedField.value = "0"; // Thêm dòng này

                }
            });
            </script>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
        </form>
        <div>
            <p>Not registered yet <a href="registration.php">Register Here</a></p>
        </div>
    </div>
</body>

</html>