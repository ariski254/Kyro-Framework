<?php

namespace App\Models;

use Core\Model;

class User extends Model {
    protected ?string $table = 'users';

    protected array $fillable = [
        'name',
        'email',
        'password',
    ];

    protected array $guarded = [
        'id',
    ];
}
