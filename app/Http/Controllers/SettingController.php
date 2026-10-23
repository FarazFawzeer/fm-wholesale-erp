<?php

namespace App\Http\Controllers;

use App\Support\Settings;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('settings.edit', ['s' => Settings::all()]);
    }

    public function update(Request $request)
    {
        abort_unless($request->user()->isManager(), 403, 'Only managers can change settings.');
        $data = $request->validate([
            'company_name' => 'required|max:100',
            'company_tagline' => 'nullable|max:255',
            'company_address' => 'nullable|max:255',
            'company_phone' => 'nullable|max:50',
            'default_markup_percent' => 'required|numeric|min:0|max:500',
            'min_margin_percent' => 'required|numeric|min:0|max:100',
            'max_discount_percent' => 'required|numeric|min:0|max:100',
            'default_payment_terms' => 'required|integer|min:0',
            'reminder_days_before_due' => 'required|integer|min:0|max:60',
        ]);
        Settings::set($data);

        return back()->with('success', 'Settings saved.');
    }
}
