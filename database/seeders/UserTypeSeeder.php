<?php

namespace Database\Seeders;

use Throwable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\User\Models\UserType;

class UserTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = collect(config('user_types.default'));

        if ($userTypes->isEmpty()) {
            Log::info('[UserTypeSeeder] No data was found to be seeded on the table user_types.');
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($userTypes as $userType) {
                UserType::updateOrCreate(
                    ['id' => $userType['id']],
                    $userType
                );
            }
    
            DB::commit();

            $this->command->info('Table user_types seeded.');
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error(
                '[UserTypeSeeder] Error while executing seeder UserTypeSeeder.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'stack_trace' => $exception->getTrace()
                ]
            );

            $this->command->error('Table user_types seeding failed.');
        }
    }
}
