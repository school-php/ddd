<?php

declare(strict_types=1);

namespace App\Domains\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order_id
 * @property int $good_id
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class OrderItemModel extends Model
{
    use SoftDeletes;

    protected $table = 'orders_items';

    protected $fillable = [
        'order_id',
        'good_id',
        'quantity',
    ];
}
