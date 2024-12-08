<?php

// Common utility functions and constants for the application

// Function to check if user is logged in and has admin role
function checkAdminPrivileges() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }
}

// Function to sanitize output
function sanitizeOutput($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeOutput($value);
        }
        return $data;
    }
    if ($data === null) {
        return null;
    }
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

// Function to validate required parameters
function validateRequiredParams($params, $required) {
    foreach ($required as $param) {
        if (!isset($params[$param]) || empty($params[$param])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing required parameter: $param"]);
            exit;
        }
    }
}

// Set default timezone
date_default_timezone_set('Asia/Bahrain');

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
