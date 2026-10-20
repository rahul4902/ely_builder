<?php

namespace App\Http\Controllers;

use App\Models\TadaClaim;
use Illuminate\Http\Request;

class HrController extends Controller
{
    public function ta_da()
    {
        $claims = TadaClaim::orderBy('id', 'desc')->get();
        $totalClaims = $claims->count();
        $totalAllowance = $claims->sum('allowance');
        $avgAllowance = $totalClaims > 0 ? round($totalAllowance / $totalClaims, 2) : 0;
        $deptCount = $claims->pluck('dept')->unique()->count();

        return view('hr.ta_da', compact('claims', 'totalClaims', 'totalAllowance', 'avgAllowance', 'deptCount'));
    }

    public function storeTaDa(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'dept' => 'nullable|string|max:100',
            'date' => 'required|date',
            'purpose' => 'required|string|max:255',
            'allowance' => 'required|numeric|min:0',
        ]);

        $dept = $request->input('department') ?: $request->input('dept', 'General');

        $companyId = app(\App\Support\CurrentCompany::class)->id()
            ?: (auth()->check() && auth()->user()->activeCompany() ? auth()->user()->activeCompany()->id : null)
            ?: 14;

        $claim = TadaClaim::create([
            'name' => $validated['name'],
            'dept' => $dept,
            'date' => $validated['date'],
            'purpose' => $validated['purpose'],
            'allowance' => $validated['allowance'],
            'user_id' => auth()->id(),
            'company_id' => $companyId,
            'status' => 'Approved',
        ]);

        $totalClaims = TadaClaim::count();
        $totalAllowance = TadaClaim::sum('allowance');
        $avgAllowance = $totalClaims > 0 ? round($totalAllowance / $totalClaims, 2) : 0;
        $deptCount = TadaClaim::distinct('dept')->count('dept');

        return response()->json([
            'success' => true,
            'message' => 'Claim submitted successfully!',
            'claim' => [
                'id' => $claim->id,
                'name' => $claim->name,
                'dept' => $claim->dept,
                'date' => is_string($claim->date) ? $claim->date : $claim->date->format('Y-m-d'),
                'purpose' => $claim->purpose,
                'allowance' => number_format((float)$claim->allowance, 2, '.', ''),
                'status' => $claim->status,
            ],
            'stats' => [
                'total' => $totalClaims,
                'amount' => number_format($totalAllowance, 2),
                'avg' => number_format($avgAllowance, 2),
                'departments' => $deptCount,
            ]
        ]);
    }

    public function destroyTaDa($id)
    {
        $claim = TadaClaim::findOrFail($id);
        $claim->delete();

        $totalClaims = TadaClaim::count();
        $totalAllowance = TadaClaim::sum('allowance');
        $avgAllowance = $totalClaims > 0 ? round($totalAllowance / $totalClaims, 2) : 0;
        $deptCount = TadaClaim::distinct('dept')->count('dept');

        return response()->json([
            'success' => true,
            'message' => 'Claim deleted successfully',
            'stats' => [
                'total' => $totalClaims,
                'amount' => number_format($totalAllowance, 2),
                'avg' => number_format($avgAllowance, 2),
                'departments' => $deptCount,
            ]
        ]);
    }

    public function leaves()
    {
        return view('hr.leaves');
    }
}
