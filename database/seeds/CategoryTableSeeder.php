<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::firstOrCreate([
            'id'    => 1,
            'name'  => 'Dinning'
        ]);

        Category::firstOrCreate([
            'id'    => 2,
            'name'  => 'Shopping'
        ]);

        Category::firstOrCreate([
            'id'    => 3,
            'name'  => 'Travel'
        ]);

        Category::firstOrCreate([
            'id'    => 4,
            'name'  => 'Daily Expense'
        ]);

        Category::firstOrCreate([
            'id'    => 5,
            'name'  => 'Big Tickets'
        ]);

        Category::firstOrCreate([
            'id'    => 6,
            'name'  => 'Airport Lounge'
        ]);
    }
}
