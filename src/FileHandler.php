<?php

namespace IBD\ClickUpStatusPlugin;

class FileHandler {
    /**
     * Get file information from a URL.
     *
     * @param string $file_url The URL of the file.
     * @return array|false An array containing 'path', 'name', and 'type' if successful, false otherwise.
     */
    public static function getFileInfo($file_url) {
        // Get the WordPress uploads directory
        $upload_dir = wp_upload_dir();

        // Check if the URL is within the uploads directory
        if (strpos($file_url, $upload_dir['baseurl']) === 0) {
            // Get the file path relative to the uploads directory
            $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_url);

            // Get the file name
            $file_name = basename($file_path);

            // Get the file type
            $file_type = mime_content_type($file_path);

            // Return the file information
            return array('path' => $file_path, 'name' => $file_name, 'type' => $file_type);
        }

        // The file URL is not within the uploads directory
        return false;
    }
}
