<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceGroupItem extends Model
{
    protected $fillable = [
        'price_group_id',
        'item_id',
        'price',
    ];
}
