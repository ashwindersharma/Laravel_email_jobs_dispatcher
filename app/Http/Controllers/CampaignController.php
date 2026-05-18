<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaignJob;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\CampaignRecipient;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller {

    public function index() {
        $campaigns = Campaign::with('template')
            ->latest()
            ->paginate(10);

        return view(
            'campaigns.index',
            compact('campaigns')
        );
    }

    public function create() {
        $templates = Template::latest()->get();

        return view(
            'campaigns.create',
            compact('templates')
        );
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => ['required'],
            'template_id' => ['required', 'exists:templates,id'],
            'send_to_all' => ['nullable'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated) {

            $campaign = Campaign::create([
                'name' => $validated['name'],
                'template_id' => $validated['template_id'],
                'status' => 'draft',
                'scheduled_at' => $validated['scheduled_at'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Attach Contacts
            |--------------------------------------------------------------------------
            */

            $contacts = Contact::where(
                'status',
                'active'
            )->get();

            $recipientData = [];

            foreach ($contacts as $contact) {

                $recipientData[] = [
                    'campaign_id' => $campaign->id,
                    'contact_id' => $contact->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            CampaignRecipient::insert($recipientData);


            SendCampaignJob::dispatch($campaign);
        });

        return redirect()
            ->route('campaigns.index')
            ->with(
                'success',
                'Campaign created successfully.'
            );
    }
}
