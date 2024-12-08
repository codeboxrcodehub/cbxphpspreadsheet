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

use Cbx\Phpspreadsheet\Hooks;

if (!defined('WPINC')) {
    die;
}

defined('CBXPHPSPREADSHEET_PLUGIN_NAME') or define('CBXPHPSPREADSHEET_PLUGIN_NAME', 'cbxphpspreadsheet');
defined('CBXPHPSPREADSHEET_PLUGIN_VERSION') or define('CBXPHPSPREADSHEET_PLUGIN_VERSION', '1.0.6');
defined('CBXPHPSPREADSHEET_BASE_NAME') or define('CBXPHPSPREADSHEET_BASE_NAME', plugin_basename(__FILE__));
defined('CBXPHPSPREADSHEET_ROOT_PATH') or define('CBXPHPSPREADSHEET_ROOT_PATH', plugin_dir_path(__FILE__));
defined('CBXPHPSPREADSHEET_ROOT_URL') or define('CBXPHPSPREADSHEET_ROOT_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, ['CBXPhpSpreadSheet', 'activation']);
require_once CBXPHPSPREADSHEET_ROOT_PATH . "lib/vendor/autoload.php";

/**
 * Class CBXPhpSpreadSheet
 */
class CBXPhpSpreadSheet
{
    public function __construct()
    {
        // Load text domain
        load_plugin_textdomain('cbxphpspreadsheet', false, dirname(plugin_basename(__FILE__)) . '/languages/');

        // Add custom row meta links
        add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 2);

        new Hooks();
    }

    /**
     * Activation hook
     */
    public static function activation()
    {
        $errors = [];
        
        if (!self::php_version_check()) {
            $errors[] = __('This plugin requires PHP version 7.4 or newer.', 'cbxphpspreadsheet');
        }

        if (!self::extension_check(['zip', 'xml', 'gd'])) {
            $errors[] = __('This plugin requires PHP extensions: Zip, XML, and GD2.', 'cbxphpspreadsheet');
        }

        if (!empty($errors)) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(implode('<br>', $errors), __('Plugin Activation Error', 'cbxphpspreadsheet'), ['back_link' => true]);
        }
    }

    /**
     * Check PHP version compatibility
     *
     * @return bool
     */
    private static function php_version_check()
    {
        return version_compare(PHP_VERSION, '7.4.0', '>=');
    }

    /**
     * Check if required PHP extensions are enabled
     *
     * @param array $extensions
     * @return bool
     */
    private static function extension_check($extensions)
    {
        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add support and documentation links to the plugin row meta
     *
     * @param array $links
     * @param string $file
     * @return array
     */
    public function plugin_row_meta($links, $file)
    {
        if (strpos($file, 'cbxphpspreadsheet.php') !== false) {
            $new_links = [
                'support' => '<a href="https://codeboxr.com/php-spreadsheet-library-wordpress-plugin/" target="_blank">' . esc_html__('Support', 'cbxphpspreadsheet') . '</a>',
                'doc' => '<a href="https://phpspreadsheet.readthedocs.io/en/latest/" target="_blank">' . esc_html__('PHP Spreadsheet Doc', 'cbxphpspreadsheet') . '</a>',
            ];

            $links = array_merge($links, $new_links);
        }

        return $links;
    }
}

/**
 * Initialize the plugin
 */
function cbxphpspreadsheet_load_plugin()
{
    new CBXPhpSpreadSheet();
}

add_action('plugins_loaded', 'cbxphpspreadsheet_load_plugin', 5);
