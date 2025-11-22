<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'title',
        'body',
        'type',
        'payload',
        'recipient',
        'read',
    ];

    protected $casts = [
        'payload' => 'array',
        'read' => 'boolean',
    ];
}
