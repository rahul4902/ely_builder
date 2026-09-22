<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 text-slate-800 text-sm font-semibold">{{ __('All Tasks') }}</h5>
    <a href="{{ route('tasks.create', ['client' => $client->id]) }}" class="crm-btn crm-btn-primary">
        <i class="fas fa-plus"></i> {{ __('New Task') }}
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Assigned') }}</th>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Deadline') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($client->tasks as $task)
                <tr>
                    <td>
                        <a href="{{ route('tasks.show', $task->id) }}" class="fw-semibold text-dark text-decoration-none">
                            {{ $task->title }}
                        </a>
                    </td>
                    <td>
                        @if($task->user)
                            <a href="{{ route('users.show', $task->user->id) }}" class="text-secondary text-decoration-none">
                                <i class="fas fa-user-circle me-1"></i>{{ $task->user->name }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ date('d, M Y, H:i', strtotime($task->created_at)) }}</td>
                    <td>
                        {{ date('d, M Y', strtotime($task->deadline)) }}
                        @if ($task->status == 1 && $task->days_until_deadline)
                            <small class="text-muted">({{ $task->days_until_deadline }})</small>
                        @endif
                    </td>
                    <td>
                        @if ($task->status == 1)
                            <span class="crm-badge crm-badge-orange">{{ __('Open') }}</span>
                        @else
                            <span class="crm-badge crm-badge-green">{{ __('Completed') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        {{ __('No tasks found for this client.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


