<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartItem;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function getCartItem()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * @return float|string
     */
    public function getTotal()
    {
        $total = 0;
        if (!empty($this->getCartItem)) {
            foreach ($this->getCartItem as $item) {
                $total += $item->getAttributes()['price'] * $item->getAttributes()['qty'];
            }
        }

        return $total;
    }
}
