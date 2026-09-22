<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use App\Models\CallLog;
use App\Models\User;
use App\Models\UserCheckinCheckout;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {

        if ($request->ajax()) {
            // Get the user ID from the request
            $monthInput = $request->input('month') . '-01';
            $year = strtotime($monthInput) ? date('Y', strtotime($monthInput)) : date('Y');
            $month = strtotime($monthInput) ? date('m', strtotime($monthInput)) : date('m');
            $query = UserCheckinCheckout::select('users_checkin_checkout.*', 'users.name as name', 'users.work_number as work_number')
                ->join('users', 'users_checkin_checkout.user_id', '=', 'users.id')
                ->whereMonth('check_in_date', $month)
                ->whereYear('check_in_date', $year)
                ->orderBy('check_in_date', 'desc');

            // Use Yajra DataTables for pagination
            return DataTables::of($query)
                ->editColumn('check_in_address', function ($row) {                    
                    return  $row->check_in_address;
                })
                ->editColumn('check_in_date', function ($row) {                    
                    return  strtotime($row->check_in_date) ? date('d-m-Y', strtotime($row->check_in_date)) : '';
                })
                ->addColumn('check_in_time', function ($row) {                    
                    return  strtotime($row->check_in_date) ? date('H:i:s', strtotime($row->check_in_date)) : '';
                })
                ->editColumn('check_out_date', function ($row) {                    
                    return  strtotime($row->check_out_date) ? date('d-m-Y', strtotime($row->check_out_date)) : '';
                })
                ->addColumn('check_out_time', function ($row) {                    
                    return  strtotime($row->check_out_date) ? date('H:i:s', strtotime($row->check_out_date)) : '';
                })
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        }
        return view('attendance.index');
    }
}
