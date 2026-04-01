<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'sepomex_id',
        'calle',
        'alias',
        'is_default',
        'numero_exterior',
        'receptor_name',
        'numero_interior',
        'referencias',
        'telefono',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postalCode()
    {
        return $this->belongsTo(PostalCode::class, 'sepomex_id');
    }
}
