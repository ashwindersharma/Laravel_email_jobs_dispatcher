<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller {
    public function index() {
        $templates = Template::latest()
            ->paginate(10);

        return view(
            'templates.index',
            compact('templates')
        );
    }

    public function create() {
        return view('templates.create_template');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => ['required'],
            'subject' => ['required'],
            'body' => ['required'],
        ]);

        Template::create($validated);

        return redirect()
            ->route('templates.index')
            ->with(
                'success',
                'Template created successfully.'
            );
    }

    public function edit(Template $template) {
        return view(
            'templates.edit',
            compact('template')
        );
    }

    public function update(
        Request $request,
        Template $template
    ) {
        $validated = $request->validate([
            'name' => ['required'],
            'subject' => ['required'],
            'body' => ['required'],
        ]);

        $template->update($validated);

        return redirect()
            ->route('templates.index')
            ->with(
                'success',
                'Template updated successfully.'
            );
    }

    public function destroy(Template $template) {
        $template->delete();

        return redirect()
            ->route('templates.index')
            ->with(
                'success',
                'Template deleted successfully.'
            );
    }
}
