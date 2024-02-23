<?php

namespace CUSTATUS\ClickUpStatusPlugin;


class GravityFormHandler {
    private $click_up_api;
    private $click_up_database;
    private $email_sender;

    public function __construct(
        ClickUpApi $click_up_api,
        ClickUpDatabase $click_up_database,
        EmailSender $email_sender
    ) {
        $this->click_up_api = $click_up_api;
        $this->click_up_database = $click_up_database;
        $this->email_sender = $email_sender;

        // Gravity Forms hooks
        add_action('gform_after_submission', array($this, 'handleGravityFormSubmission'), 10, 2);
    }

    
    public function handleGravityFormSubmission($entry, $form) {
        // Check if the submitted form has the desired form ID
        $form_id = 1; //  In the next version this will be retrive from Admin dashboard 
        if ($form['id'] == $form_id) {
            // Extract data from the form entry
            $email = rgar($entry, 'input_1'); //    In the next version this will be retrive from Admin dashboard 
            $subject = rgar($entry, 'input_2'); //  In the next version this will be retrive from Admin dashboard 
            $phone = rgar($entry, 'input_3'); //    In the next version this will be retrive from Admin dashboard
    
            // Send data to ClickUp CRM
            $response = $this->click_up_api->sendingDataToClickUpCRM($entry);
    
            // Assuming $response contains the ClickUp ID
            $decoded_response = json_decode($response, true);
    
            if (isset($decoded_response['id'])) {
                $clickUpId = $decoded_response['id'];
    
                // Create a hash using SHA-256 and truncate it to 6 characters
                $hash = substr(hash('sha256', $email . $clickUpId), 0, 6);
    
                // Set the value of the hidden hash field in the form entry
                gform_update_meta($entry['id'], 'hash_field_id', $hash);
    
                // Save data to the database
                $this->click_up_database->insertClickUpTaskData($email, $clickUpId, $hash);
    
                // Send email
                $this->email_sender->sendEmail($email, $hash);
            }
        }
    }    
}
