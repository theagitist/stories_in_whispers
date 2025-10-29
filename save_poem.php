<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Include database configuration
require_once 'config.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($input['player_name']) || !isset($input['poem_text']) || !isset($input['poem_lines']) || !isset($input['syllables_count'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    
    // Sanitize input
    $player_name = trim($input['player_name']);
    $poem_text = trim($input['poem_text']);
    $poem_lines = $input['poem_lines'];
    $syllables_count = (int)$input['syllables_count'];
    
    // Validate data
    if (empty($player_name) || empty($poem_text) || $syllables_count <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data provided']);
        exit;
    }
    
    // Prepare SQL statement
    $stmt = $pdo->prepare("
        INSERT INTO poems (player_name, poem_text, poem_lines, syllables_count) 
        VALUES (:player_name, :poem_text, :poem_lines, :syllables_count)
    ");
    
    // Bind parameters
    $stmt->bindParam(':player_name', $player_name);
    $stmt->bindParam(':poem_text', $poem_text);
    $stmt->bindParam(':poem_lines', json_encode($poem_lines));
    $stmt->bindParam(':syllables_count', $syllables_count);
    
    // Execute the statement
    if ($stmt->execute()) {
        $poem_id = $pdo->lastInsertId();
        echo json_encode([
            'success' => true,
            'poem_id' => $poem_id,
            'message' => 'Poem saved successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save poem']);
    }
    
} catch (Exception $e) {
    error_log("Error saving poem: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>
