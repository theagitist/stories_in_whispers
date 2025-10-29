<?php
// Include database configuration
require_once 'config.php';

// Start session for authentication
session_start();

// Check if user is authenticated
$is_authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

// Handle login form submission
if ($_POST && isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['authenticated'] = true;
        $is_authenticated = true;
    } else {
        $login_error = "Invalid password. Please try again.";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: view_poems.php');
    exit;
}

// Handle poem deletion
if ($is_authenticated && isset($_POST['delete_poem']) && isset($_POST['poem_id'])) {
    try {
        $poem_id = (int)$_POST['poem_id'];
        $stmt = $pdo->prepare("DELETE FROM poems WHERE id = ?");
        $stmt->execute([$poem_id]);
        $delete_success = "Poem deleted successfully.";
    } catch (Exception $e) {
        error_log("Error deleting poem: " . $e->getMessage());
        $delete_error = "Error deleting poem. Please try again.";
    }
}

// Only fetch poems if authenticated
$poems = [];
if ($is_authenticated) {
    try {
        // Get poems from database
        $stmt = $pdo->query("
            SELECT id, player_name, poem_text, syllables_count, created_at 
            FROM poems 
            ORDER BY created_at DESC 
            LIMIT 50
        ");
        $poems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("Error fetching poems: " . $e->getMessage());
        $poems = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Poems - Stories in Whispers</title>
    <style>
        body {
            background: linear-gradient(135deg, #0a0a1a, #1a1a2e, #16213e);
            color: #ffffff;
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #ffff00;
            margin-bottom: 30px;
        }
        .poem {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid #444;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .poem-text {
            font-style: italic;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #ffffff;
        }
        .poem-meta {
            color: #ff69b4;
            font-size: 14px;
            border-top: 1px solid #444;
            padding-top: 10px;
        }
        .author {
            font-weight: bold;
        }
        .date {
            color: #888;
        }
        .syllables {
            color: #44ff44;
        }
        .back-link {
            display: inline-block;
            background: #ff69b4;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-link:hover {
            background: #ff1493;
        }
        .login-form {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid #444;
            border-radius: 10px;
            padding: 30px;
            max-width: 400px;
            margin: 50px auto;
            text-align: center;
        }
        .login-form h2 {
            color: #ffff00;
            margin-bottom: 20px;
        }
        .login-form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 5px;
            background: #222;
            color: #fff;
            font-size: 16px;
        }
        .login-form button {
            background: #ff69b4;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-form button:hover {
            background: #ff1493;
        }
        .error-message {
            color: #ff4444;
            margin-bottom: 15px;
        }
        .logout-link {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #666;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .logout-link:hover {
            background: #888;
        }
        .delete-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
        }
        .delete-btn:hover {
            background: #cc0000;
        }
        .poem-actions {
            margin-top: 10px;
            text-align: right;
        }
        .success-message {
            background: #44ff44;
            color: #000;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .error-message {
            background: #ff4444;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($is_authenticated): ?>
            <!-- Show logout link when authenticated -->
            <a href="?logout=1" class="logout-link">Logout</a>
            <a href="index.html" class="back-link">← Back to Game</a>
            <a href="poem_gallery.php" class="back-link" style="margin-left: 10px;">Poem Gallery</a>
            <h1>Saved Poems</h1>
            
            <?php if (isset($delete_success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($delete_success); ?></div>
            <?php endif; ?>
            
            <?php if (isset($delete_error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($delete_error); ?></div>
            <?php endif; ?>
            
            <?php if (empty($poems)): ?>
                <div class="poem">
                    <p>No poems have been saved yet. <a href="index.html">Start playing</a> to create your first poem!</p>
                </div>
            <?php else: ?>
                <?php foreach ($poems as $poem): ?>
                    <div class="poem">
                        <div class="poem-text"><?php 
                            $poem_text = htmlspecialchars($poem['poem_text']);
                            // Convert <br> tags back to line breaks, then convert newlines to <br>
                            $poem_text = str_replace('&lt;br&gt;', "\n", $poem_text);
                            $poem_text = str_replace('&lt;br/&gt;', "\n", $poem_text);
                            $poem_text = str_replace('&lt;br /&gt;', "\n", $poem_text);
                            echo nl2br($poem_text);
                        ?></div>
                        <div class="poem-meta">
                            <span class="author">by a <?php echo htmlspecialchars(strtolower($poem['player_name'])); ?></span>
                            <span class="syllables"> • <?php echo $poem['syllables_count']; ?> syllables</span>
                            <span class="date"> • <?php echo date('M j, Y g:i A', strtotime($poem['created_at'])); ?></span>
                        </div>
                        <div class="poem-actions">
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this poem? This action cannot be undone.');">
                                <input type="hidden" name="poem_id" value="<?php echo $poem['id']; ?>">
                                <button type="submit" name="delete_poem" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php else: ?>
            <!-- Show login form when not authenticated -->
            <div class="login-form">
                <h2>Admin Access Required</h2>
                <p>Please enter the password to view saved poems.</p>
                
                <?php if (isset($login_error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($login_error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="password" name="password" placeholder="Enter password" required>
                    <br>
                    <button type="submit">Login</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
