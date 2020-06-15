<?php

function mozilla_set_language($language) {
	if ($language === 'en') {
		return;
	}
	mozilla_wpml_redirect($language);
	return;
}

function mozilla_check_language() {
	$language_set = isset( $_COOKIE['mozilla_language'] );
	if ($language_set) {
		mozilla_set_language($_COOKIE['mozilla_language']);	
		return;
	}
	$default_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	mozilla_set_language($default_lang);
}

/**
 * Updates the website locale based on browser settings
 *
 */
function mozilla_match_browser_locale() {
	$url = $_SERVER['REQUEST_URI'];
	$wpml_languages = icl_get_languages('skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str');
	preg_match('/\b[a-zA-Z]{2}\b/', $url, $matches);
	if (wp_doing_ajax()) {
		return;
	}
	if ( isset($matches[0]) && isset($wpml_languages[$matches[0]] ) ) {
		setcookie('mozilla_language', $matches[0], time()+60, '/', $_SERVER['HTTP_HOST']);
		return;
	}
	mozilla_check_language();
}

add_action('after_setup_theme', 'mozilla_match_browser_locale');

function mozilla_wpml_redirect($language) {
	$url = get_site_url( null, $_SERVER['REQUEST_URI'] );
	$wpml_permalink = apply_filters( 'wpml_permalink', $url , $language );
	$redirect = wp_sanitize_redirect($wpml_permalink);
	if ( wp_redirect($redirect) ) {
		exit();
	}
}
