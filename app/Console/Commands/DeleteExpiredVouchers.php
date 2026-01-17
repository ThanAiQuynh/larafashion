<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use Illuminate\Console\Command;

class DeleteExpiredVouchers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'vouchers:delete-expired';

    /**
     * The console command description.
     */
    protected $description = 'Xóa các voucher đã hết hạn';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredCount = Voucher::where('end_date', '<', now())->count();

        if ($expiredCount > 0) {
            Voucher::where('end_date', '<', now())->delete();
            $this->info("Đã xóa {$expiredCount} voucher hết hạn.");
        } else {
            $this->info('Không có voucher nào hết hạn cần xóa.');
        }

        return Command::SUCCESS;
    }
}
