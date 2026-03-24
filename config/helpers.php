<?php
// Helper function to generate URLs dynamically based on deployment environment
function basePath() {
    static $path = null;
    if ($path === null) {
        // Detect if app is in a subdirectory by checking the script path
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Check if /pushforgood/ is in the script path
        if (strpos($scriptName, '/pushforgood/') !== false) {
            $path = '/pushforgood';
        } elseif (strpos($scriptName, '/pushforgood') !== false) {
            $path = '/pushforgood';
        } else {
            // App is at root level
            $path = '';
        }
    }
    return $path;
}

// Get the base path for asset and form URLs
$GLOBALS['basePath'] = basePath();
