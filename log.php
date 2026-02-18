<?php

/* ================== SETTINGS ================== */

ini_set("display_errors", 0);     // Hide errors from users
ini_set("log_errors", 1);
error_reporting(E_ALL);

/* ================== LOGGER FUNCTION ================== */

function writeLog($type, $message, $file, $line)
{
    $date = date("Y-m-d");
    $dateTime = date("Y-m-d H:i:s");

    $logDir = __DIR__ . "/logs/";

    // Create logs folder if not exists
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $logFile = $logDir . "error_" . $date . ".log";

    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $url = $_SERVER['REQUEST_URI'] ?? 'CLI';

    $logMessage = "[$dateTime] [$type]
IP: $ip
URL: $url
Message: $message
File: $file
Line: $line
----------------------------------------" . PHP_EOL;

    error_log($logMessage, 3, $logFile);
}

/* ================== ERROR HANDLERS ================== */

// Handle PHP Errors (Warnings, Notices, etc.)
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    writeLog("ERROR", $errstr, $errfile, $errline);
    return true; // Prevent default PHP error output
});

// Handle Uncaught Exceptions
set_exception_handler(function ($exception) {
    writeLog(
        "EXCEPTION",
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );
});

/* ================== TEST CASE ================== */

// Trigger Warning
echo $undefined_variable;

// Trigger Exception
throw new Exception("Database connection failed!");