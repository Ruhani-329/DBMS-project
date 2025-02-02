<?php
session_start();
include 'db_config.php'; // Ensure this connects to your database

// Retrieve booking ID
$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
    die("No booking ID specified.");
}

// Fetch booking and user details
$sql = "SELECT b.*, u.name AS user_name, u.address AS user_address, u.contact AS user_contact, u.email AS user_email
        FROM booking b
        JOIN users u ON b.user_id = u.user_id
        WHERE b.booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    die("Booking not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(180deg, #ffffff, #ffefd5);
        }

        header {
            background: linear-gradient(90deg, #ff7e5f, #feb47b);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
        }
        footer {
            text-align: center;
            margin-top: 50px;
            background: #333;
            color: white;
            padding: 20px;
            font-size: 14px;
        }
        
        .container {
            max-width: 900px;
            margin: 10% auto;
            background: linear-gradient(180deg, #ffffff, #ffefd5);
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .actions {
            display: flex;
            justify-content: space-between;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-accept {
            background-color: #28a745;
            color: #fff;
        }
        .btn-reject {
            background-color: #dc3545;
            color: #fff;
        }
        #chat-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        #chat-popup {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100%;
            background-color: #fff;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.2);
            transition: right 0.3s ease;
            z-index: 9999;
        }
        #chat-popup.active {
            right: 0;
        }
        .chat-header {
            background-color: #007bff;
            color: #fff;
            padding: 15px;
            font-size: 1.5rem;
            text-align: center;
        }
        .chat-messages {
            padding: 15px;
            height: calc(100% - 130px);
            overflow-y: auto;
            background-color: #f4f4f4;
        }
        .chat-messages .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .chat-messages .message.user {
            background-color: #007bff;
            color: #fff;
            align-self: flex-end;
        }
        .chat-messages .message.provider {
            background: #ff7e5f;
            color: white;
            align-self: flex-start;
        }
        .chat-input {
            padding: 15px;
            border-top: 1px solid #ddd;
            display: flex;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .chat-input button {
            margin-left: 10px;
            padding: 10px 15px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        .notification {
            position: relative;
            display: inline-block;
        }

        .notification .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 12px;
        }
        .notification-icon {
            position: absolute;
            top: 15px;
            right: 30px;
            cursor: pointer;
            font-size: 2rem;
            color: #ff7e5f;
            transition: color 0.3s ease;
        }

        .notification-icon:hover {
            color: #e0a1c6;
        }
    </style>
</head>
<body>

    <header>
        QuickHire Provider Profile
    </header>

    <div class="container">
        <h1>Booking Details</h1>
        <p><strong>User Name:</strong> <?php echo htmlspecialchars($booking['user_name']); ?></p>
        <p><strong>User Address:</strong> <?php echo htmlspecialchars($booking['user_address']); ?></p>
        <p><strong>User Contact:</strong> <?php echo htmlspecialchars($booking['user_contact']); ?></p>
        <p><strong>User Email:</strong> <?php echo htmlspecialchars($booking['user_email']); ?></p>
        <p><strong>Booking Date:</strong> <?php echo htmlspecialchars($booking['date']); ?></p>
        <p><strong>Booking Time:</strong> <?php echo htmlspecialchars($booking['time']); ?></p>
        <p><strong>Message:</strong> <?php echo htmlspecialchars($booking['message']); ?></p>
        <div class="actions">
            <form method="POST" action="update_booking_status.php">
                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                <input type="hidden" name="status" value="Successful">
                <button class="btn btn-accept" type="submit">Accept</button>
            </form>
            <form method="POST" action="update_booking_status.php">
                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                <input type="hidden" name="status" value="Rejected">
                <button class="btn btn-reject" type="submit">Reject</button>
            </form>
        </div>
    </div>

    <!-- Chat Button -->
    <button id="chat-btn">💬</button>

    <!-- Chat Popup -->
    <div id="chat-popup">
        <div class="chat-header">Chat</div>
        <div class="chat-messages"></div>
        <div class="chat-input">
            <input type="text" id="chat-message" placeholder="Type a message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
    <script>
        const chatBtn = document.getElementById('chat-btn');
const chatPopup = document.getElementById('chat-popup');
const chatMessages = document.querySelector('.chat-messages');
const chatInput = document.getElementById('chat-message');

const bookingId = <?php echo json_encode($booking_id); ?>;
const receiverId = <?php echo json_encode($booking['user_id']); ?>; // User ID from booking

// Toggle chat popup visibility
chatBtn.addEventListener('click', () => {
    chatPopup.classList.toggle('active');
    setInterval(fetchMessages, 2000);
});

const senderId = '<?php echo $_SESSION['provider_id'] ?>';

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

// Send a chat message
function sendMessage() {
    const message = chatInput.value.trim();
    if (!message) return;

    fetch('send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `receiver_id=${receiverId}&sender_id=${senderId}&message_text=${encodeURIComponent(message)}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                chatInput.value = '';
                fetchMessages();
            }
        });
}

    </script>
</body>
</html>
