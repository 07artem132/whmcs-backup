<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.11.19 0:10
 *
 */

/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.11.19 0:08
 *
 */

/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 20.06.2019
 * Time: 19:49
 */

namespace WHMCS\Module\Addon\backupWHMCS\Widget;

use Carbon\Carbon;
use WHMCS\Module\AbstractWidget;
use WHMCS\Module\Addon\backupWHMCS\Models\BackupLogModel;

class AdminBackaupStatusWidget extends AbstractWidget
{
    /**
     * @type string The title of the widget
     */
    protected $title = 'Резервное копирование whmcs';

    /**
     * @type string A description/purpose of the widget
     */
    protected $description = '';

    /**
     * @type int The sort weighting that determines the output position on the page
     */
    protected $weight = 150;

    /**
     * @type int The number of columns the widget should span (1, 2 or 3)
     */
    protected $columns = 1;

    /**
     * @type bool Set true to enable data caching
     */
    protected $cache = false;

    /**
     * @type int The length of time to cache data for (in seconds)
     */
    protected $cacheExpiry = 120;

    /**
     * @type string The access control permission required to view this widget. Leave blank for no permission.
     * @see Permissions section below.
     */
    protected $requiredPermission = '';

    public function getWeight()
    {
        return 10;
    }

    /**
     * Get Data.
     *
     * Obtain data required to render the widget.
     *
     * We recommend executing queries and API calls within this function to enable
     * you to take advantage of the built-in caching functionality for improved performance.
     *
     * When caching is enabled, this method will be called when the cache is due for
     * a refresh or when the user invokes it.
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'last_cron' => BackupLogModel::where('upload_backup', '=', '1')->orderBy('created_at', 'desc')->first(),
        );
    }

    /**
     * Generate Output.
     *
     * Generate and return the body output for the widget.
     *
     * @param array $data The data returned by the getData method.
     *
     * @return string
     */
    public function generateOutput($data)
    {
        if (!empty($data['last_cron'])) {
            if ($data['last_cron']->backupDB) {
                $icon2 = 'fa fa fa-check fa-2x';
                $color2 = 'green';
                $text2 = 'БД сохранена';
            } else {
                $icon2 = 'fa fa-times fa-2x';
                $color2 = 'red';
                $text2 = 'БД не сохранена';
            }
            $text4 = 'Резервная копия была выполнена за ' . round(($data['last_cron']->backup_run_time_all) / 60, 2) . ' минуты';
            if ($data['last_cron']->backupFile) {
                $icon3 = 'fa fa fa-check fa-2x';
                $color3 = 'green';
                $text3 = 'Файлы сохранены';
            } else {
                $icon3 = 'fa fa-times fa-2x';
                $color3 = 'red';
                $text3 = 'Файлы не сохранены';
            }

            if ($data['last_cron']->created_at->diffInHours(Carbon::now()) > 24) {
                $icon = 'fa fa-times fa-2x';
                $text = 'Прошло более 24х часов с момента последней резервной копии.';
                $DiffLastRunHours = $data['last_cron']->created_at->diffInHours(Carbon::create());
                $color = 'red';
            } else {
                $icon = 'fa fa-check fa-2x';
                $text = 'Прошло менее 24х часов с момента последней резервной копии.';
                $DiffLastRunHours = $data['last_cron']->created_at->diffInHours(Carbon::create());
                $color = 'green';
            }
        } else {
            $icon2 = 'fa fa-times fa-2x';
            $color2 = 'red';
            $text2 = 'БД не сохранена';
            $icon3 = 'fa fa-times fa-2x';
            $color3 = 'red';
            $text3 = 'Файлы не сохранены';
            $icon = 'fa fa-times fa-2x';
            $text = 'Резервная копия ещё не выполнялась ни разу!';
            $DiffLastRunHours = "0";
            $color = 'red';
            $text4 = 'Резервная копия ещё не выполнялась ни разу!';
        }
        return <<<EOF
<div class="widget-content-padded">
    <div >
        <span >
            <i class="{$icon}" style="color: {$color}"></i>
          <span >{$text}</span>
        </span><br/>
        <span >
            <i class="{$icon2}" style="color: {$color2}"></i>
          <span style="height: 32px;display: table-cell;vertical-align: middle;">{$text2}</span>
        </span><br/>
        <span>
            <i class="{$icon3}" style="color: {$color3}"></i>
          <span style="height: 32px;display: table-cell;vertical-align: middle;">{$text3}</span>
        </span><br/>
        <span>
          <span style="padding-top: 2%">{$text4}</span>
        </span><br/>
     </div>
     
</div>
<div style="border-top: 1px solid #eee; padding-top: 1%; padding-left: 2%; padding-bottom: 1%;">
   Последний запуск был {$DiffLastRunHours} часа назад
</div>
EOF;
    }

}