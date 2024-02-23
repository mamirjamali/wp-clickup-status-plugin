<?php

namespace CUSTATUS\ClickUpStatusPlugin;

require_once CSP_PLUGIN_DIR . '/vendor/autoload.php';

// Import necessary classes
use CUSTATUS\ClickUpStatusPlugin\ClickUpDatabase;
use CUSTATUS\ClickUpStatusPlugin\ScriptEnqueuer;
use CUSTATUS\ClickUpStatusPlugin\AjaxHandler;
use CUSTATUS\ClickUpStatusPlugin\ClickUpApi;
use CUSTATUS\ClickUpStatusPlugin\ContactFormHandler; // Add this line


class ClickUpStatusPlugin {
    public function __construct() {
        // Enqueue script
        ScriptEnqueuer::enqueueCustomScript();
        
        // Register AJAX handlers
        $ajax_handler = new AjaxHandler(new ClickUpApi("//In the next version this will be retrive from Admin dashboard"), new ClickUpDatabase());
        $ajax_handler->registerHandlers();
        
        // Initialize ContactFormHandler
        $contact_form_handler = new ContactFormHandler(
            new ClickUpApi("//In the next version this will be retrive from Admin dashboard"),
            new ClickUpDatabase(),
            new EmailSender()
        );

        // Register shortcode
        add_shortcode('clickup_status_form', array($this, 'clickup_status_form_shortcode'));

    }

    // Shortcode for the form that let users to track the requset
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
                                <label class="sr-only" for="user_email">Email:</label>
                                <input type="email" name="user_email" class="form-control mb-2" id="user_email" placeholder="Email" required>
                            </div>
                            <div class="col-auto">
                                <label class="sr-only" for="hash_code">Tracking Code:</label>
                                <input type="text" name="hash_code" class="form-control mb-2" id="hash_code" placeholder="Tracking Code" required>
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

