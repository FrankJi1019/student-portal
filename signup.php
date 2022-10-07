<?php
    $password_not_match = false;
    $name_exist = false;
    if (isset($_COOKIE["name"])) {
        header("Location: index.php");
    }
    if (isset($_POST["signup"])) {
        $name = $_POST["name"];
        $birthday = $_POST["birthday"];
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];
        if ($password == $confirm_password) {
            $connection = new mysqli("localhost", "root", "", "student_portal");
            $sql = "SELECT * FROM students WHERE name = '$name'";
            $result = $connection->query($sql);
            if ($result->num_rows != 0) {
                $name_exist = true;
            } else {
                $sql = "INSERT INTO students(name, birthday, password) VALUES ('$name', '$birthday', '$password')";
                $result = $connection->query($sql);
                if ($result) {
                    setcookie("name", $name);
                    header("Location: index.php");
                }
            }
        } else {
            $password_not_match = true;
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Portal | Signup</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/signup.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <form method="POST">
        <div class="form">
            <div class="title">Student Portal</div>
            <input name="name" placeholder="name">
            <input type="date" name="birthday" placeholder="birthday">
            <input type="password" name="password" placeholder="password">
            <input type="password" name="confirm_password" placeholder="confirm password">
            <div class="error-message">
                <?php echo $password_not_match ? "Passwords do not match" : ""; ?>
                <?php echo $name_exist ? "Sdudent exists" : ""; ?>
            </div>
            <div class="button-container">
                <button name="login" class="secondary">Login</button>
                <button type="submit" name="signup" class="primary">Signup</button>
            </div>
    </form>
    </div>
    <script>
        $("button[name=\"login\"]").on({
            click: (e) => {
                e.preventDefault()
                window.location.href = "login.php"
            }
        })
    </script>
</body>
</html>
