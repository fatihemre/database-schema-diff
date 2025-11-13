<?php
/**
 * Language API Endpoint
 *
 * Returns translations for the specified language
 *
 * @license GPL-3.0
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Get language from query parameter (default: en)
    $lang = $_GET['lang'] ?? 'en';

    // Sanitize language code (only allow a-z, 2 characters)
    if (!preg_match('/^[a-z]{2}$/', $lang)) {
        $lang = 'en';
    }

    // Check if language file exists
    $langFile = __DIR__ . '/lang/' . $lang . '.php';

    if (!file_exists($langFile)) {
        throw new Exception("Language file not found: {$lang}");
    }

    // Load language file
    $translations = require $langFile;

    // Response
    echo json_encode([
        'success' => true,
        'lang' => $lang,
        'translations' => $translations
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
