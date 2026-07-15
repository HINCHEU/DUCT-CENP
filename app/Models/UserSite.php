<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSite extends Model
{
    protected $fillable = ['user_id', 'site_id', 'assigned_from', 'assigned_to'];
}
