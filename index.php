<?php
    $student_name = "none";
    $my_courses = [];
    $courses = [];
    if (!isset($_COOKIE["name"])) {
        header("Location: login.php");
        return;
    } else {
        $student_name = $_COOKIE["name"];
    }
    $connection = new mysqli("localhost", "root", "", "student_portal");
    $sql = "SELECT * FROM students WHERE name = '$student_name'";
    $user = $connection->query($sql)->fetch_assoc();
    if ($user["role"] == "ADMIN") {
        header("Location: admin.php");
        return;
    }
    $id = $user["id"];
    $sql = "
        SELECT code, name, points, prerequisite, status
        FROM student_course sc INNER JOIN courses c ON sc.course_code = c.code
        WHERE sc.student_id = $id
    ";
    $result = $connection->query($sql);
    while ($ass_arr = $result->fetch_assoc()) {
        $my_courses[] = $ass_arr;
    }
    $sql = "
        SELECT c.code, c.name, c.points, c.prerequisite, 
        (
            case 
            when p.code IS NULL OR p.code IN 
                (SELECT course_code FROM student_course WHERE student_id = $id AND status = 'COMPLETE') 
                THEN 'AVAILABLE'
            ELSE 'UNQUALIFIED'
            END
        )
        as status
        FROM courses c LEFT JOIN courses p ON c.prerequisite = p.code
        WHERE c.code NOT IN (SELECT course_code FROM student_course WHERE student_id = $id)
    ";
    $result = $connection->query($sql);
    while ($ass_arr = $result->fetch_assoc()) {
        $courses[] = $ass_arr;
    }
    $connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Portal</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/index.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
</head>
<body>
    <div class="auth-block">
        <div>Hi, <?php echo $_COOKIE["name"] ?></div>
        <button class="logout secondary">Logout</button>
    </div>
    <div class="title">Your courses</div>
    <div class="course-table">
        <div class="row">
            <div class="code">Code</div>
            <div class="name">Name</div>
            <div class="points">Points</div>
            <div class="prerequisite">Prerequisite</div>
            <div class="status">Status</div>
            <div class="action">Action</div>
        </div>
        <?php
            foreach($my_courses as $course) {
                echo"<div class='row'>";
                foreach($course as $key => $value) {
                    echo "<div class='$key'>$value</div>";
                }
                if ($course["status"] == "IN PROGRESS") {
                    $code = $course["code"];
                    echo "<div class='drop action' data-course='$code'>drop</div>";
                } else {
                    echo "<div class='action'></div>";
                }
                echo "</div>";
            }
        ?>
    </div>
    <div class="title">Other courses</div>
    <div class="course-table">
        <div class="row">
            <div class="code">Code</div>
            <div class="name">Name</div>
            <div class="points">Points</div>
            <div class="prerequisite">Prerequisite</div>
            <div class="status">Status</div>
            <div class="action">Action</div>
        </div>
        <?php 
            foreach($courses as $course) {
                echo "<div class='row'>";
                foreach($course as $key => $value) {
                    echo "<div class='$key'>$value</div>";
                }
                if ($course["status"] == "AVAILABLE") {
                    $code = $course["code"];
                    echo "<div class='select action' data-course='$code'>select</div>";
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
        $(".drop").on({
            click: (e) => {
                const course = e.target.dataset["course"]
                $.ajax({
                    url: "api/courses.php",
                    data: course,
                    type: "DELETE",
                    success: (e) => {
                        window.location.reload()
                    }
                })
            }
        })
        $(".select").on({
            click: (e) => {
                const course = e.target.dataset["course"]
                $.ajax({
                    url: "api/courses.php",
                    data: course,
                    type: "POST",
                    success: (e) => {
                        window.location.reload()
                    }
                })
            }
        })
    </script>
</body>
</html>
