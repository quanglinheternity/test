<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Tạo trường id tự động
            $table->string('name'); // Tên sản phẩm
            $table->text('description')->nullable(); // Mô tả sản phẩm
            $table->decimal('price', 8, 2); // Giá sản phẩm, có thể lưu số với 2 chữ số thập phân
            $table->integer('quantity')->default(0); // Số lượng sản phẩm, mặc định là 0
            $table->string('image')->nullable(); // Hình ảnh của sản phẩm (nếu có)
            $table->timestamps(); // Các trường created_at và updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
