<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = User::create([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'opd_id' => $row['opd_id'], // pastikan ada di excel
        ]);

        if (!empty($row['role'])) {
            $role = Role::where('name', $row['role'])->first();
            if ($role) {
                $user->assignRole($role);
            }
        }

        return $user;
    }

    public function chunkSize(): int
    {
        return 1000; // proses 1000 baris per chunk
    }

    public function batchSize(): int
    {
        return 1000; // insert 1000 data sekaligus
    }
}
