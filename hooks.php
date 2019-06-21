<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 09.06.2019
 * Time: 19:20
 */

use WHMCS\Module\Addon\backupWHMCS\Widget\AdminBackaupStatusWidget;

add_hook('AdminHomeWidgets', 1, function () {
    return new AdminBackaupStatusWidget();
});

