<?php
/**
 * Created by PhpStorm.
 * User: Artem
 * Date: 24.04.2019
 * Time: 20:24
 */

namespace WHMCS\Module\Addon\backupWHMCS\Controllers;

use \WHMCS\Database\Capsule;

class UninstallController {

	public static function dropTable( $tableName ) {
		try {
			Capsule::schema()->dropIfExists( $tableName );
		} catch ( \Exception $e ) {
			return array(
				'status'      => 'error',
				'description' => 'При удалении таблицы возникла ошибка:'. $tableName, $e->getMessage()
			);
		}

		return [];
	}
}