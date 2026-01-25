<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RefAgama; 

class RefAgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id_agama' => 1, 'nama_agama' => 'Islam'],
            ['id_agama' => 2, 'nama_agama' => 'Kristen'],
            ['id_agama' => 3, 'nama_agama' => 'Katolik'],
            ['id_agama' => 4, 'nama_agama' => 'Hindu'],
            ['id_agama' => 5, 'nama_agama' => 'Buddha'],
            ['id_agama' => 6, 'nama_agama' => 'Konghucu'],
        ];

        foreach ($data as $row) {
            RefAgama::updateOrCreate(
                ['id_agama' => $row['id_agama']],
                $row
            );
        }
    }
}
