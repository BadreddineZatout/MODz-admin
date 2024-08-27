<?php

use App\Actions\SubscriptionExpireAction;
use Illuminate\Support\Facades\Schedule;

//Schedule Tasks
Schedule::call(new SubscriptionExpireAction)->daily();
