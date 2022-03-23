<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('agencies')->truncate();
            Agency::create([
            'name'          => 'Agency1',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
            ]);
            Agency::create([
                'name'          => 'Agency2',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
            Agency::create([
                'name'          => 'Agency3',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
            Agency::create([
                'name'          => 'Agency4',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
    }
}
