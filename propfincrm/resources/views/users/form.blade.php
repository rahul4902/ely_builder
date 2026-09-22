@php
    $roleId = old('roles', isset($user) ? optional($user->userRole)->role_id : null);
    $departmentId = old('departments', isset($user) ? optional($user->department->first())->id : null);
    $companyName = \App\Models\Setting::first()?->company ?? '';
    
    $userImageUrl = null;
    if (isset($user) && !empty($user->image_path)) {
        if (file_exists(public_path('images/' . $companyName . '/' . $user->image_path))) {
            $userImageUrl = asset('images/' . $companyName . '/' . $user->image_path);
        } elseif (file_exists(public_path('images/media/' . $user->image_path))) {
            $userImageUrl = asset('images/media/' . $user->image_path);
        } elseif (file_exists(public_path('images/' . $user->image_path))) {
            $userImageUrl = asset('images/' . $user->image_path);
        }
    }
@endphp

<div class="space-y-6">
    {{-- Section 1: Profile Photo & Preview --}}
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">{{ __('Profile Photo') }}</label>
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="relative shrink-0 flex items-center justify-center">
                <img id="userAvatarPreview"
                     src="{{ $userImageUrl ?? '' }}"
                     alt="{{ isset($user) ? $user->name : 'User' }}"
                     class="h-16 w-16 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-slate-200 {{ empty($userImageUrl) ? 'hidden' : '' }}" />
                <div id="userAvatarPlaceholder"
                     class="h-16 w-16 rounded-full bg-slate-200 border-2 border-white shadow-sm ring-1 ring-slate-200 flex items-center justify-center text-slate-600 font-bold text-lg {{ !empty($userImageUrl) ? 'hidden' : '' }}">
                    {{ isset($user) && $user->name ? strtoupper(substr($user->name, 0, 1)) : 'U' }}
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <x-form.input layout="standard" field-class="form-group mb-1" label="" name="image_path" id="image_path" type="file" class="form-control text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 cursor-pointer" accept="image/*" />
                <p class="text-[11px] text-slate-400 m-0">{{ __('JPG, PNG or WEBP format. Select an image to preview instantly.') }}</p>
            </div>
        </div>
    </div>

    {{-- Section 2: Personal & Contact Information --}}
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">{{ __('Basic Information') }}</label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-form.input layout="standard" field-class="form-group" label="Full Name" name="name" :value="isset($user) ? $user->name : null" class="form-control" required placeholder="Enter full name" />
            </div>
            <div>
                <x-form.input layout="standard" field-class="form-group" label="Email Address" name="email" type="email" :value="isset($user) ? $user->email : null" class="form-control" required placeholder="name@company.com" />
            </div>
            <div>
                <x-form.input layout="standard" field-class="form-group" label="Address" name="address" :value="isset($user) ? $user->address : null" class="form-control" placeholder="Office or residential address" />
            </div>
            <div>
                <x-form.input layout="standard" field-class="form-group" label="Work Phone" name="work_number" :value="isset($user) ? $user->work_number : null" class="form-control" inputmode="numeric" placeholder="Work phone number" />
            </div>
            <div>
                <x-form.input layout="standard" field-class="form-group" label="Personal Phone" name="personal_number" :value="isset($user) ? $user->personal_number : null" class="form-control" inputmode="numeric" placeholder="Personal phone number" />
            </div>
        </div>
    </div>

    {{-- Section 3: Organization & Role Assignment --}}
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">{{ __('Role & Organization') }}</label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-form.input layout="standard" field-class="form-group" label="Assign Role" name="roles" id="roles" type="select" :options="$roles" :value="$roleId" class="form-control select2-searchable" required />
            </div>
            <div>
                <x-form.input layout="standard" field-class="form-group" label="Assign Department" name="departments" id="departments" type="select" :options="$departments" :value="$departmentId" class="form-control select2-searchable" required />
            </div>
            <div id="teamlead-container" class="{{ (isset($user) && $user->teamlead !== null) || $roleId == 3 ? '' : 'hidden' }}">
                <x-form.input layout="standard" field-class="form-group" label="Reporting Team Lead" name="teamlead" id="teamlead" type="select" :options="$teamlead" :value="isset($user) ? $user->teamlead : null" class="form-control select2-searchable" />
            </div>
        </div>
    </div>

    {{-- Section 4: Security & Password --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 m-0">{{ __('Security') }}</label>
            @if(isset($user))
                <span class="text-[11px] text-slate-400 font-normal">{{ __('Leave blank to keep existing password') }}</span>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-form.input layout="standard" field-class="form-group" label="Password" name="password" type="password" class="form-control" :required="!isset($user)" autocomplete="new-password" placeholder="{{ isset($user) ? '••••••••' : 'Enter account password' }}" />
            </div>
            <div>
                <x-form.input layout="standard" field-class="form-group" label="Confirm Password" name="password_confirmation" type="password" class="form-control" :required="!isset($user)" autocomplete="new-password" placeholder="{{ isset($user) ? '••••••••' : 'Confirm account password' }}" />
            </div>
        </div>
    </div>

    {{-- Section 5: Form Action Buttons --}}
    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition no-underline">
            {{ __('Cancel') }}
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-5 py-2 text-xs font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-lg transition shadow-xs cursor-pointer border-0">
            {{ $submitButtonText }}
        </button>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Instant Live Image Preview
        $('#image_path').on('change', function() {
            if (this.files && this.files[0]) {
                var file = this.files[0];
                if (file.type.match('image.*')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#userAvatarPreview').attr('src', e.target.result).removeClass('hidden').show();
                        $('#userAvatarPlaceholder').addClass('hidden').hide();
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        // Initialize Select2 with search enabled and standard styling
        if (typeof $.fn.select2 === 'function') {
            $('#roles, #departments, #teamlead').each(function() {
                var $this = $(this);
                if (!$this.hasClass('select2-hidden-accessible')) {
                    $this.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        minimumResultsForSearch: 0,
                        placeholder: 'Search and select'
                    });
                }
            });
        }

        // Show/hide Team Lead when Role is 3 (team member)
        function checkRoleTeamlead(val) {
            if (val == 3) {
                $('#teamlead-container').removeClass('hidden').slideDown(150);
            } else {
                $('#teamlead-container').slideUp(150, function() {
                    $(this).addClass('hidden');
                });
            }
        }

        $('#roles').on('change select2:select', function() {
            checkRoleTeamlead($(this).val());
        });
        checkRoleTeamlead($('#roles').val());
    });
</script>
@endpush
