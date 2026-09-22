@extends('layouts.master')

@section('page-title', 'Roles & Permissions')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')

@section('styles')
<style>
    #role-permissions-page { min-height:calc(100vh - 40px); font-family:'Outfit',sans-serif; }
    #role-permissions-page .role-permissions-toolbar { display:flex; justify-content:space-between; gap:16px; align-items:center; padding:12px 24px; border-bottom:1px solid #e8edf3; }
    #role-permissions-page .role-permissions-toolbar p { margin:3px 0 0; color:#64748b; font-size:12px; }
    #role-permissions-page .role-panel { margin:0; border:0; border-bottom:1px solid #e8edf3; border-radius:0; overflow:hidden; }
    #role-permissions-page .role-panel-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 24px; background:#f8fafc; border-bottom:1px solid #e8edf3; }
    #role-permissions-page .role-panel-head h2 { margin:0; color:#1e293b; font-size:13px; font-weight:600; }
    #role-permissions-page .role-permission-list { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:0; padding:0 24px; }
    #role-permissions-page .permission-option { display:flex; min-height:42px; align-items:center; gap:8px; border-right:1px solid #eef2f6; border-bottom:1px solid #eef2f6; padding:0 12px; color:#475569; font-size:12px; }
    #role-permissions-page .permission-option:nth-child(4n) { border-right:0; }
    #role-permissions-page .permission-option input { accent-color:#f97316; }
    #role-permissions-page .save-role { border:0; border-radius:6px; padding:7px 10px; background:#f97316; color:#fff; font-size:11px; font-weight:600; }
    #role-permissions-page .manage-roles { border:1px solid #cbd5e1; border-radius:6px; padding:7px 10px; color:#334155; font-size:11px; font-weight:600; text-decoration:none; }
    @media(max-width:900px) { #role-permissions-page .role-permission-list { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:600px) { #role-permissions-page .role-permissions-toolbar { align-items:flex-start; flex-direction:column; padding:12px 14px; } #role-permissions-page .role-permission-list { grid-template-columns:1fr; padding:0 14px; } #role-permissions-page .permission-option { border-right:0; } #role-permissions-page .role-panel-head { padding:10px 14px; } }
</style>
@endsection

@section('content')
<div id="role-permissions-page">
    <div class="role-permissions-toolbar">
        <div><strong class="text-sm text-slate-800">Roles &amp; Permissions</strong><p>Manage the existing CRM roles and the actions each role can perform.</p></div>
        <a href="{{ route('roles.index') }}" class="manage-roles">Manage roles</a>
    </div>

    @foreach($roles as $role)
        <form method="POST" action="{{ route('master.roles-permissions.update') }}" class="role-panel">
            @csrf
            @method('PATCH')
            <input type="hidden" name="role_id" value="{{ $role->id }}">
            <div class="role-panel-head">
                <h2>{{ $role->display_name ?: ucfirst($role->name) }}</h2>
                <button class="save-role" type="submit">Save permissions</button>
            </div>
            <div class="role-permission-list">
                @foreach($permission as $perm)
                    <label class="permission-option">
                        <input type="checkbox" name="permissions[{{ $perm->id }}]" value="1" {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}>
                        <span>{{ $perm->display_name ?: $perm->name }}</span>
                    </label>
                @endforeach
            </div>
        </form>
    @endforeach
</div>
@endsection
