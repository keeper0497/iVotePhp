<?php
// File Upload Helper

class FileHelper {
    
    public static function uploadFile($fileInputName, $targetDir, $filesArray = null) {
        // Use global $_FILES if not provided
        $files = $filesArray ?? $_FILES;
        
        if (!isset($files[$fileInputName]) || $files[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        // Check file size (2MB limit)
        if ($files[$fileInputName]['size'] > 2097152) {
            return null;
        }
        
        $filename = time() . "_" . uniqid() . "_" . basename($files[$fileInputName]['name']);
        $targetPath = $targetDir . $filename;
        
        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        if (!move_uploaded_file($files[$fileInputName]['tmp_name'], $targetPath)) {
            error_log("Failed to upload file: $filename");
            return null;
        }
        
        return $targetPath;
    }
}
?>