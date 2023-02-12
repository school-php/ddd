<?php

declare(strict_types=1);

namespace Tests\Goods\Models;

use App\Domains\Goods\Models\GoodModel;
use Tests\TestCase;

class GoodTest extends TestCase
{
    /**
     * @test
     */
    public function afterMigrationTest(): void
    {
        $goods = GoodModel::query()->get();
        $this->assertSame(3, $goods->count());
    }
}
