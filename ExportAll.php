<?php
/**
 * A simple class for drawing balls and powerball(s)
 * @author Jackson Kambaragye
 */
require('config/config.php');
class ExportAll {

    public static function downloadCsv(){
        $db   = config::getInstance();
        $conn = $db->getConnection(); 
        $sql = "SELECT main_balls,power_balls,draw_time FROM lotto_draws";
        $result = mysqli_query($conn,$sql);

        $filename ='ExportAll';
        # output headers so that the file is downloaded rather than displayed
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$filename");
        # Disable caching - HTTP 1.1
        header("Cache-Control: no-cache, no-store, must-revalidate");
        # Disable caching - HTTP 1.0
        header("Pragma: no-cache");
        # Disable caching - Proxies
        header("Expires: 0");
        
        # Start the ouput
        $output = fopen("php://output", "w");
        # output the column headings
        fputcsv($output, array('Balls', 'Powerball(s)','Draw times'));
        
        # Then loop through the rows
        while($row = mysqli_fetch_assoc($result)){
            # Add the rows to the body
            fputcsv($output, $row);  
        }
        # Close the stream off
        fclose($output);    
         
    }

}

ExportAll::downloadCsv();