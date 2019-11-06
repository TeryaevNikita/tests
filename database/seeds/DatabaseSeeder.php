<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product')->truncate();

        for ($i = 0; $i < 50; $i++) {
            $this->createProduct($i);
        }
    }

    private function createProduct($i)
    {
        DB::table('product')->insert([
            'title' => "Штука № " . $i,
            'value' => rand(0, 100000) / 100.0
        ]);
    }
}
