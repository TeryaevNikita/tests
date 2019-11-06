<?php

namespace App\Console\Commands;

use App\Services\Set\HashableElement;
use App\Services\Set\HashableEqualsElement;
use App\Services\Set\SetStorage;
use App\Services\Set\SimpleElement;
use Illuminate\Console\Command;

class SetTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set';

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
        $storage = new SetStorage();

        $simpleObj1 = new SimpleElement(123);
        $simpleObj2 = new SimpleElement(123);

        // objects with hashable interface (add by one hash)
        $hashableObj1 = new HashableElement(1111, 123);
        $hashableObj2 = new HashableElement(1111, 123);
        $hashableObj3 = new HashableElement(1111, 123);

        // objects with hashable and equals interface (add by one hash)
        $hashableEqualsObj1 = new HashableEqualsElement(2222, 333);
        $hashableEqualsObj2 = new HashableEqualsElement(2222, 333);

        $elements = [
            "add" => [
                123,
                "123",
                [123],
                $simpleObj1,
                $hashableObj1,
                $hashableObj2,
                $hashableEqualsObj1,
            ],
            "check" => [
                123,
                "123",
                [123],
                321,
                $simpleObj1,
                $simpleObj2,
                $hashableObj3,
                $hashableEqualsObj2,
            ]
        ];

        foreach ($elements['add'] as $element) {
            $storage->add($element);
        }


        foreach ($elements['check'] as $element) {
            $this->line('Try find:');
            $this->info(print_r($element, true));
            $this->line($storage->contains($element) ? 'true' : 'false');
            $this->info('--------------------');
        }

    }
}
