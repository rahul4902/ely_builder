<h3 class="moveup m-0 mb-3">{{ $client->name }} ({{ $client->company_name }})</h3>

<!--Client info leftside-->
<div class="contactleft">
    @if ($client->email != '')
        <!--MAIL-->
        <p class="d-flex align-items-end gap-2">
            <i class="far fa-envelope"></i><a href="mailto:{{ $client->email }}" class="text-dark">{{ $client->email }}</a>
        </p>
    @endif
    @if ($client->primary_number != '')
        <!--Work Phone-->

        <p class="d-flex align-items-end gap-2"><a
                href="tel:{{ $client->work_number }}">{{ $client->primary_number }}</a></p>
    @endif
    @if ($client->secondary_number != '')
        <!--Secondary Phone-->
        <p class="d-flex align-items-end gap-2"><span class="glyphicon glyphicon-phone" aria-hidden="true"
                data-toggle="tooltip" title="{{ __('Secondary number') }}" data-placement="left"> </span>
            <a href="tel:{{ $client->secondary_number }}">{{ $client->secondary_number }}</a>
        </p>
    @endif
    @if ($client->address || $client->zipcode || $client->city != '')
        <!--Address-->
        <p class="d-flex align-items-end gap-2"><i class="fas fa-home"></i>
            {{ $client->address }}
            <br />{{ $client->zipcode }} {{ $client->city }}
        </p>
    @endif
</div>

<!--Client info leftside END-->
<!--Client info rightside-->
<div class="contactright">
    @if ($client->company_name != '')
        <!--Company-->
        <p class="d-flex align-items-end gap-2"><i class="far fa-star"></i>
            {{ $client->company_name }}</p>
    @endif
    @if ($client->vat != '')
        <!--Company-->
        <p class="d-flex align-items-end gap-2"><i class="fas fa-globe"></i> {{ $client->vat }}
        </p>
    @endif
    @if ($client->industry != '')
        <!--Industry-->
        <p class="d-flex align-items-end gap-2"><span class="glyphicon glyphicon-briefcase" aria-hidden="true"
                data-toggle="tooltip" title="{{ __('Industry') }}"data-placement="left"> </span>
            {{ $client->industry }}</p>
    @endif
    @if ($client->company_type != '')
        <!--Company Type-->
        <p class="d-flex align-items-end gap-2 mb-0"><span class="glyphicon glyphicon-globe" aria-hidden="true"
                data-toggle="tooltip" title="{{ __('Company type') }}" data-placement="left"> </span>
            {{ $client->company_type }}</p>
    @endif
</div>

<!--Client info rightside END-->
