<?php
// File Upload Helper

class FileHelper {
    
    public static function uploadFile($fileInputName, $targetDir) {
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return '';
        }
        
        // Check file size (2MB limit)
        if ($_FILES[$fileInputName]['size'] > 2097152) {
            return '';
        }
        
        $filename = time() . "_" . uniqid() . "_" . basename($_FILES[$fileInputName]['name']);
        $targetPath = $targetDir . $filename;
        
        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $targetPath)) {
            die("Failed to upload file: $filename");
        }
        
        return $filename;
    }
}
?>