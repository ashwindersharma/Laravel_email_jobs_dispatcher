<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Contact;

class ContactMeta extends Model {
    protected $table = 'contact_meta';

    protected $fillable = [
        'contact_id',
        'key',
        'value',
    ];

    public function contact(): BelongsTo {
        return $this->belongsTo(Contact::class);
    }
}
