<?php
/**
 * Language Library
 *
 * Language library functions
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<?php

/**
 * Redirect
 *
 * @param string $url URL to redirect.
 */
function mozilla_wpml_redirect( $url ) {
	$redirect = wp_sanitize_redirect( $url );

	if ( ! isset( $_GET['auth0'] ) && ! isset( $_GET['code'] ) && wp_safe_redirect( $redirect ) ) {
		exit();
	}
}

/**
 * Set the language
 *
 * @param string $language language.
 * @param string $url URL.
 */
function mozilla_set_language( $language, $url ) {
	$url = apply_filters( 'wpml_permalink', $url, $language );
	mozilla_wpml_redirect( $url );
}

/**
 * Checks language
 *
 * @param string $url url.
 * @param array  $active_languages the active languages.
 */
function mozilla_check_language( $url, $active_languages ) {
	if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
		$default_lang = substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ), 0, 2 );
		if ( isset( $active_languages[ $default_lang ] ) ) {
			mozilla_set_language( $default_lang, $url );
		}
	}
}

/**
 * Updates the website locale based on browser settings
 */
function mozilla_match_browser_locale() {
	if ( isset( $_SERVER['REQUEST_URI'] ) && function_exists( 'icl_get_languages' ) ) {
		$url            = get_site_url( null, esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		$wpml_languages = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );
		if ( empty( ICL_LANGUAGE_CODE ) ) {
			return;
		}
		$base_url = get_site_url( null, ICL_LANGUAGE_CODE );
		if ( wp_doing_ajax() || is_admin() || isset( $_GET['action'] ) || false !== stripos( $url, get_site_url( null, 'wp' ) ) ) {
			return;
		}

		if ( false !== stripos( $url, $base_url ) ) {
			if ( false === stripos( $url, $base_url . '/' ) ) {
				mozilla_wpml_redirect( $base_url . '/' );
			}
			return;
		}
		mozilla_check_language( $url, $wpml_languages );
	}
}

add_action( 'after_setup_theme', 'mozilla_match_browser_locale' );


/**
 * Adds default language
 *
 * @param string $url URL.
 * @param string $code language code.
 */
function mozilla_add_default_language( $url, $code ) {
	if ( 'en' === $code ) {
		$path = wp_parse_url( $url, PHP_URL_PATH );
		$url  = get_site_url( null, $code . $path );
	}
	return $url;
}

/**
 * Get translated tag
 *
 * @param object $category Passing category object.
 */
function mozilla_get_translated_tag( $category ) {
	$current_translation = mozilla_get_current_translation();
	if ( 'en' !== $current_translation ) {
		$translation = get_term_by( 'slug', $category->slug . '_' . $current_translation, 'post_tag' );
		if ( ! empty( $translation ) ) {
			return (object) array(
				'name' => $translation->name,
				'id'   => $translation->term_id,
			);
		}
	}
	return (object) array(
		'name' => $category->name,
		'id'   => $category->term_id,
	);
}

/**
 * Handle redirect after login
 */
function mozilla_redirect_after_login() {
	$url = get_site_url();
	wp_redirect( $url );
	die();
}

add_action( 'auth0_user_login', 'mozilla_redirect_after_login', 10, 0 );


/**
 * URL used to logout of Auth0.
 *
 * @param string $default_logout_url - Logout URL.
 *
 * @return string
 */
function mozilla_logout_url( string $default_logout_url ) {
	$default_logout_url = add_query_arg( 'returnTo', get_site_url(), $default_logout_url );
	return $default_logout_url;
}
add_filter( 'auth0_logout_url', 'mozilla_logout_url' );
