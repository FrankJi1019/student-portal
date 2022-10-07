<?php

    $student_name = "none";
    if (!isset($_COOKIE["name"])) {
        header("Location: login.php");
        return;
    } else {
        $student_name = $_COOKIE["name"];
    }
    $connection = new mysqli("localhost", "root", "", "student_portal");

    if ($_SERVER["REQUEST_METHOD"] == "PATCH") {
        echo file_get_contents("php://input");
        $course = json_decode(file_get_contents("php://input"))->course;
        $student = json_decode(file_get_contents("php://input"))->student;
        $sql = "SELECT id FROM students WHERE name = '$student'";
        $id = $connection->query($sql)->fetch_assoc()["id"];
        $sql = "UPDATE student_course SET status = 'IN PROGRESS' WHERE course_code='$course' AND student_id=$id";
        $result = $connection->query($sql);
    
    }

    $connection->close();

?>