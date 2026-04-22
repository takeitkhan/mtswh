@extends('admin.layouts.master')

@section('title')

    {{ !empty($role) ? 'Edit role' : 'Add new role' }}

@endsection

@section('content')

    <div class="content-wrapper">
        <form action="{{ !empty($role) ? route('role_update') : route('role_store') }}" method="post" id="roleForm" novalidate>
            @csrf
            <input type="hidden" name="routes_debug" id="routes_debug" value="">
            <div class="row">
                <div class="col-md-8 col-lg-3 col-sm-12">
                    <h6>
                        <div class="title-with-border">
                            Role Information
                        </div>
                    </h6>
                        @if (!empty($role))
                            <input type="hidden" name="id" value="{{ $role->id }}">
                        @endif
                        <div class="form-content">

                            <div class="form-group name">
                                <label for="name">Name: </label>
                                <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name"
                                    value="{{ !empty($role) ? $role->name : old('name') }}" required>
                            </div>

                            <div class="form-group select arrow_class">
                                <label for="select">Role type </label>
                                @php
                                    $role_type = [
                                        'Global' => 'Global',
                                        'General' => 'General',
                                        'Custom' => 'Custom',
                                    ];
                                @endphp
                                <select class="form-select" name="type">
                                    <option>Select role type</option>
                                    @foreach ($role_type as $index => $data)
                                        <option value="{{ $index }}"
                                            {{ !empty($role) && $role->type == $index ? 'selected' : '' }}>
                                            {{ $data }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-submit_btn">
                                <button type="button" class="btn blue" id="roleFormSubmitBtn">Submit</button>
                            </div>

                        </div>
                </div>

                <div class="col-md-2"></div>

                <div class="col-md-8 col-lg-6 col-sm-12">
                    <h6>
                        <div class="title-with-border mb-1">
                            Route Permission
                            <div class="d-inline-block float-end valign-text-bottom me-2">
                                <input type="checkbox" class="" id="checkAll">
                                <label for="checkAll" class="valign-super">Check All</label>
                            </div>
                        </div>
                    </h6>

                    @php
                        $routeList = $Query::getData('route_lists')->groupBy('route_group');
                        //dd($routeList);
                    @endphp
                    <div class="row d-block" data-masonry='{"percentPosition": true }' >
                        @foreach ($routeList as $index => $item)
                            <div class="col-md-3">
                                <div class="xform-group">
                                    <div class="form-check">
                                        <p class="mb-1 fw-bold">
                                            {{$Query::accessModel('Routegroup')::name($index) }}
                                        </p>
                                        @foreach ($item as $key => $data)
                                            @php
                                                $checkId = \App\Models\Routelistrole::checkRouteRole($role->id ?? null, $data->id);

                                                $routeid = $checkId->route_id ?? null;
                                                $showAs = $checkId->show_as ?? null;
                                                //dump( $showAs);
                                            @endphp
                                            <div class="form-group {{!empty($data->show_for) ? 'alert-success ps-1' : ''}}">
                                                <input type="checkbox" id="{{ $data->route_name }}" class=" mb-0 checkItem route_name"
                                                    {{ $routeid == $data->id ? 'checked' : '' }} name="route_id[]"
                                                    value="{{ $data->id }}">
                                                <label class="w-100 route_name" for="{{ $data->route_name }}">{{ $data->route_title }}</label>
                                            </div>

                                            @if($data->is_show_as == 'Yes')
                                                <div class="form-group ms-3 ps-2 alert-warning  {{ $data->route_name.'_show_as' }}" style="{{ $showAs ? '' : 'display: none' }}">
                                                    <label class="w-100" for="">Show Based On</label>
                                                </div>
                                                <div class="form-group ms-3 ps-2 alert-warning {{ $data->route_name.'_show_as' }}" style="{{ $showAs ? 'display:block' :  'display: none' }}">
                                                    @php $showAsEnum = $Query::getEnumValues('route_list_roles', 'show_as'); @endphp
                                                    @foreach ($showAsEnum as $value )
                                                    <div class="d-inline-flex">
                                                        <input type="radio" id="{{$data->id.$value }}" class="checkItem mb-0"
                                                        {{ $showAs == $value ? 'checked' : '' }} name="show_as[{{$data->id}}]"
                                                        value="{{ $value }}">
                                                        <label class="w-100" for="{{$data->id.$value }}">{{$value}}</label>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

            </div>
        </form>


        <div class="row">
            @include('admin.pages.roles.ppi_spi_status_text')
        </div>
    </div>

@endsection

@section('cusjs')
<script>
    // Prevent ANY other form from submitting when we're working
    $(document).ready(function(){
        // Make sure translate form doesn't interfere
        if($('#translateForm').length){
            console.log('Translate form detected - will be handled separately');
        }
        console.log('Role form ID:', $('#roleForm').attr('id'));
    });

    // Function to update debug info in hidden field
    function updateDebugField(){
        let checkedRoutes = $('input[name="route_id[]"]:checked').map(function(){
            return $(this).val();
        }).get();
        
        let debugInfo = {
            total_checked: checkedRoutes.length,
            routes: checkedRoutes,
            name: $('input[name="name"]').val(),
            type: $('select[name="type"]').val(),
            role_id: $('input[name="id"]').val(),
            timestamp: new Date().toLocaleString()
        };
        
        $('#routes_debug').val(JSON.stringify(debugInfo));
        console.log('Debug Info Updated:', debugInfo);
        return debugInfo;
    }

    // Handle individual checkbox clicks
    $('input[type="checkbox"].route_name').on('change', function(){
        let getFor = $(this).prop('id');
        if($(this).is(':checked')){
            $('.'+getFor+'_show_as').show();
            let radioButtons = $('.'+getFor+'_show_as input[type="radio"]');
            if(radioButtons.length > 0){
                radioButtons.prop('required', true);
            }
        }else {
            $('.'+getFor+'_show_as input[type="radio"]').prop('required', false).prop('checked', false);
            $('.'+getFor+'_show_as').hide();
        }
        updateDebugField();
        console.log('Checked routes count:', $('input[name="route_id[]"]:checked').length);
    })

    // Handle Check All functionality
    $('#checkAll').on('change', function(){
        var isChecked = $(this).is(':checked');
        console.log('Check All toggled:', isChecked);
        $('input[type="checkbox"].route_name').each(function(){
            var currentChecked = $(this).is(':checked');
            if((isChecked && !currentChecked) || (!isChecked && currentChecked)){
                $(this).prop('checked', isChecked).change();
            }
        });
    })

    // Button click handler - with strict prevention
    document.getElementById('roleFormSubmitBtn').addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        console.log('=== SUBMIT BUTTON CLICKED (Native Event) ===');
        
        // Collect form data manually
        let formData = new FormData(document.getElementById('roleForm'));
        let formDataObj = Object.fromEntries(formData);
        
        console.log('Routes count:', formData.getAll('route_id[]').length);
        console.log('First few routes:', formData.getAll('route_id[]').slice(0, 5));
        
        // AJAX call
        fetch(document.getElementById('roleForm').getAttribute('action'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.text())
        .then(html => {
            console.log('Response received');
            document.body.innerHTML = html;
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
        
        return false;
    }, true);

    // Initialize on page load
    $(document).ready(function(){
        console.log('=== ROLE FORM INITIALIZATION ===');
        console.log('Total permissions available:', $('input[type="checkbox"].route_name').length);
        let checked = $('input[type="checkbox"].route_name:checked').length;
        console.log('Checked permissions on load:', checked);
        updateDebugField();
        console.log('Ready to modify permissions');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" async></script>
@endsection
