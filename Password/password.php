<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

try {
    // Establish database connection
    $conn = new PDO("mysql:3306:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Request reset token
    if (isset($_POST['request_reset'])) {
        $user = $_POST['username'];

        // Check if username exists
        $stmt = $conn->prepare("SELECT email FROM users WHERE username = :username");
        $stmt->bindParam(':username', $user);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $email = $stmt->fetch(PDO::FETCH_ASSOC)['email'];

            // Generate a unique token and expiry
            $token = bin2hex(random_bytes(16));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Save token and expiry in the database
            $update_stmt = $conn->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE username = :username");
            $update_stmt->bindParam(':token', $token);
            $update_stmt->bindParam(':expiry', $expiry);
            $update_stmt->bindParam(':username', $user);
            $update_stmt->execute();

            // Display the token to the user
            echo "Your password reset token is: <strong>$token</strong><br>";
            echo "Please use this token in the password reset form within 1 hour.";
        } else {
            echo "Username not found.";
        }
    }

    // Reset password using token
    if (isset($_POST['reset_password'])) {
        $token = $_POST['token'];
        $new_pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

        // Validate token
        $stmt = $conn->prepare("SELECT username, token_expiry FROM users WHERE reset_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (strtotime($row['token_expiry']) > time()) {
                // Update password and clear token
                $update_stmt = $conn->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expiry = NULL WHERE reset_token = :token");
                $update_stmt->bindParam(':password', $new_pass);
                $update_stmt->bindParam(':token', $token);
                $update_stmt->execute();

                echo "Password reset successful.";
            } else {
                echo "Reset token has expired.";
            }
        } else {
            echo "Invalid reset token.";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
} finally {
    // Close the database connection
    $conn = null;
}
?>