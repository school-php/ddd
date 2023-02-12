<?php

namespace App\Http\Controllers\Goods;

use App\Domains\Goods\Models\GoodModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class GoodsController extends Controller
{
    /**
     * @OA\Get(
     * path="/goods",
     * summary="Goods: get all",
     * description="Get all goods",
     * operationId="getGoods",
     * @OA\Response(
     *    response=200,
     *    description="Array of Good's Models"
     * )
     * )
     */
    public function getAllGoods(): ?Collection
    {
        return GoodModel::query()->get();
    }
}
