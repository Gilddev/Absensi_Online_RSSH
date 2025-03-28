<?php

namespace App\Helper;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    public static function log($action, $tableName, $oldData = null, $newData = null)
    {
        LogActivity::create([
            'karu_id' => Auth::id(),
            'action' => $action,
            'table_name' => $tableName,
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }
}
