<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VoteSite;

class VoteSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            [
                'name' => 'RagnaTop 100',
                'url' => 'https://ragnatop100.com/votar',
                'points' => 1,
                'block_timer' => 43200, // 12 horas
                'active' => true,
            ],
            [
                'name' => 'TopRagnarok',
                'url' => 'https://topragnarok.org/vote',
                'points' => 2,
                'block_timer' => 86400, // 24 horas
                'active' => true,
            ],
            [
                'name' => 'RateMyServer',
                'url' => 'https://ratemyserver.net/vote',
                'points' => 1,
                'block_timer' => 43200, // 12 horas
                'active' => true,
            ],
            [
                'name' => 'MMORPG Top 200',
                'url' => 'https://mmorpg-top200.com/vote',
                'points' => 3,
                'block_timer' => 86400, // 24 horas
                'active' => true,
            ],
        ];

        foreach ($sites as $site) {
            VoteSite::create($site);
        }
    }
}
