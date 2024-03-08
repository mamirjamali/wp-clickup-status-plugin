<?php
// Utility class for common methods
namespace CUSTATUS\ClickUpStatusPlugin\Utils;

class ClickUpApiHelper {

    //Get statuses for the defined List ID
    public static function get_forms_statuses_detaile($api_key, $list_id){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: ".$api_key
            ],
            CURLOPT_URL => "https://api.clickup.com/api/v2/list/" . $list_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $decoded_response = json_decode($response, true);
        $statuses_from_clickup = $decoded_response['statuses'];
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return "cURL Error #:" . $error;
        } else {
            return $statuses_from_clickup;   
        }
    }
}
