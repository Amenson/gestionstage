<?php
// Simple CSRF helper
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token()
{
    return $_SESSION['csrf_token'];
}

function csrf_validate($token = null)
{
    // Accept token from POST body, or from X-CSRF-Token header for AJAX
    if (!$token) {
        // Prefer explicit server header
        if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        } else {
            // Some environments provide headers via getallheaders()
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
                if (!empty($headers['X-CSRF-Token'])) $token = $headers['X-CSRF-Token'];
            }
        }
    }

    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], (string)$token);
}
