<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;

class TransactionDecryptAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:decrypt-amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('max_execution_time', 0);

        $transactions = Transaction::all();
        foreach ($transactions as $item) {
            $item->update(['amount1' => $item->amount]);
        }
        return Command::SUCCESS;
    }
}
