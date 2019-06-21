<?php
/**
 * Created by PhpStorm.
 * User: Artem
 * Date: 24.04.2019
 * Time: 20:23
 */

namespace WHMCS\Module\Addon\backupWHMCS\Controllers;

use WHMCS\Database\Capsule;

class InstallController
{
    public static function createTableBackupHistory()
    {
        try {
            if (!Capsule::schema()->hasTable('mod_addon_backup_whmcs')) {
                Capsule::schema()->create('mod_addon_backup_whmcs', function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');

                    $table->string('backup_name')->nullable();

                    $table->boolean('backupDB');
                    $table->integer('backup_db_run_time')->nullable();

                    $table->boolean('backupFile');
                    $table->integer('backup_file_run_time')->nullable();

                    $table->boolean('upload_backup')->nullable();
                    $table->integer('backup_upload_run_time')->nullable();

                    $table->integer('backup_run_time_all')->nullable();

                    $table->timestamps();
                });
            }
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' =>  ''. $e->getMessage()
            );
        }

        return [];
    }

}