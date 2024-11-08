<?php

use App\Console\Commands\CheckExpiredSubscriptions;
use App\Console\Commands\SendInvoiceNearExpiredSubscriptions;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckExpiredSubscriptions::class)->dailyAt('23:00');
Schedule::command(SendInvoiceNearExpiredSubscriptions::class)->dailyAt('23:00');
