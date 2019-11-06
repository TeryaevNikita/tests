<?php

namespace App\Console\Commands;

use App\Services\Firewall\FirewallService;
use Illuminate\Console\Command;

class IpBanner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firewall';

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
        $firewall = new FirewallService();

        $ips = [
            "127.0.0.1",
            "127.0.0.2",
            "127.0.0.3",
            "127.0.0.4",
            "127.0.0.5",
        ];

        $firewall->set($ips);

        $checkIps = [
            "127.0.0.1",
            "127.0.0.10",
        ];

        foreach ($checkIps as $ip) {
            $this->line('check:');
            $this->info($ip);
            $this->line($firewall->check($ip) ? 'true' : 'false');
            $this->info('--------------------');
        }

        return;
    }
}
