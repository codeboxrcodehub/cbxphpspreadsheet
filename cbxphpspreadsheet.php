<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://codeboxr.com
 * @since             1.0.0
 * @package           cbxphpspreadsheet
 *
 * @wordpress-plugin
 * Plugin Name:       CBX PhpSpreadSheet Library
 * Plugin URI:        https://codeboxr.com/php-spreadsheet-library-wordpress-plugin/
 * Description:       A pure PHP library for reading and writing spreadsheet files https://phpspreadsheet.readthedocs.io/
 * Version:           1.0.12
 * Requires PHP:      8.1.99
 * Author:            Codeboxr
 * Author URI:        https://github.com/PHPOffice/PhpSpreadsheet
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cbxphpspreadsheet
 * Domain Path:       /languages
 */



if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'CBXPHPSPREADSHEET_PLUGIN_NAME' ) or define( 'CBXPHPSPREADSHEET_PLUGIN_NAME', 'cbxphpspreadsheet' );
defined( 'CBXPHPSPREADSHEET_PLUGIN_VERSION' ) or define( 'CBXPHPSPREADSHEET_PLUGIN_VERSION', '1.0.12' );
defined( 'CBXPHPSPREADSHEET_BASE_NAME' ) or define( 'CBXPHPSPREADSHEET_BASE_NAME', plugin_basename( __FILE__ ) );
defined( 'CBXPHPSPREADSHEET_ROOT_PATH' ) or define( 'CBXPHPSPREADSHEET_ROOT_PATH', plugin_dir_path( __FILE__ ) );
defined( 'CBXPHPSPREADSHEET_ROOT_URL' ) or define( 'CBXPHPSPREADSHEET_ROOT_URL', plugin_dir_url( __FILE__ ) );

register_activation_hook( __FILE__, [ 'CBXPhpSpreadSheet', 'activation' ] );


/**
 * Class CBXPhpSpreadSheet
 */
class CBXPhpSpreadSheet {
	public function __construct() {
		
		// Load text domain
		add_action( 'init', [ $this, 'load_plugin_textdomain' ]);
		
		// Add custom row meta links
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 4 );
		add_action( 'admin_notices', [ $this, 'activation_error_display' ] );

		add_filter( 'pre_set_site_transient_update_plugins', [
			$this,
			'pre_set_site_transient_update_plugins'
		] );
		add_filter( 'plugins_api', [ $this, 'plugin_info' ], 10, 3 );
	}//end constructor

	/**
	 * Activation hook
	 *
	 * @return void
	 */
	public static function activation() {
		$errors = [];

		if ( ! self::php_version_check() ) {
			$errors[] = esc_html__( 'CBX PhpSpreadSheet Library plugin requires PHP version 8.1.99 or newer.', 'cbxphpspreadsheet' );
		}

		if ( ! self::extension_check( [ 'zip', 'xml', 'gd' ] ) ) {
			$errors[] = esc_html__( 'CBX PhpSpreadSheet Library plugin requires PHP extensions: Zip, XML, and GD2.', 'cbxphpspreadsheet' );
		}

		if ( sizeof( $errors ) > 0 ) {
			update_option( 'cbxphpspreadsheet_activation_error', $errors );
			//deactivate_plugins(plugin_basename(__FILE__));

			//wp_die('Plugin not activated due to dependency not fulfilled.');

			//die();
		}
	}//end method activation

	/**
	 * Show error
	 *
	 * @return void
	 */
	public function activation_error_display() {
		// Only display on specific admin pages (e.g., plugins page)
		$screen = get_current_screen();
		if ( $screen && $screen->id === 'plugins' ) {
			$errors = get_option( 'cbxphpspreadsheet_activation_error' );
			if ( $errors ) {
				if ( is_array( $errors ) && sizeof( $errors ) > 0 ) {
					foreach ( $errors as $error ) {
						echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $error ) . '</p></div>';
					}
				}

				delete_option( 'cbxphpspreadsheet_activation_error' );
				//deactivate_plugins('cbxphpspreadsheet/cbxphpspreadsheet.php');
			}
		}
	}//end method activation_error_display

	/**
	 * Check PHP version compatibility
	 *
	 * @return bool
	 */
	private static function php_version_check() {
		return version_compare( PHP_VERSION, '8.1.99', '>=' );
	}//end method php_version_check

	/**
	 * Check if required PHP extensions are enabled
	 *
	 * @param array $extensions
	 *
	 * @return bool
	 */
	private static function extension_check( $extensions ) {
		foreach ( $extensions as $extension ) {
			if ( ! extension_loaded( $extension ) ) {
				return false;
			}
		}

		return true;
	}//end method extension_check

	/**
	 * Is the environment ready for the phpspreadsheet package
	 *
	 * @return bool
	 */
	public static function environment_ready() {
		return self::php_version_check() && self::extension_check(  [ 'zip', 'xml', 'gd' ]);
	}//end method environment_ready
	

	/**
	 * Filters the array of row meta for each/specific plugin in the Plugins list table.
	 * Appends additional links below each/specific plugin on the plugins page.
	 *
	 * @access  public
	 *
	 * @param  array  $links_array  An array of the plugin's metadata
	 * @param  string  $plugin_file_name  Path to the plugin file
	 * @param  array  $plugin_data  An array of plugin data
	 * @param  string  $status  Status of the plugin
	 *
	 * @return  array       $links_array
	 */
	public function plugin_row_meta( $links_array, $plugin_file_name, $plugin_data, $status ) {
		if ( strpos( $plugin_file_name, CBXPHPSPREADSHEET_BASE_NAME ) !== false ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$links_array[] = '<a target="_blank" style="color:#005ae0 !important; font-weight: bold;" href="https://github.com/codeboxrcodehub/cbxphpspreadsheet" aria-label="' . esc_attr__( 'Github Repo', 'cbxphpspreadsheet' ) . '">' . esc_html__( 'Github Repo', 'cbxphpspreadsheet' ) . '</a>';
			$links_array[] = '<a target="_blank" style="color:#005ae0 !important; font-weight: bold;" href="https://github.com/codeboxrcodehub/cbxphpspreadsheet/releases" aria-label="' . esc_attr__( 'Download', 'cbxphpspreadsheet' ) . '">' . esc_html__( 'Download Latest', 'cbxphpspreadsheet' ) . '</a>';
		}

		return $links_array;
	}//end plugin_row_meta

	/**
	 * Load textdomain
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'cbxphpspreadsheet', false, CBXPHPSPREADSHEET_ROOT_PATH . 'languages/' );
	}//end method load_plugin_textdomain

	/**
	 * Custom update checker implemented
	 *
	 * @param $transient
	 *
	 * @return mixed
	 */
	public function pre_set_site_transient_update_plugins( $transient ) {
		// Ensure the transient is set
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$plugin_slug = 'cbxphpspreadsheet';
		$plugin_file = 'cbxphpspreadsheet/cbxphpspreadsheet.php';

		if ( isset( $transient->response[ $plugin_file ] ) ) {
			return $transient;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$url = 'https://comforthrm.com/product_updates.json'; // Replace with your remote JSON file URL
		
		// Fetch the remote JSON file
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
			return $transient;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );// Set true for associative array, false for object


		if ( ! isset( $data['cbxphpspreadsheet'] ) ) {
			return $transient;
		}

		$remote_data = $data['cbxphpspreadsheet'];

		$plugin_url  = isset( $remote_data['url'] ) ? $remote_data['url'] : '';
		$package_url = isset( $remote_data['api_url'] ) ? $remote_data['api_url'] : false;

		$remote_version = isset( $remote_data['new_version'] ) ? sanitize_text_field( $remote_data['new_version'] ) : '';

		if ( $remote_version != '' && version_compare( $remote_version, $transient->checked[ $plugin_file ], '>' ) ) {
			$transient->response[ $plugin_file ] = (object) [
				'slug'        => $plugin_slug,
				'new_version' => $remote_version,
				'url'         => $plugin_url,
				'package'     => $package_url, // Link to the new version
			];
		}

		return $transient;
	}//end method pre_set_site_transient_update_plugins

	public function plugin_info( $res, $action, $args ) {
		// Plugin slug
		$plugin_slug = 'cbxphpspreadsheet';                                      // Replace with your plugin slug

		// Ensure we're checking the correct plugin
		if ( $action !== 'plugin_information' || $args->slug !== $plugin_slug ) {
			return $res;
		}

		// Fetch detailed plugin information
		$response = wp_remote_get( 'https://comforthrm.com/product_updates.json' ); // Replace with your API URL

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
			return $res;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! isset( $data[ $plugin_slug ] ) ) {
			return $res;
		}

		$remote_data = $data[ $plugin_slug ];		
		$package_url = isset( $remote_data['api_url'] ) ? $remote_data['api_url'] : false;

		// Build the plugin info response
		return (object) [
			'name'          => isset( $remote_data['name'] ) ? sanitize_text_field( $remote_data['name'] ) : 'CBX PhpSpreadSheet Library',
			'slug'          => $plugin_slug,
			'version'       => isset( $remote_data['new_version'] ) ? sanitize_text_field( $remote_data['new_version'] ) : '',
			'author'        => isset( $remote_data['author'] ) ? sanitize_text_field( $remote_data['author'] ) : '',
			'homepage'      => isset( $remote_data['url'] ) ? $remote_data['url'] : '',
			'requires'      => isset( $remote_data['requires'] ) ? sanitize_text_field( $remote_data['requires'] ) : '',
			'tested'        => isset( $remote_data['tested'] ) ? sanitize_text_field( $remote_data['tested'] ) : '',
			'download_link' => $package_url,
			'sections'      => [
				'description' => isset( $remote_data['description'] ) ? wp_kses_post( $remote_data['description'] ) : '',
				'changelog'   => isset( $remote_data['changelog'] ) ? wp_kses_post( $remote_data['changelog'] ) : '',
			],
		];

	}//end method plugin_info
}//end class CBXPhpSpreadSheet

/**
 * Initialize the plugin
 */
function cbxphpspreadsheet_load_plugin() {
	new CBXPhpSpreadSheet();
}

add_action( 'plugins_loaded', 'cbxphpspreadsheet_load_plugin', 5 );


if(!function_exists('cbxphpspreadsheet_loadable')){
	/**
	 * Check if the enviroment ready for phpspreadsheet library
	 *
	 * @return bool
	 */
	function cbxphpspreadsheet_loadable(){
		return CBXPhpSpreadSheet::environment_ready();
	}//end function cbxphpspreadsheet_loadable
}

if(!function_exists('cbxphpspreadsheet_load')){
	/**
	 * If the enviroment is ready then load the autoloaded
	 *
	 * @return void
	 */
	function cbxphpspreadsheet_load(){
		if(CBXPhpSpreadSheet::environment_ready()){
			require_once CBXPHPSPREADSHEET_ROOT_PATH . "lib/vendor/autoload.php";
		}
	}//end function cbxphpspreadsheet_load
}

