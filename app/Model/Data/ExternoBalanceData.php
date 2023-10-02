<?php

namespace App\Model\Data;

use Illuminate\Database\Eloquent\Model;

class ExternoBalanceData extends Model
{
    protected $connection = 'mysql_data';
    protected $table = "balances_data";
}

