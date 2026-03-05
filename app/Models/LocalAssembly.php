<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalAssembly extends Model
{
    use SoftDeletes;
    protected $fillable = ['church_group_id', 'name'];

    public function churchGroup(): BelongsTo
    {
        return $this->belongsTo(ChurchGroup::class);
    }
}