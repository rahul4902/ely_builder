
<div class="col-lg-12">
    <div class="profilepic">
        @if ($contact && $contact->image_path != '')
            <img class="profilepicsize" src="../images/{{ $companyname }}/{{ $contact->image_path }}"
                alt="Profile Image" />
        @else
            <div class="initial-avatar">
                {{ strtoupper(substr($contact->name, 0, 1)) .
                    (strpos($contact->name, ' ') ? strtoupper(substr($contact->name, strpos($contact->name, ' ') + 1, 1)) : '') }}
            </div>
        @endif
    </div>
    <div>
        
        <div class="row">
            <div class="col-12"><h3 class="mt-0">{{ $contact->nameAndDepartment }} </h3></div>
            <div class="col-12 col-md-12 col-xl-1  col-xl-1">
                <p class="d-flex align-items-end gap-2"><i class="far fa-envelope"></i> <a
                        href="mailto:{{ $contact->email }}" class="text-dark">{{ $contact->email }}</a></p>
            </div>
            <div class="col-12 col-md-12">
                <p class="d-flex align-items-end gap-2"><i class="fas fa-headset"></i><a
                        href="tel:{{ $contact->work_number }}" class="text-dark">{{ $contact->work_number }}</a></p>
            </div>
            <div class="col-12 col-md-12">
                <p class="d-flex align-items-end gap-2"><i class="fas fa-headset"></i>
                    <a href="tel:{{ $contact->personal_number }}" class="text-dark">{{ $contact->personal_number }}</a>
                </p>
            </div>
            <div class="col-12 col-md-12">
                <p class="d-flex align-items-end gap-2 mb-0"><i class="far fa-address-card"></i>{{ $contact->address }} </p>
            </div>
        </div>
    </div>
</div>
