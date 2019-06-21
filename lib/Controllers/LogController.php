<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 20.06.2019
 * Time: 19:36
 */

namespace WHMCS\Module\Addon\backupWHMCS\Controllers;

use WHMCS\Module\Addon\backupWHMCS\Models\BackupLogModel;
use WHMCS\Module\Addon\backupWHMCS\Configs\ModuleConfig;

class LogController
{
  static  function cron(
        $backupDB,
        $backup_db_run_time,
        $backupFile,
        $backup_file_run_time,
        $upload_backup,
        $backup_upload_run_time,
        $backup_run_time_all,
        $backupArchiveFileName,
        $error = false,
        $e = null
    )
    {
        $BackupLog = new BackupLogModel();

        $BackupLog->backup_name = $backupArchiveFileName;

        $BackupLog->backupDB = $backupDB;
        $BackupLog->backup_db_run_time = $backup_db_run_time;

        $BackupLog->backupFile = $backupFile;
        $BackupLog->backup_file_run_time = $backup_file_run_time;

        $BackupLog->upload_backup = $upload_backup;
        $BackupLog->backup_upload_run_time = $backup_upload_run_time;

        $BackupLog->backup_run_time_all = $backup_run_time_all;
        $BackupLog->save();

        if($error === true){
            logModuleCall(
                ModuleConfig::getModuleName(),
                'backup cron',
                'null', [
                $backupDB,
                $backup_db_run_time,
                $backupFile,
                $backup_file_run_time,
                $upload_backup,
                $backup_upload_run_time,
                $backup_run_time_all,
                $backupArchiveFileName,
                $error ,
                $e
            ]);
        }
    }
}