<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//?>

<?php
session_start();
include_once('../config/db.php'); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['pswd'];

    // Validate input
    if (empty($username) || empty($password)) {
        die('Please fill in both fields.');
    }

    // Prepare and execute query
    $stmt = $conn->prepare('SELECT id, password FROM User WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            // Redirect to a protected page
            header('Location: /home/home.php');
            exit();
        } else {
            echo 'Invalid password.';
        }
    } else {
        echo 'No user found with that email address.';
    }

    $stmt->close();
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
