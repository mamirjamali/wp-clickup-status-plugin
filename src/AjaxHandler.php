<?php

namespace CUSTATUS\ClickUpStatusPlugin;

use CUSTATUS\ClickUpStatusPlugin\ClickUpDatabase;
use CUSTATUS\ClickUpStatusPlugin\ClickUpApi;

class AjaxHandler {

    private $click_up_api;
    private $click_up_database;

    public function __construct(ClickUpApi $click_up_api, ClickUpDatabase $click_up_database) {
        $this->click_up_api = $click_up_api;
        $this->click_up_database = $click_up_database;
    }

    public function clickup_status_handle_form_submission() {
        // Check if the form identifier matches
        $form_identifier = isset($_POST['form_identifier']) ? sanitize_text_field($_POST['form_identifier']) : '';
        if ($form_identifier !== 'clickup_client_status') {
            wp_send_json_error("Invalid form submission.");
        }

        // Get user input
        $user_email = sanitize_email($_POST['user_email']);
        $hash_code = sanitize_text_field($_POST['hash_code']);

        // Perform database query to check for a match
        $task_id = $this->click_up_database->getTaskIdByEmailAndHash($user_email, $hash_code);

        // Check if task_id exists before making API call
        if ($task_id) {
            $response = $this->click_up_api->getTaskStatus($task_id);
            $decoded_response_task_status = json_decode($response, true);

            $status = $decoded_response_task_status['status']['status'];
            $orderIndex = $decoded_response_task_status['status']['orderindex'];
            $assigneeUsername = $decoded_response_task_status['assignees'][0]['username']; // Assuming there is only one assignee
    
            // Respond with the task status (e.g., JSON)
            wp_send_json_success(array(
                'status' => $status,
                'orderIndex' => $orderIndex,
                'assigneeUsername' => $assigneeUsername,
            ));
        } else {
            wp_send_json_success(array('status' => "Data doesn't match"));
        }
        // Make sure to exit after sending the response
        exit();
    }

    
    public function registerHandlers() {
        // Hook the clickup_status_handle_form_submission method to the appropriate WordPress AJAX action
        add_action('wp_ajax_clickup_status_handle_form_submission', array($this, 'clickup_status_handle_form_submission'));
        add_action('wp_ajax_nopriv_clickup_status_handle_form_submission', array($this, 'clickup_status_handle_form_submission'));
    }
}
