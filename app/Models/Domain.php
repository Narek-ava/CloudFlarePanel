<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;
    protected $fillable = ['account_id', 'domain_name', 'zone_id'];

    public function account()
    {
        return $this->belongsTo(CloudflareAccount::class, 'account_id');
    }
}
