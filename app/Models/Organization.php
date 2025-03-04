<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organization extends Model
{
    public function account() : BelongsTo
    {
        return $this->belongsTo(Account::class)->withDefault(['value'=>'']);
    }
}
