<?php

namespace App\Models;

use DateTimeInterface;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use Filterable;

    public function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
