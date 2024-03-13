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