<?php
function loadEnvVariables()
{
    $envFile = __DIR__ . '/../../../.env';

    if (!file_exists($envFile)) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Environment file not found.']);
        exit;
    }

    $env = parse_ini_file($envFile);

    if (!isset($env['SUPABASE_URL']) || !isset($env['SUPABASE_KEY']) || !isset($env['SUPABASE_BUCKET'])) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Missing required environment variables.']);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'supabaseUrl' => $env['SUPABASE_URL'],
        'supabaseKey' => $env['SUPABASE_KEY'],
        'bucketName' => $env['SUPABASE_BUCKET']
    ]);
}

// Call the function
loadEnvVariables();
