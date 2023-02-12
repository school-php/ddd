<?php

declare(strict_types=1);

namespace App\Domains\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class GoodModel extends Model
{
    use SoftDeletes;

    protected $table = 'goods';

    protected $fillable = [
        'title',
        'is_active',
    ];
}
