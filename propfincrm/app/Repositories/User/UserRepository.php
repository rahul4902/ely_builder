<?php
namespace App\Repositories\User;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;
use Gate;
use Datatables;
use Carbon;
use Auth;
use DB;
use Illuminate\Support\Arr;
use App\Models\Lead;
use App\Models\Task;
use App\Models\Client;
use App\Models\Role;
use App\Support\CurrentCompany;
use App\Services\TenantUserSynchronizer;
/**
 * Class UserRepository
 * @package App\Repositories\User
 */
class UserRepository implements UserRepositoryContract
{

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return User::findOrFail($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllUsers()
    {
        $company = app(CurrentCompany::class)->get();
        return $company ? $company->activeUsers()->get() : collect();
    }



    public function listAllTeamLead()
    {
        $all_user = $this->getAllUsers();
        $teamlead = collect();
        foreach ($all_user as $key => $value) {
			//change by indrajeet
            if ($value->companyRole() === 'team_leader') {
			
                $teamlead[] = $value;
            }
        }      

        return $teamlead->pluck('name', 'id');
    }

    /**
     * @return mixed
     */
    public function getAllUsersWithDepartments()
    {
        $company = app(CurrentCompany::class)->get();
        if (!$company) {
            return collect();
        }

        $query = $company->activeUsers()->select('users.*');
        if (Auth::user()->companyRole() === 'team_leader') {
            $query->where(function ($builder) {
                $builder->where('users.id', Auth::id())
                    ->orWhere('company_user.teamlead_user_id', Auth::id());
            });
        } elseif (Auth::user()->companyRole() === 'employee') {
            $query->where('users.id', Auth::id());
        }

        return $query->get()->pluck('name', 'id');
    }

    /**
     * @param $requestData
     * @return static
     */
    public function create($requestData)
    {
        $companyname = Setting::first()->company;
        $filename = null;
        if ($requestData->hasFile('image_path')) {
            if (!is_dir(public_path(). '/images/'. $companyname)) {
                mkdir(public_path(). '/images/'. $companyname, 0777, true);
            }
            $file =  $requestData->file('image_path');

            $destinationPath = public_path(). '/images/'. $companyname;
            $filename = str_random(8) . '_' . $file->getClientOriginalName() ;
            $file->move($destinationPath, $filename);
        }

        $user = New User();
        $user->name = $requestData->name;
        $user->email = $requestData->email;
        $user->address = $requestData->address;
        $user->work_number = $requestData->work_number;
        $user->personal_number = $requestData->personal_number;
        $user->password = bcrypt($requestData->password);
        $user->image_path = $filename;
        $user->teamlead = $requestData->teamlead;
        $user->save();
        $user->roles()->attach($requestData->roles);
        $user->department()->attach($requestData->departments);
        $company = app(CurrentCompany::class)->get();
        if ($company) {
            $roleName = Role::find($requestData->roles)?->name;
            $companyRole = $roleName === 'administrator' ? 'company_admin' : (in_array($roleName, ['team leader', 'team_leader'], true) ? 'team_leader' : 'employee');
            $user->companies()->attach($company->id, [
                'role' => $companyRole,
                'teamlead_user_id' => $requestData->teamlead,
                'is_active' => true,
            ]);
            app(TenantUserSynchronizer::class)->syncUser($company, $user);
        }
        $user->save();

        Session::flash('flash_message', 'User successfully added!'); //Snippet in Master.blade.php
        return $user;
    }

    /**
     * @param $id
     * @param $requestData
     * @return mixed
     */
    public function update($id, $requestData)
    {
        $settings = Setting::first();
        $companyname = $settings->company;
        $user = User::findorFail($id);
        $validated = $requestData->validated();
        $role = $validated['roles'];
        $department = $validated['departments'];
        if($role==3){
            $user->teamlead = $requestData->teamlead;
        }else{
            $user->teamlead = null;
        }

        $input = Arr::except($validated, ['password', 'password_confirmation', 'roles', 'departments', 'image_path', 'teamlead']);
        if ($requestData->filled('password')) {
            $input['password'] = bcrypt($requestData->password);
        }

        if ($requestData->hasFile('image_path')) {
            $settings = Setting::query()->firstOrFail();
            $companyname = $settings->company;
            $file =  $requestData->file('image_path');

            $destinationPath =  public_path(). '/images/'. $companyname;
            $filename = str_random(8) . '_' . $file->getClientOriginalName() ;

            $file->move($destinationPath, $filename);
            $input['image_path'] = $filename;
        }

        $user->fill($input)->save();
        $user->roles()->sync([$role]);
        $user->department()->sync([$department]);
        $company = app(CurrentCompany::class)->get();
        if ($company && $user->belongsToCompany($company)) {
            $roleName = Role::find($role)?->name;
            $companyRole = $roleName === 'administrator' ? 'company_admin' : (in_array($roleName, ['team leader', 'team_leader'], true) ? 'team_leader' : 'employee');
            $user->companies()->updateExistingPivot($company->id, [
                'role' => $companyRole,
                'teamlead_user_id' => $requestData->teamlead,
                'is_active' => true,
            ]);
            app(TenantUserSynchronizer::class)->syncUser($company, $user);
        }

        Session::flash('flash_message', 'User successfully updated!');

        return $user;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($request, $id)
    {
        $user = User::findorFail($id);
        if ($user->hasRole('super_administrator')) {
            return Session()->flash('flash_message_warning', 'Not allowed to delete super admin');
        }

        if ($request->tasks == "move_all_tasks" && $request->task_user != "" ) {
            $user->moveTasks($request->task_user);
        }else{
		$getAllTasks=Task::where('user_created_id',$id)->get();
        $getAllassigntask=Task::where('user_assigned_id',$id)->get();
		if(count($getAllTasks) > 0){
				$deleteTask=Task::where('user_created_id',$id)->delete();
		}
        if(count($getAllassigntask) > 0){
            $deleteletask=Task::where('user_assigned_id',$id)->delete();
             }
		}

        if($request->leads == "move_all_leads" && $request->lead_user != "") {
            $user->moveLeads($request->lead_user);
        }else{
		$getAllLeads=Lead::where('user_created_id',$id)->get();
        $getAllassignlead=Lead::where('user_assigned_id',$id)->get();

		if(count($getAllLeads) > 0){
				$deletelead=Lead::where('user_created_id',$id)->delete();
		}
        if(count($getAllassignlead) > 0){
            $deletelead=Lead::where('user_assigned_id',$id)->delete();
             }
		}

        if($request->clients == "move_all_clients" && $request->client_user != "") {
            $user->moveClients($request->client_user);
        }
        
        try {
            // $user->delete();
            $user = User::where('id',$id)->delete($request->id);
            Session()->flash('flash_message', 'User successfully deleted');
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e);
            Session()->flash('flash_message_warning', 'User can NOT have, leads, clients, or tasks assigned when deleted');
        }
    }
}
