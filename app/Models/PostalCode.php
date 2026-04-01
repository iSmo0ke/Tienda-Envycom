<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'zip_code',
        'settlement',
        'settlement_type',
        'municipality',
        'state',
        'city',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class, 'sepomex_id');
    }
}