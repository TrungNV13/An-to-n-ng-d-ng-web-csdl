<?php
    session_start();
    $captcha_code = $_SESSION["captcha_code"];
    if (isset($_SESSION["user"])) {
        header("Location: index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body onload="cap()">
    <div class="container">
        <?php
            if(isset($_POST["submit"])){
                $fullName = $_POST["fullname"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $passwordRepeat = $_POST["repeat_password"];
                $captcha = $_POST["captcha"];
                
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $errors = array();
                
                if(empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat) OR empty($captcha)){
                    array_push($errors,"All fields are required");
                }
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    array_push($errors, "Email is not valid");
                }
                if(strlen($password)<8){
                    array_push($errors,"Password must be at least 8 charactes long");
                }
                if($password!==$passwordRepeat){
                    array_push($errors,"Password does not match");
                }
                require_once "database.php";
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($conn, $sql);
                $rowCount = mysqli_num_rows($result);
                if($rowCount>0){
                    array_push($errors,"Email already exists!");
                }
                if(count($errors)>0){
                    foreach($errors as  $error){
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                }
                else{
                    if($captcha === $captcha_code){
                        $sql = "INSERT INTO users (full_name, email, password) VALUES ( ?, ?, ? )";
                        $stmt = mysqli_stmt_init($conn);
                        $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
                        if($prepareStmt){
                            mysqli_stmt_bind_param($stmt,"sss",$fullName, $email, $passwordHash);
                            mysqli_stmt_execute($stmt);
                            echo "<div class='alert alert-success'>You are registered successfully.</div>";
                        }
                        else{
                            die("Something went wrong");
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Please enter a valid captcha.</div>";
                    }
                }
            }
            $captcha_code = $_SESSION["captcha_code"];
        ?>
        <form action="registration.php" method="post">
            <h1>Create Account</h1>
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <img src="captcha_test.php" alt="Captcha Image" class="captcha-image" id="captcha-image1">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="textinput" name="captcha"
                            placeholder="Enter Captcha">
                    </div>
                </div>
            </div>
            <h6 class="text">Reset Captcha <img class="m1" src="refresh.png" width="40px" onclick="cap()"></h6>
            <div class="form-btn">
                <input onclick="return validcap()" type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
            <div>
                <p>Already Registered <a href="login.php">Login Here</a></p>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    function cap() {
        var captchaImage = document.getElementById('captcha-image1');
        captchaImage.src = 'captcha_test.php?' + Date.now();
    }
    window.onload = cap;
    document.querySelector('.m1').addEventListener('click', function() {
        cap();
    });

    function validcap() {
        var stg2 = document.getElementById('textinput').value;
        if ($captcha_code === stg2) {
            return true;
        } else {
            return false;
        }
    }
    </script>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
</script>

</html>