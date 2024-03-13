<?php

namespace Cbx\Phpspreadsheet;

/**
 * All hooks
 */
class Hooks {
	public function __construct() {
		$this->update_checker();
	}

	/**
	 * Update checker
	 *
	 * @return void
	 */
	public function update_checker() {
		$updater = new PDUpdater( CBXPHPSPREADSHEET_ROOT_PATH . 'cbxphpspreadsheet.php' );
		$updater->set_username( 'codeboxrcodehub' );
		$updater->set_repository( 'cbxphpspreadsheet' );
		$updater->authorize( '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' );
		$updater->initialize();
	}//end method update_checker
}//end class Hooks