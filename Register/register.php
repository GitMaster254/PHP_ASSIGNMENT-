<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";


try {
    $conn = new PDO("mysql:3306:host=$servername;dbname=$dbname", $username, $password, $email);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Registration
    if (isset($_POST['register'])) {
        $user = $_POST['username'];
        $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $email = $_POST['email'];

        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        $stmt->bindParam(':username', $user, PDO::PARAM_STR);
        $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Registration successful";
            header('Location: login.php');
            exit;
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
    $conn = null;
?>