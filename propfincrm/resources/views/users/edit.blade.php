@extends('layouts.master')
@section('page-title', 'Edit user')
@section('main-class', 'p-0 bg-slate-50 min-h-[calc(100vh-44px)]')

@section('content')
<div id="user-edit-page" class="w-full px-4 py-6 md:px-8 max-w-5xl mx-auto font-sans">
    {{-- Back Link --}}
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition no-underline shadow-xs">
            <i data-lucide="arrow-left" class="h-3.5 w-3.5 text-slate-500"></i>
            <span>{{ __('Team members') }}</span>
        </a>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h1 class="text-base font-semibold text-slate-900 m-0 leading-tight">{{ __('Edit user') }}</h1>
                <p class="text-xs text-slate-500 m-0 mt-1">{{ __('Update user details, credentials, profile photo, and role permissions.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700">
                    ID: #{{ $user->id }}
                </span>
            </div>
        </div>

        {{-- Form Content --}}
        <div class="p-6">
            {!! Form::model($user, [
                'method' => 'PATCH',
                'route' => ['users.update', $user->id],
                'files' => true,
                'enctype' => 'multipart/form-data',
                'id' => 'userForm'
            ]) !!}

            @include('users.form', ['submitButtonText' => __('Update user')])

            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop