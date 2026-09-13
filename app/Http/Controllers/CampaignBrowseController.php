<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\View\View;

class CampaignBrowseController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::with('pharmacy')->where('is_active', true)->where('starts_at', '<=', now())->where('ends_at', '>=', now())->where(function ($query) {
            $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
        })->latest()->paginate(12);

        return view('campaigns.browse', compact('campaigns'));
    }
}