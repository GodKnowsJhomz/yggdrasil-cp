<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteSite extends Model
{
    protected $fillable = [
        'name',
        'cover',
        'url',
        'points',
        'block_timer',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'points' => 'integer',
        'block_timer' => 'integer',
    ];

    public function voteBlocks()
    {
        return $this->hasMany(VoteBlock::class);
    }

    public function canVote($userId, $ipAddress)
    {
        $currentTime = time();
        
        // Verificar bloqueio por conta
        $blockByAccount = VoteBlock::where('vote_site_id', $this->id)
            ->where('user_id', $userId)
            ->first();
            
        // Verificar bloqueio por IP
        $blockByIp = VoteBlock::where('vote_site_id', $this->id)
            ->where('ip_address', $ipAddress)
            ->first();
            
        $lastTimer = 0;
        
        if ($blockByAccount) {
            $lastTimer = max($lastTimer, $blockByAccount->last_timer);
        }
        
        if ($blockByIp) {
            $lastTimer = max($lastTimer, $blockByIp->last_timer);
        }
        
        $blockUntil = $lastTimer + $this->block_timer;
        
        return $blockUntil <= $currentTime;
    }

    public function getTimeLeft($userId, $ipAddress)
    {
        $currentTime = time();
        
        $blockByAccount = VoteBlock::where('vote_site_id', $this->id)
            ->where('user_id', $userId)
            ->first();
            
        $blockByIp = VoteBlock::where('vote_site_id', $this->id)
            ->where('ip_address', $ipAddress)
            ->first();
            
        $lastTimer = 0;
        
        if ($blockByAccount) {
            $lastTimer = max($lastTimer, $blockByAccount->last_timer);
        }
        
        if ($blockByIp) {
            $lastTimer = max($lastTimer, $blockByIp->last_timer);
        }
        
        $blockUntil = $lastTimer + $this->block_timer;
        
        if ($blockUntil > $currentTime) {
            $diff = $blockUntil - $currentTime;
            $hours = floor($diff / 3600);
            $minutes = floor(($diff % 3600) / 60);
            $seconds = $diff % 60;
            
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return null;
    }
}
