=== CBX PhpSpreadSheet Library ===
Contributors: codeboxr, manchumahara
Requires at least: 5.3
Tested up to: 6.7.1
Stable tag: 1.0.8
License: MIT
License URI: https://github.com/codeboxrcodehub/cbxphpspreadsheet/blob/master/LICENSE.txt

Open source PhpSpreadSheet php library released as wordpress plugin to use easily

== Description ==

A pure [PHP library for reading and writing spreadsheet files](https://phpspreadsheet.readthedocs.io)

From Codeboxr we wrapped the library as wordpress plugin to easy distribute as wordpress plugin. WordPress now doesn't allow this type library as
plugin but providing a large php package with plugin makes the plugin heavy and troublesome for updates. We hosted this in github so that user can download and later
update easily.

Software requirements

The following software is required to develop using PhpSpreadsheet:

 * PHP version 7.4 or newer
 * PHP extension php_zip enabled
 * PHP extension php_xml enabled
 * PHP extension php_gd2 enabled (if not compiled in)


The plugin check php version, php_zip, php_xml and php_gd2 library compatible or installed or not, based on success it activated.

How to use:

`
if ( defined('CBXPHPSPREADSHEET_PLUGIN_NAME') && cbxphpspreadsheet_loadable() ) {

	//Include PHPExcel
	require_once( CBXPHPSPREADSHEET_ROOT_PATH . 'lib/vendor/autoload.php' ); //or use 'cbxphpspreadsheet_load();'

	//now take instance
	$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

	//do whatever you need to do
}
`

== Installation ==

1. Download the latest zip from here (https://github.com/codeboxrcodehub/cbxphpspreadsheet/releases)
2. [WordPress has clear documentation about how to install a plugin].(https://codex.wordpress.org/Managing_Plugins)
3. After install activate the plugin "CBX PhpSpreadSheet Library" through the 'Plugins' menu in WordPress
4. This plugin doesn't load any library by default, it doesn't create extra folder or menu.


== Changelog ==
= 1.0.8 =
* Dependency vendor packages updated to latest
* Added some helper functions for better environment checking

= 1.0.7 =
* Dependency vendor packages updated to latest

= 1.0.6 =
* Dependency vendor packages updated to latest

= 1.0.5 =
* Dependency vendor packages updated to latest
* Github.com based updater

= 1.0.4 =
* Updated the phpspreadsheet package to latest V1.29.0

= 1.0.3 =
* Updated the phpspreadsheet package to latest V1.21.0

= 1.0.2 =
* Updated the phpspreadsheet package to latest V1.21.0 

= 1.0.1 =
* Updated the phpspreadsheet package to latest

= 1.0.0 =
* First public release
