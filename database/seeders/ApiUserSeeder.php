<?php

namespace Database\Seeders;

use Throwable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        if ($apiUsers->isEmpty()) {
            Log::info('[ApiUserSeeder] No data was found to be seeded on the table api_users.');
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($apiUsers as $apiUser) {
                ApiUser::updateOrCreate(
                    ['id' => $apiUser['id']],
                    $apiUser
                );
            }
    
            DB::commit();

            $this->command->info('Table api_users seeded.');
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error(
                '[ApiUserSeeder] Error while executing seeder ApiUserSeeder.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'stack_trace' => $exception->getTrace()
                ]
            );

            $this->command->error('Table api_users seeding failed.');
        }
    }
}
