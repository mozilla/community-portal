<?php

function mozilla_mailchimp_subscribe() {

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['campaign']) && intval($_POST['campaign']) > 0) {
            $user = wp_get_current_user();

            // Only accepting logged in users at the moment
            if($user->ID !== 0 && isset($user->data->user_email)) {
                $campaign = intval($_POST['campaign']);    
                $post = get_post($campaign);

                if($post && $post->post_type === 'campaign') {
                    $result = mozilla_add_email_to_list($campaign, $user->data->user_email);
                    if(isset($result['id'])) {
                        print json_encde(Array('status' =>  'OK'));
                    }
                } else {
                    print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'Invalid content'));
                }
            } else {
                print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'Invalid user'));
            }
        } else {
            print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'Invalid request'));
        }
    } else {
        print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'Invalid request'));
    }

    die();
}



?>