<?php

namespace IBD\ClickUpStatusPlugin;

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
                $data .=  $field_name . ' = ' . $field_data;
            }
        }

        $payload = array(
            "name" => $posted_data['task_name'],
            "description" => $data,
            "assignees" => array(
                89423077, // Amir Jamali ClickUp ID
            ),
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
            $taskId = $decoded_response['id'];

            if (isset($posted_data['attachment'])) {
                $curl = curl_init();

                //Loop through Attachments and send them to related ClickUP tasks
                foreach ($posted_data['attachment'] as $attachment) {

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
                        CURLOPT_URL => "https://api.clickup.com/api/v2/task/" . $taskId . "/attachment?",
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
     * @param string $taskId
     * @param string $api_key
     * @return string JSON response from ClickUp API
     */
    public function getTaskStatus($task_id, $api_key) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: " . $api_key,
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
}
