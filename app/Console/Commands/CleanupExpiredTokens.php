<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Token;

class CleanupExpiredTokens extends Command
{
    protected $signature = 'tokens:cleanup';
    protected $description = 'Remove expired tokens from the database';

    public function handle()
    {
        $deletedCount = Token::where('expires_at', '<', now())->delete();
        $this->info("Deleted {$deletedCount} expired tokens.");
    }
}
