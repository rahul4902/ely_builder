<?php

namespace App\Http\Controllers;

use App\Services\CompanyProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class CompanyRegistrationController extends Controller
{
    /** Display the dedicated company workspace registration page. */
    public function create()
    {
        return view('company-registration.create');
    }

    /** Provision a company workspace and its initial company administrator. */
    public function store(Request $request, CompanyProvisioner $provisioner)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'timezone' => ['required', 'timezone'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'terms' => ['accepted'],
        ]);

        [$company, $user] = $provisioner->registerAndProvision($data);
        Auth::login($user);
        $request->session()->regenerate();
        // Regeneration preserves existing session values. Replace any context
        // left by an earlier authenticated account with this new workspace.
        $request->session()->put('active_company_id', $company->id);

        return redirect()->route('dashboard')->with('flash_message', 'Your company workspace is ready. Welcome to ElyLeads.');
    }
}
