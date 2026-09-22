@section('page-title', 'Bulk Upload Leads')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
    #bulk-upload-page { min-height:calc(100vh - 40px); background:#fff; }
    #bulk-upload-page .bulk-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 20px; border-bottom:1px solid #e2e8f0; }
    #bulk-upload-page .bulk-action { display:inline-flex; align-items:center; justify-content:center; gap:7px; height:34px; padding:0 11px; border:1px solid #dbe3ee; border-radius:6px; color:#475569; background:#fff; font-size:12px; font-weight:500; text-decoration:none; transition:.15s ease; }
    #bulk-upload-page .bulk-action:hover { border-color:#cbd5e1; background:#f8fafc; color:#1e293b; }
    #bulk-upload-page .bulk-content { max-width:960px; padding:16px 20px 24px; }
    #bulk-upload-page .bulk-title { margin:0; color:#1e293b; font-size:16px; font-weight:600; }
    #bulk-upload-page .bulk-description { margin:4px 0 12px; color:#64748b; font-size:12px; }
    #bulk-upload-page .bulk-upload-card { border:1px solid #e2e8f0; border-radius:8px; padding:12px; background:#fff; }
    #bulk-upload-page .bulk-drop-zone { display:flex; min-height:82px; align-items:center; justify-content:center; border:1px dashed #cbd5e1; border-radius:7px; background:#f8fafc; cursor:pointer; transition:.15s ease; }
    #bulk-upload-page .bulk-drop-zone:hover, #bulk-upload-page .bulk-drop-zone.is-active { border-color:#f97316; background:#fff7ed; }
    #bulk-upload-page .bulk-file-input { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; }
    #bulk-upload-page .bulk-upload-button { display:inline-flex; align-items:center; justify-content:center; gap:7px; height:34px; border:0; border-radius:6px; padding:0 14px; color:#fff; background:#f97316; font-size:12px; font-weight:600; }
    #bulk-upload-page .bulk-upload-button:hover { background:#ea580c; }
    #bulk-upload-page .bulk-notice { margin:0 0 16px; border:1px solid #bbf7d0; border-radius:6px; padding:10px 12px; color:#166534; background:#f0fdf4; font-size:12px; }
    #bulk-upload-page .bulk-results { margin-top:16px; }
    #bulk-upload-page .bulk-results-heading { margin:0 0 10px; color:#1e293b; font-size:14px; font-weight:600; }
    #bulk-upload-page .bulk-stat { border:1px solid #e2e8f0; border-radius:7px; padding:10px 12px; background:#fff; }
    #bulk-upload-page .bulk-stat-label { color:#64748b; font-size:12px; }
    #bulk-upload-page .bulk-stat-value { margin-top:2px; color:#1e293b; font-size:18px; font-weight:600; }
    #bulk-upload-page .bulk-history { overflow:hidden; border:1px solid #e2e8f0; border-radius:7px; background:#fff; }
    #bulk-upload-page .bulk-history-head { padding:11px 14px; border-bottom:1px solid #e2e8f0; color:#1e293b; background:#f7f9fc; font-size:12px; font-weight:600; }
    #bulk-upload-page .bulk-history-table { width:100%; margin:0; border-collapse:collapse; }
    #bulk-upload-page .bulk-history-table td { padding:8px 12px; border-bottom:1px solid #f1f5f9; color:#475569; font-size:12px; }
    #bulk-upload-page .bulk-history-table tr:last-child td { border-bottom:0; }
    @media (max-width:640px) { #bulk-upload-page .bulk-toolbar, #bulk-upload-page .bulk-content { padding-left:12px; padding-right:12px; } #bulk-upload-page .bulk-upload-card { padding:14px; } }
</style>
@endsection

@section('content')
<div id="bulk-upload-page">
    <div class="bulk-toolbar">
        <a href="{{ url('lead_list') }}" class="bulk-action"><i data-lucide="arrow-left" class="h-3.5 w-3.5"></i> All leads</a>
        <a href="{{ route('downloadFormat') }}" target="_blank" class="bulk-action"><i data-lucide="download" class="h-3.5 w-3.5"></i> Download CSV format</a>
    </div>
    <div class="bulk-content">
        <h1 class="bulk-title">Import leads from CSV</h1>
        <p class="bulk-description">Download the CSV template, add your lead records, then upload the completed file.</p>
        @if(session('success'))<p class="bulk-notice">{{ session('success') }}</p>@endif

        {!! Form::open(['route'=>'leads.bulk.upload.store','method'=>'POST','files'=>true,'enctype'=>'multipart/form-data']) !!}
            <div class="bulk-upload-card">
                <label class="bulk-drop-zone" for="bulk-file-input" id="bulk-drop-zone">
                    <div class="text-center">
                        <i data-lucide="file-up" class="mx-auto mb-2 h-7 w-7 text-slate-400"></i>
                        <p class="mb-1 text-sm font-medium text-slate-700">Choose a CSV file to upload</p>
                        <p class="mb-0 text-xs text-slate-500" id="bulk-file-name">CSV files only</p>
                    </div>
                </label>
                {!! Form::file('file', ['id'=>'bulk-file-input','class'=>'bulk-file-input','accept'=>'.csv,text/csv','required'=>true]) !!}
                <div class="mt-2 flex items-center justify-between gap-3"><span class="text-xs text-slate-500">Use the downloaded template for correct columns.</span><button type="submit" class="bulk-upload-button"><i data-lucide="upload" class="h-3.5 w-3.5"></i> Upload CSV</button></div>
            </div>
        {!! Form::close() !!}

        <section class="bulk-results">
            <h2 class="bulk-results-heading">Upload history & record count details</h2>
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div class="bulk-stat"><div class="bulk-stat-label">Success</div><div class="bulk-stat-value">{{ $CountValues['SucessSavedCount'] ?? 0 }}</div></div>
                <div class="bulk-stat"><div class="bulk-stat-label">Unsuccessful</div><div class="bulk-stat-value">{{ $CountValues['UnsucessSavedCount'] ?? 0 }}</div></div>
                <div class="bulk-stat"><div class="bulk-stat-label">Insert records</div><div class="bulk-stat-value">{{ $CountValues['InsertRecordCount'] ?? 0 }}</div></div>
                <div class="bulk-stat"><div class="bulk-stat-label">Already exist records</div><div class="bulk-stat-value">{{ $CountValues['AlredyExistRecordCount'] ?? 0 }}</div></div>
            </div>
            <div class="bulk-history mt-4">
                <div class="bulk-history-head">Contact No Exist</div>
                <table class="bulk-history-table"><tbody>
                    @forelse($AlreadyExistContactNo as $contact)<tr><td>{{ $contact }}</td></tr>
                    @empty<tr><td class="text-slate-500">No existing contact numbers to show.</td></tr>
                    @endforelse
                </tbody></table>
            </div>
        </section>
    </div>
</div>
@stop

@push('scripts')
<script>
$(function () {
    $('#bulk-file-input').on('change', function () { var file=this.files && this.files[0]; $('#bulk-file-name').text(file ? file.name : 'CSV files only'); $('#bulk-drop-zone').toggleClass('is-active', Boolean(file)); });
});
</script>
@endpush
