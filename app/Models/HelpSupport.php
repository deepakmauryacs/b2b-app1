<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpSupport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'name',
        'contact_no',
        'email',
        'message',
        'attachment',
        'reply_message',
        'status',
        'created_by',
        'updated_by',
    ];
}
