<?php
// Include database configuration
require_once 'config.php';

// Get statistics and poems
$stats = [];
$poems = [];
$word_connections = [];

try {
    // Get all poems with word analysis
    $stmt = $pdo->query("
        SELECT id, player_name, poem_text, syllables_count, created_at 
        FROM poems 
        ORDER BY created_at DESC 
        LIMIT 100
    ");
    $poems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get most used words
    $word_stats = [];
    foreach ($poems as $poem) {
        $words = preg_split('/\s+/', strtolower(preg_replace('/[^\w\s]/', '', $poem['poem_text'])));
        foreach ($words as $word) {
            if (strlen($word) > 2) { // Only count words longer than 2 characters
                $word_stats[$word] = ($word_stats[$word] ?? 0) + 1;
            }
        }
    }
    arsort($word_stats);
    $stats['most_used_words'] = array_slice($word_stats, 0, 10, true);
    
    // Get user statistics
    $user_stats = [];
    foreach ($poems as $poem) {
        $player = $poem['player_name'];
        if (!isset($user_stats[$player])) {
            $user_stats[$player] = [
                'poem_count' => 0,
                'total_syllables' => 0,
                'first_poem' => $poem['created_at'],
                'last_poem' => $poem['created_at']
            ];
        }
        $user_stats[$player]['poem_count']++;
        $user_stats[$player]['total_syllables'] += $poem['syllables_count'];
        if ($poem['created_at'] < $user_stats[$player]['first_poem']) {
            $user_stats[$player]['first_poem'] = $poem['created_at'];
        }
        if ($poem['created_at'] > $user_stats[$player]['last_poem']) {
            $user_stats[$player]['last_poem'] = $poem['created_at'];
        }
    }
    
    // Calculate time spent (rough estimate based on syllables)
    foreach ($user_stats as $player => &$data) {
        $data['estimated_time_minutes'] = round($data['total_syllables'] * 0.5); // Rough estimate: 0.5 min per syllable
    }
    
    $stats['user_stats'] = $user_stats;
    $stats['total_poems'] = count($poems);
    $stats['total_players'] = count($user_stats);
    
    // Find word connections between poems
    $word_connections = [];
    for ($i = 0; $i < count($poems); $i++) {
        for ($j = $i + 1; $j < count($poems); $j++) {
            $poem1_words = array_map('strtolower', preg_split('/\s+/', preg_replace('/[^\w\s]/', '', $poems[$i]['poem_text'])));
            $poem2_words = array_map('strtolower', preg_split('/\s+/', preg_replace('/[^\w\s]/', '', $poems[$j]['poem_text'])));
            
            $common_words = array_intersect($poem1_words, $poem2_words);
            $common_words = array_filter($common_words, function($word) { return strlen($word) > 2; });
            
            if (count($common_words) > 0) {
                $word_connections[] = [
                    'poem1_id' => $poems[$i]['id'],
                    'poem2_id' => $poems[$j]['id'],
                    'poem1_text' => $poems[$i]['poem_text'],
                    'poem2_text' => $poems[$j]['poem_text'],
                    'poem1_author' => $poems[$i]['player_name'],
                    'poem2_author' => $poems[$j]['player_name'],
                    'common_words' => array_values($common_words),
                    'connection_strength' => count($common_words)
                ];
            }
        }
    }
    
    // Sort connections by strength
    usort($word_connections, function($a, $b) {
        return $b['connection_strength'] - $a['connection_strength'];
    });
    
} catch (Exception $e) {
    error_log("Error fetching gallery data: " . $e->getMessage());
    $poems = [];
    $stats = [];
    $word_connections = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poem Gallery - Stories in Whispers</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .ascii-title {
            font-size: 12px;
            line-height: 1.2;
            color: #ffff00;
            margin-bottom: 20px;
        }
        h1 {
            color: #ffff00;
            margin-bottom: 10px;
        }
        .nav-links {
            margin-bottom: 30px;
        }
        .nav-links a {
            color: #ff69b4;
            text-decoration: none;
            margin: 0 15px;
            padding: 8px 15px;
            border: 1px solid #ff69b4;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            background: #ff69b4;
            color: #000;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid #444;
            border-radius: 10px;
            padding: 20px;
        }
        .stat-card h3 {
            color: #44ff44;
            margin-bottom: 15px;
            text-align: center;
        }
        .word-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .word-item {
            background: #ff69b4;
            color: #000;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .word-item:nth-child(1) { font-size: 18px; background: #ffff00; }
        .word-item:nth-child(2) { font-size: 16px; background: #ff69b4; }
        .word-item:nth-child(3) { font-size: 14px; background: #44ff44; }
        .user-stats {
            max-height: 200px;
            overflow-y: auto;
        }
        .user-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #333;
        }
        .user-name {
            color: #ff69b4;
            font-weight: bold;
        }
        .user-data {
            color: #888;
            font-size: 12px;
        }
        .connections-section {
            margin-bottom: 40px;
        }
        .connection {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid #444;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .connection-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .connection-strength {
            background: #44ff44;
            color: #000;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .common-words {
            margin-bottom: 15px;
        }
        .common-words span {
            background: #ffff00;
            color: #000;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 11px;
            margin-right: 5px;
        }
        .poems-pair {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .poem-card {
            background: rgba(255, 105, 180, 0.1);
            border: 1px solid #ff69b4;
            border-radius: 8px;
            padding: 15px;
        }
        .poem-text {
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 10px;
            color: #ffffff;
        }
        .poem-meta {
            color: #ff69b4;
            font-size: 12px;
            border-top: 1px solid #ff69b4;
            padding-top: 8px;
        }
        .author {
            font-weight: bold;
        }
        .date {
            color: #888;
        }
        .empty-state {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
                <div class="ascii-title">
    ╔══════════════════════════════════════════════════════════════╗
    ║                                                              ║
    ║    ██████╗  ██████╗ ███████╗███╗   ███╗    ██████╗  █████╗ ██╗     ║
    ║    ██╔══██╗██╔═══██╗██╔════╝████╗ ████║   ██╔════╝ ██╔══██╗██║     ║
    ║    ██████╔╝██║   ██║█████╗  ██╔████╔██║   ██║  ███╗███████║██║     ║
    ║    ██╔═══╝ ██║   ██║██╔══╝  ██║╚██╔╝██║   ██║   ██║██╔══██║██║     ║
    ║    ██║     ╚██████╔╝███████╗██║ ╚═╝ ██║   ╚██████╔╝██║  ██║███████╗║
    ║    ╚═╝      ╚═════╝ ╚══════╝╚═╝     ╚═╝    ╚═════╝ ╚═╝  ╚═╝╚══════╝║
    ║                                                              ║
    ║         ██████╗  █████╗ ██╗     ██╗     ███████╗████████╗██████╗ ██╗║
    ║        ██╔════╝ ██╔══██╗██║     ██║     ██╔════╝╚══██╔══╝██╔══██╗██║║
    ║        ██║  ███╗███████║██║     ██║     █████╗     ██║   ██████╔╝██║║
    ║        ██║   ██║██╔══██║██║     ██║     ██╔══╝     ██║   ██╔══██╗██║║
    ║        ╚██████╔╝██║  ██║███████╗███████╗███████╗   ██║   ██║  ██║██║║
    ║         ╚═════╝ ╚═╝  ╚═╝╚══════╝╚══════╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚═╝║
    ║                                                              ║
    ╚══════════════════════════════════════════════════════════════╝
                </div>
                <h1>Poem Gallery</h1>
                <p>Discover connections between poems through shared words</p>
            </div>
            
        <div class="nav-links">
            <a href="index.html">← Back to Game</a>
            <a href="view_poems.php">View All Poems</a>
        </div>
        
        <?php if (empty($poems)): ?>
            <div class="empty-state">
                <p>No poems have been created yet. <a href="index.html">Start playing</a> to create the first poem!</p>
            </div>
        <?php else: ?>
                <!-- Statistics Section -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Overall Stats</h3>
                        <p><strong>Total Poems:</strong> <?php echo $stats['total_poems']; ?></p>
                        <p><strong>Total Players:</strong> <?php echo $stats['total_players']; ?></p>
                        <p><strong>Word Connections:</strong> <?php echo count($word_connections); ?></p>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Most Used Words</h3>
                        <div class="word-cloud">
                            <?php foreach ($stats['most_used_words'] as $word => $count): ?>
                                <span class="word-item"><?php echo htmlspecialchars($word); ?> (<?php echo $count; ?>)</span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Player Activity</h3>
                        <div class="user-stats">
                            <?php foreach ($stats['user_stats'] as $player => $data): ?>
                                <div class="user-item">
                                    <span class="user-name"><?php echo htmlspecialchars($player); ?></span>
                                    <span class="user-data">
                                        <?php echo $data['poem_count']; ?> poems • 
                                        <?php echo $data['estimated_time_minutes']; ?> min
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Word Connections Section -->
                <div class="connections-section">
                    <h2 style="color: #44ff44; text-align: center; margin-bottom: 30px;">
                        Poems Connected by Words
                    </h2>
                    
                    <?php if (empty($word_connections)): ?>
                        <div class="empty-state">
                            <p>No word connections found between poems yet. Keep writing to discover connections!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($word_connections, 0, 10) as $connection): ?>
                            <div class="connection">
                                <div class="connection-header">
                                    <h3 style="color: #ffff00; margin: 0;">
                                        Connected Poems
                                    </h3>
                                    <span class="connection-strength">
                                        <?php echo $connection['connection_strength']; ?> shared words
                                    </span>
                                </div>
                                
                                <div class="common-words">
                                    <strong style="color: #44ff44;">Common words:</strong>
                                    <?php foreach ($connection['common_words'] as $word): ?>
                                        <span><?php echo htmlspecialchars($word); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="poems-pair">
                                    <div class="poem-card">
                                        <div class="poem-text">
                                            <?php 
                                                $poem_text = htmlspecialchars($connection['poem1_text']);
                                                $poem_text = str_replace('&lt;br&gt;', "\n", $poem_text);
                                                $poem_text = str_replace('&lt;br/&gt;', "\n", $poem_text);
                                                $poem_text = str_replace('&lt;br /&gt;', "\n", $poem_text);
                                                echo nl2br($poem_text);
                                            ?>
                                        </div>
                                        <div class="poem-meta">
                                            <span class="author">by <?php echo htmlspecialchars(strtolower($connection['poem1_author'])); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="poem-card">
                                        <div class="poem-text">
                                            <?php 
                                                $poem_text = htmlspecialchars($connection['poem2_text']);
                                                $poem_text = str_replace('&lt;br&gt;', "\n", $poem_text);
                                                $poem_text = str_replace('&lt;br/&gt;', "\n", $poem_text);
                                                $poem_text = str_replace('&lt;br /&gt;', "\n", $poem_text);
                                                echo nl2br($poem_text);
                                            ?>
                                        </div>
                                        <div class="poem-meta">
                                            <span class="author">by <?php echo htmlspecialchars(strtolower($connection['poem2_author'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
        <?php endif; ?>
    </div>
</body>
</html>
