<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickHire Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #ffffff, #ffefd5);
            overflow-x: hidden;
            color: #333;
        }

        header {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background: linear-gradient(90deg, #ff7e5f, #feb47b);
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-circle {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #ffffff;
            color: #ff7e5f;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            line-height: 50px; /* Match the height of the circle */
        }
        



        .user-circle:hover {
            transform: scale(1.2);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .user-circle:hover::after {
            content: '<?php echo htmlspecialchars($_SESSION['username']); ?>';
            position: absolute;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            color: #333;
            padding: 5px 10px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-size: 14px;
            white-space: nowrap;
        }

        .user-circle a {
            display: block;
            width: 100%;
            height: 100%;
            text-decoration: none;
            color: inherit;
        }

        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            min-height: 70vh;
            padding: 50px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            text-align: center;
            transform: translateY(-30%);
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ff7e5f;
        }

        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            color: #555;
        }

        .search-bar {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            justify-content: center;
        }

        .search-bar input,
        .search-bar select,
        .search-bar button {
            padding: 20px;
            border-radius: 30px;
            border: 1px solid #ddd;
            font-size: 18px;
            min-width: 220px;
        }

        .search-bar button {
            background-color: #ff7e5f;
            color: white;
            border: none;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .search-bar button:hover {
            transform: scale(1.1);
        }

        .categories-section {
            padding: 50px 20px;
            text-align: center;
            background-color: #fdfdfd;
        }

        .categories-section h2 {
            font-size: 2.5rem;
            color: #ff7e5f;
            margin-bottom: 30px;
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            padding: 0 50px;
        }

        .category {
            background: #ff7e5f;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, background-color 0.3s;
        }

        .category:hover {
            background-color: #feb47b;
            transform: scale(1.05);
        }

        .hero-animation {
            position: absolute;
            top: 0;
            left: 50%;
            width: 120%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 193, 143, 0.3), rgba(255, 125, 95, 0.1));
            animation: rotateBg 10s infinite linear;
            z-index: 0;
        }

        @keyframes rotateBg {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">QuickHire</div>
        <div class="header-icons">
            <div class="user-circle">
                <a href="user_profile.php"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to QuickHire</h1>
            <p>Find and hire skilled professionals near you in just a few clicks!</p>
            <div class="search-bar">
                <form action="find.php" method="POST">
                    <select name="category" required>
                        <option value="">Job Category</option>
                        <option>Photographer</option>
                        <option>Handyman</option>
                        <option>Tutor</option>
                        <option>Car Mechanic</option>
                        <option>Plumber</option>
                        <option>Junk Hauler</option>
                        <option>Tow Truck</option>
                        <option>Electrician</option>
                        <option>House Cleaner</option>
                        <option>Painter</option>
                        <option>Landscaper</option>
                        <option>Personal Trainer</option>
                        <option>Massage Therapist</option>
                        <option>Makeup Artist</option>
                        <option>Hairstylist</option>
                        <option>Event Planner</option>
                        <option>Accountant</option>
                        <option>Author</option>
                        <option>Baker</option>
                        <option>Doctor</option>
                        <option>Butcher</option>
                        <option>Florist</option>
                        <option>Lawyer</option>
                        <option>Tailor</option>
                        <option>Watchmen</option>
                        <option>Cook</option>
                        <option>Vet</option>
                        <option>Musician</option>
                        <option>Pharmacist</option>
                    </select>
                    <input type="text" name="location" placeholder="Enter your location">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
        <div class="hero-animation"></div>
    </section>

    <section class="categories-section">
        <h2>Explore Job Categories</h2>
        <div class="categories">
            <div class="category">Photographer</div>
            <div class="category">Handyman</div>
            <div class="category">Tutor</div>
            <div class="category">Car Mechanic</div>
            <div class="category">Plumber</div>
            <div class="category">Junk Hauler</div>
            <div class="category">Tow Truck</div>
            <div class="category">Electrician</div>
            <div class="category">House Cleaner</div>
            <div class="category">Painter</div>
            <div class="category">Landscaper</div>
            <div class="category">Personal Trainer</div>
            <div class="category">Massage Therapist</div>
            <div class="category">Makeup Artist</div>
            <div class="category">Hairstylist</div>
            <div class="category">Event Planner</div>
            <div class="category">Accountant</div>
            <div class="category">Author</div>
            <div class="category">Baker</div>
            <div class="category">Doctor</div>
            <div class="category">Butcher</div>
            <div class="category">Florist</div>
            <div class="category">Lawyer</div>
            <div class="category">Tailor</div>
            <div class="category">Watchmen</div>
            <div class="category">Cook</div>
            <div class="category">Vet</div>
            <div class="category">Musician</div>
            <div class="category">Pharmacist</div>
        </div>
    </section>
</body>
</html>
