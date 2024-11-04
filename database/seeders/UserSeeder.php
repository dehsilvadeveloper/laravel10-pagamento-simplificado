<?php

namespace Database\Seeders;

use Throwable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = collect(config('users.default'));

        if ($users->isEmpty()) {
            Log::info('[UserSeeder] No data was found to be seeded on the table users and on its related tables.');
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($users as $data) {
                $user = User::create($data['user']);

                Wallet::create([
                    'user_id' => $user->id,
                    'balance' => $data['wallet']['balance']
                ]);

                unset($user);
            }

            DB::commit();

            $this->command->info('Table users and table wallets seeded.');
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error(
                '[UserSeeder] Error while executing seeder UserSeeder.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'users' => $users->toArray() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );
        }
    }
}
