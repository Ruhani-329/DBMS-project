
<?php
session_start();
include 'db_config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form data is received via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category']; // Get category from the form
    $location = $_POST['location']; // Get location from the form

    // SQL query to fetch matching providers
    $sql = "SELECT p.name, p.image, p.category, p.location, p.contact, p.address, p.experience
            FROM provider p
            WHERE p.category = ? AND p.location = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $category, $location);
    $stmt->execute();
    $result = $stmt->get_result(); // Get the query results
} else {
    echo "Invalid request.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - QuickHire</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #ffffff, #ffefd5);
            color: #333;
            overflow-x: hidden;
        }

        header {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(90deg, #ff7e5f, #feb47b);
            color: white;
            font-size: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            transition: transform 0.3s, box-shadow 0.3s;
            animation: fadeIn 1s ease-in-out;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }

        .card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .card-content {
            flex: 1;
        }

        .card-content h3 {
            margin: 0 0 10px;
            color: #ff7e5f;
            font-size: 1.5rem;
        }

        .card-content p {
            margin: 5px 0;
            color: #555;
        }

        .card-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            background-color: #ff7e5f;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .card-actions button:hover {
            background-color: #fd9d82;
            transform: scale(1.05);
        }

        .no-results {
            text-align: center;
            margin-top: 50px;
            font-size: 1.5rem;
            color: #555;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <header>
        Professionals in <?php echo htmlspecialchars($category); ?> (<?php echo htmlspecialchars($location); ?>)
    </header>
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="avarter.png" alt="Profile Image">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p>Category: <?php echo htmlspecialchars($row['category']); ?></p>
                        <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
                        <p>Experience: <?php echo htmlspecialchars($row['experience']); ?> years</p>
                    </div>
                    <div class="card-actions">
                        <form action="user_login.html" method="GET">
                            <button type="submit">Hire</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">No professionals found for this category and location.</div>
        <?php endif; ?>
    </div>
</body>
</html>
 