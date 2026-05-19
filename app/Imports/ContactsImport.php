<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\ContactMeta;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactsImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts,
    ShouldQueue {
    /**
     * Queue settings
     */
    public int $tries = 3;

    public int $timeout = 120;

    /**
     * Process each chunk
     */
    public function collection(Collection $collection): void {
        $contacts = [];
        $metaRows = [];

        /*
        |--------------------------------------------------------------------------
        | Prepare contacts
        |--------------------------------------------------------------------------
        */
        foreach ($collection as $row) {

            $data = $row->toArray();

            $validator = Validator::make($data, [
                'email' => ['required', 'email'],
            ]);

            if ($validator->fails()) {
                continue;
            }

            $contacts[] = [
                'email'      => trim($data['email']),
                'first_name' => $data['first_name'] ?? null,
                'last_name'  => $data['last_name'] ?? null,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Stop if no valid contacts
        |--------------------------------------------------------------------------
        */
        if (empty($contacts)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Bulk upsert contacts
        |--------------------------------------------------------------------------
        */
        Contact::upsert(
            $contacts,
            ['email'],
            ['first_name', 'last_name', 'status', 'updated_at']
        );

        /*
        |--------------------------------------------------------------------------
        | Fetch contacts map
        |--------------------------------------------------------------------------
        */
        $contactMap = Contact::whereIn(
            'email',
            collect($contacts)->pluck('email')
        )->get()->keyBy('email');

        /*
        |--------------------------------------------------------------------------
        | Prepare meta rows
        |--------------------------------------------------------------------------
        */
        foreach ($collection as $row) {

            $data = $row->toArray();

            if (empty($data['email'])) {
                continue;
            }

            $email = trim($data['email']);

            $contact = $contactMap[$email] ?? null;

            if (!$contact) {
                continue;
            }

            foreach ($data as $key => $value) {

                if (in_array($key, [
                    'email',
                    'first_name',
                    'last_name'
                ])) {
                    continue;
                }

                if ($value === null || $value === '') {
                    continue;
                }

                $metaRows[] = [
                    'contact_id' => $contact->id,
                    'key'        => $key,
                    'value'      => (string) $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Bulk upsert metadata
        |--------------------------------------------------------------------------
        */
        if (!empty($metaRows)) {

            ContactMeta::upsert(
                $metaRows,
                ['contact_id', 'key'],
                ['value', 'updated_at']
            );
        }
    }

    /**
     * Chunk size
     */
    public function chunkSize(): int {
        return 500;
    }

    /**
     * Batch insert size
     */
    public function batchSize(): int {
        return 500;
    }
}
