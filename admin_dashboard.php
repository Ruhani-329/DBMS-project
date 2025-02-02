<?php
session_start();
include 'db_config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Handle deletion directly
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_type'], $_POST['delete_id'])) {
    $delete_type = $_POST['delete_type'];
    $delete_id = intval($_POST['delete_id']);

    if ($delete_type === 'user') {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
    } elseif ($delete_type === 'provider') {
        $stmt = $conn->prepare("DELETE FROM provider WHERE provider_id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
    exit();
}

// Fetch users and providers
$users = $conn->query("SELECT * FROM users");
$providers = $conn->query("SELECT * FROM provider");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            height: 100vh;
            background: linear-gradient(-45deg, #ff7e5f, #feb47b, #86a8e7, #91eac9);
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        header {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #ff7e5f;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background: #ff7e5f;
            color: white;
        }

        table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .btn {
            background: #ff7e5f;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #e0695b;
        }

        .delete-btn {
            background: #e74c3c;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        .logout {
            text-align: right;
            margin-bottom: 20px;
        }

        .logout a {
            text-decoration: none;
            font-size: 16px;
            color: #ff7e5f;
            font-weight: bold;
        }

        .logout a:hover {
            text-decoration: underline;
        }

        .subscription-btn {
            margin-bottom: 20px;
            display: inline-block;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function deleteEntry(type, id) {
            if (confirm(`Are you sure you want to delete this ${type}?`)) {
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: { delete_type: type, delete_id: id },
                    success: function(response) {
                        const res = JSON.parse(response);
                        if (res.success) {
                            alert(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted successfully.`);
                            location.reload(); // Refresh the page to reflect changes
                        } else {
                            alert(`Failed to delete ${type}.`);
                        }
                    },
                    error: function() {
                        alert('An error occurred.');
                    }
                });
            }
        }
    </script>
</head>
<body>
    <header>Welcome, Admin</header>
    <div class="container">
        <div class="logout">
            <a href="admin_logout.php" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>

        <div class="subscription-btn">
            <a href="view_subscriptions.php" class="btn">View Subscription Fees</a>
        </div>

        <h2>Users</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['contact']); ?></td>
                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                        <td>
                            <button class="btn delete-btn" onclick="deleteEntry('user', <?php echo $user['user_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Providers</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Contact</th>
                    <th>Experience</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($provider = $providers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($provider['name']); ?></td>
                        <td><?php echo htmlspecialchars($provider['category']); ?></td>
                        <td><?php echo htmlspecialchars($provider['location']); ?></td>
                        <td><?php echo htmlspecialchars($provider['contact']); ?></td>
                        <td><?php echo htmlspecialchars($provider['experience']); ?> years</td>
                        <td>
                            <button class="btn delete-btn" onclick="deleteEntry('provider', <?php echo $provider['provider_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

