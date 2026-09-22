<div class="card">
    <!--Client info leftside-->
    <div class="row">
        @if (isset($lead_info))
            <div class="col-12">
                <h3 class="moveup m-0 mb-2">Name : {{ isset($lead_info->name) ? $lead_info->name : '' }} </h3>
            </div>
        @endif
		@if ($lead->email != '')
            <!--Work Phone-->
            <div class="col-6"><span class="fw-bold text-primary me-2">Email :</span>&nbsp;{{ $lead->email }}</div>
        @endif
        @if ($lead->contact_date != '')
            <!--MAIL-->
            <div class="col-6"><span class="fw-bold text-primary me-2">Date :</span> {{ $lead->contact_date }}</div>
        @endif
		
        @if ($lead->status != '')
            <!--Work Phone-->
            <div class="col-6"><span class="fw-bold text-primary me-2">Status :</span>&nbsp;{{ $lead->status }}</div>
        @endif
		
        @if ($lead->source != '')
            <!--Secondary Phone-->
            <div class="col-6"><span class="fw-bold text-primary me-2">Source :</span>&nbsp;{{ $lead->source }}</div>
        @endif
        @if ($lead->country != '')
            <!--Address-->
            <div class="col-6"><span class="fw-bold text-primary me-2">Country :</span>&nbsp; {{ $lead->country }}
            </div>
        @endif
		
        @if ($lead->Budget != '')
            <!--Address-->
            <div class="col-6"><span class="fw-bold text-primary me-2">Budget :</span>&nbsp; {{ $lead->Budget }}
            </div>
        @endif


        @if ($lead->state != '')
            <!--Company-->
            <div class="col-6"><span class="fw-bold text-primary me-2">State :</span>&nbsp; {{ $lead->state }}</div>
        @endif
        @if ($lead->city != '')
            <!--Company-->
            <div class="col-6"><span class="fw-bold text-primary me-2">City :</span>&nbsp; {{ $lead->city }}</div>
        @endif
        @if ($lead->location != '')
            <!--Industry-->
            <div class="col-6"><span class="fw-bold text-primary me-2">Location :</span>&nbsp; </span>
                {{ $lead->location }}</div>
        @endif
        @if ($lead->pin != '')
            <!--Company Type-->
            <div class="col-6"><span class="fw-bold text-primary me-2">Pin/Zipcode :</span>&nbsp;{{ $lead->pin }}
            </div>
        @endif
    </div>
</div>

<!--Client info rightside END-->
