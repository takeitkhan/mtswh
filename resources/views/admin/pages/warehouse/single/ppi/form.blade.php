@extends('admin.layouts.master')

@section('title')
    PPI | Purchase Product Information
@endsection
@section('onlytitle')
    @php
        $generalUser = auth()->user()->checkUserRoleTypeGeneral();
        $globalUser = auth()->user()->checkUserRoleTypeGlobal();
    @endphp
    Ppi Generate
    @if(!empty($ppi))
        ID : {{$ppi->id}}
    @endif
    @if(!empty($ppi))
        @php
            $getPpiProduct = $Model('PpiProduct')::products($ppi->id);
        @endphp
        <!-- PPI Action Button  -->
        @if (count($getPpiProduct))
            @php
                $warehouse_code = request()->get('warehouse_code');
                $checkPpiLastSts = $Model('PpiSpiStatus')::where('ppi_spi_id', $ppi->id)
                                        ->where('status_for', 'Ppi')
                                        ->where('status_format', 'Main')
                                        ->orderBy('status_order', 'desc')
                                        ->first();
            @endphp
        @endif
        <!-- End Ppi Action -->
    @endif
@endsection


@section('content')
    <div class="content-wrapper" id="ppi_content" style="overflow: hidden;">
        <?php
        $warehouse_code = request()->get('warehouse_code');
        if (!empty($ppi)) {
            $routeUrl = route('ppi_update', $warehouse_code);
            $disabled = 'disabled';
        } else {
            $routeUrl = route('ppi_store', $warehouse_code);
            $disabled = '';
        }
        ?>
        <div class="row">
            <div id="printJS-form" class="col-md-10 ppi_left_data" style="max-height: 87vh; overflow: scroll;">

                <form action="{{$routeUrl}}" method="post">
                    @csrf
                    @if(!empty($ppi))
                        <input type="hidden" name="id" value="{{$ppi->id}}">
                    @endif
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group d-block">
                                <label for="ppi_type">PPI Type</label>
                                @php $ppi_type = ['Supply', 'Service'] @endphp
                                <select name="ppi_type" id="ppi_type" class="form-select" required {{$disabled}}>
                                    <option value="" disabled selected>Select</option>
                                    @foreach($ppi_type as $value)
                                        <option value="{{$value}}"
                                            {{!empty($ppi) && $ppi->ppi_spi_type == $value ? 'selected' : ''}}
                                        >{{$value}}
                                        </option>
                                    @endforeach
                                </select>
                            </div><!-- PPI Type -->
                        </div>
                        <div class="col-md-2" id="project_col">
                            <!-- Project Select -->
                            @if(!empty($ppi))
                                <div class="form-group d-block">
                                    <label for="project">Project</label>
                                    <input type="text" class="form-control" name="project"
                                           value="{{$ppi->project}}" {{$disabled}}>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <div class="form-group d-block">
                                <label for="tran_type">Transaction Type</label>
                                @php $tran_type = ['With Money', 'Without Money'] @endphp
                                <select name="tran_type" id="tran_type" class="form-select" required {{$disabled}}>
                                    <option value="" disabled selected>Select</option>
                                    @foreach($tran_type as $value)
                                        <option value="{{$value}}"
                                            {{!empty($ppi) && $ppi->tran_type == $value ? 'selected' : ''}}
                                        >{{$value}}
                                        </option>
                                    @endforeach
                                </select>
                            </div><!-- Trnx Type -->
                        </div>
                    </div> <!-- ENd PPI Basic Info -->

                    @php
                        $contacts = $Model('Contact')::get();
                        //dd($contacts);
                    @endphp



                    <div class="row not_print">
                        <div class="col-md-5">
                            <div class="form-group d-block">
                                <label for="ppi_type">Product Source</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            &nbsp;
                        </div>
                    </div>
                    <!-- PPI Source -->
                    <div class="row">
                        <div class="col-md-7">
                            <div class="row" id="{{!empty($ppi) ? '' : 'ppi_source'}}">
                                @if(!empty($ppi))
                                    @php
                                        //$ppiSourceTree = explode('|', $ppi->source_tree);
                                        $ppiSourceTree = $Model('PpiSpiSource')::where('ppi_spi_id', $ppi->id)->where('action_format', 'Ppi')->get();
                                    @endphp
                                    @foreach($ppiSourceTree as $data)
                                        <div class="col-md-6">
                                            <label class="font-12" for="">{{$data->source_type}}</label>
                                            <input type="text" class="form-control" xname="main_source[]"
                                                   value="{{$data->who_source}}" {{$disabled}}>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="row" id="source_warehouse_site">

                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        @if(!empty($ppi))
                            Note: {{$ppi->note}}
                        @else
                        <div class="form-group d-block">
                            <label for="">Note</label>
                            <textarea name="note" id="" cols="50" rows="2" {{!empty($ppi) ? 'disabled' : null}}>{{$ppi->note ?? null}}</textarea>
                        </div>

                        @endif

                    </div>
                    <!-- Breadcrumb -->
                    <div class="row not_print">
                        <div class="col-md-12">
                            <div class="crumbswrapper">
                                <div class="crumbs" id="source_breadcrumb">
                                    @if(!empty($ppi))
                                        @php //$getSourceTree = explode('|',$ppi->source_tree) @endphp
                                        @foreach($ppiSourceTree as $tree)

                                            <div class="innerwrap">
                                                <span class="innerItem">
                                                    <span>{{$tree->source_type}}:</span> {{$tree->who_source}}
                                                </span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- Submit BTN -->
                    <div class="form-submit_btn">
                        @if(!empty($ppi))
                        @else
                            <button type="submit" class="btn blue px-2 w-auto">Save changes</button>
                        @endif
                    </div>
                </form>

                <!-- PPI Product Modal /Information -->
            @if(!empty($ppi->id))
                <!-- ENd product Modal Row -->
                    <div class="row mt-3 not_print">
                        <h6>
                            <div class="title-with-border text-center ">
                                <span class="done_this_action">
                                    @if(auth()->user()->hasRoutePermission('ppi_product_add'))
                                        <button title="Add Product to PPI" type="button"
                                                class="btn btn-lg btn-outline-teal py-0 rounded-circle"
                                                style=" height: 50px;" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    @endif

                                    @if(auth()->user()->hasRoutePermission('ppi_product_import_from_another_ppi'))
                                        <button title="Import Product from another PPI" type="button"
                                                class="btn btn-lg btn-outline-primary py-0 rounded-circle"
                                                style=" height: 50px;"
                                                id="importProductFromPpi">
                                            <i class="fa fa-file-import"></i>
                                        </button>
                                    @endif
                                </span>
                                <button title="Ppi Data Print" type="button"
                                        class="btn btn-lg btn-outline-orange py-0 rounded-circle ppi_print_data"
                                        style=" height: 50px;"
                                        id="">
                                    <i class="fa fa-print"></i>
                                </button>

                            </div>
                        </h6>


                        <!-- Product Modal -->
                        @php
                            if(isset($ppiEditProduct)){
                                $ppiProductRouteUrl = route('ppi_product_update', $warehouse_code);
                            }else{
                                $ppiProductRouteUrl = route('ppi_product_store', $warehouse_code);
                            }
                        @endphp
                        <form action="{{$ppiProductRouteUrl}}" method="post">
                            @csrf
                            <input type="hidden" name="ppi_id" value="{{$ppi->id}}">
                            @if(isset($ppiEditProduct))
                                <input type="hidden" name="ppi_product_id" value="{{$ppiEditProduct->id}}">
                            @endif
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-fullscreen modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Product information</h5>
                                            @if(isset($ppiEditProduct))
                                            @else
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            @endif
                                        </div>
                                        <div class="modal-body">
                                            @include('admin.pages.warehouse.single.ppi.form.product-modal')
                                        </div>
                                        <div class="modal-footer d-inline-block">
                                            @if(isset($ppiEditProduct))
                                                <a href="{{route('ppi_edit', [$warehouse_code, $ppi->id])}}"
                                                   class="btn btn-sm btn-secondary float-end">Cancel</a>
                                            @else
                                                <div class="d-inline-block" id="add_btn"></div>
                                                <button type="button" class="btn btn-sm btn-secondary float-end"
                                                        data-bs-dismiss="modal">Close
                                                </button>
                                            @endif
                                            <button type="submit" class="btn btn-sm btn-primary float-end">Save
                                                changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- ENd Modal -->
                        </form>
                    </div> <!-- ENd product Modal Row -->

                    <!-- PPi Product Step Information -->
                @php
                    global $setProductValidationComplete;
                        $setProductValidationComplete = 0;
                    global $setProductValidationTotal;
                        $setProductValidationTotal = 0;
                    global $setProductValidationDone;
                        $setProductValidationDone = false;
                    global $singleProductValidationComplete;
                        $singleProductValidationComplete = 0;
                    global $singleProductValidationTotal;
                        $singleProductValidationTotal = 0;
                    global $singleProductValidationDone;
                        $singleProductValidationDone = false;
                @endphp
                <!-- Product Information -->
                    @include('admin.pages.warehouse.single.ppi.ppi-product')
                <!-- End Product Information -->
                @endif

            </div>

            <!-- Ppi Status -->
            <div class="col-md-2">
{{--                $generalUser--}}
            @if(isset($getPpiProduct) &&  count($getPpiProduct))
                    <div class="done_this_action">
                        <h6>
                            <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0 fw-bold">
                                PPI Action
                            </div>
                        </h6>
                        <div class="py-2 alert-gray">
                            @include('admin.pages.warehouse.single.ppi.ppi-step-action-button')
                        </div>
                    </div>
                @endif


                @if(!empty($ppi))
                    <?php $ppi_id = $ppi->id; ?>
                    <h6>
                        <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0 fw-bold">
                            PPI Status
                        </div>
                    </h6>
                    @include('admin.pages.warehouse.single.ppi.ppi-status')
                @endif
            </div>
            <!-- ENd PPi status -->
        </div>
    </div>
@endsection


@section('cusjs')

    <!--Service Project Select Option Template -->
    <script type="text/template" data-template="service_project_template">
        <div class="form-group d-block">
            <label for="project">Project</label>
            @php
//                $allMtsProject = $ApiCollection::getMtsProject();
                $allMtsProject = $Model('Project')::where('type', 'Service')->get();
            @endphp
            <select name="project" id="project" class="form-select select-box" required>
                <option value="" disabled selected>Select</option>
                @if(!empty($allMtsProject))
                    @foreach($allMtsProject as $value)
                        <option value="{{$value->name}}">{{$value->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </script>

    <!-- Supply Project select option Template -->
    <script type="text/template" data-template="supply_project_template">
        <div class="form-group d-block">
            <label for="project">Project</label>
            @php  $allProject = $Query::accessModel('Project')::where('type', 'Supply')->get(); @endphp
            <select name="project" id="project" class="form-select select-box" required>
                <option value="" disabled selected>Select</option>
                @foreach($allProject as $value)
                    <option value="{{$value->name}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
    </script>

    <!-- Contacts / Source template -->
    {{--    <script type="text/x-jQuery-tmpl" data-template="source_template">--}}
    <script>

        function source_template(id) {
            let html = `
                      <div class="col-md-6">
                        <div class="form-group d-block">
                            <select name="main_source[${id}][source]" id="main_source" class="form-select" required>
                                <option value="" disabled selected>Select</option>
                                @foreach($contacts as $value)
                        <option value="{{ $value->name }}|{{$value->id}}" data-name="{{$value->name}}" >{{ $value->name }}</option>
                                @endforeach
                        </select>
                        <!-- Contact Type Role -->
                        @php $contactRole = $Model('AttributeValue')::where('unique_name', 'Contact Type')->get(); @endphp
                        <div class="d-inline-block">
                            @foreach($contactRole as $role)
                            <div class="" style="display: inline-flex; width: 100px;">
                                <input required class="mb-0" type="radio" class="" name="main_source[${id}][type]" value="{{$role->value}}" id="contactRole{{$role->id}}${id}">
                                <label class="w-100 ms-2" style="line-height: 22px;" for="contactRole{{$role->id}}${id}">{{$role->value}}</label>
                            </div>
                            @endforeach
                        </div>
                    </div><!-- PPI Type -->
                </div>
                <div class="col-md-4">
                    <div class="form-group d-block">
                        @php
                            $child_status = ['Haschild', 'Warehouse', 'Site', 'Shop'];
                        @endphp
                        <select name="main_source[${id}][source_level]" id="source_child" class="form-select" required>
                                <option value="" disabled selected>Select</option>
                                @foreach($child_status as $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                        </select>
                    </div><!-- PPI Type -->
                </div>
            `
            return html;
        }
    </script>


    <script src="{{$viewDir}}/admin/pages/warehouse/single/ppi/ppi.js?{{rand(0,9999)}}"></script>
    <script type="text/javascript"
            src="https://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
    <script>
        /**
         * Contact List Use ajax
         */
        function ppiSource() {
            $.ajax({
                "url": "{{route('contact_api_source')}}",
                "type": "GET",
                "success": function (data) {
                    //console.log(data)
                }
            })
        }

        ppiSource();
    </script>

    <style>
        .category-related-link {
            justify-content: flex-start;
        }
    </style>

   <!-- Import Product Modal -->
    @if(!empty($ppi->id))
        {!! $Component::bootstrapModal('importProductFromPpi', [
            'modalHeader' => 'Import product from Ppi',
            'position' => 'right',
            'backdrop' => true,
            'saveBtn' => 'import',
            'formAction' => route('ppi_product_import_from_another_ppi', request()->get('warehouse_code')),
            ]) !!}

        <script type="text/template" id="importProductTem">
            <div class="form-group">
                <label for="">Select PPI ID</label>
                <select name="from_ppi_id" id="" class="form-control from-control-sm" required>
                    <option value=""></option>
                    @foreach($Model('PpiSpi')::where('action_format', 'Ppi')->where('warehouse_id', request()->get('warehouse_id'))->get() as $data)
                        @if($ppi->id == $data->id)
                        @else
                        <option value="{{$data->id}}">{{$data->id}}</option>
                        @endif
                    @endforeach
                </select>
                <input type="hidden" name="to_ppi_id" value="{{$ppi->id}}">
            </div>
        </script>
        <script>
            jQuery('#importProductFromPpiModalBody').html($('script#importProductTem').html())
        </script>
    @endif

    <link rel="stylesheet" href="{{ $publicDir }}/assets/css/bootstrap.min.cssx"  media="print">
    <link rel="stylesheet" href="{{ $publicDir }}/assets/css/form.css"  media="print">
    <link rel="stylesheet" href="{{ $publicDir }}/assets/css/blue.css"  media="print">
    <link rel="stylesheet" href="{{ $publicDir }}/assets/css/style.css"  media="print">
    <link rel="stylesheet" href="{{ $publicDir }}/assets/css/form.css"  media="print">
    <link rel="stylesheet" href="{{ $publicDir }}/assets/css/button.css"  media="print">
    <link rel="stylesheet" href="{{ $publicDir }}/assets/css/custom.css"  media="print">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"  media="print">
    <link rel="stylesheet" href="{{ $publicDir }}/assets/css/ppi_spi_print_css.css"  media="print">
    <script>
        //prinnt ppi Data

        $('button.ppi_print_data').on('click',function(){
            $('#printJS-form').printThis({
                debug: true,               // show the iframe for debugging
                importCSS: false,            // import parent page css
                importStyle: true,         // import style tags
                printContainer: true,       // print outer container/$.selector
                loadCSS: [
                    "{{ $publicDir }}/assets/css/bootstrap.min.css",
                    "{{ $publicDir }}/assets/css/form.css",
                    "{{ $publicDir }}/assets/css/blue.css",
                    "{{ $publicDir }}/assets/css/style.css",
                    "{{ $publicDir }}/assets/css/form.css",
                    //"{{ $publicDir }}/assets/css/responsive.css",
                    "{{ $publicDir }}/assets/css/button.css",
                    "{{ $publicDir }}/assets/css/custom.css",
                    "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css",
                    "{{ $publicDir }}/assets/css/ppi_spi_print_css.css",

                ],                // path to additional css file - use an array [] for multiple
                pageTitle: "MTS",              // add title to print page
                removeInline: false,        // remove inline styles from print elements
                removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
                printDelay: 1,            // variable print delay
                header: "PPI ID {{$ppi->id ?? null}}",               // prefix to html
                footer: null,               // postfix to html
                base: false,                // preserve the BASE tag or accept a string for the URL
                formValues: true,           // preserve input/form values
                canvas: false,              // copy canvas content
                doctypeString: '<!DOCTYPE html>', // enter a different doctype for older markup
                removeScripts: false,       // remove script tags from print content
                copyTagClasses: false,      // copy classes from the html & body tag
                copyTagStyles: false,       // copy styles from html & body tag (for CSS Variables)
                beforePrintEvent: null,     // callback function for printEvent in iframe
                beforePrint: null,          // function called before iframe is filled
                afterPrint: null            // function called before iframe is removed
            });
        }) //End
    </script>

@endsection

@php
    if(!empty($ppi)){
       $getSatus = $Model('PpiSpiStatus')::where('ppi_spi_id', $ppi->id)->get();
       foreach($getSatus as $status){
           $Model('PpiSpiNotification')::firstOrCreate([
               'action_performed_by' => auth()->user()->id,
               'status_id' => $status->id,
               'is_read' => 2,
           ]);
       }
   }
@endphp


@if(auth()->user()->checkUserRoleTypeGlobal())
    @section('bottomjs')
        <script>
            $('#ppi_content .ppiProductEditBtn').remove();
        </script>
    @endsection
@endif
