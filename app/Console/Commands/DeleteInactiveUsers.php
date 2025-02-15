<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\clients;
use Carbon\Carbon;

class DeleteInactiveUsers extends Command
{
    protected $signature = 'users:delete-inactive';
    protected $description = 'حذف المستخدمين غير النشطين الذين لم يتم تفعيلهم بعد مدة معينة';

    public function handle()
    {
        $deleted = clients::where('status', 'inactive')->where('created_at', '<', Carbon::now()->subHour())->delete();

        $this->info("$deleted accounts inactive deleted.");
    }
}
