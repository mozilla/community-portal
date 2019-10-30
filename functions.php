<?php
// Mozilla theme functions file

// Remove the admin header styles for homepage
add_action('get_header', 'remove_admin_login_header');

// Native Wordpress Actions
add_action('init', 'mozilla_custom_menu');
add_action('wp_enqueue_scripts', 'mozilla_init_scripts');

// Ajax Calls
add_action('wp_ajax_nopriv_upload_group_image', 'mozilla_upload_image');
add_action('wp_ajax_upload_group_image', 'mozilla_upload_image');
add_action('wp_ajax_join_group', 'mozilla_join_group');
add_action('wp_ajax_leave_group', 'mozilla_leave_group');
add_action('wp_ajax_get_users', 'mozilla_get_users');
add_action('wp_ajax_validate_email', 'mozilla_validate_email');
add_action('wp_ajax_nopriv_validate_group', 'mozilla_validate_group_name');
add_action('wp_ajax_validate_group', 'mozilla_validate_group_name');
add_action('wp_ajax_check_user', 'mozilla_validate_username');


// Buddypress Actions
add_action('bp_before_create_group_page', 'mozilla_create_group', 10, 1);
add_action('bp_before_edit_member_page', 'mozilla_update_member', 10, 1);


// Auth0 Actions
add_action('auth0_user_login', 'mozilla_post_user_creation', 10, 6);

// Filters
add_filter('nav_menu_link_attributes', 'mozilla_add_menu_attrs', 10, 3);
add_filter('nav_menu_css_class', 'mozilla_add_active_page' , 10 , 2);


// Include theme style.css file not in admin page
if(!is_admin()) 
    wp_enqueue_style('style', get_stylesheet_uri());

$countries = Array(
    "AF" => "Afghanistan",
    "AL" => "Albania",
    "DZ" => "Algeria",
    "AS" => "American Samoa",
    "AD" => "Andorra",
    "AO" => "Angola",
    "AI" => "Anguilla",
    "AQ" => "Antarctica",
    "AG" => "Antigua and Barbuda",
    "AR" => "Argentina",
    "AM" => "Armenia",
    "AW" => "Aruba",
    "AU" => "Australia",
    "AT" => "Austria",
    "AZ" => "Azerbaijan",
    "BS" => "Bahamas",
    "BH" => "Bahrain",
    "BD" => "Bangladesh",
    "BB" => "Barbados",
    "BY" => "Belarus",
    "BE" => "Belgium",
    "BZ" => "Belize",
    "BJ" => "Benin",
    "BM" => "Bermuda",
    "BT" => "Bhutan",
    "BO" => "Bolivia",
    "BA" => "Bosnia and Herzegovina",
    "BW" => "Botswana",
    "BV" => "Bouvet Island",
    "BR" => "Brazil",
    "IO" => "British Indian Ocean Territory",
    "BN" => "Brunei Darussalam",
    "BG" => "Bulgaria",
    "BF" => "Burkina Faso",
    "BI" => "Burundi",
    "KH" => "Cambodia",
    "CM" => "Cameroon",
    "CA" => "Canada",
    "CV" => "Cape Verde",
    "KY" => "Cayman Islands",
    "CF" => "Central African Republic",
    "TD" => "Chad",
    "CL" => "Chile",
    "CN" => "China",
    "CX" => "Christmas Island",
    "CC" => "Cocos (Keeling) Islands",
    "CO" => "Colombia",
    "KM" => "Comoros",
    "CG" => "Congo",
    "CD" => "Congo, the Democratic Republic of the",
    "CK" => "Cook Islands",
    "CR" => "Costa Rica",
    "CI" => "Cote D'Ivoire",
    "HR" => "Croatia",
    "CU" => "Cuba",
    "CY" => "Cyprus",
    "CZ" => "Czech Republic",
    "DK" => "Denmark",
    "DJ" => "Djibouti",
    "DM" => "Dominica",
    "DO" => "Dominican Republic",
    "EC" => "Ecuador",
    "EG" => "Egypt",
    "SV" => "El Salvador",
    "GQ" => "Equatorial Guinea",
    "ER" => "Eritrea",
    "EE" => "Estonia",
    "ET" => "Ethiopia",
    "FK" => "Falkland Islands (Malvinas)",
    "FO" => "Faroe Islands",
    "FJ" => "Fiji",
    "FI" => "Finland",
    "FR" => "France",
    "GF" => "French Guiana",
    "PF" => "French Polynesia",
    "TF" => "French Southern Territories",
    "GA" => "Gabon",
    "GM" => "Gambia",
    "GE" => "Georgia",
    "DE" => "Germany",
    "GH" => "Ghana",
    "GI" => "Gibraltar",
    "GR" => "Greece",
    "GL" => "Greenland",
    "GD" => "Grenada",
    "GP" => "Guadeloupe",
    "GU" => "Guam",
    "GT" => "Guatemala",
    "GN" => "Guinea",
    "GW" => "Guinea-Bissau",
    "GY" => "Guyana",
    "HT" => "Haiti",
    "HM" => "Heard Island and Mcdonald Islands",
    "VA" => "Holy See (Vatican City State)",
    "HN" => "Honduras",
    "HK" => "Hong Kong",
    "HU" => "Hungary",
    "IS" => "Iceland",
    "IN" => "India",
    "ID" => "Indonesia",
    "IR" => "Iran, Islamic Republic of",
    "IQ" => "Iraq",
    "IE" => "Ireland",
    "IL" => "Israel",
    "IT" => "Italy",
    "JM" => "Jamaica",
    "JP" => "Japan",
    "JO" => "Jordan",
    "KZ" => "Kazakhstan",
    "KE" => "Kenya",
    "KI" => "Kiribati",
    "KP" => "Korea, Democratic People's Republic of",
    "KR" => "Korea, Republic of",
    "KW" => "Kuwait",
    "KG" => "Kyrgyzstan",
    "LA" => "Lao People's Democratic Republic",
    "LV" => "Latvia",
    "LB" => "Lebanon",
    "LS" => "Lesotho",
    "LR" => "Liberia",
    "LY" => "Libyan Arab Jamahiriya",
    "LI" => "Liechtenstein",
    "LT" => "Lithuania",
    "LU" => "Luxembourg",
    "MO" => "Macao",
    "MK" => "Macedonia, the Former Yugoslav Republic of",
    "MG" => "Madagascar",
    "MW" => "Malawi",
    "MY" => "Malaysia",
    "MV" => "Maldives",
    "ML" => "Mali",
    "MT" => "Malta",
    "MH" => "Marshall Islands",
    "MQ" => "Martinique",
    "MR" => "Mauritania",
    "MU" => "Mauritius",
    "YT" => "Mayotte",
    "MX" => "Mexico",
    "FM" => "Micronesia, Federated States of",
    "MD" => "Moldova, Republic of",
    "MC" => "Monaco",
    "MN" => "Mongolia",
    "MS" => "Montserrat",
    "MA" => "Morocco",
    "MZ" => "Mozambique",
    "MM" => "Myanmar",
    "NA" => "Namibia",
    "NR" => "Nauru",
    "NP" => "Nepal",
    "NL" => "Netherlands",
    "AN" => "Netherlands Antilles",
    "NC" => "New Caledonia",
    "NZ" => "New Zealand",
    "NI" => "Nicaragua",
    "NE" => "Niger",
    "NG" => "Nigeria",
    "NU" => "Niue",
    "NF" => "Norfolk Island",
    "MP" => "Northern Mariana Islands",
    "NO" => "Norway",
    "OM" => "Oman",
    "PK" => "Pakistan",
    "PW" => "Palau",
    "PS" => "Palestinian Territory, Occupied",
    "PA" => "Panama",
    "PG" => "Papua New Guinea",
    "PY" => "Paraguay",
    "PE" => "Peru",
    "PH" => "Philippines",
    "PN" => "Pitcairn",
    "PL" => "Poland",
    "PT" => "Portugal",
    "PR" => "Puerto Rico",
    "QA" => "Qatar",
    "RE" => "Reunion",
    "RO" => "Romania",
    "RU" => "Russian Federation",
    "RW" => "Rwanda",
    "SH" => "Saint Helena",
    "KN" => "Saint Kitts and Nevis",
    "LC" => "Saint Lucia",
    "PM" => "Saint Pierre and Miquelon",
    "VC" => "Saint Vincent and the Grenadines",
    "WS" => "Samoa",
    "SM" => "San Marino",
    "ST" => "Sao Tome and Principe",
    "SA" => "Saudi Arabia",
    "SN" => "Senegal",
    "CS" => "Serbia and Montenegro",
    "SC" => "Seychelles",
    "SL" => "Sierra Leone",
    "SG" => "Singapore",
    "SK" => "Slovakia",
    "SI" => "Slovenia",
    "SB" => "Solomon Islands",
    "SO" => "Somalia",
    "ZA" => "South Africa",
    "GS" => "South Georgia and the South Sandwich Islands",
    "ES" => "Spain",
    "LK" => "Sri Lanka",
    "SD" => "Sudan",
    "SR" => "Suriname",
    "SJ" => "Svalbard and Jan Mayen",
    "SZ" => "Swaziland",
    "SE" => "Sweden",
    "CH" => "Switzerland",
    "SY" => "Syrian Arab Republic",
    "TW" => "Taiwan, Province of China",
    "TJ" => "Tajikistan",
    "TZ" => "Tanzania, United Republic of",
    "TH" => "Thailand",
    "TL" => "Timor-Leste",
    "TG" => "Togo",
    "TK" => "Tokelau",
    "TO" => "Tonga",
    "TT" => "Trinidad and Tobago",
    "TN" => "Tunisia",
    "TR" => "Turkey",
    "TM" => "Turkmenistan",
    "TC" => "Turks and Caicos Islands",
    "TV" => "Tuvalu",
    "UG" => "Uganda",
    "UA" => "Ukraine",
    "AE" => "United Arab Emirates",
    "GB" => "United Kingdom",
    "US" => "United States",
    "UM" => "United States Minor Outlying Islands",
    "UY" => "Uruguay",
    "UZ" => "Uzbekistan",
    "VU" => "Vanuatu",
    "VE" => "Venezuela",
    "VN" => "Viet Nam",
    "VG" => "Virgin Islands, British",
    "VI" => "Virgin Islands, U.s.",
    "WF" => "Wallis and Futuna",
    "EH" => "Western Sahara",
    "YE" => "Yemen",
    "ZM" => "Zambia",
    "ZW" => "Zimbabwe"
);

abstract class PrivacySettings {
    const REGISTERED_USERS = 0;
    const PUBLIC_USERS = 1; 
    const PRIVATE_USERS = 2;
}


function remove_admin_login_header() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}

function mozilla_custom_menu() {
    register_nav_menu('mozilla-theme-menu', __('Mozilla Custom Theme Menu'));
}

function mozilla_add_menu_attrs($attrs, $item, $args) {
    $attrs['class'] = 'menu-item__link';
    return $attrs;
}

function mozilla_add_active_page($classes, $item) {

    $pagename = strtolower(get_query_var('pagename'));  
    if($pagename === strtolower($item->post_name)) {
        $classes[] = 'menu-item--active';
    }

    return $classes;
}

function mozilla_init_scripts() {

    // Vendor scripts
    wp_enqueue_script('dropzonejs', get_stylesheet_directory_uri()."/js/vendor/dropzone.min.js", array('jquery'));
    wp_enqueue_script('autcomplete', get_stylesheet_directory_uri()."/js/vendor/autocomplete.js", array('jquery'));

    // Custom scripts
    wp_enqueue_script('groups', get_stylesheet_directory_uri()."/js/groups.js", array('jquery'));
    wp_enqueue_script('nav', get_stylesheet_directory_uri()."/js/nav.js", array('jquery'));
    wp_enqueue_script('profile', get_stylesheet_directory_uri()."/js/profile.js", array('jquery'));

}

// If the create group page is called create a group 
function mozilla_create_group() {

    if(is_user_logged_in()) {
        $required = Array(
            'group_name',
            'group_type',
            'group_desc',
            'group_address',
            'my_nonce_field'
        );

        $optional = Array(
            'image_url',
            'group_address_type',
            'group_address',
            'group_meeting_details',
            'group_discourse',
            'group_facebook',
            'group_telegram',
            'group_github',
            'group_twitter',
            'group_other'
        );

        // If we're posting data lets create a group
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_POST['step']) && isset($_POST['my_nonce_field']) && wp_verify_nonce($_REQUEST['my_nonce_field'], 'protect_content')) {
                switch($_POST['step']) {
                    case '1':
                        // Gather information
                        $error = false;
                        foreach($required AS $field) {
                            if(isset($_POST[$field])) {
                                if($_POST[$field] === "" || $_POST[$field] === 0) {
                                    $error = true;
                                }
                            }
                            
                            if(isset($_POST['group_type']) && trim(strtolower($_POST['group_type'])) == 'offline') {
                                if(!isset($_POST['group_country']) || $_POST['group_country'] == '0')  {
                                    $error = true;
                                }

                                if(!isset($_POST['group_city']) || $_POST['group_city'] === '') {
                                    $error = true;
                                }
                            }
                        }
  
                        $_SESSION['form'] = $_POST;

                        // Cleanup
                        if($error) {
                            if(isset($_SESSION['uploaded_file']) && file_exists($_SESSION['uploaded_file'])) {
                                $image = getimagesize($_SESSION['uploaded_file']);
                                if(isset($image[2]) && in_array($image[2], Array(IMAGETYPE_JPEG ,IMAGETYPE_PNG))) {
                                    unlink($_SESSION['uploaded_file']);
                                }
                            }

                            $_POST['step'] = 0;                           
                        }
                        
                        break;
                    case 2:

                        if(isset($_POST['agree']) && $_POST['agree']) {
                            $args = Array(
                                'group_id'  =>  0,
                            );
                            
                            $args['name'] = $_POST['group_name'];
                            $args['description'] = $_POST['group_desc'];
                            $args['status'] = 'private';
                            
                            $group_id = groups_create_group($args);
                            $meta = Array();

                            if($group_id) {
                                // Loop through optional fields and save to meta
                                foreach($optional AS $field) {
                                    if(isset($_POST[$field]) && $_POST[$field] !== "") {
                                        $meta[$field] = trim(sanitize_text_field($_POST[$field]));
                                    }
                                }

                                if(isset($_POST['group_admin_id']) && $_POST['group_admin_id']) {
                                    groups_promote_member(intval($_POST['group_admin_id']), $group_id, 'admin');
                                }

                                // Required information but needs to be stored in meta data because buddypress does not support these fields
                                $meta['group_image_url'] = trim(sanitize_text_field($_POST['image_url']));
                                $meta['group_city'] = trim(sanitize_text_field($_POST['group_city']));
                                $meta['group_address'] = trim(sanitize_text_field($_POST['group_address']));
                                $meta['group_country'] = trim(sanitize_text_field($_POST['group_country']));
                                $meta['group_type'] = trim(sanitize_text_field($_POST['group_type']));
                    

                                if(isset($_POST['tags'])) {
                                    $tags = explode(',', $_POST['tags']);
                                    $meta['group_tags'] = array_filter($tags);
                                }

                                $result = groups_update_groupmeta($group_id, 'meta', $meta);
                                // Could not update group information so reset form
                                if($result) {
                                    unset($_SESSION['form']);
                                    $_POST = Array();
                                    $_POST['step'] = 3;
                                } else {
                                    groups_delete_group($group_id);
                                    $_POST['step'] = 0;
                                }                                
                            }
                        } else {
                            $_POST['step'] = 2;
                        }

                        break;
                        
                }
            }
        } else {
            unset($_SESSION['form']);
        }
    } else {
        wp_redirect("/");
    }
}

function mozilla_upload_image() {

    if(!empty($_FILES) && wp_verify_nonce($_REQUEST['my_nonce_field'], 'protect_content')) {
        $image = getimagesize($_FILES['file']['tmp_name']);

        if(isset($image[2]) && in_array($image[2], Array(IMAGETYPE_JPEG ,IMAGETYPE_PNG))) {
            $uploaded_bits = wp_upload_bits($_FILES['file']['name'], null, file_get_contents($_FILES['file']['tmp_name']));
            
            if (false !== $uploaded_bits['error']) {
                
            } else {
                $uploaded_file     = $uploaded_bits['file'];
                $_SESSION['uploaded_file'] = $uploaded_bits['file'];
                $uploaded_url      = $uploaded_bits['url'];
                $uploaded_filetype = wp_check_filetype(basename($uploaded_bits['file'] ), null);
        
                print $uploaded_url;
            }
        }
    }
	die();
}

function mozilla_validate_group_name() {
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['q'])) {
            $query = $_GET['q'];
            $group = mozilla_search_groups($query);

            if(isset($group['total']) && $group['total'] == 0) {
                print json_encode(true);
            } else {
                print json_encode(false);
            }
            die();
        }
    }
}

function mozilla_search_groups($name) {
    $groups = groups_get_groups();
    $group_array = $groups['groups'];

    $group = array_filter($groups, function($object) {
        return trim(strtolower($object->name)) === trim(strtolower($name));
    });

    return $group;
}

function mozilla_validate_username() {

    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['u']) && strlen($_GET['u']) > 0) {
            $u = sanitize_text_field(trim($_GET['u']));
            $current_user_id = get_current_user_id();

            $query = new WP_User_Query(Array(
                'search'            =>  $u,
                'search_columns'    =>  Array(
                    'user_nicename'
                ),
                'exclude'   => Array($current_user_id)
            ));
   
            print (sizeof($query->get_results()) === 0) ? json_encode(true) : json_encode(false);
        }
    }
    die();
}

function mozilla_validate_email() {

    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['u']) && strlen($_GET['u']) > 0) {
            $u = sanitize_text_field(trim($_GET['u']));
            $current_user_id = get_current_user_id();

            $query = new WP_User_Query(Array(
                'search'            =>  $u,
                'search_columns'    =>  Array(
                    'user_email'
                ),
                'exclude'   => Array($current_user_id)
            ));
   
            print (sizeof($query->get_results()) === 0) ? json_encode(true) : json_encode(false);
        }
    }
    die();
}

function mozilla_get_users() {
    $json_users = Array();

    if(isset($_GET['q']) && $_GET['q']) {
        $q = esc_attr(trim($_GET['q']));
        $current_user_id = get_current_user_id();

        $query = new WP_User_Query(Array(
            'search'            =>  "*{$q}*",
            'search_columns'    =>  Array(
                'user_nicename'
            ),
            'exclude'   => Array($current_user_id)
        ));

        print json_encode($query->get_results());

    }
    die();
}

function mozilla_join_group() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = wp_get_current_user();
        if($user) {
            if(isset($_POST['group']) && $_POST['group']) {
                $joined = groups_join_group(intval(trim($_POST['group'])), $user->ID);
                if($joined) {
                    print json_encode(Array('status'   =>  'success', 'msg'  =>  'Joined Group'));
                } else {
                    print json_encode(Array('status'   =>  'error', 'msg'   =>  'Could not join group'));
                }
                die();
            } 
        }
    }

    print json_encode(Array('status'    =>  'error', 'msg'  =>  'Invalid Request'));
    die();
}

function mozilla_leave_group() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = wp_get_current_user();
        if($user) {
            if(isset($_POST['group']) && $_POST['group']) {
                $group = intval(trim($_POST['group']));
                if(!groups_is_user_admin($user->ID, $group)) {
                    $left = groups_leave_group($group, $user->ID);
                    if($left) {
                        print json_encode(Array('status'   =>  'success', 'msg'  =>  'Left Group'));
                    } else {
                        print json_encode(Array('status'   =>  'error', 'msg'   =>  'Could not leaev group'));
                    }
                } else {
                    print json_encode(Array('status'   =>  'error', 'msg'   =>  'Admin cannot leave a group'));
                }
                die();
            }
        }
    }

    print json_encode(Array('status'    =>  'error', 'msg'  =>  'Invalid Request'));
    die();
}

function mozilla_post_user_creation($user_id, $userinfo, $is_new, $id_token, $access_token, $refresh_token ) {
    $meta = get_user_meta($user_id);

    if($is_new || !isset($meta['agree'][0]) || (isset($meta['agree'][0]) && $meta['agree'][0] != 'I Agree')) {
        $user = get_user_by('ID', $user_id);
        wp_redirect("/members/{$user->data->user_nicename}/profile/edit/group/1/");
        die();        
    }
}


function mozilla_update_member() {

    // Submited Form
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(is_user_logged_in()) {
            $user = wp_get_current_user()->data;

            // Get current meta to compare to
            $meta = get_user_meta($user->ID);

            $required = Array(
                'username',
                'username_visibility',
                'first_name',
                'first_name_visibility',
                'last_name',
                'last_name_visibility',
                'email',
                'email_visibility',
                'agree'
            );

            $additional_fields = Array(
                'image_url',
                'profile_image_visibility',
                'pronoun',
                'profile_pronoun_visibility',
                'bio',
                'profile_bio_visibility',
            );

            // Add additional required fields after initial setup
            if(isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree') {
                unset($required[8]);
                $required[] = 'city';
                $required[] = 'country';
                $required[] = 'profile_location_visibility';

            }

            $error = false;

            foreach($required AS $field) {
                if(isset($_POST[$field])) {
                    if($_POST[$field] === "" || $_POST[$field] === 0) {
                        $error = true;
                    }
                }
            }

            // Validate email and username
            if($error === false) {

                if(!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
                    $error = true;
                    $_POST['email_error_message'] = 'Invalid email address';
                }


                $query = new WP_User_Query(Array(
                    'search'            =>  sanitize_text_field(trim($_POST['email'])),
                    'search_columns'    =>  Array(
                        'user_email'
                    ),
                    'exclude'   => Array($user->ID)
                ));

                if(sizeof($query->get_results()) !== 0) {
                    $error = true;
                    $_POST['email_error_message'] = 'This email is already in use';
                }

                $query = new WP_User_Query(Array(
                    'search'            =>  sanitize_text_field(trim($_POST['username'])),
                    'search_columns'    =>  Array(
                        'user_nicename'
                    ),
                    'exclude'   => Array($user->ID)
                ));

                // Validate email

                if(sizeof($query->get_results()) !== 0) {
                    $_POST['username_error_message'] = 'This username is already in use';
                    $error = true;
                }
            }
           
            // Create the user and save meta data
            if($error === false) {

                $_POST['complete'] = true;

                // Update regular wordpress user data
                $data = Array(
                    'ID'            =>  $user->ID,
                    'user_email'    =>  sanitize_text_field(trim($_POST['email'])),
                );

                // We need to udpate the user
                if($_POST['username'] !== $user->user_nicename) {
                    $data['user_nicename'] = sanitize_text_field(trim($_POST['username']));
                }

                wp_update_user($data);

                // No longe need this key
                unset($required[0]);

                foreach($required AS $field) {
                    $form_data = sanitize_text_field(trim($_POST[$field]));
                    update_user_meta($user->ID, $field, $form_data);
                }


                // Update other fields here
                $addtional_meta = Array();

                


                foreach($additional_fields AS $field) {
                    if(isset($_POST[$field])) {
                        $additional_meta[$field] = sanitize_text_field(trim($_POST[$field]));
                    }
                }    

                update_user_meta($user->ID, 'community-meta-fields', $additional_meta);
            }
        }
    }
}

function mozilla_is_logged_in() {
    $current_user = wp_get_current_user()->data;
    return sizeof((Array)$current_user) > 0 ? true : false; 
}


function mozilla_get_user_visibility_settings($user_id) {
    $user = get_user_by('ID', $user_id);
    $meta = get_user_meta($user_id);

    $visibility_fields = Array(
                                'username',
                                'first_name',
                                'last_name',
                                'email'
    );

    $visibility_settings = Array();

    foreach($visibility_fields AS $field) {
        if(isset($meta["{$field}_visibility"][0])) {
            $visibility_settings["{$field}_visibility"] = intval($meta["{$field}_visibility"][0]);
        } else {
            if($field === 'username') {
                $visibility_settings["{$field}_visobility"] = PrivacySettings::PUBLIC_USERS;
            } else {
                $visibility_settings["{$field}_visibility"] = PrivacySettings::REGISTERED_USERS;
            }
        }
    }

    return $visibility_settings;
}

