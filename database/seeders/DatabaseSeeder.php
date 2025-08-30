<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Branch::create([
            'name' => 'Nano Geyser',
            'address' => 'Kiến Hưng - Hà Đông - Hà Nội',
            'code_misa' => 'NANOGEYSER',
            'is_active' => 1,
            'slug' => generateSlug('Nano Geyser')
        ]);
        Branch::create([
            'name' => 'Ofan',
            'address' => 'Hưng Yên - Hà Nội',
            'code_misa' => 'OFAN',
            'is_active' => 1,
            'branch_id' => 1,
            'slug' => generateSlug('Ofan')
        ]);
    }
}
