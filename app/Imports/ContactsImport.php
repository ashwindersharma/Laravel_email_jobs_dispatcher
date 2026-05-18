<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use App\Models\ContactMeta;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactsImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    ShouldQueue {

    public int $tries = 3;                  // max attempts before failed()
    // public array $backoff = [60, 300, 900]; // wait 60s, 300s, 900s between retries
    public int $timeout = 120;

    public function collection(Collection $collection) {
        DB::transaction(function () use ($collection) {

            $contacts = [];
            $metaRows = [];

            foreach ($collection as $row) {

                $data = $row->toArray();

                $validator = Validator::make($data, [
                    'email' => ['required', 'email'],
                ]);

                if ($validator->fails()) {
                    continue;
                }

                $contacts[] = [
                    'email'      => $data['email'],
                    'first_name' => $data['first_name'] ?? null,
                    'last_name'  => $data['last_name'] ?? null,
                    'status'     => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Bulk upsert contacts
            |--------------------------------------------------------------------------
            */
            Contact::upsert(
                $contacts,
                ['email'], // unique key
                ['first_name', 'last_name', 'status', 'updated_at']
            );

            /*
            |--------------------------------------------------------------------------
            | Fetch inserted/updated contacts
            |--------------------------------------------------------------------------
            */
            $contactMap = Contact::whereIn(
                'email',
                collect($contacts)->pluck('email')
            )->get()->keyBy('email');

            /*
            |--------------------------------------------------------------------------
            | Prepare contact meta rows
            |--------------------------------------------------------------------------
            */
            foreach ($collection as $row) {

                $data = $row->toArray();

                if (empty($data['email'])) {
                    continue;
                }

                $contact = $contactMap[$data['email']] ?? null;

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

                    $metaRows[] = [
                        'contact_id' => $contact->id,
                        'key'        => $key,
                        'value'      => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Bulk upsert meta
            |--------------------------------------------------------------------------
            */
            ContactMeta::upsert(
                $metaRows,
                ['contact_id', 'key'], // unique composite key
                ['value', 'updated_at']
            );
        });
    }

    public function chunkSize(): int {
        return 500;
    }
}
