<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Integration;
use Illuminate\Http\Request;

class IntegrationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Integration::class);
        $check = Integration::all();
        return view('integrations.index')->withCheck($check);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Integration::class);
        $input = $request->all();

        $existing = Integration::where([
            // 'user_id' => $request->post['user_id'] ? $userId : null,
            'api_type' => $request->api_type
        ])->get();
        $existing = isset($existing[0]) ? $existing[0] : null;

        if ($existing) {
            $this->authorize('update', $existing);
            $existing->fill($input)->save();
        } else {
            Integration::create($input);
        }

        return $this->index();
    }

    public function create()
    {
        return redirect()->route('integrations.index');
    }

    public function show($id)
    {
        return redirect()->route('integrations.index');
    }

    public function edit($id)
    {
        return redirect()->route('integrations.index');
    }
}
