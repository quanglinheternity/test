<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Các trường bạn muốn cho phép gán giá trị hàng loạt
    protected $fillable = [
        'name', 'description', 'price', 'quantity', 'image',
    ];
}
