<?php
require_once '../settings/db_class.php';

class image_search_class extends db_connection
{
    /**
     * Binary search for images by timestamp
     * This provides efficient O(log n) search performance
     */
    public function binary_search_images_by_timestamp($user_id, $target_timestamp)
    {
        // Get all image files for the user
        $image_dir = "../uploads/u{$user_id}/";
        $all_images = $this->get_all_user_images($image_dir);
        
        if (empty($all_images)) {
            return array();
        }
        
        // Sort images by timestamp (required for binary search)
        usort($all_images, function($a, $b) {
            return $a['timestamp'] - $b['timestamp'];
        });
        
        // Binary search implementation
        $left = 0;
        $right = count($all_images) - 1;
        $result = array();
        
        while ($left <= $right) {
            $mid = intval(($left + $right) / 2);
            
            if ($all_images[$mid]['timestamp'] == $target_timestamp) {
                // Found exact match, collect all images with same timestamp
                $result[] = $all_images[$mid];
                
                // Check for duplicates on both sides
                for ($i = $mid - 1; $i >= 0 && $all_images[$i]['timestamp'] == $target_timestamp; $i--) {
                    $result[] = $all_images[$i];
                }
                for ($i = $mid + 1; $i < count($all_images) && $all_images[$i]['timestamp'] == $target_timestamp; $i++) {
                    $result[] = $all_images[$i];
                }
                break;
            } elseif ($all_images[$mid]['timestamp'] < $target_timestamp) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }
        
        return $result;
    }
    
    /**
     * Get all images for a user from the uploads directory
     */
    private function get_all_user_images($image_dir)
    {
        $images = array();
        
        if (!is_dir($image_dir)) {
            return $images;
        }
        
        // Recursively scan all subdirectories
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($image_dir));
        
        foreach ($iterator as $file) {
            if ($file->isFile() && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file->getFilename())) {
                $filename = $file->getFilename();
                
                // Extract timestamp from filename pattern: img_name_timestamp.ext
                if (preg_match('/img_.*_(\d+)\./', $filename, $matches)) {
                    $images[] = array(
                        'filename' => $filename,
                        'path' => str_replace('../', '', $file->getPathname()),
                        'timestamp' => intval($matches[1]),
                        'size' => $file->getSize(),
                        'modified' => $file->getMTime()
                    );
                }
            }
        }
        
        return $images;
    }
    
    /**
     * Search images by product ID
     */
    public function search_images_by_product($user_id, $product_id)
    {
        $image_dir = "../uploads/u{$user_id}/p{$product_id}/";
        $images = array();
        
        if (is_dir($image_dir)) {
            $files = scandir($image_dir);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                    $images[] = "uploads/u{$user_id}/p{$product_id}/{$file}";
                }
            }
            // Sort by filename for consistent ordering
            sort($images);
        }
        
        return $images;
    }
    
    /**
     * Get recent images for a user
     */
    public function get_recent_images($user_id, $limit = 10)
    {
        $image_dir = "../uploads/u{$user_id}/";
        $all_images = $this->get_all_user_images($image_dir);
        
        // Sort by timestamp descending
        usort($all_images, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return array_slice($all_images, 0, $limit);
    }
}
?>
