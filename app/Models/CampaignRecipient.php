<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Campaign;
use App\Models\Contact;

class CampaignRecipient extends Model {
    //
    protected $fillable = [
        'campaign_id',
        'contact_id',
        'status',
        'provider',
        'provider_message_id',
        'error_message',
        'retry_count',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function campaign(): BelongsTo {
        return $this->belongsTo(Campaign::class);
    }

    public function contact(): BelongsTo {
        return $this->belongsTo(Contact::class);
    }
}
