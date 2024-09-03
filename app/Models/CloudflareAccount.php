<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudflareAccount extends Model
{
    use HasFactory;

    protected $fillable = ['id','name', 'email', 'api_key', 'user_id'];

    public function domains()
    {
        return $this->hasMany(Domain::class, 'account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
