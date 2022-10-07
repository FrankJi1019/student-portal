<?php
    $name = "none";
    if (!isset($_COOKIE["name"])) {
        header("Location: login.php");
        return;
    } else {
        $name = $_COOKIE["name"];
    }
    $connection = new mysqli("localhost", "root", "", "student_portal");
    $sql = "SELECT * FROM students WHERE name = '$name'";
    $user = $connection->query($sql)->fetch_assoc();
    if ($user["role"] != "ADMIN") {
        header("Location: index.php");
        return;
    }

    $course_records = [];
    $sql = "
        SELECT s.name as student_name, c.code, c.name, c.points, c.prerequisite, sc.status
        FROM student_course sc 
        INNER JOIN courses c ON c.code = sc.course_code
        INNER JOIN students s ON sc.student_id = s.id
    ";
    $result = $connection->query($sql);
    while ($ass_arr = $result->fetch_assoc()) {
        $course_records[] = $ass_arr;
    }

    $connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student portal | admin</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/admin.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
</head>
<body>
    <div class="auth-block">
        <div>Hi, <?php echo $_COOKIE["name"] ?></div>
        <button class="logout secondary">Logout</button>
    </div>
    <div class="title">Student records</div>
    <div class="course-table">
        <div class="row">
            <div class="student_name">Student</div>
            <div class="code">Course Code</div>
            <div class="name">Course Name</div>
            <div class="points">Points</div>
            <div class="prerequisite">Prerequisite</div>
            <div class="status">Status</div>
            <div class="action">Action</div>
        </div>
        <?php
            foreach($course_records as $course) {
                echo"<div class='row'>";
                foreach($course as $key => $value) {
                    echo "<div class='$key'>$value</div>";
                }
                if ($course["status"] == "IN PROGRESS") {
                    $code = $course["code"];
                    $student = $course["student_name"];
                    echo "<div class='complete action' data-student='$student' data-course='$code'>complete</div>";
                } else if ($course["status"] == "PENDING") {
                    $code = $course["code"];
                    $student = $course["student_name"];
                    echo "<div class='approve action' data-student='$student' data-course='$code'>approve</div>";
                } else {
                    echo "<div class='action'></div>";
                }
                echo "</div>";
            }
        ?>
    </div>

    <script>
        $("button.logout").on({
            click: () => {
                $.removeCookie("name", {path: "/student-portal"})
                window.location.href = "login.php"
            }
        })
        $(".complete").on({
            click: (e) => {
                const course = e.target.dataset["course"]
                const student = e.target.dataset["student"]
                $.ajax({
                    url: "api/complete.php",
                    data: JSON.stringify({
                        course, student
                    }),
                    type: "PATCH",
                    success: (e) => {
                        window.location.reload()
                    }
                })
            }
        })
        $(".approve").on({
            click: (e) => {
                const course = e.target.dataset["course"]
                const student = e.target.dataset["student"]
                $.ajax({
                    url: "api/approve.php",
                    data: JSON.stringify({
                        course, student
                    }),
                    type: "PATCH",
                    success: (e) => {
                        window.location.reload()
                    }
                })
            }
        })
    </script>
    
</body>
</html>
