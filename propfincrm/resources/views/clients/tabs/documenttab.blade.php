<div class="mb-4">
    <h5 class="mb-3 text-slate-800 text-sm font-semibold">{{ __('Upload Document') }}</h5>
    <form method="POST" action="{{ url('/clients/upload', $client->id) }}" class="dropzone p-3 border rounded-3 bg-light text-center" id="dropzone" enctype="multipart/form-data">
        @csrf
        <div class="dz-message needsclick py-3">
            <i class="fas fa-cloud-upload-alt fa-2x text-slate-400 mb-2"></i>
            <p class="mb-0 text-sm text-slate-600">{{ __('Drop files here or click to upload') }}</p>
            <span class="text-xs text-slate-400">{{ __('Max file size applies') }}</span>
        </div>
    </form>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 text-slate-800 text-sm font-semibold">{{ __('All Documents') }}</h5>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>{{ __('File') }}</th>
                <th>{{ __('Size') }}</th>
                <th>{{ __('Created at') }}</th>
                <th class="text-end">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($client->documents as $document)
                <tr>
                    <td>
                        <a href="../files/{{ $companyname }}/{{ $document->path }}" target="_blank" class="fw-semibold text-dark text-decoration-none">
                            <i class="far fa-file me-2 text-primary"></i>{{ $document->file_display }}
                        </a>
                    </td>
                    <td>{{ $document->size }} MB</td>
                    <td>{{ $document->created_at }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ action('DocumentsController@destroy', $document->id) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this document?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="crm-btn crm-btn-danger">
                                <i class="fas fa-trash"></i> {{ __('Delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        {{ __('No documents uploaded yet.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

