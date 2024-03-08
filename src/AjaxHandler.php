<?php

namespace CUSTATUS\ClickUpStatusPlugin;

use CUSTATUS\ClickUpStatusPlugin\ClickUpDatabase;
use CUSTATUS\ClickUpStatusPlugin\ClickUpApi;

class AjaxHandler {

    private $click_up_api;
    private $click_up_database;

    public function __construct(ClickUpDatabase $click_up_database) {
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
        $result = $this->click_up_database->getTaskIdByEmailAndHash($user_email, $hash_code);
        $result = isset($result[0]) ? $result[0]: '';
        $settings = $this->get_click_up_settings();

        if (!$result || !isset($settings[$result->form_id])) {
            wp_send_json_error(array('status' => "اطلاعات وارد شده اشتباه است."));
        }

        $api_key = $settings[$result->form_id]['setting']['api_key'];
        $click_up_list_id = $settings[$result->form_id]['setting']['list_id'];

        $this->click_up_api = new ClickUpApi($api_key, $click_up_list_id);

        // Check if task_id exists before making API call
        if ($result->task_id) {
            $response = $this->click_up_api->getTaskStatus($result->task_id);
            $decoded_response_task_status = json_decode($response, true);

            if(isset($decoded_response_task_status['status'])){

                $status = $decoded_response_task_status['status']['status'];
                $orderIndex = $decoded_response_task_status['status']['orderindex'];
                $assigneeUsername = (isset($decoded_response_task_status['assignees'][0]) && isset($decoded_response_task_status['assignees'][0]['username'])) ?$decoded_response_task_status['assignees'][0]['username']: ''; // Assuming there is only one assignee
        
                // Respond with the task status (e.g., JSON)
                wp_send_json_success(array(
                    'status' => $status,
                    'orderIndex' => $orderIndex,
                    'assigneeUsername' => $assigneeUsername,
                ));
            }
        } 
        // Make sure to exit after sending the response
        exit();
    }

    
    public function registerHandlers() {
        // Hook the clickup_status_handle_form_submission method to the appropriate WordPress AJAX action
        add_action('wp_ajax_clickup_status_handle_form_submission', array($this, 'clickup_status_handle_form_submission'));
        add_action('wp_ajax_nopriv_clickup_status_handle_form_submission', array($this, 'clickup_status_handle_form_submission'));
    }

    // Retrieve click_up_setting_fields from the database
    private function get_click_up_settings() {
        return get_option('click_up_setting_fields', array());
    }
}