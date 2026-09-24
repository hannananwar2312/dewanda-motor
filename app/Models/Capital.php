<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capital extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'date', 'note'];
}