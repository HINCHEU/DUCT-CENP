<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuctType extends Model
{
    protected $fillable = ['name', 'formula_key', 'config'];

    protected function casts(): array
    {
        return [
            'config' => 'json',
        ];
    }
}
