<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         //\App\Models\User::factory(10)->create();

         DB::table('users')->insert([
            'slug' => 'devgate',
            'name' => 'Devgate Consórcios LTDA',
            'name_display' => 'Devgate Consórcios',
            'email' => 'wobetoberlitz'.'@gmail.com',
            'email_display' => 'wobetoberlitz'.'@gmail.com',
            'cnpj' => '28.099.840/0001-69',
            'master' => true,
            'password' => Hash::make('Berlitz@206905'),
            'phone' => "(51) 99109-0700",
            'address' => "Rua dos pinheiros número 76",
            'color' => "#7fceff",
            'second_color' => "#8159d1",
        ]);
    }
}
