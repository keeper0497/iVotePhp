<?php
// Encryption Helper for Vote Security

class EncryptionHelper {
    private static $key = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6a7b8c9d0e1f2';
    
    public static function encryptVote($data) {
        $cipher = "aes-256-cbc";
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt(json_encode($data), $cipher, self::$key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    
    public static function decryptVote($encrypted) {
        $cipher = "aes-256-cbc";
        list($encrypted_data, $iv) = explode('::', base64_decode($encrypted), 2);
        return json_decode(openssl_decrypt($encrypted_data, $cipher, self::$key, 0, $iv), true);
    }
}
?>