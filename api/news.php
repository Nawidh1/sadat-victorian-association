<?php
/**
 * News API Endpoint
 * Returns news items as JSON from database
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM news ORDER BY date DESC, created_at DESC");
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($news, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load news'], JSON_UNESCAPED_UNICODE);
}
