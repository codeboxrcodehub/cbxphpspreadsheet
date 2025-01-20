Open source PhpSpreadSheet php library released as WordPress plugin to use easily

## Description

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

````
if ( defined('CBXPHPSPREADSHEET_PLUGIN_NAME') && cbxphpspreadsheet_loadable() ) {

	//Include PHPExcel
	require_once( CBXPHPSPREADSHEET_ROOT_PATH . 'lib/vendor/autoload.php' ); //or use 'cbxphpspreadsheet_load();'

	//now take instance
	$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

	//do whatever you need to do
}
````

## Installation

1. Download the latest zip from here (https://github.com/codeboxrcodehub/cbxphpspreadsheet/releases)
2. [WordPress has clear documentation about how to install a plugin].(https://codex.wordpress.org/Managing_Plugins)
3. After install activate the plugin "CBX PhpSpreadSheet Library" through the 'Plugins' menu in WordPress
4. This plugin doesn't load any library by default, it doesn't create extra folder or menu.

## Licence

[MIT](https://github.com/codeboxrcodehub/cbxphpspreadsheet/blob/master/LICENSE.txt)