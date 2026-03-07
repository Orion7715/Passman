<?php

function is_strong_password($password) {
    $requirements = [];
    if (strlen($password) < 12) $requirements[] = "12 characters";
    if (!preg_match('/[A-Z]/', $password)) $requirements[] = "an uppercase letter";
    if (!preg_match('/[a-z]/', $password)) $requirements[] = "a lowercase letter";
    if (!preg_match('/[0-9]/', $password)) $requirements[] = "a number";
    if (!preg_match('/[!@#$%^&*()\-_=+\[\]{}<>?]/', $password)) $requirements[] = "a special character";

    return empty($requirements) ? [] : ["Password must include " . implode(", ", $requirements) . "."];
}


function encrypt_with_master($data, $key) {
    if (empty($data)) return "";
    $iv_length = openssl_cipher_iv_length('AES-256-CBC');
    $iv = openssl_random_pseudo_bytes($iv_length);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}


function decrypt_with_master($data, $key) {
    if (empty($data)) return "";
    $data = base64_decode($data);
    $iv_length = openssl_cipher_iv_length('AES-256-CBC');
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}
