<?php
namespace CUSTATUS\ClickUpStatusPlugin;

class ClickUpDatabase {
    /**
     * Create or check the existence of the clickup_tasks table.
     */
    public static function createClickUpTasksTable() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'clickup_tasks';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Table doesn't exist, create it
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                email varchar(100) NOT NULL,
                task_id varchar(100) NOT NULL,
                hash_code varchar(20) NOT NULL,
                form_id varchar(20) NOT NULL,
                submission_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    /**
     * Insert data into the clickup_tasks table.
     *
     * @param string $email
     * @param string $clickUpId
     * @param string $hash
     */
    public static function insertClickUpTaskData($email, $clickUpId, $hash, $form_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'clickup_tasks';

        $data = array(
            'email' => $email,
            'task_id' => $clickUpId,
            'hash_code' => $hash,
            'form_id' => $form_id,
            'submission_time' => current_time('mysql'),
        );

        $wpdb->insert($table_name, $data);
    }

    /**
     * Get task ID by email and hash.
     *
     * @param string $email
     * @param string $hash
     * @return string|null Task ID or null if not found.
     */
    public static function getTaskIdByEmailAndHash($email, $hash) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'clickup_tasks';

        self::createClickUpTasksTable();
        
        $query = $wpdb->prepare("SELECT task_id, form_id FROM $table_name WHERE email = %s AND hash_code = %s", $email, $hash);
        $results = $wpdb->get_results($query);

        return $results;
    }
}
