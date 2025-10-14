<?php
// Export Helper Functions

class ExportHelper {
    
    public static function exportToCSV($data, $type, $title) {
        $filename = self::sanitizeFilename($title) . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        if ($type === 'complete_election') {
            // Handle complex report structure
            fputcsv($output, ['COMPLETE ELECTION REPORT - ' . date('Y-m-d H:i:s')]);
            fputcsv($output, []);
            
            // Voters statistics
            fputcsv($output, ['VOTERS STATISTICS']);
            fputcsv($output, ['Total Registered Voters', $data['voters_stats']['total_voters']]);
            fputcsv($output, ['Voters Who Voted', $data['voters_stats']['voted_count']]);
            if ($data['voters_stats']['total_voters'] > 0) {
                fputcsv($output, ['Voting Percentage', round(($data['voters_stats']['voted_count'] / $data['voters_stats']['total_voters']) * 100, 2) . '%']);
            }
            fputcsv($output, []);
        } else {
            // Handle regular tabular data
            if (!empty($data)) {
                fputcsv($output, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
        }
        
        fclose($output);
        exit;
    }
    
    public static function exportToExcel($data, $type, $title) {
        // For simplicity, export as CSV with .xlsx extension
        $filename = self::sanitizeFilename($title) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        self::exportToCSV($data, $type, $title);
    }
    
    private static function sanitizeFilename($filename) {
        return preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
    }
}
?>