<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteBlock extends Model
{
    protected $fillable = [
        'vote_site_id',
        'user_id',
        'ip_address',
        'last_timer',
    ];

    protected $casts = [
        'last_timer' => 'integer',
    ];

    public function voteSite()
    {
        return $this->belongsTo(VoteSite::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
