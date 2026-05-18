<?php

namespace App\Http\Controllers;

use App\Imports\ContactsImport;
use App\Models\Contact;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ContactImportController extends Controller {


    public function index(Request $request) {
        $query = Contact::select([
            'id',
            'email',
            'first_name',
            'last_name',
            'status',
            'created_at',
        ]);

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('email', 'ILIKE', "{$search}%")
                    ->orWhere('first_name', 'ILIKE', "{$search}%")
                    ->orWhere('last_name', 'ILIKE', "{$search}%");
            });
        }

        $contacts = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('contacts.index', compact('contacts'));
    }

    public function import(Request $request) {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,xlsx,xls',
            ],
        ]);

        Excel::queueImport(
            new ContactsImport,
            $request->file('file')
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Import started successfully.'
            );
    }
}
