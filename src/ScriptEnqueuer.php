<?php
namespace CUSTATUS\ClickUpStatusPlugin;

class ScriptEnqueuer {
    public static function enqueueCustomScript() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'registerAndEnqueueScript'));
    }

    public static function registerAndEnqueueScript() {
        // Enqueue Bootstrap CSS from a CDN
        wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), '4.3.1');

        // Enqueue your script
        wp_enqueue_script('clickup-status', plugins_url('/assets/js/clickupStatus.js', __FILE__), array('jquery'), '1.0', true);

        // Enqueue Bootstrap Icons CSS from a CDN
        wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.17.0/font/bootstrap-icons.css', array(), '1.17.0');

        // Enqueue your stylesheet with a different handle
        wp_enqueue_style('clickup-status-style', plugins_url('/assets/css/clickupStatus.css', __FILE__), array(), '1.0');

        // Localize the script with necessary variables
        wp_localize_script('clickup-status', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }
}
