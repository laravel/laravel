<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CreditPackageController extends Controller
{
    public function index(): View
    {
        $packages = CreditPackage::orderBy('price')->paginate(20);
        return view('admin.packages.index', compact('packages'));
    }

    public function create(): View
    {
        return view('admin.packages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0.5',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        CreditPackage::create($data);
        return redirect()->route('admin.packages.index')->with('status', 'Package created');
    }

    public function edit(CreditPackage $creditPackage): View
    {
        return view('admin.packages.edit', ['pkg' => $creditPackage]);
    }

    public function update(Request $request, CreditPackage $creditPackage): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0.5',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $creditPackage->update($data);
        return redirect()->route('admin.packages.index')->with('status', 'Package updated');
    }

    public function destroy(CreditPackage $creditPackage): RedirectResponse
    {
        $creditPackage->delete();
        return back()->with('status', 'Package deleted');
    }
}
