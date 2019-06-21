<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 09.06.2019
 * Time: 17:32
 */

use WHMCS\Module\Addon\backupWHMCS\Configs\ModuleConfig;
use WHMCS\Module\Addon\backupWHMCS\Controllers\FtpClientController;
use WHMCS\Module\Addon\backupWHMCS\Controllers\InstallController;
use WHMCS\Module\Addon\backupWHMCS\Controllers\UninstallController;
use WHMCS\Module\Addon\backupWHMCS\Models\BackupLogModel;
use WHMCS\Module\Addon\Setting;

function backupWHMCS_config()
{
    return [
        "name" => "Резервное копирование WHMCS",
        "description" => "Только для PHP 7.0 и выше",
        "version" => "1",
        "author" => "service-voice",
        "fields" => [
            "backupDatabases" => [
                "FriendlyName" => "Включить резервное копирование базы данных?",
                "Type" => "yesno",
                "Description" => " Отметьте здесь дабы включить резервное копирование базы данных.",
            ],
            "BackupFiles" => [
                "FriendlyName" => "Включить резервное копирование файлов?",
                "Type" => "yesno",
                "Description" => " Отметьте здесь дабы включить резервное копирование файлов.",
            ],
            "IgnoreFilePaths" => [
                "FriendlyName" => "Укажите список папок или файлов для игнорирования",
                "Type" => "textarea",
                "Description" => "По одному на строку, пример: <br/> /modules/addons/",
            ], "StoreDay" => [
                "FriendlyName" => "Хранить резервныые копии за ",
                "Type" => "text",
                "Description" => "дней",
            ],
            'note1' => [
                "FriendlyName" => "Данные для выгрузки",
            ],
            "FtpIp" => [
                "FriendlyName" => "IP FTP сервера",
                "Type" => "text",
                "Description" => "",
            ],
            "FtpPort" => [
                "FriendlyName" => "Порт FTP сервера",
                "Type" => "text",
                "Description" => "",
            ],
            "FtpLogin" => [
                "FriendlyName" => "Логин FTP сервера",
                "Type" => "text",
                "Description" => "",
            ],
            "FtpPassword" => [
                "FriendlyName" => "Пароль FTP сервера",
                "Type" => "password",
                "Description" => "",
            ], "FtpPath" => [
                "FriendlyName" => "Путь на FTP сервере для бекапов",
                "Type" => "text",
                "Description" => "",
            ],
            'note2' => [
                "Description" => "Для просмотра списка резервных копий смотрите админ вывод",
            ],
            "DeleteTableWhenDisabled" => [
                "FriendlyName" => "Удаление данных",
                "Type" => "yesno",
                "Description" => " Отметьте здесь дабы удалить данные модуля при отключении оного.",
            ],
            'note3' => [
                "FriendlyName" => "Добавьте в планировшик задач:",
                "Description" => ModuleConfig::getWhmcsRootDir() . DIRECTORY_SEPARATOR
                    . 'modules' . DIRECTORY_SEPARATOR
                    . 'addons' . DIRECTORY_SEPARATOR
                    . ModuleConfig::getModuleName() . DIRECTORY_SEPARATOR
                    . 'cron.php <br/>'
                    . '<spzn class="label closed">Внимание! Если вы не добавите запись в cron модуль работать не будет.</spzn>',
            ]
        ]
    ];
}

function backupWHMCS_activate()
{
    if (!empty($error = InstallController::createTableBackupHistory())) {
        return $error;
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно активирован',
    );
}

function backupWHMCS_deactivate()
{
    if (!empty($dropTable = Setting::Module(ModuleConfig::getModuleName())->where('setting', '=', 'DeleteTableWhenDisabled')->first())) {
        if ($dropTable->value === 'on') {
            if (!empty($error = UninstallController::dropTable('mod_addon_backup_whmcs'))) {
                return $error;
            }
        }
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно деактивирован'
    );
}

function backupWHMCS_output($vars)
{
    $ftp = new  FtpClientController();
    $ftp->connect(ModuleConfig::getModuleSetting('FtpIp'), false, ModuleConfig::getModuleSetting('FtpPort'));
    $ftp->login(ModuleConfig::getModuleSetting('FtpLogin'), ModuleConfig::getModuleSetting('FtpPassword'));
    $ftp->pasv(true);
    $items = $ftp->scanDir(ModuleConfig::getModuleSetting('FtpPath'));

    echo '<table class="table">';
    echo '<thead>';
    echo '<tr>';
    echo '<td class="text-center">Время создания</td>';
    echo '<td class="text-center">Бекап базы данных</td>';
    echo '<td class="text-center">Бекап файлов</td>';
    echo '<td class="text-center">Выгрузка бекапа</td>';
    echo '<td class="text-center">Наличие бекапа на удаленном сервере</td>';
    echo '<td class="text-center">Общее время выполнения </td>';
    echo '</tr>';
    echo '</thead>';
    foreach (BackupLogModel::orderBy('created_at', 'desc')->get() as $item) {
        echo '<tr>';

        echo '<td class="text-center">';
        echo str_replace('_'," ",substr($item->backup_name,0,-4 ));
        echo '</td>';

        echo '<td class="text-center">';
        if ($item->backupDB) {
            echo '<i class="fa fa-check" style="color:green"></i>';
        }
        echo '</td>';

        echo '<td class="text-center">';
        if ($item->backupFile) {
            echo '<i class="fa fa-check" style="color:green"></i>';
        }
        echo '</td>';

        echo '<td class="text-center">';
        if ($item->upload_backup) {
            echo '<i class="fa fa-check" style="color:green"></i>';
        }
        echo '</td>';

        echo '<td class="text-center">';
        if (array_key_exists('file#' . ModuleConfig::getModuleSetting('FtpPath') . '/' . $item->backup_name, $items)) {
            echo '<i class="fa fa-check" style="color:green"></i>';
        }
        echo '</td>';

        echo '<td class="text-center">' . $item->backup_run_time_all . ' сек. </td>';

        echo '</tr>';
    }
    echo '</table>';
}