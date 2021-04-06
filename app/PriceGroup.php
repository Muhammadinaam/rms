<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceGroup extends Model
{
    public function priceGroupItems()
    {
        return $this->hasMany('\App\PriceGroupItem');
    }
}
