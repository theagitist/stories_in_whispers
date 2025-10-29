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
    
    // Get most used words (excluding articles and conjunctions)
    $word_stats = [];
    $excluded_words = ['the', 'a', 'an', 'and', 'but', 'or', 'so', 'yet', 'for', 'nor', 'while', 'when', 'where', 'because', 'if', 'unless', 'since', 'until', 'though', 'although', 'as', 'than', 'that'];
    
    foreach ($poems as $poem) {
        $words = preg_split('/\s+/', strtolower(preg_replace('/[^\w\s]/', '', $poem['poem_text'])));
        foreach ($words as $word) {
            if (strlen($word) > 2 && !in_array($word, $excluded_words)) { // Only count words longer than 2 characters and not in excluded list
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
    
    // Find word connections between poems (grouping poems by shared words)
    $word_connections = [];
    $word_to_poems = []; // Maps words to arrays of poem indices
    
    // First pass: build word-to-poems mapping
    for ($i = 0; $i < count($poems); $i++) {
        $poem_words = array_map('strtolower', preg_split('/\s+/', preg_replace('/[^\w\s]/', '', $poems[$i]['poem_text'])));
        
        // Filter out articles and conjunctions
        $poem_words = array_filter($poem_words, function($word) use ($excluded_words) { 
            return strlen($word) > 2 && !in_array($word, $excluded_words); 
        });
        
        // Add poem to each word's list (only once per word)
        foreach (array_unique($poem_words) as $word) {
            if (!isset($word_to_poems[$word])) {
                $word_to_poems[$word] = [];
            }
            $word_to_poems[$word][] = $i;
        }
    }
    
    // Second pass: find groups of poems that share words
    $processed_groups = [];
    foreach ($word_to_poems as $word => $poem_indices) {
        // Remove duplicates and only consider words that appear in multiple poems
        $poem_indices = array_unique($poem_indices);
        if (count($poem_indices) > 1) {
            // Create a group key to avoid duplicates
            sort($poem_indices);
            $group_key = implode(',', $poem_indices);
            
            if (!isset($processed_groups[$group_key])) {
                $processed_groups[$group_key] = [
                    'poems' => [],
                    'common_words' => [],
                    'total_common_words' => 0
                ];
            }
            
            $processed_groups[$group_key]['common_words'][] = $word;
            $processed_groups[$group_key]['total_common_words']++;
        }
    }
    
    // Convert processed groups to display format
    foreach ($processed_groups as $group_key => $group_data) {
        $poem_indices = explode(',', $group_key);
        $poems_in_group = [];
        
        foreach ($poem_indices as $poem_index) {
            $poems_in_group[] = [
                'id' => $poems[$poem_index]['id'],
                'text' => $poems[$poem_index]['poem_text'],
                'author' => $poems[$poem_index]['player_name'],
                'created_at' => $poems[$poem_index]['created_at']
            ];
        }
        
        $word_connections[] = [
            'poems' => $poems_in_group,
            'common_words' => $group_data['common_words'],
            'connection_strength' => $group_data['total_common_words'],
            'poem_count' => count($poems_in_group)
        ];
    }
    
    // Sort connections by strength (number of common words) and then by number of poems
    usort($word_connections, function($a, $b) {
        if ($a['connection_strength'] == $b['connection_strength']) {
            return $b['poem_count'] - $a['poem_count'];
        }
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
            background: rgba(255, 105, 180, 0.3);
            color: #ff69b4;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: normal;
            border: 1px solid rgba(255, 105, 180, 0.5);
        }
        .word-item:nth-child(1) { font-size: 18px; background: rgba(255, 255, 0, 0.3); color: #ffff00; border-color: rgba(255, 255, 0, 0.5); }
        .word-item:nth-child(2) { font-size: 16px; background: rgba(255, 105, 180, 0.3); color: #ff69b4; border-color: rgba(255, 105, 180, 0.5); }
        .word-item:nth-child(3) { font-size: 14px; background: rgba(68, 255, 68, 0.3); color: #44ff44; border-color: rgba(68, 255, 68, 0.5); }
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
        .connections-carousel {
            position: relative;
            height: 500px;
            overflow: hidden;
        }
        .connection {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid #444;
            border-radius: 10px;
            padding: 20px;
            margin: 20px;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            opacity: 0;
            transition: opacity 1.2s ease-in-out;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .connection.active {
            opacity: 1;
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
        .poems-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
        .random-poem {
            background: rgba(255, 105, 180, 0.1);
            border: 1px solid #ff69b4;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }
        .random-poem .poem-text {
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 10px;
            color: #ffffff;
            font-size: 14px;
        }
        .random-poem .poem-meta {
            color: #ff69b4;
            font-size: 12px;
            border-top: 1px solid #ff69b4;
            padding-top: 8px;
        }
        .random-poem .author {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
                <h1>Poem Gallery</h1>
                <p>Discover connections between poems through shared words</p>
            </div>
            
        <div class="nav-links">
            <a href="index.html" id="backToGameLink">← Back to Game (Press B)</a>
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
                        <h3>Random Poem</h3>
                        <?php 
                        $random_poem = $poems[array_rand($poems)];
                        $poem_text = htmlspecialchars($random_poem['poem_text']);
                        $poem_text = str_replace('&lt;br&gt;', "\n", $poem_text);
                        $poem_text = str_replace('&lt;br/&gt;', "\n", $poem_text);
                        $poem_text = str_replace('&lt;br /&gt;', "\n", $poem_text);
                        ?>
                        <div class="random-poem">
                            <div class="poem-text">
                                <?php echo nl2br($poem_text); ?>
                            </div>
                            <div class="poem-meta">
                                <span class="author">by a <?php echo htmlspecialchars(strtolower($random_poem['player_name'])); ?></span>
                            </div>
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
                        
                        <div class="connections-carousel">
                            <?php foreach (array_slice($word_connections, 0, 10) as $index => $connection): ?>
                                <div class="connection <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                    <div class="connection-header">
                                        <h3 style="color: #ffff00; margin: 0;">
                                            Connected Poems (<?php echo $connection['poem_count']; ?> poems)
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
                                    
                                    <div class="poems-grid">
                                        <?php foreach ($connection['poems'] as $poem): ?>
                                            <div class="poem-card">
                                                <div class="poem-text">
                                                    <?php 
                                                        $poem_text = htmlspecialchars($poem['text']);
                                                        $poem_text = str_replace('&lt;br&gt;', "\n", $poem_text);
                                                        $poem_text = str_replace('&lt;br/&gt;', "\n", $poem_text);
                                                        $poem_text = str_replace('&lt;br /&gt;', "\n", $poem_text);
                                                        
                                                        // Highlight common words
                                                        foreach ($connection['common_words'] as $common_word) {
                                                            $poem_text = preg_replace('/\b' . preg_quote($common_word, '/') . '\b/i', '<span style="background: #ffff00; color: #000; padding: 2px 4px; border-radius: 3px; font-weight: bold;">' . $common_word . '</span>', $poem_text);
                                                        }
                                                        
                                                        echo nl2br($poem_text);
                                                    ?>
                                                </div>
                                                <div class="poem-meta">
                                                    <span class="author">by a <?php echo htmlspecialchars(strtolower($poem['author'])); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                    <?php endif; ?>
                </div>
        <?php endif; ?>
    </div>

    <script>
        // Handle B key to go back to game
        document.addEventListener('keydown', (e) => {
            if (e.key === 'b' || e.key === 'B') {
                e.preventDefault();
                window.location.href = 'index.html';
            }
        });

        // Carousel functionality
        let currentConnectionIndex = 0;
        const connections = document.querySelectorAll('.connection');
        const totalConnections = connections.length;

        function updateCarousel() {
            // Update connection visibility with crossfade
            connections.forEach((connection, index) => {
                connection.classList.remove('active');
                if (index === currentConnectionIndex) {
                    connection.classList.add('active');
                }
            });
        }

        function nextConnection() {
            currentConnectionIndex = (currentConnectionIndex + 1) % totalConnections;
            updateCarousel();
        }

        // Auto-rotate every 4 seconds
        let autoRotateInterval;
        function startAutoRotate() {
            autoRotateInterval = setInterval(() => {
                nextConnection();
            }, 4000);
        }

        function stopAutoRotate() {
            if (autoRotateInterval) {
                clearInterval(autoRotateInterval);
            }
        }

        // Initialize carousel
        document.addEventListener('DOMContentLoaded', () => {
            updateCarousel();
            startAutoRotate();
        });

        // Pause auto-rotate on hover
        const carousel = document.querySelector('.connections-carousel');
        if (carousel) {
            carousel.addEventListener('mouseenter', stopAutoRotate);
            carousel.addEventListener('mouseleave', startAutoRotate);
        }
    </script>
</body>
</html>
