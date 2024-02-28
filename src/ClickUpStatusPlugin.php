<?php

namespace IBD\ClickUpStatusPlugin;

require_once CSP_PLUGIN_DIR . '/vendor/autoload.php';

// Import necessary classes
use IBD\ClickUpStatusPlugin\ClickUpDatabase;
use IBD\ClickUpStatusPlugin\ScriptEnqueuer;
use IBD\ClickUpStatusPlugin\AjaxHandler;
use IBD\ClickUpStatusPlugin\GravityFormHandler; 
use IBD\ClickUpStatusPlugin\AdminSettings; 


class ClickUpStatusPlugin {
    public function __construct() {
        // Initialize AdminSettings
        $admin_settings = new AdminSettings();
        
        // Enqueue script
        ScriptEnqueuer::enqueueCustomScript();

        // Register AJAX handlers
        $ajax_handler = new AjaxHandler(new ClickUpDatabase());
        $ajax_handler->registerHandlers();

        // Initialize GravityFormHandler
        $gravity_form_handler = new GravityFormHandler(new ClickUpDatabase(), new EmailSender());


        // Register shortcode
        add_shortcode('clickup_status_form', array($this, 'clickup_status_form_shortcode'));

    }

    // Shortcode for the form
    function clickup_status_form_shortcode() {
        ob_start(); // Start output buffering
        ?>
        <style>
            /* Add custom CSS for button alignment */
            #clickup-status-checker .col-auto {
                display: flex;
                align-items: baseline;
            }

            #clickup-status-checker button {
                margin-left: 8px; /* Adjust the margin as needed */
            }
        </style>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <form id="clickup-status-checker" method="post" action="">
                        <div class="form-row align-items-center">
                             <input type="hidden" name="form_identifier" value="clickup_client_status">

                            <div class="col-auto">
                                <label class="sr-only" for="user_email">ایمیل:</label>
                                <input type="email" name="user_email" class="form-control mb-2" id="user_email" placeholder="ایمیل" required>
                            </div>
                            <div class="col-auto">
                                <label class="sr-only" for="hash_code">کد پیگیری:</label>
                                <input type="text" name="hash_code" class="form-control mb-2" id="hash_code" placeholder="کد پیگیری" required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="submit" class="btn btn-primary mb-2 btn-align">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add a container to display the result -->
        <div id="result-container"></div>

        <?php
        return ob_get_clean(); // Return the buffered content
    }
    
}

$clickUpStatusPlugin = new ClickUpStatusPlugin();
