<?php 
function mozilla_unsubscribe_mailchimp_member() {
	$user = wp_get_current_user()->data;
	$options = wp_load_alloptions();
	$campaignId = $_REQUEST['campaignId'];
	$subscriberHash = md5(strtolower('ky@playgroundinc.com'));

    if(isset($options['mailchimp'])) {
        $apikey = trim($options['mailchimp']);
        $dc = substr($apikey, -3);
        if($dc) {
            
            $curl = curl_init();
            $api_url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$campaignId}/members/{$subscriberHash}";
            $auth_string = "user:{$apikey}";
            $auth = base64_encode($auth_string);

            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
            curl_setopt($curl, CURLOPT_USERPWD, 'user:' . $apikey);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");

            $data['status'] = "unsubscribed";

            $json = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            $result = curl_exec($curl);
            curl_close($curl);

			$json_result = json_decode($result);
			return;
        }
    }

    return false;
}