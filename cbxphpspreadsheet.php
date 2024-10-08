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
 * Version:           1.0.6
 * Author:            Codeboxr
 * Author URI:        https://github.com/PHPOffice/PhpSpreadsheet
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cbxphpspreadsheet
 * Domain Path:       /languages
 */

// If this file is called directly, abort.

use Cbx\Phpspreadsheet\Hooks;

if (!defined('WPINC')) {
	die;
}

defined('CBXPHPSPREADSHEET_PLUGIN_NAME') or define('CBXPHPSPREADSHEET_PLUGIN_NAME', 'cbxphpspreadsheet');
defined('CBXPHPSPREADSHEET_PLUGIN_VERSION') or define('CBXPHPSPREADSHEET_PLUGIN_VERSION', '1.0.6');
defined('CBXPHPSPREADSHEET_BASE_NAME') or define('CBXPHPSPREADSHEET_BASE_NAME', plugin_basename(__FILE__));
defined('CBXPHPSPREADSHEET_ROOT_PATH') or define('CBXPHPSPREADSHEET_ROOT_PATH', plugin_dir_path(__FILE__));
defined('CBXPHPSPREADSHEET_ROOT_URL') or define('CBXPHPSPREADSHEET_ROOT_URL', plugin_dir_url(__FILE__));


register_activation_hook(__FILE__, array('CBXPhpSpreadSheet', 'activation'));
require_once CBXPHPSPREADSHEET_ROOT_PATH . "lib/vendor/autoload.php";



/**
 * Class CBXPhpSpreadSheet
 */
class CBXPhpSpreadSheet
{
	function __construct()
	{
		//load text domain
		load_plugin_textdomain('cbxphpspreadsheet', false, dirname(plugin_basename(__FILE__)) . '/languages/');

		add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);

		new Hooks();
	}

	/**
	 * Activation hook
	 */
	public static function activation()
	{
		if (!CBXPhpSpreadSheet::php_version_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = __('This plugin requires PHP version 7.4 or newer', 'cbxphpspreadsheet');
			die($error_message);
		}


		if (!CBXPhpSpreadSheet::php_zip_enabled_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = __(
				'This plugin requires PHP php_zip extension installed and enabled',
				'cbxphpspreadsheet'
			);
			die($error_message);
		}

		if (!CBXPhpSpreadSheet::php_xml_enabled_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = __(
				'This plugin requires PHP php_xml extension installed and enabled',
				'cbxphpspreadsheet'
			);
			die($error_message);
		}

		if (!CBXPhpSpreadSheet::php_gd_enabled_check()) {

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			// Throw an error in the wordpress admin console
			$error_message = __(
				'This plugin requires PHP php_gd2 extension installed and enabled',
				'cbxphpspreadsheet'
			);
			die($error_message);
		}

	}//end method activation

	/**
	 * PHP version compatibility check
	 *
	 * @return bool
	 */
	public static function php_version_check()
	{
		if (version_compare(PHP_VERSION, '7.4.0', '<')) {
			return false;
		}

		return true;
	}//end method php_version_check

	/**
	 * php_zip enabled check
	 *
	 * @return bool
	 */
	public static function php_zip_enabled_check()
	{
		if (extension_loaded('zip')) {
			return true;
		}

		return false;
	}//end method php_zip_enabled_check

	/**
	 * php_xml enabled check
	 *
	 * @return bool
	 */
	public static function php_xml_enabled_check()
	{
		if (extension_loaded('xml')) {
			return true;
		}

		return false;
	}//end method php_xml_enabled_check

	/**
	 * php_gd2 enabled check
	 *
	 * @return bool
	 */
	public static function php_gd_enabled_check()
	{
		if (extension_loaded('gd')) {
			return true;
		}

		return false;
	}//end method php_gd_enabled_check

	/**
	 * Plugin support and doc page url
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	public function plugin_row_meta($links, $file)
	{

		if (strpos($file, 'cbxphpspreadsheet.php') !== false) {
			$new_links = array(
				'support' => '<a href="https://codeboxr.com/php-spreadsheet-library-wordpress-plugin/" target="_blank">' . esc_html__(
					'Support',
					'cbxphpspreadsheet'
				) . '</a>',
				'doc' => '<a href="https://phpspreadsheet.readthedocs.io/en/latest/" target="_blank">' . esc_html__(
					'PHP Spreadsheet Doc',
					'cbxphpspreadsheet'
				) . '</a>'
			);

			$links = array_merge($links, $new_links);
		}

		return $links;
	}

}//end method CBXPhpSpreadSheet


function cbxphpspreadsheet_load_plugin()
{
	new CBXPhpSpreadSheet();
}

add_action('plugins_loaded', 'cbxphpspreadsheet_load_plugin', 5);