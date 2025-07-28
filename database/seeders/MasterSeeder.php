<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // FuleType
        DB::table('fule_type')->insert([
            ['FuleTypeName' => 'Gasohol 95', 'Status' => 1],
            ['FuleTypeName' => 'Gasohol 91', 'Status' => 1],
            ['FuleTypeName' => 'Diesel B7',   'Status' => 1],
            ['FuleTypeName' => 'Diesel B20',  'Status' => 1],
            ['FuleTypeName' => 'E20',         'Status' => 1],
        ]);

        // Brand
        DB::table('brand')->insert([
            ['BrandName' => 'PTT',      'Status' => 1],
            ['BrandName' => 'Shell',    'Status' => 1],
            ['BrandName' => 'Bangchak', 'Status' => 1],
            ['BrandName' => 'Esso',     'Status' => 1],
            ['BrandName' => 'Susco',    'Status' => 1],
        ]);

        // ComunicataeType
        DB::table('comunicatae_type')->insert([
            ['ComunicataeName' => 'โทรศัพท์'],
            ['ComunicataeName' => 'อีเมล'],
            ['ComunicataeName' => 'LINE'],
            ['ComunicataeName' => 'เข้าเยี่ยม'],
            ['ComunicataeName' => 'อื่น ๆ'],
        ]);
    }
}
