<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueLoans extends Command
{
    protected $signature = 'library:check-overdue';
    protected $description = 'Check for overdue loans and update their status';

    public function handle()
    {
        $overdueLoans = Loan::where('status', 'active')
            ->where('due_date', '<', Carbon::now())
            ->get();

        foreach ($overdueLoans as $loan) {
            $loan->status = 'overdue';
            $loan->save();
            
            $this->info("Loan #{$loan->loan_number} marked as overdue");
        }

        $this->info("Checked " . $overdueLoans->count() . " overdue loans");
        
        return Command::SUCCESS;
    }
}
