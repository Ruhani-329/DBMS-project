<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quickhire";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search input
$category = $_POST['category'] ?? '';
$location = $_POST['location'] ?? '';

// Fetch providers from the database based on the search criteria
$stmt = $conn->prepare("SELECT provider_id AS id, name, category, location, contact, experience 
                        FROM provider 
                        WHERE category = ? AND location LIKE ?");
$search_location = "%$location%";
$stmt->bind_param("ss", $category, $search_location);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Providers</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
            color: #333;
        }

        header {
            background: linear-gradient(90deg, #ff7e5f, #feb47b);
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .provider {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .provider:last-child {
            border-bottom: none;
        }

        .provider-details {
            flex-grow: 1;
        }

        .provider-name {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .provider-info {
            margin: 5px 0;
            color: #555;
        }

        .hire-btn {
            background: #ff7e5f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .hire-btn:hover {
            background: #feb47b;
        }

        .no-results {
            text-align: center;
            font-size: 1.2rem;
            color: #888;
        }
    </style>
</head>
<body>
<header>
    Find Providers
</header>
<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($provider = $result->fetch_assoc()): ?>
            <div class="provider">
                <div class="provider-details">
                    <div class="provider-name"><?php echo htmlspecialchars($provider['name']); ?></div>
                    <div class="provider-info">
                        <strong>Category:</strong> <?php echo htmlspecialchars($provider['category']); ?> <br>
                        <strong>Location:</strong> <?php echo htmlspecialchars($provider['location']); ?> <br>
                        <strong>Contact:</strong> <?php echo htmlspecialchars($provider['contact']); ?> <br>
                        <strong>Experience:</strong> <?php echo htmlspecialchars($provider['experience']); ?> years
                    </div>
                </div>
                <form action="booking.php" method="GET">
                    <input type="hidden" name="provider_id" value="<?php echo $provider['id']; ?>">
                    <button type="submit" class="hire-btn">Hire</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-results">No providers found for your search.</div>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
