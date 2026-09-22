<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use App\Models\CallLog;
use App\Models\User;

class CallLogController extends Controller
{

    public function index(Request $request)
    {

        if ($request->ajax()) {
            
            // Get the user ID from the request
            $userId = $request->input('user');

            // Get the date filters, if provided
            $fromDate = $request->input('from') ? Carbon::parse($request->input('from')) : null;
            $endDate = $request->input('to') ? Carbon::parse($request->input('to'))->endOfDay() : null;
            // Build the query
            $query = CallLog::select([
                'call_logs.id',
                'call_logs.call_start_datetime',
                'call_logs.call_end_datetime',
                'call_logs.created_at',
                'users.name as call_from_user',
                'leads.name as lead_name',
                'leads.contact_no'
            ])
            
                ->join('users', 'call_logs.user_id', '=', 'users.id')
                ->join('leads', 'leads.id', '=', 'call_logs.lead_id');
                // ->where('call_logs.user_id', $userId);
              
            
            // Apply date filters
            if ($userId) {
                $query->where('call_logs.user_id', $userId);
            }
            if ($fromDate) {
                $query->where('call_logs.call_start_datetime', '>=', $fromDate);
            }
            if ($endDate) {
                $query->where('call_logs.call_start_datetime', '<=', $endDate);
            }

            // Order the results
            $query->orderBy('call_logs.id', 'desc');
            
            // Use Yajra DataTables for pagination
            return DataTables::of($query)
                ->editColumn('call_start_datetime', function ($row) {
                    return  strtotime($row->call_start_datetime) ? date('d-m-Y H:i:s', strtotime($row->call_start_datetime)) : '';
                })
                ->editColumn('call_end_datetime', function ($row) {
                    return  strtotime($row->call_end_datetime) ? date('d-m-Y H:i:s', strtotime($row->call_end_datetime)) : '';
                })
                ->editColumn('created_at', function ($row) {
                    return  strtotime($row->created_at) ? date('d-m-Y H:i:s', strtotime($row->created_at)) : '';
                })
                ->addColumn('duration', function ($row) {
                    if (strtotime($row->call_start_datetime) && strtotime($row->call_end_datetime)) {
                        $start = Carbon::parse($row->call_start_datetime);
                        $end = Carbon::parse($row->call_end_datetime);
                        $totalSeconds = $end->diffInSeconds($start);
                        $minutes = floor($totalSeconds / 60);
                        $seconds = $totalSeconds % 60;
                        if($minutes != 0){
                            return $minutes . ' minutes ' . $seconds . ' seconds';
                        }elseif($seconds!=0){
                            return $seconds . ' seconds';
                        }else{
                            return '';
                        }
                        
                    }
                    return '';
                })
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        }
        $users = User::select(['id', 'name', 'email'])->get();
        return view('leads.call_logs.index', compact('users'));
    }
}
