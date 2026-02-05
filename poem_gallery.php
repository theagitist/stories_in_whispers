<?php
// Include database configuration
require_once 'config.php';

// Handle player name from URL parameter and store in session
if (isset($_GET['player'])) {
    $player_name = htmlspecialchars($_GET['player']);
    session_start();
    
    // Validate that the player name is in the correct format (verb adjective noun)
    // If it's not in the correct format, generate a new one
    $name_parts = explode(' ', $player_name);
    if (count($name_parts) !== 3) {
        // Generate a proper player name if the format is incorrect
        $verbs = ['Dancing', 'Singing', 'Whispering', 'Dreaming', 'Floating', 'Glowing', 'Shining', 'Drifting', 'Soaring', 'Sparkling'];
        $adjectives = ['Mystical', 'Ethereal', 'Luminous', 'Gentle', 'Radiant', 'Serene', 'Magical', 'Peaceful', 'Graceful', 'Enchanting'];
        $nouns = ['Poet', 'Dreamer', 'Wanderer', 'Storyteller', 'Soul', 'Spirit', 'Heart', 'Mind', 'Voice', 'Whisper'];
        
        $random_verb = $verbs[array_rand($verbs)];
        $random_adjective = $adjectives[array_rand($adjectives)];
        $random_noun = $nouns[array_rand($nouns)];
        
        $player_name = "$random_verb $random_adjective $random_noun";
    }
    
    $_SESSION['playerName'] = $player_name;
} else {
    session_start();
    $player_name = $_SESSION['playerName'] ?? 'Dancing Mystical Poet';
}

// Function to determine word type based on the game's word database
function getWordType($word) {
    $word = strtolower($word);
    
    // Word database from the game (simplified version)
    $wordDatabase = [
        'verb' => ['run', 'walk', 'fly', 'sing', 'dance', 'jump', 'fall', 'rise', 'flow', 'glow', 'shine', 'bloom', 'grow', 'dream', 'whisper', 'speak', 'laugh', 'cry', 'smile', 'touch', 'feel', 'think', 'know', 'see', 'hear', 'taste', 'smell', 'breathe', 'live', 'love', 'hope', 'wish', 'pray', 'believe', 'trust', 'remember', 'forget', 'learn', 'teach', 'help', 'give', 'take', 'hold', 'let', 'go', 'come', 'stay', 'leave', 'return', 'begin', 'end', 'start', 'stop', 'continue', 'pause', 'wait', 'hurry', 'slow', 'fast', 'quick', 'gentle', 'soft', 'loud', 'quiet', 'bright', 'dark', 'light', 'heavy', 'light', 'strong', 'weak', 'brave', 'afraid', 'happy', 'sad', 'angry', 'calm', 'wild', 'free', 'bound', 'open', 'close', 'break', 'fix', 'build', 'destroy', 'create', 'make', 'do', 'be', 'have', 'get', 'find', 'lose', 'win', 'fail', 'succeed', 'try', 'attempt', 'manage', 'fail', 'succeed', 'achieve', 'accomplish', 'complete', 'finish', 'end', 'start', 'begin', 'initiate', 'launch', 'release', 'free', 'liberate', 'escape', 'flee', 'chase', 'follow', 'lead', 'guide', 'direct', 'point', 'show', 'hide', 'reveal', 'discover', 'explore', 'search', 'seek', 'look', 'watch', 'observe', 'notice', 'see', 'spot', 'find', 'locate', 'place', 'put', 'set', 'lay', 'stand', 'sit', 'lie', 'rest', 'sleep', 'wake', 'awake', 'arise', 'get', 'up', 'down', 'over', 'under', 'through', 'across', 'around', 'between', 'among', 'within', 'without', 'inside', 'outside', 'near', 'far', 'close', 'distant', 'here', 'there', 'everywhere', 'nowhere', 'somewhere', 'anywhere', 'always', 'never', 'sometimes', 'often', 'rarely', 'seldom', 'frequently', 'occasionally', 'regularly', 'constantly', 'continuously', 'forever', 'temporarily', 'permanently', 'briefly', 'quickly', 'slowly', 'gradually', 'suddenly', 'immediately', 'instantly', 'eventually', 'finally', 'ultimately', 'initially', 'originally', 'first', 'last', 'next', 'previous', 'before', 'after', 'during', 'while', 'when', 'where', 'why', 'how', 'what', 'who', 'which', 'whose', 'whom', 'that', 'this', 'these', 'those', 'all', 'some', 'any', 'none', 'many', 'much', 'few', 'little', 'more', 'most', 'less', 'least', 'enough', 'too', 'very', 'quite', 'rather', 'pretty', 'fairly', 'somewhat', 'rather', 'quite', 'very', 'extremely', 'incredibly', 'amazingly', 'wonderfully', 'beautifully', 'perfectly', 'completely', 'totally', 'entirely', 'fully', 'partially', 'partly', 'mostly', 'mainly', 'primarily', 'chiefly', 'principally', 'largely', 'greatly', 'considerably', 'significantly', 'substantially', 'dramatically', 'radically', 'fundamentally', 'basically', 'essentially', 'primarily', 'mainly', 'mostly', 'largely', 'chiefly', 'principally', 'primarily', 'mainly', 'mostly', 'largely', 'chiefly', 'principally'],
        'noun' => ['moon', 'sun', 'star', 'sky', 'cloud', 'rain', 'wind', 'storm', 'lightning', 'thunder', 'snow', 'ice', 'fire', 'flame', 'smoke', 'mist', 'fog', 'dew', 'drop', 'tear', 'smile', 'laugh', 'cry', 'song', 'music', 'dance', 'dream', 'hope', 'love', 'heart', 'soul', 'spirit', 'mind', 'thought', 'idea', 'memory', 'moment', 'time', 'day', 'night', 'morning', 'evening', 'dawn', 'dusk', 'twilight', 'sunrise', 'sunset', 'noon', 'midnight', 'hour', 'minute', 'second', 'year', 'month', 'week', 'season', 'spring', 'summer', 'autumn', 'winter', 'flower', 'tree', 'leaf', 'branch', 'root', 'seed', 'fruit', 'berry', 'garden', 'forest', 'meadow', 'field', 'mountain', 'hill', 'valley', 'river', 'stream', 'lake', 'ocean', 'sea', 'wave', 'shore', 'beach', 'sand', 'stone', 'rock', 'crystal', 'gem', 'pearl', 'gold', 'silver', 'diamond', 'ruby', 'emerald', 'sapphire', 'amethyst', 'topaz', 'jade', 'coral', 'ivory', 'marble', 'granite', 'wood', 'bark', 'timber', 'lumber', 'paper', 'ink', 'paint', 'color', 'hue', 'shade', 'tint', 'tone', 'brightness', 'darkness', 'light', 'shadow', 'silhouette', 'outline', 'shape', 'form', 'figure', 'image', 'picture', 'portrait', 'landscape', 'scene', 'view', 'sight', 'vision', 'dream', 'nightmare', 'fantasy', 'reality', 'truth', 'lie', 'story', 'tale', 'legend', 'myth', 'fable', 'parable', 'poem', 'verse', 'line', 'word', 'letter', 'sound', 'voice', 'whisper', 'shout', 'scream', 'cry', 'laugh', 'giggle', 'chuckle', 'smile', 'grin', 'frown', 'scowl', 'grimace', 'expression', 'face', 'eye', 'nose', 'mouth', 'lip', 'tooth', 'tongue', 'cheek', 'chin', 'forehead', 'brow', 'eyebrow', 'eyelash', 'hair', 'beard', 'mustache', 'hand', 'finger', 'thumb', 'nail', 'palm', 'wrist', 'arm', 'elbow', 'shoulder', 'chest', 'breast', 'heart', 'lung', 'stomach', 'belly', 'waist', 'hip', 'leg', 'thigh', 'knee', 'shin', 'calf', 'ankle', 'foot', 'toe', 'heel', 'sole', 'arch', 'instep', 'back', 'spine', 'neck', 'throat', 'head', 'skull', 'brain', 'mind', 'thought', 'idea', 'concept', 'notion', 'belief', 'opinion', 'viewpoint', 'perspective', 'angle', 'aspect', 'side', 'part', 'piece', 'bit', 'fragment', 'shard', 'splinter', 'chip', 'crumb', 'morsel', 'bite', 'taste', 'flavor', 'savor', 'aroma', 'scent', 'perfume', 'fragrance', 'odor', 'smell', 'stench', 'stink', 'reek', 'foul', 'sweet', 'sour', 'bitter', 'salty', 'spicy', 'hot', 'cold', 'warm', 'cool', 'fresh', 'stale', 'rotten', 'decayed', 'spoiled', 'moldy', 'musty', 'damp', 'wet', 'dry', 'moist', 'humid', 'arid', 'parched', 'thirsty', 'hungry', 'starving', 'famished', 'full', 'satisfied', 'content', 'happy', 'joyful', 'cheerful', 'merry', 'gay', 'glad', 'pleased', 'delighted', 'thrilled', 'excited', 'enthusiastic', 'eager', 'keen', 'anxious', 'worried', 'concerned', 'troubled', 'distressed', 'upset', 'sad', 'sorrowful', 'melancholy', 'gloomy', 'depressed', 'dejected', 'downcast', 'disappointed', 'discouraged', 'disheartened', 'crushed', 'broken', 'shattered', 'devastated', 'destroyed', 'ruined', 'wrecked', 'damaged', 'hurt', 'injured', 'wounded', 'scarred', 'marked', 'stained', 'tainted', 'corrupted', 'polluted', 'contaminated', 'infected', 'diseased', 'sick', 'ill', 'unwell', 'ailing', 'weak', 'feeble', 'frail', 'fragile', 'delicate', 'tender', 'soft', 'gentle', 'mild', 'calm', 'peaceful', 'serene', 'tranquil', 'quiet', 'silent', 'still', 'motionless', 'static', 'stationary', 'fixed', 'firm', 'solid', 'stable', 'steady', 'secure', 'safe', 'protected', 'guarded', 'defended', 'shielded', 'sheltered', 'covered', 'hidden', 'concealed', 'secret', 'private', 'personal', 'individual', 'unique', 'special', 'particular', 'specific', 'certain', 'definite', 'clear', 'obvious', 'evident', 'apparent', 'visible', 'noticeable', 'remarkable', 'extraordinary', 'exceptional', 'outstanding', 'excellent', 'superb', 'magnificent', 'wonderful', 'marvelous', 'amazing', 'astonishing', 'surprising', 'shocking', 'stunning', 'breathtaking', 'spectacular', 'dramatic', 'theatrical', 'artistic', 'creative', 'imaginative', 'inventive', 'original', 'novel', 'new', 'fresh', 'modern', 'contemporary', 'current', 'present', 'today', 'now', 'here', 'there', 'everywhere', 'somewhere', 'anywhere', 'nowhere', 'home', 'house', 'building', 'structure', 'construction', 'creation', 'work', 'labor', 'effort', 'energy', 'power', 'force', 'strength', 'might', 'vigor', 'vitality', 'life', 'existence', 'being', 'reality', 'truth', 'fact', 'reality', 'actuality', 'existence', 'presence', 'absence', 'void', 'emptiness', 'nothingness', 'zero', 'null', 'void', 'blank', 'empty', 'vacant', 'unoccupied', 'free', 'available', 'open', 'accessible', 'reachable', 'attainable', 'achievable', 'possible', 'feasible', 'practical', 'realistic', 'reasonable', 'sensible', 'logical', 'rational', 'intelligent', 'smart', 'clever', 'wise', 'knowledgeable', 'learned', 'educated', 'informed', 'aware', 'conscious', 'awake', 'alert', 'attentive', 'focused', 'concentrated', 'centered', 'balanced', 'stable', 'steady', 'firm', 'strong', 'powerful', 'mighty', 'great', 'grand', 'magnificent', 'splendid', 'glorious', 'brilliant', 'bright', 'shining', 'radiant', 'luminous', 'glowing', 'sparkling', 'twinkling', 'glistening', 'glimmering', 'flickering', 'flashing', 'blinking', 'winking', 'dancing', 'moving', 'flowing', 'streaming', 'pouring', 'rushing', 'racing', 'speeding', 'flying', 'soaring', 'floating', 'drifting', 'sailing', 'gliding', 'sliding', 'slipping', 'falling', 'dropping', 'descending', 'sinking', 'diving', 'plunging', 'jumping', 'leaping', 'bounding', 'springing', 'bouncing', 'hopping', 'skipping', 'running', 'walking', 'strolling', 'marching', 'tramping', 'trudging', 'plodding', 'crawling', 'creeping', 'slithering', 'wriggling', 'squirming', 'twisting', 'turning', 'rotating', 'spinning', 'whirling', 'swirling', 'circling', 'orbiting', 'revolving', 'rolling', 'tumbling', 'flipping', 'somersaulting', 'cartwheeling', 'handstanding', 'balancing', 'standing', 'sitting', 'lying', 'resting', 'sleeping', 'dreaming', 'waking', 'awakening', 'arising', 'getting', 'up', 'down', 'over', 'under', 'through', 'across', 'around', 'between', 'among', 'within', 'without', 'inside', 'outside', 'near', 'far', 'close', 'distant', 'here', 'there', 'everywhere', 'somewhere', 'anywhere', 'nowhere', 'always', 'never', 'sometimes', 'often', 'rarely', 'seldom', 'frequently', 'occasionally', 'regularly', 'constantly', 'continuously', 'forever', 'temporarily', 'permanently', 'briefly', 'quickly', 'slowly', 'gradually', 'suddenly', 'immediately', 'instantly', 'eventually', 'finally', 'ultimately', 'initially', 'originally', 'first', 'last', 'next', 'previous', 'before', 'after', 'during', 'while', 'when', 'where', 'why', 'how', 'what', 'who', 'which', 'whose', 'whom', 'that', 'this', 'these', 'those', 'all', 'some', 'any', 'none', 'many', 'much', 'few', 'little', 'more', 'most', 'less', 'least', 'enough', 'too', 'very', 'quite', 'rather', 'pretty', 'fairly', 'somewhat', 'rather', 'quite', 'very', 'extremely', 'incredibly', 'amazingly', 'wonderfully', 'beautifully', 'perfectly', 'completely', 'totally', 'entirely', 'fully', 'partially', 'partly', 'mostly', 'mainly', 'primarily', 'chiefly', 'principally', 'largely', 'greatly', 'considerably', 'significantly', 'substantially', 'dramatically', 'radically', 'fundamentally', 'basically', 'essentially', 'primarily', 'mainly', 'mostly', 'largely', 'chiefly', 'principally'],
        'adjective' => ['beautiful', 'lovely', 'gorgeous', 'stunning', 'magnificent', 'wonderful', 'amazing', 'incredible', 'fantastic', 'marvelous', 'splendid', 'brilliant', 'bright', 'shining', 'radiant', 'glowing', 'sparkling', 'twinkling', 'glistening', 'glimmering', 'flickering', 'flashing', 'blinking', 'dancing', 'moving', 'flowing', 'streaming', 'pouring', 'rushing', 'racing', 'speeding', 'flying', 'soaring', 'floating', 'drifting', 'sailing', 'gliding', 'sliding', 'slipping', 'falling', 'dropping', 'descending', 'sinking', 'diving', 'plunging', 'jumping', 'leaping', 'bounding', 'springing', 'bouncing', 'hopping', 'skipping', 'running', 'walking', 'strolling', 'marching', 'tramping', 'trudging', 'plodding', 'crawling', 'creeping', 'slithering', 'wriggling', 'squirming', 'twisting', 'turning', 'rotating', 'spinning', 'whirling', 'swirling', 'circling', 'orbiting', 'revolving', 'rolling', 'tumbling', 'flipping', 'somersaulting', 'cartwheeling', 'handstanding', 'balancing', 'standing', 'sitting', 'lying', 'resting', 'sleeping', 'dreaming', 'waking', 'awakening', 'arising', 'getting', 'up', 'down', 'over', 'under', 'through', 'across', 'around', 'between', 'among', 'within', 'without', 'inside', 'outside', 'near', 'far', 'close', 'distant', 'here', 'there', 'everywhere', 'somewhere', 'anywhere', 'nowhere', 'always', 'never', 'sometimes', 'often', 'rarely', 'seldom', 'frequently', 'occasionally', 'regularly', 'constantly', 'continuously', 'forever', 'temporarily', 'permanently', 'briefly', 'quickly', 'slowly', 'gradually', 'suddenly', 'immediately', 'instantly', 'eventually', 'finally', 'ultimately', 'initially', 'originally', 'first', 'last', 'next', 'previous', 'before', 'after', 'during', 'while', 'when', 'where', 'why', 'how', 'what', 'who', 'which', 'whose', 'whom', 'that', 'this', 'these', 'those', 'all', 'some', 'any', 'none', 'many', 'much', 'few', 'little', 'more', 'most', 'less', 'least', 'enough', 'too', 'very', 'quite', 'rather', 'pretty', 'fairly', 'somewhat', 'rather', 'quite', 'very', 'extremely', 'incredibly', 'amazingly', 'wonderfully', 'beautifully', 'perfectly', 'completely', 'totally', 'entirely', 'fully', 'partially', 'partly', 'mostly', 'mainly', 'primarily', 'chiefly', 'principally', 'largely', 'greatly', 'considerably', 'significantly', 'substantially', 'dramatically', 'radically', 'fundamentally', 'basically', 'essentially', 'primarily', 'mainly', 'mostly', 'largely', 'chiefly', 'principally'],
        'adverb' => ['quickly', 'slowly', 'gently', 'softly', 'loudly', 'quietly', 'brightly', 'darkly', 'lightly', 'heavily', 'strongly', 'weakly', 'bravely', 'fearfully', 'happily', 'sadly', 'angrily', 'calmly', 'wildly', 'freely', 'bound', 'openly', 'closely', 'brokenly', 'fixedly', 'built', 'destroyed', 'created', 'made', 'done', 'been', 'had', 'gotten', 'found', 'lost', 'won', 'failed', 'succeeded', 'tried', 'attempted', 'managed', 'failed', 'succeeded', 'achieved', 'accomplished', 'completed', 'finished', 'ended', 'started', 'begun', 'initiated', 'launched', 'released', 'freed', 'liberated', 'escaped', 'fled', 'chased', 'followed', 'led', 'guided', 'directed', 'pointed', 'shown', 'hidden', 'revealed', 'discovered', 'explored', 'searched', 'sought', 'looked', 'watched', 'observed', 'noticed', 'seen', 'spotted', 'found', 'located', 'placed', 'put', 'set', 'laid', 'stood', 'sat', 'lain', 'rested', 'slept', 'woken', 'awakened', 'arisen', 'gotten', 'up', 'down', 'over', 'under', 'through', 'across', 'around', 'between', 'among', 'within', 'without', 'inside', 'outside', 'near', 'far', 'close', 'distant', 'here', 'there', 'everywhere', 'somewhere', 'anywhere', 'nowhere', 'always', 'never', 'sometimes', 'often', 'rarely', 'seldom', 'frequently', 'occasionally', 'regularly', 'constantly', 'continuously', 'forever', 'temporarily', 'permanently', 'briefly', 'quickly', 'slowly', 'gradually', 'suddenly', 'immediately', 'instantly', 'eventually', 'finally', 'ultimately', 'initially', 'originally', 'first', 'last', 'next', 'previous', 'before', 'after', 'during', 'while', 'when', 'where', 'why', 'how', 'what', 'who', 'which', 'whose', 'whom', 'that', 'this', 'these', 'those', 'all', 'some', 'any', 'none', 'many', 'much', 'few', 'little', 'more', 'most', 'less', 'least', 'enough', 'too', 'very', 'quite', 'rather', 'pretty', 'fairly', 'somewhat', 'rather', 'quite', 'very', 'extremely', 'incredibly', 'amazingly', 'wonderfully', 'beautifully', 'perfectly', 'completely', 'totally', 'entirely', 'fully', 'partially', 'partly', 'mostly', 'mainly', 'primarily', 'chiefly', 'principally', 'largely', 'greatly', 'considerably', 'significantly', 'substantially', 'dramatically', 'radically', 'fundamentally', 'basically', 'essentially', 'primarily', 'mainly', 'mostly', 'largely', 'chiefly', 'principally'],
        'article' => ['the', 'a', 'an'],
        'conjunction' => ['and', 'but', 'or', 'so', 'yet', 'for', 'nor', 'while', 'when', 'where', 'because', 'if', 'unless', 'since', 'until', 'though', 'although', 'as', 'than', 'that']
    ];
    
    // Check each word type
    foreach ($wordDatabase as $type => $words) {
        if (in_array($word, $words)) {
            return $type;
        }
    }
    
    // Default to conjunction if not found
    return 'conjunction';
}

// Get statistics and poems
$stats = [];
$poems = [];
$word_connections = [];

try {
    // Get total poem count for stats (accurate regardless of any limit)
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM poems");
    $stats['total_poems'] = (int) $count_stmt->fetchColumn();

    // Get all poems with word analysis (no limit so gallery reflects full collection)
    $stmt = $pdo->query("
        SELECT id, player_name, poem_text, syllables_count, created_at 
        FROM poems 
        ORDER BY created_at DESC
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
            min-height: 200px;
        }
        .connection {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid #444;
            border-radius: 4px;
            padding: 20px;
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
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: normal;
        }
        .common-words {
            margin-bottom: 15px;
        }
        .common-words span {
            padding: 2px 6px;
            border-radius: 4px;
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
        
        #galleryReferencesScreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            color: #fff;
            font-family: 'Courier New', monospace;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 5000;
            padding: 40px;
            box-sizing: border-box;
        }
        
        #galleryReferencesScreen.visible {
            display: flex;
        }
        
        #galleryReferencesContent {
            max-width: 900px;
            text-align: left;
            font-size: 16px;
            line-height: 1.8;
            color: #cccccc;
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border: 1px solid #00ffff;
            border-radius: 5px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        #galleryReferencesContent h2 {
            color: #00ffff;
            font-size: 22px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        #galleryReferencesContent p {
            margin: 20px 0;
            text-align: justify;
        }
        
        #galleryReferencesContent .instruction {
            margin-top: 30px;
            color: #888;
            font-size: 14px;
            font-style: italic;
            text-align: center;
        }
        
        /* Animation for "Found 4 out of 4" text */
        @keyframes celebrateFoundAll {
            0%, 100% {
                transform: scale(1);
                color: #ffff00;
            }
            25% {
                transform: scale(1.15);
                color: #00ffff;
            }
            50% {
                transform: scale(1.2);
                color: #ff69b4;
            }
            75% {
                transform: scale(1.15);
                color: #44ff44;
            }
        }
        
        .found-all-animated {
            animation: celebrateFoundAll 2s ease-in-out 1;
            font-weight: bold !important;
            text-shadow: 0 0 10px currentColor;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
                <h1>Poem Gallery</h1>
                <p>Discover connections between poems through shared words.</p>
                <p style="font-size: 14px; color: #cccccc; margin-top: 5px;">Navigate using WASD or arrow keys. <span style="color: #ffff00; font-weight: bold;">Press R to see references for the current context.</span></p>
                <?php if (isset($player_name)): ?>
                <p style="color: #ff69b4; font-style: italic; margin-top: 10px;">
                    A <span style="font-weight: bold;"><?php echo strtolower(htmlspecialchars($player_name)); ?></span> is exploring the gallery.
                </p>
                <?php endif; ?>
            </div>
            
        <div class="nav-links">
            <a href="index.html?returnFromGallery=1" id="backToGameLink">← Back to the Floating Isle (Press B)</a>
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
                                        <?php 
                                            // Determine the most common word type in this connection
                                            $word_types = [];
                                            foreach ($connection['common_words'] as $word) {
                                                $word_type = getWordType($word);
                                                $word_types[$word_type] = ($word_types[$word_type] ?? 0) + 1;
                                            }
                                            arsort($word_types);
                                            $dominant_type = array_key_first($word_types);
                                            
                                            $colors = [
                                                'verb' => ['bg' => 'rgba(255, 68, 68, 0.3)', 'text' => '#ff4444', 'border' => 'rgba(255, 68, 68, 0.5)'],
                                                'noun' => ['bg' => 'rgba(170, 68, 255, 0.3)', 'text' => '#aa44ff', 'border' => 'rgba(170, 68, 255, 0.5)'],
                                                'adjective' => ['bg' => 'rgba(255, 255, 68, 0.3)', 'text' => '#ffff44', 'border' => 'rgba(255, 255, 68, 0.5)'],
                                                'adverb' => ['bg' => 'rgba(68, 255, 68, 0.3)', 'text' => '#44ff44', 'border' => 'rgba(68, 255, 68, 0.5)'],
                                                'article' => ['bg' => 'rgba(255, 136, 68, 0.3)', 'text' => '#ff8844', 'border' => 'rgba(255, 136, 68, 0.5)'],
                                                'conjunction' => ['bg' => 'rgba(255, 105, 180, 0.3)', 'text' => '#ff69b4', 'border' => 'rgba(255, 105, 180, 0.5)']
                                            ];
                                            $color = $colors[$dominant_type] ?? $colors['conjunction'];
                                        ?>
                                        <span class="connection-strength" style="background: <?php echo $color['bg']; ?>; color: <?php echo $color['text']; ?>; border: 1px solid <?php echo $color['border']; ?>;">
                                            <?php echo $connection['connection_strength']; ?> shared words
                                        </span>
                                    </div>
                                    
                                    <div class="common-words">
                                        <strong style="color: #44ff44;">Common words:</strong>
                                        <?php foreach ($connection['common_words'] as $word): ?>
                                            <?php 
                                                $word_type = getWordType($word);
                                                $colors = [
                                                    'verb' => ['bg' => 'rgba(255, 68, 68, 0.3)', 'text' => '#ff4444', 'border' => 'rgba(255, 68, 68, 0.5)'],
                                                    'noun' => ['bg' => 'rgba(170, 68, 255, 0.3)', 'text' => '#aa44ff', 'border' => 'rgba(170, 68, 255, 0.5)'],
                                                    'adjective' => ['bg' => 'rgba(255, 255, 68, 0.3)', 'text' => '#ffff44', 'border' => 'rgba(255, 255, 68, 0.5)'],
                                                    'adverb' => ['bg' => 'rgba(68, 255, 68, 0.3)', 'text' => '#44ff44', 'border' => 'rgba(68, 255, 68, 0.5)'],
                                                    'article' => ['bg' => 'rgba(255, 136, 68, 0.3)', 'text' => '#ff8844', 'border' => 'rgba(255, 136, 68, 0.5)'],
                                                    'conjunction' => ['bg' => 'rgba(255, 105, 180, 0.3)', 'text' => '#ff69b4', 'border' => 'rgba(255, 105, 180, 0.5)']
                                                ];
                                                $color = $colors[$word_type] ?? $colors['conjunction'];
                                            ?>
                                            <span style="background: <?php echo $color['bg']; ?>; color: <?php echo $color['text']; ?>; border: 1px solid <?php echo $color['border']; ?>;"><?php echo htmlspecialchars($word); ?></span>
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
                                                        
                                                        // Highlight common words with appropriate colors
                                                        foreach ($connection['common_words'] as $common_word) {
                                                            $word_type = getWordType($common_word);
                                                            $colors = [
                                                                'verb' => ['bg' => 'rgba(255, 68, 68, 0.3)', 'text' => '#ff4444', 'border' => 'rgba(255, 68, 68, 0.5)'],
                                                                'noun' => ['bg' => 'rgba(170, 68, 255, 0.3)', 'text' => '#aa44ff', 'border' => 'rgba(170, 68, 255, 0.5)'],
                                                                'adjective' => ['bg' => 'rgba(255, 255, 68, 0.3)', 'text' => '#ffff44', 'border' => 'rgba(255, 255, 68, 0.5)'],
                                                                'adverb' => ['bg' => 'rgba(68, 255, 68, 0.3)', 'text' => '#44ff44', 'border' => 'rgba(68, 255, 68, 0.5)'],
                                                                'article' => ['bg' => 'rgba(255, 136, 68, 0.3)', 'text' => '#ff8844', 'border' => 'rgba(255, 136, 68, 0.5)'],
                                                                'conjunction' => ['bg' => 'rgba(255, 105, 180, 0.3)', 'text' => '#ff69b4', 'border' => 'rgba(255, 105, 180, 0.5)']
                                                            ];
                                                            
                                                            $color = $colors[$word_type] ?? $colors['conjunction']; // Default to conjunction if unknown
                                                            $poem_text = preg_replace('/\b' . preg_quote($common_word, '/') . '\b/i', '<span style="background: ' . $color['bg'] . '; color: ' . $color['text'] . '; padding: 2px 4px; border-radius: 4px; font-weight: normal; border: 1px solid ' . $color['border'] . ';">' . $common_word . '</span>', $poem_text);
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
    
    <div id="galleryReferencesScreen">
        <div id="galleryReferencesContent">
            <h2>References</h2>
            <h3 id="galleryReferenceSubtitle" style="color: #ffff00; font-size: 18px; font-weight: normal; margin-top: -10px; margin-bottom: 25px; text-align: center;">(Found 0 out of 4)</h3>
            <p>I've always liked to play with language, and the concept of dialogue, be it solo or multiplayer. From page 37: "Conversation need not be direct or instant or even between two people." The idea of adding a Poem Gallery came from that passage; finding a way to make poems dialogue with each other, even if not created at the same time, by the same people.</p>
            <p class="instruction">Press any key to return</p>
        </div>
    </div>

    <script>
        // Store player name in session storage for consistency
        <?php if (isset($player_name)): ?>
        sessionStorage.setItem('playerName', '<?php echo addslashes($player_name); ?>');
        <?php endif; ?>
        
        // Audio context for sound effects
        let audioContext;
        
        // Sound effect functions
        function initAudio() {
            try {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            } catch (e) {
                // Web Audio API not supported
            }
        }

        function playSound(frequency, duration, type = 'sine', volume = 0.3) {
            if (!audioContext) return;
            
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(frequency, audioContext.currentTime);
            oscillator.type = type;
            
            gainNode.gain.setValueAtTime(0, audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(volume, audioContext.currentTime + 0.01);
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + duration);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + duration);
        }
        
        // Track which reference screens have been viewed
        let allReferencesFoundCelebrated = false; // Track if we've already celebrated finding all 4
        
        // Fireworks system for celebrating all 4 references found
        let fireworks = [];
        let fireworksActive = false;
        
        class Firework {
            constructor(x, y) {
                this.x = x;
                this.y = y;
                this.particles = [];
                this.colors = ['#ff4444', '#ffff44', '#44ff44', '#aa44ff', '#ff69b4', '#00ffff'];
                this.exploded = false;
                
                // Create particles for this firework
                for (let i = 0; i < 30; i++) {
                    const angle = (Math.PI * 2 * i) / 30;
                    const speed = 2 + Math.random() * 3;
                    const color = this.colors[Math.floor(Math.random() * this.colors.length)];
                    this.particles.push({
                        x: x,
                        y: y,
                        vx: Math.cos(angle) * speed,
                        vy: Math.sin(angle) * speed,
                        life: 1.0,
                        decay: 0.015 + Math.random() * 0.01,
                        color: color,
                        size: 3 + Math.random() * 2
                    });
                }
                this.exploded = true;
            }
            
            update() {
                for (let i = this.particles.length - 1; i >= 0; i--) {
                    const p = this.particles[i];
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.1; // Gravity
                    p.life -= p.decay;
                    
                    if (p.life <= 0) {
                        this.particles.splice(i, 1);
                    }
                }
                return this.particles.length > 0;
            }
            
            draw(ctx) {
                for (const p of this.particles) {
                    ctx.save();
                    ctx.globalAlpha = p.life;
                    ctx.fillStyle = p.color;
                    
                    // Add glow effect
                    ctx.shadowBlur = 15;
                    ctx.shadowColor = p.color;
                    
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.restore();
                }
            }
        }
        
        function celebrateAllReferencesFound() {
            if (allReferencesFoundCelebrated) return; // Already celebrated
            allReferencesFoundCelebrated = true;
            
            // Play subtle celebration sound
            playAllReferencesFoundSound();
            
            // Create fireworks
            fireworks = [];
            fireworksActive = true;
            
            // Create multiple fireworks at different positions
            const positions = [
                { x: window.innerWidth * 0.2, y: window.innerHeight * 0.3 },
                { x: window.innerWidth * 0.5, y: window.innerHeight * 0.25 },
                { x: window.innerWidth * 0.8, y: window.innerHeight * 0.3 },
                { x: window.innerWidth * 0.35, y: window.innerHeight * 0.5 },
                { x: window.innerWidth * 0.65, y: window.innerHeight * 0.5 }
            ];
            
            // Create first firework immediately, then stagger the rest
            fireworks.push(new Firework(positions[0].x, positions[0].y));
            
            positions.slice(1).forEach((pos, index) => {
                setTimeout(() => {
                    fireworks.push(new Firework(pos.x, pos.y));
                }, (index + 1) * 200);
            });
            
            // Stop fireworks after 4 seconds (to allow all fireworks to finish)
            setTimeout(() => {
                fireworksActive = false;
            }, 4000);
            
            // Start animation immediately
            animateFireworks();
        }
        
        function animateFireworks() {
            // Create a temporary canvas overlay for fireworks if it doesn't exist
            let fireworksCanvas = document.getElementById('fireworksCanvas');
            if (!fireworksCanvas) {
                fireworksCanvas = document.createElement('canvas');
                fireworksCanvas.id = 'fireworksCanvas';
                fireworksCanvas.style.position = 'fixed';
                fireworksCanvas.style.top = '0';
                fireworksCanvas.style.left = '0';
                fireworksCanvas.style.width = '100%';
                fireworksCanvas.style.height = '100%';
                fireworksCanvas.style.pointerEvents = 'none';
                fireworksCanvas.style.zIndex = '10000';
                document.body.appendChild(fireworksCanvas);
            }
            
            // Update canvas size to match window size
            const rect = fireworksCanvas.getBoundingClientRect();
            fireworksCanvas.width = rect.width;
            fireworksCanvas.height = rect.height;
            
            const ctx = fireworksCanvas.getContext('2d');
            ctx.clearRect(0, 0, fireworksCanvas.width, fireworksCanvas.height);
            
            // Update and draw fireworks
            for (let i = fireworks.length - 1; i >= 0; i--) {
                const firework = fireworks[i];
                if (!firework.update()) {
                    fireworks.splice(i, 1);
                } else {
                    firework.draw(ctx);
                }
            }
            
            // Continue animation if active or if there are still fireworks to display
            if (fireworksActive || fireworks.length > 0) {
                requestAnimationFrame(animateFireworks);
            } else {
                // Clean up canvas when done
                setTimeout(() => {
                    const canvas = document.getElementById('fireworksCanvas');
                    if (canvas && canvas.parentNode) {
                        canvas.parentNode.removeChild(canvas);
                    }
                }, 100);
            }
        }
        
        function playAllReferencesFoundSound() {
            if (!audioContext) {
                initAudio();
            }
            if (!audioContext) return;
            
            // Subtle, celebratory sound - ascending notes
            const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6 (major chord)
            notes.forEach((freq, i) => {
                setTimeout(() => {
                    playSound(freq, 0.4, 'sine', 0.15);
                }, i * 100);
            });
        }
        
        function markReferenceSeen(referenceType) {
            // Reference types: 'prelude', 'intro', 'game', 'gallery'
            const seenKey = 'referenceSeen_' + referenceType;
            sessionStorage.setItem(seenKey, 'true');
            updateReferenceCounter();
        }
        
        function getReferenceCount() {
            let count = 0;
            const types = ['prelude', 'intro', 'game', 'gallery'];
            types.forEach(type => {
                if (sessionStorage.getItem('referenceSeen_' + type) === 'true') {
                    count++;
                }
            });
            return count;
        }
        
        function updateReferenceCounter() {
            const count = getReferenceCount();
            const subtitle = `(Found ${count} out of 4)`;
            
            // Update gallery reference screen subtitle
            const gallerySubtitle = document.getElementById('galleryReferenceSubtitle');
            if (gallerySubtitle) {
                gallerySubtitle.textContent = subtitle;
                // Add animation if all found
                if (count === 4) {
                    gallerySubtitle.classList.add('found-all-animated');
                    // Remove animation after 2 seconds
                    setTimeout(() => {
                        gallerySubtitle.classList.remove('found-all-animated');
                    }, 2000);
                } else {
                    gallerySubtitle.classList.remove('found-all-animated');
                }
            }
            
            // Check if all 4 references have been found
            if (count === 4 && !allReferencesFoundCelebrated) {
                // Use setTimeout to ensure the reference screen is fully visible first
                setTimeout(() => {
                    celebrateAllReferencesFound();
                }, 100);
            }
        }
        
        function showGalleryReferencesScreen() {
            const galleryReferencesScreen = document.getElementById('galleryReferencesScreen');
            if (galleryReferencesScreen) {
                // Track that this reference has been seen
                markReferenceSeen('gallery');
                
                galleryReferencesScreen.classList.add('visible');
            }
        }
        
        function hideGalleryReferencesScreen() {
            const galleryReferencesScreen = document.getElementById('galleryReferencesScreen');
            if (galleryReferencesScreen) {
                galleryReferencesScreen.classList.remove('visible');
            }
        }
        
        // Initialize reference counter display on page load
        updateReferenceCounter();
        
        // Handle keyboard controls
        document.addEventListener('keydown', (e) => {
            // Handle X key to fully restart (clear session and go back to prelude)
            if (e.key === 'x' || e.key === 'X') {
                e.preventDefault();
                e.stopPropagation();
                // Clear all session storage (including reference tracking)
                sessionStorage.clear();
                // Reset celebration flag
                allReferencesFoundCelebrated = false;
                fireworks = [];
                fireworksActive = false;
                // Redirect to main page (which will show prelude)
                window.location.href = 'index.html';
                return;
            }
            
            const galleryReferencesScreen = document.getElementById('galleryReferencesScreen');
            
            // If gallery references screen is visible, any key closes it
            if (galleryReferencesScreen && galleryReferencesScreen.classList.contains('visible')) {
                e.preventDefault();
                e.stopPropagation();
                hideGalleryReferencesScreen();
                return;
            }
            
            // Handle R key to show references
            if (e.key === 'r' || e.key === 'R') {
                e.preventDefault();
                e.stopPropagation();
                showGalleryReferencesScreen();
                return;
            }
            
            // Handle B key to go back to game
            if (e.key === 'b' || e.key === 'B') {
                e.preventDefault();
                window.location.href = 'index.html?returnFromGallery=1';
            }
            
            // Handle W key to scroll up
            if (e.key === 'w' || e.key === 'W') {
                e.preventDefault();
                window.scrollBy({ top: -100, behavior: 'smooth' });
            }
            
            // Handle S key to scroll down
            if (e.key === 's' || e.key === 'S') {
                e.preventDefault();
                window.scrollBy({ top: 100, behavior: 'smooth' });
            }
            
            // Handle D or right arrow to show next connection
            if (e.key === 'd' || e.key === 'D' || e.key === 'ArrowRight') {
                e.preventDefault();
                stopAutoRotate(); // Stop auto-rotate when user manually navigates
                nextConnection();
            }
            
            // Handle A or left arrow to show previous connection
            if (e.key === 'a' || e.key === 'A' || e.key === 'ArrowLeft') {
                e.preventDefault();
                stopAutoRotate(); // Stop auto-rotate when user manually navigates
                previousConnection();
            }
            
            // Arrow Up and Arrow Down work by default for scrolling
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
            
            // Adjust container height to fit active connection
            const activeConnection = connections[currentConnectionIndex];
            const carousel = document.querySelector('.connections-carousel');
            if (activeConnection && carousel) {
                // Temporarily make all connections visible to measure height
                connections.forEach(conn => conn.style.visibility = 'hidden');
                activeConnection.style.visibility = 'visible';
                
                // Get the height of the active connection
                const connectionHeight = activeConnection.offsetHeight;
                
                // Set container height to fit the content
                carousel.style.height = connectionHeight + 'px';
                
                // Restore visibility
                connections.forEach(conn => conn.style.visibility = '');
            }
        }

        function nextConnection() {
            currentConnectionIndex = (currentConnectionIndex + 1) % totalConnections;
            updateCarousel();
        }

        function previousConnection() {
            currentConnectionIndex = (currentConnectionIndex - 1 + totalConnections) % totalConnections;
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
