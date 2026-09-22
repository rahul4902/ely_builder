@extends('layouts.master')

@section('page-title', 'Roles')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')

@section('styles')
<style>
    #roles-page { min-height:calc(100vh - 40px); font-family:'Outfit',sans-serif; }
    #roles-page .roles-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 24px; border-bottom:1px solid #e8edf3; }
    #roles-page .roles-toolbar p { margin:3px 0 0; color:#64748b; font-size:12px; }
    #roles-page .roles-toolbar-actions { display:flex; align-items:center; gap:8px; }
    #roles-page .roles-search { width:220px; height:32px; border:1px solid #d8e0e8; border-radius:6px; padding:0 10px; color:#334155; font-size:12px; }
    #roles-page .roles-add { border:0; border-radius:6px; background:#f97316; padding:8px 12px; color:#fff; font-size:12px; font-weight:600; text-decoration:none; }
    #roles-page table { width:100%; border-collapse:collapse; font-size:12px; }
    #roles-page th { padding:9px 24px; border-bottom:1px solid #e2e8f0; background:#f7f9fc; color:#94a3b8; font-size:10px; font-weight:600; letter-spacing:.05em; text-align:left; text-transform:uppercase; }
    #roles-page td { padding:12px 24px; border-bottom:1px solid #eef2f6; color:#475569; vertical-align:middle; }
    #roles-page tr:hover td { background:#fafbfc; }
    #roles-page .role-name { color:#1e293b; font-weight:600; }
    #roles-page .role-default { display:inline-block; margin-left:8px; border-radius:999px; background:#fff7ed; padding:2px 7px; color:#c2410c; font-size:10px; font-weight:600; }
    #roles-page .role-view { border:1px solid #d8e0e8; border-radius:5px; background:#fff; padding:5px 8px; color:#334155; font-size:11px; }
    #roles-page .role-delete { border:1px solid #fecaca; border-radius:5px; background:#fff; padding:5px 8px; color:#b91c1c; font-size:11px; }
    #roles-page .role-actions { display:flex; align-items:center; gap:6px; }
    #roles-page .roles-footer { display:flex; justify-content:flex-end; border-top:1px solid #e8edf3; padding:10px 24px; }
    #roles-page .dataTables_wrapper .dataTables_filter, #roles-page .dt-search { display:none; }
    #role-permissions-offcanvas { width:100vw !important; max-width:100vw !important; }
    #role-permissions-offcanvas .offcanvas-header { padding:12px 24px; border-bottom:1px solid #e8edf3; }
    #role-permissions-offcanvas .offcanvas-body { padding:20px 24px; }
    #role-permissions-offcanvas .permission-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); border-top:1px solid #e8edf3; border-left:1px solid #e8edf3; }
    #role-permissions-offcanvas .permission-cell { min-height:48px; display:flex; align-items:center; border-right:1px solid #e8edf3; border-bottom:1px solid #e8edf3; padding:0 12px; color:#475569; font-size:12px; }
    #role-permissions-offcanvas .permission-cell i { margin-right:8px; color:#f97316; }
    @media(max-width:600px) { #roles-page .roles-toolbar { align-items:flex-start; flex-direction:column; padding:12px 14px; } #roles-page .roles-toolbar-actions { width:100%; } #roles-page .roles-search { flex:1; width:auto; } #roles-page th, #roles-page td { padding-left:14px; padding-right:14px; } #roles-page th:nth-child(2), #roles-page td:nth-child(2) { display:none; } #role-permissions-offcanvas .offcanvas-header, #role-permissions-offcanvas .offcanvas-body { padding-left:14px; padding-right:14px; } #role-permissions-offcanvas .permission-grid { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
<div id="roles-page">
    <div class="roles-toolbar"><div><strong class="text-sm text-slate-800">Roles</strong><p>Create and maintain the existing CRM roles.</p></div><div class="roles-toolbar-actions"><input id="roles-search" class="roles-search" placeholder="Search roles"><a href="{{ route('roles.create') }}" class="roles-add">+ Add role</a></div></div>
    <table id="roles-table"><thead><tr><th>Role</th><th>Description</th><th>Permissions</th><th>Action</th></tr></thead><tbody>
        @forelse($roles as $role)
            <tr><td><span class="role-name">{{ $role->display_name ?: ucfirst($role->name) }}</span>@if($role->id === 1)<span class="role-default">Default</span>@endif</td><td>{{ Str_limit($role->description, 80) ?: '—' }}</td><td>{{ $role->permissions->count() }}</td><td>
                        <div class="crm-row-actions">
                            <div class="dropdown">
                                <button type="button"
                                        class="crm-ellipsis-btn dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        data-bs-auto-close="true"
                                        aria-expanded="false">
                                    <i class="fa fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end crm-action-menu">
                                    <li>
                                        <button type="button"
                                                class="dropdown-item"
                                                data-role-permissions="{{ $role->id }}"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#role-permissions-offcanvas">
                                            <i class="fa fa-shield"></i> View Permissions
                                        </button>
                                    </li>
                                    @if($role->id !== 1)
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST"
                                                  action="{{ route('roles.destroy', $role->id) }}"
                                                  onsubmit="return confirm('Delete this role? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </td></tr>
        @empty
            <tr><td colspan="4" class="text-center text-slate-400">No roles found.</td></tr>
        @endforelse
    </tbody></table>
    @foreach($roles as $role)
        <template id="role-permission-template-{{ $role->id }}"><div><p class="mb-4 text-xs text-slate-500">Permissions assigned to <strong class="text-slate-700">{{ $role->display_name ?: ucfirst($role->name) }}</strong>.</p><div class="permission-grid">@forelse($role->permissions as $permission)<div class="permission-cell"><i data-lucide="check-circle-2" class="h-4 w-4"></i>{{ $permission->display_name ?: $permission->name }}</div>@empty<div class="permission-cell">No permissions assigned.</div>@endforelse</div></div></template>
    @endforeach
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="role-permissions-offcanvas" aria-labelledby="role-permissions-title"><div class="offcanvas-header"><div><h5 class="mb-0 text-sm font-semibold text-slate-800" id="role-permissions-title">Role permissions</h5><p class="mb-0 mt-1 text-xs text-slate-500">Read-only permission details.</p></div><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></div><div class="offcanvas-body" id="role-permissions-body"></div></div>
@endsection

@push('scripts')
<script>
$(function () {
    var rolesTable = $('#roles-table').DataTable({ pagingType:'full_numbers', order:[], autoWidth:false, dom:'<"w-full overflow-x-auto"t><"roles-footer"ip>', language:{ emptyTable:'No roles found', info:'Showing _START_ to _END_ of _TOTAL_ roles', infoEmpty:'Showing 0 to 0 of 0 roles', paginate:{previous:'Previous', next:'Next'} } });
    $('#roles-search').on('input', function () { rolesTable.search(this.value).draw(); });
    $(document).on('click', '[data-role-permissions]', function () { var template = document.getElementById('role-permission-template-' + this.dataset.rolePermissions); $('#role-permissions-body').html(template ? template.innerHTML : '<p class="text-muted">Permissions are unavailable.</p>'); window.lucide && window.lucide.createIcons(); });
});
</script>
@endpush
