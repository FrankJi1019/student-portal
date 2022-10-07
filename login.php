<?php
    $wrong_credential = false;
    if (isset($_COOKIE["name"])) {
        header("Location: index.php");
    }
    if (isset($_POST["login"])) {
        $name = $_POST["name"];
        $password = $_POST["password"];
        $connection = new mysqli("localhost", "root", "", "student_portal");
        $sql = "SELECT * FROM students WHERE name = '$name' AND password = '$password'";
        $result = $connection->query($sql);
        if ($result->num_rows > 0) {
            setcookie("name", $name);
            header("Location: index.php");
        } else {
            $wrong_credential = true;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Portal | Login</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/login.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <form method="POST">
        <div class="form">
            <div class="title">Student Portal</div>
            <input name="name" placeholder="name">
            <input type="password" name="password" placeholder="password">
            <div class="error-message">
                <?php echo $wrong_credential ? "Invalid credential" : "" ?>
            </div>
            <div class="button-container">
                <button type="submit" name="login" class="primary">Login</button>
                <button name="signup" class="secondary">Signup</button>
            </div>
        </div>
    </form>
    <script>
        $("button[name=\"signup\"]").on({
            click: (e) => {
                e.preventDefault()
                window.location.href = "signup.php"
            }
        })
    </script>
</body>
</html>