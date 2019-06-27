<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 09.06.2019
 * Time: 19:20
 */


require __DIR__ . '/../../../init.php';

ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 900);

use WHMCS\Module\Addon\backupWHMCS\Configs\ModuleConfig;
use WHMCS\Module\Addon\backupWHMCS\Controllers\FtpClientController;
use WHMCS\Module\Addon\backupWHMCS\Controllers\LogController;
use WHMCS\Module\Addon\backupWHMCS\Controllers\MysqlDumpController;
use WHMCS\Module\Addon\backupWHMCS\Controllers\ZipArchiveController;
use WHMCS\Module\Addon\backupWHMCS\Exceptions\backupWHMCSBaseException;
use WHMCS\Module\Addon\backupWHMCS\Exceptions\ZipArchiveAddFileErrorArrayException;
use WHMCS\Module\Addon\backupWHMCS\Exceptions\ZipArchiveCloseException;
use WHMCS\Module\Addon\backupWHMCS\Exceptions\ZipArchiveFileRenameException;
use WHMCS\Module\Addon\backupWHMCS\Exceptions\ZipArchiveOpenException;

$backupDB = false;
$backup_db_run_time = 0;

$backupFile = false;
$backup_file_run_time = 0;

$upload_backup = false;
$backup_upload_run_time = 0;

$backup_run_time_all = 0;

$backup_run_time_all_start = microtime(true);

try {
    $ignorePath = explode("\r\n", ModuleConfig::getModuleSetting('IgnoreFilePaths'));
    $backupPath = ModuleConfig::getWhmcsRootDir() . '/modules/addons/' . ModuleConfig::getModuleName() . '/backaups';

    $backupDBFileName = 'db.sql.bzip2';
    $backupArchiveFileName = date("Y-m-d_H-i-s") . '.zip';


    $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name;

    if (ModuleConfig::getModuleSetting('backupDatabases') !== 'on' &&
        ModuleConfig::getModuleSetting('BackupFiles') !== 'on') {
        die();
    }

    try {
        if (ModuleConfig::getModuleSetting('backupDatabases') === 'on') {
            $backup_db_run_time_start = microtime(true);
            $dump = new MysqlDumpController($dsn, $db_username, $db_password, [
                'hex-blob' => false,
                'add-drop-table' => true,
                'compress' => MysqlDumpController::BZIP2,
            ]);

            $dump->start($backupPath . DIRECTORY_SEPARATOR . $backupDBFileName);

            $backup_db_run_time = microtime(true) - $backup_db_run_time_start;
            $backupDB = true;
        }


        $zip = new ZipArchiveController();

        if (!$zip->open($backupPath . DIRECTORY_SEPARATOR . $backupArchiveFileName, ZIPARCHIVE::CREATE)) {
            throw new ZipArchiveOpenException($backupPath . DIRECTORY_SEPARATOR . $backupArchiveFileName);
        }

        if (ModuleConfig::getModuleSetting('BackupFiles') === 'on') {
            $backup_file_run_time_start = microtime(true);

            $zip->addDir(ModuleConfig::getWhmcsRootDir(), $ignorePath);

            $backup_file_run_time = microtime(true) - $backup_file_run_time_start;
            $backupFile = true;
        }

        if (!empty($logs = $zip->getLog())) {
            throw new ZipArchiveAddFileErrorArrayException($logs);
        }

        if (ModuleConfig::getModuleSetting('backupDatabases') === 'on') {
            if (ModuleConfig::getModuleSetting('BackupFiles') !== 'on') {
                $zip->addFromString(
                    'modules/addons/backupWHMCS/backaups/' . $backupDBFileName,
                    file_get_contents($backupPath . DIRECTORY_SEPARATOR . $backupDBFileName)
                );
            }

            if (!$zip->renameName(
                'modules/addons/backupWHMCS/backaups/' . $backupDBFileName,
                '/db.sql.bzip2'
            )) {
                throw new ZipArchiveFileRenameException(
                    'modules/addons/backupWHMCS/backaups/' . $backupDBFileName,
                    '/db.sql.bzip2'
                );
            }

        }

        if (!$zip->close()) {
            throw new ZipArchiveCloseException();
        }

        unlink($backupPath . DIRECTORY_SEPARATOR . $backupDBFileName);

    } catch (backupWHMCSBaseException $e) {
        LogController::cron(
            $backupDB,
            $backup_db_run_time,
            $backupFile,
            $backup_file_run_time,
            $upload_backup,
            $backup_upload_run_time,
            microtime(true) - $backup_run_time_all_start,
            $backupArchiveFileName,
            true,
            $e
        );
        echo 'Ошибка при создании резервной копии: ' . $e->getMessage();
        die();
    }
} catch (\Throwable $e) {
    LogController::cron(
        $backupDB,
        $backup_db_run_time,
        $backupFile,
        $backup_file_run_time,
        $upload_backup,
        $backup_upload_run_time,
        microtime(true) - $backup_run_time_all_start,
        $backupArchiveFileName,
        true,
        $e
    );
    echo 'Возникла неизвестная ошибка при создании резервной копии  ' . $e->getMessage();
    die();
}

try {
    $remotePath = ModuleConfig::getModuleSetting('FtpPath');

    if (substr($remotePath, -1) === '/') {
        $remotePath = substr($remotePath, 0, -1);
    }

    $ftp = new  FtpClientController();
    $ftp->connect(ModuleConfig::getModuleSetting('FtpIp'), false, ModuleConfig::getModuleSetting('FtpPort'));
    $ftp->login(ModuleConfig::getModuleSetting('FtpLogin'), ModuleConfig::getModuleSetting('FtpPassword'));
    $ftp->pasv(true);
    $backup_upload_run_time_start = microtime(true);
    $ftp->putAll(
        $backupPath,
        $remotePath
    );

    $upload_backup = true;
    $backup_upload_run_time = microtime(true) - $backup_upload_run_time_start;

    unlink($backupPath . DIRECTORY_SEPARATOR . $backupArchiveFileName);

    $backup_run_time_all = microtime(true) - $backup_run_time_all_start;

    LogController::cron(
        $backupDB,
        $backup_db_run_time,
        $backupFile,
        $backup_file_run_time,
        $upload_backup,
        $backup_upload_run_time,
        $backup_run_time_all,
        $backupArchiveFileName
    );

} catch (\Exception $e) {
    LogController::cron(
        $backupDB,
        $backup_db_run_time,
        $backupFile,
        $backup_file_run_time,
        $upload_backup,
        $backup_upload_run_time,
        microtime(true) - $backup_run_time_all_start,
        $backupArchiveFileName,
        true,
        $e
    );
    echo 'Возникла неизвестная ошибка при выгрузке резервной копии  ' . $e->getMessage();
    die();
}

$StoreSecond = ((int)ModuleConfig::getModuleSetting('StoreDay') * 24 * 60 * 60);
$expireBackaup = time() - $StoreSecond;

$items = $ftp->scanDir(ModuleConfig::getModuleSetting('FtpPath'));

foreach ($items as $name => $info) {
    if ($expireBackaup > strtotime($info['day'] . " " . $info['month'])) {
        $ftp->remove(explode('#', $name)[1]);
    }
}
