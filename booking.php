<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.html");
    exit();
}

include 'db_config.php';

// Get provider ID from the query string
$provider_id = $_GET['provider_id'] ?? null;

if (!$provider_id) {
    echo "Invalid provider selected.";
    exit();
}

// Fetch provider details
$stmt = $conn->prepare("SELECT name, category, location, contact, experience FROM provider WHERE provider_id = ?");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();

if (!$provider) {
    echo "Provider not found.";
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Provider</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
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
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #ff7e5f;
            font-size: 2.5rem;
        }

        .provider-details {
            margin-bottom: 30px;
            text-align: center;
            padding: 20px;
            background: #ff7e5f;
            color: white;
            border-radius: 10px;
        }

        .provider-details h2 {
            margin: 0;
        }

        .provider-details p {
            margin: 5px 0;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group textarea {
            resize: vertical;
        }

        .submit-btn {
            display: block;
            width: 100%;
            background: #ff7e5f;
            color: white;
            border: none;
            padding: 15px;
            font-size: 1.2rem;
            border-radius: 30px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background: #feb47b;
            transform: scale(1.05);
        }

        .back-btn {
            display: block;
            width: 100%;
            margin-top: 10px;
            text-align: center;
            color: #ff7e5f;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: #feb47b;
        }

        /* Chat button */
        #chat-btn {
            position: fixed;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            background-color: rgb(255, 119, 0);
            color: #fff;
            border: none;
            border-radius: 100px;
            width: 60px;
            height: 60px;
            cursor: pointer;
            z-index: 1000;
        }

        /* Chat popup */
        #chat-popup {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100%;
            background: white;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.2);
            transition: right 0.3s ease;
            z-index: 999;
        }

        #chat-popup.active {
            right: 0;
        }

        .chat-header {
            background: #ff7e5f;
            color: white;
            padding: 15px;
            font-size: 1.5rem;
            text-align: center;
        }

        .chat-messages {
            height: calc(100% - 130px);
            padding: 15px;
            overflow-y: auto;
            background: #f4f4f4;
        }

        .chat-messages div {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        .chat-messages .user {
            background: #ff7e5f;
            color: white;
            align-self: flex-start;
        }

        .chat-messages .provider {
            background-color: #007bff;
            color: #fff;
            align-self: flex-end;
        }

        .chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .chat-input button {
            background: #ff7e5f;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-left: 5px;
            border-radius: 5px;
            cursor: pointer;
        }

        .chat-input button:hover {
            background: #feb47b;
        }
    </style>
</head>
<body>
<header>
    Booking
</header>

    <div class="container">
        <div class="header">
            <h1>Book Provider</h1>
        </div>
        <div class="provider-details">
            <h2><?php echo htmlspecialchars($provider['name']); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($provider['category']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($provider['location']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($provider['contact']); ?></p>
            <p><strong>Experience:</strong> <?php echo htmlspecialchars($provider['experience']); ?> years</p>
        </div>
        <form action="process_booking.php" method="POST">
            <input type="hidden" name="provider_id" value="<?php echo $provider_id; ?>">
            <div class="form-group">
                <label for="date">Booking Date:</label>
                <input type="date" name="date" id="date" required>
            </div>
            <div class="form-group">
                <label for="time">Time Slot:</label>
                <input type="text" name="time" id="time" placeholder="e.g., 10:00 AM - 12:00 PM" required>
            </div>
            <div class="form-group">
                <label for="message">Additional Message:</label>
                <textarea name="message" id="message" rows="4" placeholder="Enter additional details"></textarea>
            </div>
            <div class="form-group">
                <label for="paymentMethod">Payment Method:</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="Online Payment">Online Payment</option>
                    <option value="Cash">Cash</option>
                </select>
            </div>
            <button type="submit" class="submit-btn">Confirm Booking</button>
            <a href="find.php" class="back-btn">Back to Search</a>
        </form>
    </div>

    <!-- Chat button -->
    <button id="chat-btn">💬 Chat</button>

    <!-- Chat popup -->
    <div id="chat-popup">
        <div class="chat-header">Chat</div>
        <div class="chat-messages"></div>
        <div class="chat-input">
            <input type="text" id="chat-message" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
    <script>

const chatBtn = document.getElementById('chat-btn');
const chatPopup = document.getElementById('chat-popup');
const chatMessages = document.querySelector('.chat-messages');
const chatInput = document.getElementById('chat-message');

chatBtn.addEventListener('click', () => {
    chatPopup.classList.toggle('active');
    setInterval(fetchMessages, 2000);
});

const senderId = '<?php echo $_SESSION['user_id'] ?>';
// Fetch chat messages
function fetchMessages() {
    
    fetch(`fetch_messages.php?user_id=${senderId}`)
        .then(response => response.json())
        .then(data => {
            chatMessages.innerHTML = '';
            if (data.length > 0) {
                data.forEach(msg => {
                    const msgDiv = document.createElement('div');
                    msgDiv.className = `message ${msg.sender_id == senderId ? 'provider' : 'user'}`;
                    msgDiv.textContent = msg.message_text;
                    chatMessages.appendChild(msgDiv);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
}
async function sendMessage() {
    const message = chatInput.value.trim();
    if (!message) return;

    // Display the message locally
    const userMessage = document.createElement('div');
    userMessage.textContent = message;
    userMessage.className = 'provider';
    chatMessages.appendChild(userMessage);

    // Send the message to the server
    try {
        const response = await fetch('save_message.php?is_provider=1', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ receiver_id: senderId, message }),
        });
    
    const result = await response.json();
  } catch (error) {
    console.error("Error:", error);
  }
    chatInput.value = '';
}

</script> 
        
</body>
</html>
