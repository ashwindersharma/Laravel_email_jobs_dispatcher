<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Campaign;

class Template extends Model {
    //
    protected $fillable = [
        'name',
        'subject',
        'body',
    ];

    public function campaigns(): HasMany {
        return $this->hasMany(Campaign::class);
    }
}
