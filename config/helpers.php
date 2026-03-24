<?php
function basePath() {
    static $path = null;
    if ($path === null) {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        if (strpos($scriptName, '/pushforgood/') !== false) {
            $path = '/pushforgood';
        } elseif (strpos($scriptName, '/pushforgood') !== false) {
            $path = '/pushforgood';
        } else {
            $path = '';
        }
    }
    return $path;
}

$GLOBALS['basePath'] = basePath();
