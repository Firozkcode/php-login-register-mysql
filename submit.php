<?php
$conn = new mysqli("localhost", "root", "", "youtube");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // REGISTER FORM (username exists)
    if (isset($_POST['username'])) {

        $username = $_POST['username'];

        $password = password_hash($password, PASSWORD_DEFAULT); // Hashing the password for security

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            echo "Registration successful";   //For success registration
        } else {
            echo "Email already exists";  //For duplicate email registration
        }
    }

    // LOGIN FORM (no username field)
    else {

        $stmt = $conn->prepare("SELECT password FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                echo "Login successful";  // For successful login
            } else {
                echo "Wrong password";  // For incorrect password
            }

        } else {
            echo "User not found";  // For email not found in database Or incorrect email
        }
    }
}
?>
