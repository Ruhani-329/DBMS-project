<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.html");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "quickhire");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

// Update user data on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Optional password update

    // Validate inputs
    if (empty($name) || empty($email)) {
        $error = "Name and Email are required fields.";
    } else {
        // Update query
        $update_sql = "UPDATE users SET name = ?, address = ?, contact = ?, email = ?, password = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);

        // Hash password if provided
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $hashed_password = $user['password']; // Keep existing password
        }

        $update_stmt->bind_param("sssssi", $name, $address, $contact, $email, $hashed_password, $user_id);

        if ($update_stmt->execute()) {
            $success = "Profile updated successfully!";
            // Refresh user data
            $user = array_merge($user, ['name' => $name, 'address' => $address, 'contact' => $contact, 'email' => $email]);
        } else {
            $error = "Error updating profile. Please try again.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #FF7E5F, #FEB47B);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .profile-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }

        .profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            color: green;
            margin-bottom: 15px;
            text-align: center;
        }

        .submit-btn, .back-btn {
            width: 100%;
            padding: 10px;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .submit-btn {
            background-color: #ff7e5f;
        }

        .submit-btn:hover {
            background-color: #e76f59;
        }

        .back-btn {
            background-color: #6c757d;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Edit Profile</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
            </div>
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password (leave blank to keep current):</label>
                <input type="password" id="password" name="password">
            </div>
            <button type="submit" class="submit-btn">Update Profile</button>
        </form>

        <!-- Back button -->
        <form action="user_profile.php" method="get">
            <button type="submit" class="back-btn">Back</button>
        </form>
    </div>
</body>
</html>
