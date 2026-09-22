<div class="col-md-12">
    <div class="card">
        <div class="row">
            <div class="col-md-12"><h3 class="moveup" style="margin-top: 0!important;">Name: {{ $lead_info->name }} </h3></div>
            <!--Client info leftside-->
            <div class="col-md-3">
                @if ($lead_info->category != '')
                    <!--MAIL-->
                    <div class="row ">
                        <div class="col-md-7">
                            <label style="color: #800000">Category</label>
                        </div>
                        <div class="col-md-5">
                            <p>{{ $lead_info->category }}</p>
                        </div>
                    </div>

                    {{-- <p><span style="color: #800000" class="col-md-7">Category:</span>&nbsp;<span class="col-md-4">{{$lead_info->category}}</span></p> --}}
                @endif
                @if ($lead_info->sub_category != '')
                    <!--Work Phone-->
                    <div class="row">
                        <div class="col-md-7">
                            <label style="color: #800000">Sub Category</label>
                        </div>
                        <div class="col-md-5">
                            <p>{{ $lead_info->sub_category }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000" class="col-md-7">Sub Category:</span>&nbsp;<span class="col-md-4">{{$lead_info->sub_category}}</span></p> --}}
                @endif
                @if ($lead_info->expected_price != '')
                    <!--Secondary Phone-->
                    <div class="row">
                        <div class="col-md-7">
                            <label style="color: #800000">Expected Price</label>
                        </div>
                        <div class="col-md-5">
                            <p>{{ $lead_info->expected_price }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000" class="col-md-7">Expected Price:</span>&nbsp;<span class="col-md-4">{{$lead_info->expected_price}}</span></p> --}}
                @endif
                @if ($lead_info->primium != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-7">
                            <label style="color: #800000">Primium</label>
                        </div>
                        <div class="col-md-5">
                            <p>{{ $lead_info->primium }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Primium:</span>&nbsp; {{$lead_info->primium}}</p> --}}
                @endif
                @if ($lead_info->market_price != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-7">
                            <label style="color: #800000">Market Price</label>
                        </div>
                        <div class="col-md-5">
                            <p>{{ $lead_info->market_price }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Market Price:</span>&nbsp; {{$lead_info->market_price}}</p> --}}
                @endif
                @if ($lead_info->booking_price != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-7">
                            <label style="color: #800000">Booking Price</label>
                        </div>
                        <div class="col-md-5">
                            <p>{{ $lead_info->booking_price }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Booking Price:</span>&nbsp; {{$lead_info->booking_price}}</p> --}}
                @endif
                @if ($lead_info->maintenance_charge != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-7">
                            <label style="color: #800000">Maintenance Charge</label>
                        </div>
                        <div class="col-md-5">
                            <p>{{ $lead_info->maintenance_charge }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Maintenance Charge:</span>&nbsp; {{$lead_info->maintenance_charge}}</p> --}}
                @endif
                @if ($lead_info->other_charge != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-7">
                            <label style="color: #800000">Other Charge</label>
                        </div>
                        <div class="col-md-5">
                            <p>{{ $lead_info->other_charge }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Other Charge:</span>&nbsp; {{$lead_info->other_charge}}</p> --}}
                @endif
                @if ($lead_info->properry_status != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-7">
                            <label style="color: #800000">Properry Status</label>
                        </div>
                        <div class="col-md-5">
                            <p>{{ $lead_info->properry_status }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Properry Status:</span>&nbsp; {{$lead_info->properry_status}}</p> --}}
                @endif

            </div>
            <div class="col-md-3">
                @if ($lead_info->disposition != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Disposition</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->disposition }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Disposition:</span>&nbsp; {{$lead_info->disposition}}</p> --}}
                @endif
                @if ($lead_info->source != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Source</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->source }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Source:</span>&nbsp; {{$lead_info->source}}</p> --}}
                @endif
                @if ($lead_info->property_tag != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Property Tag</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->property_tag }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Property Tag:</span>&nbsp; {{$lead_info->property_tag}}</p> --}}
                @endif
                @if ($lead_info->address != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Address</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->address }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Address:</span>&nbsp; {{$lead_info->address}}</p> --}}
                @endif
                @if ($lead_info->block != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Block</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->block }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Block:</span>&nbsp; {{$lead_info->block}}</p> --}}
                @endif
                @if ($lead_info->country != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Country</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->country }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Country:</span>&nbsp; {{$lead_info->country}}</p> --}}
                @endif
                @if ($lead_info->state != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">State</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->state }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">State:</span>&nbsp; {{$lead_info->state}}</p> --}}
                @endif
                @if ($lead_info->city != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">City</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->city }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">City:</span>&nbsp; {{$lead_info->city}}</p> --}}
                @endif
                @if ($lead_info->security_deposit != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Security Deposit</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->security_deposit }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Security Deposit:</span>&nbsp; {{$lead_info->security_deposit}}</p> --}}
                @endif

            </div>

            <!--Client info leftside END-->
            <!--Client info rightside-->
            <div class="col-md-3">
                @if ($lead_info->locality != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Locality</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->locality }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Locality:</span>&nbsp; {{$lead_info->locality}}</p> --}}
                @endif
                @if ($lead_info->super_area != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Super Areat</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->super_area }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Super Area:</span>&nbsp; {{$lead_info->super_area}}</p> --}}
                @endif
                @if ($lead_info->plot_area != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Plot Area</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->plot_area }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Plot Area:</span>&nbsp; {{$lead_info->plot_area}}</p> --}}
                @endif
                @if ($lead_info->cover_area != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Cover Area</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->cover_area }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Cover Area:</span>&nbsp; {{$lead_info->cover_area}}</p> --}}
                @endif
                @if ($lead_info->carpet_area != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Carpet Area</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->carpet_area }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Carpet Area:</span>&nbsp; {{$lead_info->carpet_area}}</p> --}}
                @endif
                @if ($lead_info->bathroom != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Bathroom</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->bathroom }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Bathroom:</span>&nbsp; {{$lead_info->bathroom}}</p> --}}
                @endif
                @if ($lead_info->work_station != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Work Station</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->work_station }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Work Station:</span>&nbsp; {{$lead_info->work_station}}</p> --}}
                @endif
                @if ($lead_info->cabins != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Cabins</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->cabins }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Cabins:</span>&nbsp; {{$lead_info->cabins}}</p> --}}
                @endif
                @if ($lead_info->facing != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Facing</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->facing }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Facing:</span>&nbsp; {{$lead_info->facing}}</p> --}}
                @endif


            </div>
            <div class="col-md-3">
                @if ($lead_info->furnish != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Furnish</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->furnish }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Furnish:</span>&nbsp; {{$lead_info->furnish}}</p> --}}
                @endif
                @if ($lead_info->floor != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Floor</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->floor }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Floor:</span>&nbsp; {{$lead_info->floor}}</p> --}}
                @endif
                @if ($lead_info->built_year != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Built Year</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->built_year }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Built Year:</span>&nbsp; {{$lead_info->built_year}}</p> --}}
                @endif
                @if ($lead_info->car_parking != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Car Parking</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->car_parking }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Car Parking:</span>&nbsp; {{$lead_info->car_parking}}</p> --}}
                @endif
                @if ($lead_info->remarks != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Remarks</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->remarks }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Remarks:</span>&nbsp; {{$lead_info->remarks}}</p> --}}
                @endif
                @if ($lead_info->description != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Description</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->description }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Description:</span>&nbsp; {{$lead_info->description}}</p> --}}
                @endif
                @if ($lead_info->other_feature != '')
                    <!--Address-->
                    <div class="row">
                        <div class="col-md-6">
                            <label style="color: #800000">Other Feature</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $lead_info->other_feature }}</p>
                        </div>
                    </div>
                    {{-- <p><span style="color: #800000">Other Feature:</span>&nbsp; {{$lead_info->other_feature}}</p> --}}
                @endif


            </div>
        </div>
    </div>

</div>

<!--Client info rightside END-->
