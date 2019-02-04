<?php
/**
 * Plugin Name:     Klasifai
 * Description:     Classifies WordPress content using IBM Watson NLU API
 * Author:          Darshan Sawardekar, Ryan Welcher, 10up
 * Author URI:      https://10up.com
 * Text Domain:     klasifai
 * Domain Path:     /languages/
 * Version:         1.1.0
 */

/**
 * Small wrapper around PHP's define function. The defined constant is
 * ignored if it has already been defined. This allows the
 * config.local.php to override any constant in config.php.
 *
 * @param string $name The constant name
 * @param mixed  $value The constant value
 * @return void
 */
function klasifai_define( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

if ( file_exists( __DIR__ . '/config.test.php' ) && defined( 'PHPUNIT_RUNNER' ) ) {
	require_once( __DIR__ . '/config.test.php' );
}

if ( file_exists( __DIR__ . '/config.local.php' ) ) {
	require_once( __DIR__ . '/config.local.php' );
}

require_once( __DIR__ . '/config.php' );

/**
 * Loads the KLASIFAI PHP autoloader if possible.
 *
 * @return bool True or false if autoloading was successfull.
 */
function klasifai_autoload() {
	if ( klasifai_can_autoload() ) {
		require_once( klasifai_autoloader() );

		return true;
	} else {
		return false;
	}
}

/**
 * In server mode we can autoload if autoloader file exists. For
 * test environments we prevent autoloading of the plugin to prevent
 * global pollution and for better performance.
 */
function klasifai_can_autoload() {
	if ( file_exists( klasifai_autoloader() ) ) {
		return true;
	} else {
		error_log(
			'Fatal Error: Composer not setup in ' . KLASIFAI_PLUGIN_DIR
		);

		return false;
	}
}

/**
 * Default is Composer's autoloader
 */
function klasifai_autoloader() {
	if ( file_exists( KLASIFAI_PLUGIN_DIR . '/vendor/autoload.php' ) ) {
		return KLASIFAI_PLUGIN_DIR . '/vendor/autoload.php';
	} else {
		return KLASIFAI_PLUGIN_DIR . '/autoload.php';
	}
}

/**
 * Plugin code entry point. Singleton instance is used to maintain a common single
 * instance of the plugin throughout the current request's lifecycle.
 *
 * If autoloading failed an admin notice is shown and logged to
 * the PHP error_log.
 */
function klasifai_autorun() {
	if ( klasifai_autoload() ) {
		$plugin = \Klasifai\Plugin::get_instance();
		$plugin->enable();
	} else {
		add_action( 'admin_notices', 'klasifai_autoload_notice' );
	}
}

/**
 * Generate a notice if autoload fails.
 */
function klasifai_autoload_notice() {
	$class   = 'notice notice-error';
	$message = 'Error: Please run $ composer install in the klasifai plugin directory.';

	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );// @codingStandardsIgnoreLine This is not a security issue.
	error_log( $message );
}

klasifai_autorun();
