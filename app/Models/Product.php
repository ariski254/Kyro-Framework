<?php

namespace App\Models;

use Core\Model;

class Product extends Model {
    protected ?string $table = 'products';

    protected array $fillable = [
        // 'name', 'status'
    ];

    protected array $guarded = [
        'id'
    ];
}
