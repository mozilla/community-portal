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
	if ( wp_safe_redirect( $redirect ) ) {
		exit();
	}
}


	/**
	 * Handle english
	 *
	 * @param string $url URL to redirect.
	 */
function handle_english( $url ) {
	$url = preg_replace( '/\/en/', '', $url );
	mozilla_wpml_redirect( $url );
}

	/**
	 * Set the language
	 *
	 * @param string $language language.
	 * @param string $url URL.
	 */
function mozilla_set_language( $language, $url ) {
	if ( 'en' === $language ) {
		return;
	}
	$url = apply_filters( 'wpml_permalink', $url, $language );
	mozilla_wpml_redirect( $url );
}

	/**
	 * Checks language
	 *
	 * @param string $language langauge.
	 * @param string $url url.
	 * @param array  $active_languages the active languages.
	 */
function mozilla_check_language( $language, $url, $active_languages ) {
	if ( $language ) {
		mozilla_set_language( $language, $url );
		return;
	}
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
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$url            = get_site_url( null, esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		$language       = isset( $_COOKIE['mozilla_language'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mozilla_language'] ) ) : false;
		$wpml_languages = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );
		preg_match( '/\b[a-zA-Z]{2}\b/', $url, $matches );
		if ( wp_doing_ajax() || is_admin() ) {
			return;
		}

		if ( isset( $matches[0] ) && isset( $wpml_languages[ $matches[0] ] ) ) {
			if ( $language && 'en' === $language && 'en' === $matches[0] ) {
				handle_english( $url );
				return;
			}
			if ( isset( $_SERVER['HTTP_HOST'] ) ) {
				setcookie( 'mozilla_language', $matches[0], time() + 60 * 60 * 24, '/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) );

				if ( 'en' === $matches[0] ) {
					handle_english( $url );
					return;
				}
			}

			return;
		}
		mozilla_check_language( $language, $url, $wpml_languages );
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

	add_filter( 'wpml_permalink', 'mozilla_add_default_language', 10, 2 );
