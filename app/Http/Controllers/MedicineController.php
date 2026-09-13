<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicineRequest;
use App\Http\Requests\UpdateMedicineRequest;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Pharmacy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicineController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Medicine::class);
        $search = trim($request->string('q')->toString());

        $medicines = Medicine::with(['category', 'pharmacy'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('medicine_name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%");
            }))
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->orderBy('medicine_name')
            ->paginate(10)
            ->withQueryString();

        return view('medicines.index', [
            'medicines' => $medicines,
            'categories' => MedicineCategory::where('is_active', true)->orderBy('name')->get(),
            'search' => $search,
        ]);
    }

    public function show(Medicine $medicine): View
    {
        $this->authorize('view', $medicine);

        return view('medicines.show', compact('medicine'));
    }

    public function create(): View
    {
        $this->authorize('create', Medicine::class);

        return view('medicines.create', ['categories' => MedicineCategory::where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(StoreMedicineRequest $request): RedirectResponse
    {
        $pharmacy = $this->managedPharmacy($request);
        $medicine = $pharmacy->medicines()->create($request->safe()->except('requires_prescription') + [
            'requires_prescription' => $request->boolean('requires_prescription'),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('medicines.show', $medicine)->with('success', 'Medicine added successfully.');
    }

    public function edit(Medicine $medicine): View
    {
        $this->authorize('update', $medicine);

        return view('medicines.edit', [
            'medicine' => $medicine,
            'categories' => MedicineCategory::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateMedicineRequest $request, Medicine $medicine): RedirectResponse
    {
        $medicine->update($request->safe()->except('requires_prescription') + [
            'requires_prescription' => $request->boolean('requires_prescription'),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('medicines.show', $medicine)->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Request $request, Medicine $medicine): RedirectResponse
    {
        $this->authorize('delete', $medicine);
        $medicine->delete();

        return redirect()->route('medicines.index')->with('success', 'Medicine deleted successfully.');
    }

    private function managedPharmacy(Request $request): Pharmacy
    {
        $pharmacy = $request->user()->pharmacies()->first();

        abort_unless($pharmacy || $request->user()->role === 'admin', 403, 'No pharmacy is assigned to this account.');

        return $pharmacy ?? Pharmacy::where('is_active', true)->firstOrFail();
    }
}