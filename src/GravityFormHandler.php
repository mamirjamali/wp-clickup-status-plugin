<?php


namespace CUSTATUS\ClickUpStatusPlugin;

use CUSTATUS\ClickUpStatusPlugin\FileHandler;
use CUSTATUS\ClickUpStatusPlugin\ClickUpApi;

class GravityFormHandler {
    private $click_up_database;
    private $email_sender;

    public function __construct(
        ClickUpDatabase $click_up_database,
        EmailSender $email_sender
    ) {
        // Assign injected instances
        $this->click_up_database = $click_up_database;
        $this->email_sender = $email_sender;

        // Gravity Forms hooks
        add_action('gform_after_submission', array($this, 'handleGravityFormSubmission'), 10, 2);
        add_filter('gform_confirmation', array($this, 'customizeConfirmationMessage'), 99999999, 4);
    }

    public function handleGravityFormSubmission($entry, $form) {
        // Check if the submitted form has the desired form ID

        $submitted_form_id = $form['id']; // Replace with your actual Gravity Form ID
        $settings = $this->get_click_up_settings();

        if(isset($settings[$submitted_form_id]['setting']) && isset($settings[$submitted_form_id]['setting']['task_name'])) {
            foreach ($settings as $form_id => $form_data) {
                if ($submitted_form_id == $form_id) {
    
                    $list_id = isset($form_data['setting']['list_id']) ?$form_data['setting']['list_id']: '';
                    $api_key = isset($form_data['setting']['api_key']) ? $form_data['setting']['api_key'] :'';
                    $task_name_field_id = isset($form_data['setting']['task_name']) ? $form_data['setting']['task_name']: '';
                    $email_field_id = isset($form_data['setting']['email_field_id']) ? $form_data['setting']['email_field_id']: '';
                    $gf_tracking_form_field = isset($form_data['setting']['gf_tracking_code_field']) ? $form_data['setting']['gf_tracking_code_field']: '';
    
                    $posted_data = $this->preparePostedData($entry, $form_data, $task_name_field_id);
    
                    $click_up_api = new ClickUpApi($api_key, $list_id);
    
                    try {
                        // Send data to ClickUp CRM
                        $response = $click_up_api->sendingDataToClickUpCRM($posted_data);
     
                        // Assuming $response contains the ClickUp ID
                        $decoded_response = json_decode($response, true);
    
                        
                        if (isset($decoded_response['id'])) {
                            $clickUpId = $decoded_response['id'];
    
    
                            // Send email the field has been set
                            if(!empty($email_field_id)){
                                // Create a hash using SHA-256 and truncate it to 6 characters based on the Form ID and the name of the Task on ClickUP
                                $hash = substr(hash('sha256', $posted_data['task_name']. $entry['id']), 0, 6);

                                if(!empty($gf_tracking_form_field)){
                                    // Set the value of the hidden field in the form entry
                                    gform_update_meta($entry['id'], $gf_tracking_form_field, $hash);
                                }

                                $email = $entry[$email_field_id];
                                $this->email_sender->sendEmail($email, $hash);

                                // Save data to the database
                                 $this->click_up_database::insertClickUpTaskData($email, $clickUpId, $hash, $form_id);

                            }
    
                            // Set a flag or perform a conditional check for success
                            $form_submission_successful = true;
                        }
    
                    } catch (\Exception $e) {
                        // Handle the exception (log, notify, etc.)
                        error_log('Error sending data to ClickUp: ' . $e->getMessage());
                    }
                }
            }
        }

    }

    private function preparePostedData($entry, $form_data, $task_name_field_id) {
        $posted_data = ['fields' => [], 'attachment' => [], 'custom_fields' => []];
    
        // Gravity Form choose field id 1 for name and last name and save them in two different id - 1.3, 1.6
        $task_name_field_data = $task_name_field_id == '1' ? (isset($entry['1.3']) && isset($entry['1.6']) ? $entry['1.3'] . ' ' . $entry['1.6'] : '') : $entry[$task_name_field_id];
    
        $posted_data['task_name'] = $task_name_field_data;
    
        if (isset($form_data['dynamic_fields']) && !empty($form_data['dynamic_fields'])) {
            foreach ($form_data['dynamic_fields'] as $field_name => $field_data) {
                $field_id = $field_data[0];
                $field_type = $field_data[1];
                $custom_field = isset($field_data[2]) ?$field_data[2]:  false; // Check if the field has the related custom field id

                // Gravity Form choose field id 1 for name and last name and save them in two different id - 1.3, 1.6
                $entry[$field_id] = $field_id == '1' && isset($entry['1.3']) && isset($entry['1.6']) ? $entry['1.3'] . ' ' . $entry['1.6'] : $entry[$field_id];
    
                if (isset($entry[$field_id])) {
                    if ($field_type == 'attachment') {
                        $file_url = $entry[$field_id];
                        $file_info = FileHandler::getFileInfo($file_url);
                        $posted_data['attachment'][] = $file_info;
                    }elseif( $custom_field ){
                        $posted_data['custom_fields'][$field_name] = array( 'value' => $entry[$field_id],'id' =>$field_data[2]);
                    }else {
                        $posted_data['fields'][$field_name] = $entry[$field_id];
                    }
                }
            }
    
            return $posted_data;
        } else {
            return $posted_data;
        }
    }
    

    /**
     * Customize the confirmation message to include the hash code.
     *
     * @param string $confirmation The confirmation message.
     * @param array $form The form object.
     * @param array $entry The entry object.
     * @param bool $ajax True if the form was submitted via AJAX, false otherwise.
     * @return string The modified confirmation message.
     */
    public function customizeConfirmationMessage($confirmation, $form, $entry, $ajax) {

        $submitted_form_id = $form['id']; // Replace with your actual Gravity Form ID
        $settings = $this->get_click_up_settings();
        if(isset($settings[$submitted_form_id]['setting']) && isset($settings[$submitted_form_id]['setting']['email_field_id'])) {
                $task_name_field_id = $settings[$submitted_form_id]['setting']['task_name'];
                $task_name_field_data = $task_name_field_id == '1' ? (isset($entry['1.3']) && isset($entry['1.6']) ? $entry['1.3'] . ' ' . $entry['1.6'] : '') : $entry[$task_name_field_id];
    
                // Create a hash using SHA-256 and truncate it to 6 characters
                $hash = substr(hash('sha256', $task_name_field_data. $entry['id']), 0, 6);
    
                $email_field_id = $settings[$submitted_form_id]['setting']['email_field_id'];
    
                $confirmation = '<div style="align-items:center;text-align: center;">
                                     <p style="text-align: center;"><span style="color: #339966;">
                                        <strong>درخواست شما با موفقیت دریافت شد <img class="alignnone wp-image-26" src="http://clickup-test.local/wp-content/uploads/2024/02/check.png" alt="" width="21" height="21"></strong></span>
                                     </p>';
                
                // Add the hash code to the confirmation message
                $confirmation .= '<p style="direction:rtl; display: inline-grid;"><b>کد پیگیری شما: ' . $hash . '</b>
                </br><button class="btn btn-primary mb-2 btn-align" onclick="copyToClipboard(\'' . $hash . '\')">کپی کد</button></p>
                <script>
                function copyToClipboard(text) {
                        var textArea = document.createElement("textarea");
                        textArea.value = text;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand("Copy");
                        document.body.removeChild(textArea);
                        alert("Hash code copied to clipboard!");
                    }
                </script>';
    
                $confirmation .= '</div>';
    
                return $confirmation;
    
        }

        return $confirmation;
    }

    // Retrieve click_up_setting_fields from the database
    private function get_click_up_settings() {
        return get_option('click_up_setting_fields', array());
    }
}
