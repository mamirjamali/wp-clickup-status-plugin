<?php
namespace CUSTATUS\ClickUpStatusPlugin;

class ClickUpApi {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    /**
     * Send data to ClickUp CRM.
     *
     * @param string $posted_data
     * @return string JSON response from ClickUp API
     */
    public function sendingDataToClickUpCRM($posted_data) {
        //    In the next version this will be retrive from Admin dashboard
        $listId = "123456789"; // Replace with your actual ClickUp List ID

        $curl = curl_init();

        $payload = array(
            "name" => "Name of the taks in ClickUp",//    In the next version this will be retrive from Admin dashboard
            //    In the next version this will be retrive from Admin dashboard
            "description" => $posted_data['your-subject'] . "\n\n" . $posted_data['phone-number']."\n\n". $posted_data['your-email'], // Date grabed from the Form
            "assignees" => array(
                12354567, // //    In the next version this will be retrive from Admin dashboard
            ),
            "due_date_time" => false,
            "start_date_time" => false,          
        );

        curl_setopt_array($curl,  [
            CURLOPT_HTTPHEADER => [
                "Authorization: pk_" . $this->api_key,
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
            return "cURL Error #:" . $error;
        } else {
            return $response;
        }
    }

    /**
     * Get task status from ClickUp API.
     *
     * @param string $taskId
     * @return string JSON response from ClickUp API
     */
    public function getTaskStatus($taskId) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: pk_" . $this->api_key,
            ],
            CURLOPT_URL => "https://api.clickup.com/api/v2/task/" . $taskId . "?",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return "cURL Error #:" . $error;
        } else {
            return $response;
        }
    }
}
