<?php

//Print the error message in a nice format
function printErrorMessage(string $message, string $errorFile = "File could not be found", string $errorLine = "Line could not be found", $errorStack = "")
{
    global $config;
    $errorObject = [
        "message" => $message,
        "file" => $errorFile,
        "line" => $errorLine,
    ];

    $appType = $config["APP_TYPE"];
    $errorCode = 500;

    http_response_code($errorCode); // set the response code

    if ($appType === "api") {
        $errorObject["code"] = $errorCode;

        header("Content-Type: application/json");
        echo json_encode($errorObject, JSON_PRETTY_PRINT);
    } else {
        $errorObject["stack"] = $errorStack;
        include("./views/error_page.php");
    }
}

function errorToException($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

function exceptionHandler(Error | Exception $exception)
{
    ob_clean();

    $errorMessage = "Message: " . $exception->getMessage() . "\n";
    $errorFile = "File: " . $exception->getFile() . "<br>";
    $errorLine = "Code: " . $exception->getCode() . "&nbsp;&nbsp;&nbsp;&nbsp";
    $errorLine .= "Line: " . $exception->getLine() . "<br>";
    $errorLine .= "File: " . $exception->getFile() . "<br>";
    $errorStack = formatStackTrace($exception->getTrace());

    printErrorMessage($errorMessage, $errorFile, $errorLine, $errorStack);
}

function formatStackTrace($traceArray)
{
    $traceString = '';
    foreach ($traceArray as $index => $trace) {
        $traceString .= "#" . $index . " ";
        if (isset($trace['file'])) {
            $traceString .= $trace['file'] . "(" . $trace['line'] . "): ";
        }
        if (isset($trace['class'])) {
            $traceString .= $trace['class'] . "->";
        }
        $traceString .= $trace['function'] . "()\n";
    }
    return $traceString;
}

// ini_set("display_errors", 0);
set_error_handler("errorToException");
set_exception_handler("exceptionHandler");
