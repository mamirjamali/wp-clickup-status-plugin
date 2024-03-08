<?php

namespace CUSTATUS\ClickUpStatusPlugin;

require_once CSP_PLUGIN_DIR . '/vendor/autoload.php';

// Import necessary classes
use CUSTATUS\ClickUpStatusPlugin\ClickUpDatabase;
use CUSTATUS\ClickUpStatusPlugin\ScriptEnqueuer;
use CUSTATUS\ClickUpStatusPlugin\AjaxHandler;
use CUSTATUS\ClickUpStatusPlugin\GravityFormHandler; 
use CUSTATUS\ClickUpStatusPlugin\ClickUpStatusForm; 
use CUSTATUS\ClickUpStatusPlugin\Admin\GFAdminSettings; 
use CUSTATUS\ClickUpStatusPlugin\Admin\TFAdminSettings; 


class ClickUpStatusPlugin {
    public function __construct() {

        // Initialize Admin Dashboard settings
        $gravity_form_admin_settings = new GFAdminSettings();
        $tracking_form_settings = new TFAdminSettings();
        
        // Register AJAX handlers
        $ajax_handler = new AjaxHandler(new ClickUpDatabase());
        $ajax_handler->registerHandlers();

        //Initialize Tracking Form
        $tracking_form = new ClickUpStatusForm();

        // Initialize GravityFormHandler
        $gravity_form_handler = new GravityFormHandler(new ClickUpDatabase(), new EmailSender());

        // Register shortcode
        add_shortcode('clickup_status_form', array($tracking_form, 'clickup_status_form_shortcode'));
    }
    
}

$clickUpStatusPlugin = new ClickUpStatusPlugin();
