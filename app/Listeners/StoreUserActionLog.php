<?php

namespace App\Listeners;

use App\Events\UserActionLogged;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreUserActionLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserActionLogged $event): void
    {
        Log::create([
            'user_id' => $event->userId,
            'action' => $event->action,
            'ip_address' => $event->ipAddress,
        ]);
    }
}
