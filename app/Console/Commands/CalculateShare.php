<?php

namespace App\Console\Commands;

use App\Services\CalculateShares;
use Illuminate\Console\Command;

class CalculateShare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate {value}';

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
     * @return mixed
     */
    public function handle()
    {
        $value = $this->argument('value');

        if (!is_numeric($value) || $value <= 0){
            $this->alert('value must be numeric and greater than 0');
            return;
        }

        $service = new CalculateShares();

        $res = $service->calculate($value);

        if (!is_array($res)) {
            $this->alert('cant calculate shares');
            return;
        }

        [$s, $p, $m] = $res;
        $this->line("For {$value}. Result [S, P, M]: [{$s}, {$p}, {$m}]");
    }
}
