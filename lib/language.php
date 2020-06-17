<?php

function mozilla_wpml_redirect($url) {
	$redirect = wp_sanitize_redirect($url);
	if ( wp_redirect($redirect) ) {
		exit();
	}
}

function handle_english($url) {
	$url = preg_replace('/en\//', '', $url);
	mozilla_wpml_redirect($url);
}

function mozilla_set_language($language, $url) {
	if ($language === 'en') {
		return;
	}
	$url = apply_filters( 'wpml_permalink', $url , $language );
	mozilla_wpml_redirect($url);
	return;
}

function mozilla_check_language($language, $url, $active_languages) {
	if ($language) {
		mozilla_set_language($language, $url);	
		return;
	}
	$default_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	if (isset($active_languages[$default_lang])) {
		mozilla_set_language($default_lang, $url);
	}
}

/**
 * Updates the website locale based on browser settings
 *
 */
function mozilla_match_browser_locale() {
	$url = get_site_url(null, $_SERVER['REQUEST_URI']);
	$language = isset( $_COOKIE['mozilla_language'] ) ? $_COOKIE['mozilla_language'] : false;
	$wpml_languages = icl_get_languages('skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str');
	preg_match('/\b[a-zA-Z]{2}\b/', $url, $matches);
	if (wp_doing_ajax() || is_admin()) {
		return;
	}
	if ( isset($matches[0]) && isset($wpml_languages[$matches[0]] ) ) {
		if ($language && $language === 'en' && $matches[0] === 'en') {
			handle_english($url);
			return;
		}
		setcookie('mozilla_language', $matches[0], time()+60*60*24, '/', $_SERVER['HTTP_HOST']);
		if ($matches[0] === 'en') {
			handle_english($url);
			return;
		}
		return;
	}
	mozilla_check_language($language, $url, $wpml_languages);
}

add_action('after_setup_theme', 'mozilla_match_browser_locale');


function mozilla_add_default_language($url, $code) {
	if ($code === 'en') {
		$path = parse_url( $url, PHP_URL_PATH);
		$url = get_site_url(null, $code . $path);
	}
	return $url;
}

add_filter('wpml_permalink', 'mozilla_add_default_language', 10, 2);