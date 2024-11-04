<?php

namespace Database\Seeders;

use Throwable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\Transfer\Models\TransferStatus;

class TransferStatusSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transferStatuses = collect(config('transfer_statuses.default'));

        if ($transferStatuses->isEmpty()) {
            Log::info('[TransferStatusSeeder] No data was found to be seeded on the table transfer_statuses.');
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($transferStatuses as $transferStatus) {
                TransferStatus::updateOrCreate(
                    ['id' => $transferStatus['id']],
                    $transferStatus
                );
            }

            DB::commit();

            $this->command->info('Table transfer_statuses seeded.');
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error(
                '[TransferStatusSeeder] Error while executing seeder TransferStatusSeeder.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'stack_trace' => $exception->getTrace()
                ]
            );

            $this->command->error('Table transfer_statuses seeding failed.');
        }
    }
}
