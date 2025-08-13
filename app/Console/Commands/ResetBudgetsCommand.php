<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetBudgetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'budgets:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
public function handle()
{
    \App\Models\User::where('auto_reset', true)
        ->where(function($q) {
            $q->whereNull('last_reset_at')
              ->orWhereDate('last_reset_at', '<', now()->startOfMonth());
        })
        ->each(function($user) {
            $user->categories()->update(['current_spent' => 0]);
            $user->update(['last_reset_at' => now()]);
            $user->profile()->update(['currentspent' => 0]);
        });

    $this->info('Monthly budgets reset!');
}
}
