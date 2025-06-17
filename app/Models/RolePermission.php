<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'module',
        'can_add',
        'can_edit',
        'can_view',
        'can_export'
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
