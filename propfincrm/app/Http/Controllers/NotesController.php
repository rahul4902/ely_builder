<?php
namespace App\Http\Controllers;

use Auth;
use Session;
use App\Models\Note;
use App\Models\Lead;
use App\Http\Requests;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    /**
     * Create a note for leads
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function store(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $this->authorize('update', $lead);
        $this->validate($request, [
            'note' => 'required',
            'status' => '',
            'lead_id' => '',
            'user_id' => '']);

        $input = $request = array_merge(
            $request->all(),
            ['lead_id' => $id, 'user_id' => \Auth::id(), 'company_id' => $lead->company_id, 'status' => 0]
        );

        Note::create($input);
        Session::flash('flash_message', 'Note successfully added!'); //Snippet in Master.blade.php
        return redirect()->back();
    }
}
