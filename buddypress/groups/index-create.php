<?php
    // Main header template 
    get_header(); 
    do_action('bp_before_create_group_page'); 

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
?>
<div class="content">
    <div class="create-group">
        <div class="create-group__container">
            <?php do_action('bp_before_create_group_content_template'); ?>
            <form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form create-group__form" enctype="multipart/form-data">
                <?php print wp_nonce_field('protect_content', 'my_nonce_field'); ?>
                <?php do_action('bp_before_create_group'); ?>
                <section class="create-group__nav">
                    <ul class="create-group__menu">
                        <li class="create-group__menu-item"><?php print strtoupper(__("Basic Information")); ?></li>
                        <li class="create-group__menu-item create-group__menu-item--disabled"><?php print strtoupper(__("TERMS AND RESPONSIBILITIES")); ?></li>
                    </ul>
                </section>
                <section class="create-group__details">
                    <h1 class="create-group__title"><?php print __("Create a Mozilla Group"); ?></h1>
                    <div class="create-group__input-container">
                        <label class="create-group__label" for="group-name"><?php print __("What is your group's name?"); ?></label>
                        <input type="text" name="group_name" id="group-name" class="create-group__input" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--short">
                        <label class="create-group__label" for="group-desc"><?php print __("Online or Offline Group *"); ?></label>
                        <label class="create-group__radio-container">
                            <?php print __("Online"); ?>
                            <input type="radio" name="group_type" id="group-type" value="<?php print __("Online"); ?>" />
                            <span class="create-group__radio"></span>
                        </label>
                        <label class="create-group__radio-container">
                            <?php print __("Offline"); ?>
                            <input type="radio" name="group_type" id="group-type" value="<?php print __("Offline"); ?>" />
                            <span class="create-group__radio"></span>
                        </label>
                    </div>
                    <div class="create-group__input-container">
                        <label class="create-group__label" for="group-desc"><?php print __("Description *"); ?></label>
                        <textarea name="group_desc" id="group-desc" class="create-group__textarea"></textarea>
                    </div>
                    <div class="create-group__input-container create-group__input-container--short">
                        <label class="create-group__label" for="group-desc"><?php print __("Group Photo"); ?></label>
                        <div id="group-photo-uploader" class="create-group__image-upload">
                           
                        </div>
                        <input type="hidden" name="image_url" id="image-url" value="" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--full">
                        <label class="create-group__label"><?php print __("Tags for your group"); ?></label>
                        <?php 
                            // Get all tags
                            $tags = get_tags(array('hide_empty' => false));
                        ?>
                        <div class="create-group__tag-container">
                            <?php foreach($tags AS $tag): ?>
                                <div class="create-group__checkbox-container">
                                    <label for="tag-<?php print $tag->name; ?>"  class="create-group__label create-group__label--checkbox"><?php print __($tag->name); ?>
                                        <input id="tag-<?php print $tag->name; ?>" type="checkbox" name="tag" value="<?php print __($tag->slug); ?>" class="create-group__checkbox" />
                                        <span class="create-group__check">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <section class="create-group__details">
                    <div class="create-group__input-container create-group__input-container--full">
                        <label class="create-group__label"><?php print __("Where is your group from ?"); ?></label>
                        <div class="create-group__select-container create-group__select-container--inline">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                                </g>
                            </svg>
                            <select class="create-group__select">
                                <option value="0">Country</option>
                                <?php foreach($countries AS $code => $country): ?>
                                <option value="<?php print $code; ?>"><?php print __($country); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="text" name="group_city" id="group-city" class="create-group__input create-group__input--inline" placeholder="<?php print __("Type your city"); ?>" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--location">
                        <label class="create-group__label"><?php print __("Where do you meet?"); ?></label>
                        <input type="text" name="group_city" id="group-city" class="create-group__input create-group__input--location" placeholder="<?php print __("Address"); ?>" />

                    </div>
                </section>
                <section class="create-group__details">
                    <div class="create-group__input-container">
                        <label class="create-group__label"><?php print __("Community Links"); ?></label>
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline"><?php print __("Discourse"); ?></label><input type="text" name="group_discourse" id="group-discourse" class="create-group__input create-group__input--inline" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline"><?php print __("GitHub"); ?></label><input type="text" name="group_github" id="group-github" class="create-group__input create-group__input--inline" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline"><?php print __("Facebook"); ?></label><input type="text" name="group_facebook" id="group-facebook" class="create-group__input create-group__input--inline" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline"><?php print __("Twitter"); ?></label><input type="text" name="group_twitter" id="group-twitter" class="create-group__input create-group__input--inline" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline"><?php print __("Telegram"); ?></label><input type="text" name="group_telegram" id="group-telegram" class="create-group__input create-group__input--inline" />
                    </div>
                </section>
                <section class="create-group__cta-container">
                    <input type="button" class="create-group__cta" value="<?php print strtoupper(__("Continue")); ?>" />
                </section>
            </form>
        </div>
    </div>
</div>
<?php 
    do_action('bp_after_create_group_page');
    get_footer();

?>

