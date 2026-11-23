<?php

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function csrf_token() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function verify_csrf() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $token = $_POST["csrf_token"] ?? "";
    return is_string($token)
        && isset($_SESSION["csrf_token"])
        && hash_equals($_SESSION["csrf_token"], $token);
}

function set_flash($type, $message) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION["flash"] = ["type" => $type, "message" => $message];
}

function get_flash() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $flash = $_SESSION["flash"] ?? null;
    unset($_SESSION["flash"]);
    return $flash;
}
