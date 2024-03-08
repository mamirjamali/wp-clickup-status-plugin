<?php

namespace CUSTATUS\ClickUpStatusPlugin;

class ClickUpApi {
    private $api_key;
    private $click_up_list_id;

    public function __construct($api_key, $click_up_list_id) {
        // Load new settings from Admin panel
        $this->api_key = $api_key;
        $this->click_up_list_id = $click_up_list_id;
    }

    /**
     * Send data to ClickUp CRM.
     *
     * @param array $posted_data
     * @return string JSON response from ClickUp API
     */
    public function sendingDataToClickUpCRM($posted_data) {
        $listId = $this->click_up_list_id; // Use the stored list ID

        $curl = curl_init();

        $data = "";
        if (isset($posted_data['fields'])) {
            foreach ($posted_data['fields'] as $field_name => $field_data) {
                $data .=  $field_name . ' = ' . $field_data."\n";
            }
        }

        $payload = array(
            "name" => $posted_data['task_name'],
            "description" => $data,
            "assignees" => array(),
            "due_date_time" => false,
            "start_date_time" => false,
        );

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: " . $this->api_key,
                "Content-Type: application/json",
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_URL => "https://api.clickup.com/api/v2/list/" . $listId . "/task?",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            error_log("ClickUp API Error: " . $error);
            return "cURL Error: " . $error;
        }

        $decoded_response = json_decode($response, true);

        if (isset($decoded_response['id'])) {
            $task_id = $decoded_response['id'];

            if (isset($posted_data['attachment'])) {
                $curl = curl_init();

                //Loop through Attachments and send them to related ClickUP tasks
                foreach ($posted_data['attachment'] as $attachment) {
                    $this->setTaskAttachments($task_id, $attachment);
                }
            }
            if(isset($posted_data['custom_fields'])){
                foreach($posted_data['custom_fields'] as $custom_field){
                    $this->setTaskCustomField($task_id,  $custom_field);
                }
            }
            return $response;
        } else {
            error_log("ClickUp API Error: Unexpected response - " . $response);
            return "ClickUp API Error: Unexpected response";
        }
    }

    /**
     * Get task status from ClickUp API.
     *
     * @param string $task_id
     * @param string $api_key
     * @return string JSON response from ClickUp API
     */
    public function getTaskStatus($task_id) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: " . $this->api_key,
            ],
            CURLOPT_URL => "https://api.clickup.com/api/v2/task/" . $task_id . "?",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            error_log("ClickUp API Error: " . $error);
            return "cURL Error: " . $error;
        }

        return $response;
    }

    private function setTaskAttachments($task_id, $attachment){

        $curl = curl_init();

        $file_path = $attachment['path'];
        $file_name = $attachment['name'];
        $file_mime_type = $attachment['type'];

        $file = new \CURLFile($file_path, $file_mime_type, $file_name);

        $attachment_payload = array(
            "attachment" => $file
        );

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: " . $this->api_key,
                "Content-Type: multipart/form-data",
            ],
            CURLOPT_POSTFIELDS => $attachment_payload,
            CURLOPT_URL => "https://api.clickup.com/api/v2/task/" . $task_id . "/attachment?",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $attachment_response = curl_exec($curl);
        $attachment_error = curl_error($curl);

        if ($attachment_error) {
            error_log("ClickUp Attachment Error: " . $attachment_error);
        }

        curl_close($curl);
    }

    
    private function setTaskCustomField($task_id,  $custom_field){

        $curl = curl_init();
        $value = $custom_field['value'];
        $id = $custom_field['id'];

        $payload = array(
        "value" => $value
        );

        curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => [
            "Authorization: ". $this->api_key,
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_URL => "https://api.clickup.com/api/v2/task/" . $task_id . "/field/" . $id . "?",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $custom_field_response = curl_exec($curl);
        $custom_field_error = curl_error($curl);

        if ($custom_field_error) {
            error_log("ClickUp Custom Fiels Error: " . $custom_field_error);
        } 

        curl_close($curl);

    }
}
