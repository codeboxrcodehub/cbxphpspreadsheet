<?php

namespace Cbx\Phpspreadsheet;


class Hooks {

	public function __construct() {
		$this->update_checker();
	}

	public function update_checker() {
		$updater = new PDUpdater( CBXPHPSPREADSHEET_ROOT_PATH . 'cbxphpspreadsheet.php' );
		$updater->set_username( 'codeboxrcodehub' );
		$updater->set_repository( 'cbxphpspreadsheet' );
		$updater->authorize( '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' );
		$updater->initialize();

		return;
	}//end method update_checker
}//end class Hooks