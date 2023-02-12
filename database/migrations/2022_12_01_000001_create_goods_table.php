<?php

use App\Domains\Goods\Models\GoodModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('is_active');
            $table->timestamps();
            $table->softDeletes();
        });
        GoodModel::query()->insert([
            'title' => 'Iphone 12',
            'is_active' => false,
        ]);
        GoodModel::query()->insert([
            'title' => 'Iphone 13',
            'is_active' => true,
        ]);
        GoodModel::query()->insert([
            'title' => 'Iphone 14',
            'is_active' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
    }
};
