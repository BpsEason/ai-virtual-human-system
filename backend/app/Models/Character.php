<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'persona',
    ];

    protected $casts = [
        'persona' => 'array',
    ];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
