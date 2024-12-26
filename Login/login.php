<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database.db";

try {
    $conn = new PDO("mysql:3306:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Login
    if (isset($_POST['login'])) {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        $stmt = $conn->prepare("SELECT password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $user,PDO::PARAM_STR );
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($pass, $row['password'])) {

                header('Location: dashboard.html');
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "No user found";
        }
    }


} catch(PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
}

$conn = null;
?>