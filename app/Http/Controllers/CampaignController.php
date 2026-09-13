<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Medicine;
use App\Models\Pharmacy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::with('pharmacy')->latest()->paginate(15);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('campaigns.create', ['pharmacies' => Pharmacy::where('is_active', true)->get(), 'medicines' => Medicine::where('is_active', true)->orderBy('medicine_name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $campaign = Campaign::create($validated + ['created_by' => $request->user()->id, 'promo_code' => $validated['promo_code'] ? strtoupper($validated['promo_code']) : null]);
        $campaign->medicines()->sync($this->medicineIds($request, $campaign->pharmacy_id));

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function edit(Campaign $campaign): View
    {
        return view('campaigns.edit', ['campaign' => $campaign, 'pharmacies' => Pharmacy::where('is_active', true)->get(), 'medicines' => Medicine::where('is_active', true)->orderBy('medicine_name')->get()]);
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $validated = $this->validated($request, $campaign);
        $campaign->update($validated + ['promo_code' => $validated['promo_code'] ? strtoupper($validated['promo_code']) : null]);
        $campaign->medicines()->sync($this->medicineIds($request, $campaign->pharmacy_id));

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return back()->with('success', 'Campaign deleted successfully.');
    }

    private function validated(Request $request, ?Campaign $campaign = null): array
    {
        return $request->validate([
            'pharmacy_id' => ['required', 'exists:pharmacies,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'gt:0', $request->input('discount_type') === 'percentage' ? 'lte:100' : ''],
            'promo_code' => ['nullable', 'string', 'max:50', 'alpha_dash', 'unique:campaigns,promo_code,'.($campaign?->id ?? 'NULL')],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function medicineIds(Request $request, int $pharmacyId): array
    {
        return Medicine::where('pharmacy_id', $pharmacyId)->whereIn('id', $request->input('medicine_ids', []))->pluck('id')->all();
    }
}