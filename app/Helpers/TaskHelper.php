<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use Carbon\Carbon;

class TaskHelper
{
    public static function getDataIDs($data)
    {
        $dataFields = [];

        if ($data) {
            foreach ($data as $data) {
                $dataFields[] = $data['id'];
            }
        }

        return $dataFields;
    }
}
