<?php

namespace App\Actions;

use App\Models\Subscription;

class SubscriptionExpireAction
{
    public function __invoke()
    {
        Subscription::where('status', 'ACTIVE')
            ->get()->map(function (Subscription $subscription) {
                if ($subscription->ends_at?->lt(now())) {
                    $subscription->status = 'EXPIRED';
                    $subscription->save();
                }
            });
    }
}
