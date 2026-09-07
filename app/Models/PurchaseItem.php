<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = ['purchase_id', 'product_id', 'qty', 'buy_price', 'subtotal'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}