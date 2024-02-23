<?php

namespace CUSTATUS\ClickUpStatusPlugin;

class ContactFormHandler {
    private $form_processed = false;
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

        // Contact Form 7 hook
        add_action('wpcf7_before_send_mail', array($this, 'handleContactFormSubmission'), 10, 3, true);
    }

    public function handleContactFormSubmission($contact_form, $abort, $submission) {

        $form_id = $contact_form->id(); //    In the next version this will be retrive from Admin dashboard
        
        if ($form_id == "1") {

            $posted_data = $submission->get_posted_data();

            // Check and adjust the field names based on your Contact Form 7 form
            $email = isset($posted_data['your-email']) ? $posted_data['your-email'] : ''; //          In the next version this will be retrive from Admin dashboard
            $subject = isset($posted_data['your-subject']) ? $posted_data['your-subject'] : ''; //    In the next version this will be retrive from Admin dashboard
            $phone = isset($posted_data['phone-number']) ? $posted_data['phone-number'] : '';//       In the next version this will be retrive from Admin dashboard
    

            // Send data to ClickUp 
            $response = $this->click_up_api->sendingDataToClickUpCRM($posted_data);
            $decoded_response = json_decode($response, true);

            if (isset($decoded_response['id'])) {
                $clickUpId = $decoded_response['id'];

                // Create a hash using SHA-256 and truncate it to 10 characters
                $hash = substr(hash('sha256', $email . $clickUpId), 0, 6);

                // Save data to the database
                $this->click_up_database->insertClickUpTaskData($email, $clickUpId, $hash);

                // Send email to user in order tracking the request
                $this->email_sender->sendEmail($email, $hash);

            } 

            $this->form_processed = true;
        }

        return $contact_form;
    }
}




