<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domain\ApiUser\Models\ApiUser;

class ApiUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apiUsers = collect(config('api_users.default'));

        DB::beginTransaction();

        $apiUsers->map(
            fn($value) => ApiUser::updateOrCreate(
                ['id' => $value['id']],
                $value
            )
        );

        DB::commit();
    }
}
