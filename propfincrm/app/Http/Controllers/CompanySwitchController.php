<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanySwitchController extends Controller
{
    public function create(Request $request)
    {
        return view('companies.select', [
            'companies' => $request->user()->companies()
                ->wherePivot('is_active', true)
                ->where('companies.is_active', true)
                ->orderBy('companies.name')
                ->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate(['company_id' => ['required', 'integer']]);
        $company = $request->user()->companies()
            ->whereKey($data['company_id'])
            ->wherePivot('is_active', true)
            ->where('companies.is_active', true)
            ->firstOrFail();

        $request->session()->put('active_company_id', $company->id);

        return redirect()->intended(route('dashboard'));
    }
}
