<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ContactMeta;
use App\Models\CampaignRecipient;

class Contact extends Model {
    //
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'status',
    ];

    public function meta(): HasMany {
        return $this->hasMany(ContactMeta::class);
    }

    public function campaignRecipients(): HasMany {
        return $this->hasMany(CampaignRecipient::class);
    }
}
