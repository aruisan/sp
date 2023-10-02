<?php

namespace App\Model\Data;

use Illuminate\Database\Eloquent\Model;

class ExternoBalance extends Model
{
    protected $connection = 'mysql_data';
    protected $table = "balances";

    public function data(){
        return $this->hasMany(ExternoBalanceData::class,'balance_id');
    }
}

