<?php
/**
 * Created by PhpStorm.
 * User: Artem
 * Date: 20.04.2019
 * Time: 19:09
 */

namespace WHMCS\Module\Addon\backupWHMCS\Models;


class BackupLogModel extends \WHMCS\Model\AbstractModel {
	protected $table = "mod_addon_backup_whmcs";
	protected $booleans = [
		"backupDB",
		"backupFile",
		"upload_backup",
	];
	protected $primaryKey = 'id';
	public $incrementing = true;
	protected $fillable = [
	];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

}