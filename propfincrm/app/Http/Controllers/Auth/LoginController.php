<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
		
		
    }

    /**
     * Route members of more than one active company to an explicit workspace
     * selection before any tenant data is loaded.
     */
    protected function authenticated(Request $request, $user)
    {
        // The browser can carry an active workspace or impersonation from a different account.
        // Workspace context must always be derived again from this user's
        // active memberships after login.
        $request->session()->forget('active_company_id');
        $request->session()->forget('impersonating_company_id');

        // Standalone Platform Superadmin routes directly to tenant/company management
        if ($user->isPlatformSuperAdmin() && $user->companies()->wherePivot('is_active', true)->count() === 0) {
            return redirect()->route('platform.companies.index');
        }

        $companies = $user->companies()
            ->wherePivot('is_active', true)
            ->where('companies.is_active', true)
            ->count();

        if ($companies > 1) {
            return redirect()->route('companies.select');
        }

        return null;
    }

   

    
}
