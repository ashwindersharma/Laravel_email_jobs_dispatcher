<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Template;
use App\Models\CampaignRecipient;

class Campaign extends Model {
    //
    protected $fillable = [
        'name',
        'template_id',
        'status',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function template(): BelongsTo {
        return $this->belongsTo(Template::class);
    }

    public function recipients(): HasMany {
        return $this->hasMany(CampaignRecipient::class);
    }
}
