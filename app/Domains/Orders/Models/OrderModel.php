<?php

declare(strict_types=1);

namespace App\Domains\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $status
 * @property string $message
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class OrderModel extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'status',
        'message',
    ];
}
