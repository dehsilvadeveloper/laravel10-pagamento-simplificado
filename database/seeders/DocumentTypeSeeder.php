<?php

namespace Database\Seeders;

use Throwable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\DocumentType\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = collect(config('document_types.default'));

        if ($documentTypes->isEmpty()) {
            Log::info('[DocumentTypeSeeder] No data was found to be seeded on the table document_types.');
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($documentTypes as $documentType) {
                DocumentType::updateOrCreate(
                    ['id' => $documentType['id']],
                    $documentType
                );
            }

            DB::commit();

            $this->command->info('Table document_types seeded.');
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error(
                '[DocumentTypeSeeder] Error while executing seeder DocumentTypeSeeder.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'stack_trace' => $exception->getTrace()
                ]
            );

            $this->command->error('Table document_types seeding failed.');
        }
    }
}
