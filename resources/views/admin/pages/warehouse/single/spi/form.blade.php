@extends('admin.layouts.master')

@section('title')
    SPI | Sale Product Information
@endsection

@section('onlytitle')
    @php
        $generalUser = auth()->user()->checkUserRoleTypeGeneral();
    @endphp
    Spi Generate
    @if(!empty($spi))
        ID : {{$spi->id}}
    @endif
    @if(!empty($spi))
        @php
            $getSpiProduct = $Model('SpiProduct')::products($spi->id);
        @endphp
        <!-- PPI Action Button  -->
        @if (count($getSpiProduct))
            @php
                $warehouse_code = request()->get('warehouse_code');
                $checkSpiLastMainSts = $Model('PpiSpiStatus')::where('ppi_spi_id', $spi->id)
                                        ->where('status_for', 'Spi')
                                        ->where('status_format', 'Main')
                                        ->orderBy('status_order', 'desc')
                                        ->first();
            @endphp
        @endif
        <!-- End Ppi Action -->
    @endif
@endsection


@section('content')

    <div class="content-wrapper" id="spi_content" style="overflow: hidden;">
        <?php
        $warehouse_code = request()->get('warehouse_code');
        if (!empty($spi)) {
            $routeUrl = route('spi_update', $warehouse_code);
            $disabled = 'disabled';
        } else {
            $routeUrl = route('spi_store', $warehouse_code);
            $disabled = '';
        }
        ?>
        <div class="row">
            <div id="printJS-form" class="col-md-10" style="max-height: 87vh; overflow: scroll;">
                <!-- Spi Basic Info -->
                <form action="{{$routeUrl}}" method="post">
                    @csrf
                    @if(!empty($spi))
                        <input type="hidden" name="id" value="{{$spi->id}}">
                    @endif
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group d-block">
                                <label for="spi_type">SPI Type</label>
                                @php $spi_type = ['Supply', 'Service'] @endphp
                                <select name="spi_type" id="spi_type" class="form-select" required {{$disabled}}>
                                    <option value="" disabled selected>Select</option>
                                    @foreach($spi_type as $value)
                                        <option value="{{$value}}"
                                            {{!empty($spi) && $spi->ppi_spi_type == $value ? 'selected' : ''}}
                                        >{{$value}}
                                        </option>
                                    @endforeach
                                </select>
                            </div><!-- PPI Type -->
                        </div>
                        <div class="col-md-2" id="project_col">
                            <!-- Project Select -->
                            @if(!empty($spi))
                                <div class="form-group d-block">
                                    <label for="project">Project</label>
                                    <input type="text" class="form-control" name="project"
                                           value="{{$spi->project}}" {{ $disabled }}>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <div class="form-group d-block">
                                <label for="tran_type">Transaction Type</label>
                                @php $tran_type = ['With Money', 'Without Money'] @endphp
                                <select name="tran_type" id="tran_type" class="form-select" required {{ $disabled }}>
                                    <option value="" disabled selected>Select</option>
                                    @foreach($tran_type as $value)
                                        <option value="{{$value}}"
                                            {{!empty($spi) && $spi->tran_type == $value ? 'selected' : ''}}
                                        >
                                            {{$value}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Trnx Type -->
                        </div>
                    </div> <!-- End SPI Basic Info -->

                    <!-- Source -->
                    @php
                        $contacts = $Model('Contact')::get();
                    @endphp

                    <div class="row not_print">
                        <div class="">
                            <label>
                                <input type="checkbox" {{!empty($spi) && $spi->transferable ? 'checked' : null}} name="transferable" id=""
                                       {{!empty($spi)  ? 'disabled' : null}}
                                       value="yes"
                                       style="height: auto; line-height: normal">
                                Transferable
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group d-block">
                                <label for="spi_type">To Whom</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            &nbsp;
                        </div>
                    </div>
                    <!-- SPI Source -->
                    <div class="row">
                        <div class="col-md-7">
                            <div class="row" id="{{!empty($spi) ? '' : 'spi_source'}}">
                                @if(!empty($spi))
                                
                                    @php
                                        $spiSourceTree = $Model('PpiSpiSource')::where('ppi_spi_id', $spi->id)
                                                                ->where('action_format', 'Spi')
                                                                ->get();
                                    @endphp
                                    
                                    @foreach($spiSourceTree as $data)
                                        <div class="col-md-6">
                                            <label class="font-12" for="">
                                                {{ $data->source_type }}
                                            </label>
                                            <input type="text" class="form-control" xname="main_source[]"
                                                   value="{{ $data->who_source }}" {{ $disabled }}>
                                        </div>
                                    @endforeach
                                    
                                @endif
                            </div>
                            <div class="row" id="source_warehouse_site">
                            </div>
                            
                            @php
                                $wh_code = request()->get('warehouse_code');
                            @endphp
                            <input type="hidden" name="warehouse_code" value="{{ $wh_code }}">
                        </div>
                    </div>

                    <div class="form-group">
                        @if(!empty($spi))
                            Note: {{$spi->note}}
                        @else
                        <div class="form-group d-block">
                            <label for="">Note</label>
                            <textarea name="note" id="" cols="50" rows="2" {{!empty($spi) ? 'disabled' : null}}>{{$spi->note ?? null}}</textarea>
                        </div>
                        @endif
                    </div>

                    <!-- Breadcrumb -->
                    <div class="row not_print">
                        <div class="col-md-12">
                            <div class="crumbswrapper">
                                <div class="crumbs" id="source_breadcrumb">
                                    @if(!empty($spi))
                                        @foreach($spiSourceTree as $tree)
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
                        @if(!empty($spi))
                        @else
                            <button type="submit" class="btn blue px-2 w-auto">Save changes</button>
                        @endif
                    </div>
                </form>
                @if(!empty($spi->id))
                <!-- ENd product Modal Row -->
                <div class="row mt-3 not_print">
                    <h6>
                        <div class="title-with-border text-center ">
                            <span class="done_this_action">
                                @if(auth()->user()->hasRoutePermission('spi_product_add'))
                                <button title="Add Product to SPI" type="button"
                                        class="btn btn-lg btn-outline-teal py-0 rounded-circle"
                                        style=" height: 50px;" data-bs-toggle="modal" data-bs-target="#spiProductModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                                @endif
                                @if(auth()->user()->hasRoutePermission('spi_product_import_from_another_spi'))
                                    <button title="Import Product from another SPI"
                                            type="button"
                                            class="btn btn-lg btn-outline-primary py-0 rounded-circle"
                                            style=" height: 50px;"
                                            id="importProductFromSpi">
                                        <i class="fa fa-file-import"></i>
                                    </button>
                                @endif
                            </span>
                            <button title="Ppi Data Print" type="button"
                                    class="btn btn-lg btn-outline-orange py-0 rounded-circle ppi_print_data not_print"
                                    style=" height: 50px;"
                                    id="">
                                <i class="fa fa-print"></i>
                            </button>
                        </div>
                    </h6>


                    <!-- Product Modal -->
                    @php
                        if(isset($spiEditProduct)){
                            $spiProductRouteUrl = route('spi_product_update', $warehouse_code);
                        }else{
                            $spiProductRouteUrl = route('spi_product_store', $warehouse_code);
                        }
                    @endphp
                    <form action="{{$spiProductRouteUrl}}" method="post">
                        @csrf
                        <input type="hidden" name="spi_id" value="{{$spi->id}}">
                        @if(isset($spiEditProduct))
                            <input type="hidden" name="spi_product_id" value="{{$spiEditProduct->id}}">
                        @endif
                        <div class="modal fade" id="spiProductModal" xtabindex="-1" aria-labelledby="spiProductModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-fullscreen modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="spiProductModalLabel">SPI Product information</h5>
                                        @if(isset($spiEditProduct))
                                        @else
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        @endif
                                    </div>
                                    <div class="modal-body">
                                        @include('admin.pages.warehouse.single.spi.form.product-modal')
                                    </div>
                                    <div class="modal-footer d-inline-block">
                                        @if(isset($spiEditProduct))
                                            <a href="{{route('spi_edit', [$warehouse_code, $spi->id])}}" class="btn btn-sm btn-secondary float-end">Cancel</a>
                                        @else
                                            <div class="d-inline-block" id="add_btn"></div>
                                            <button type="button" class="btn btn-sm btn-secondary float-end" data-bs-dismiss="modal">Close</button>
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-primary float-end">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- ENd Modal -->
                    </form>
                </div><!-- ENd product Modal Row -->

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
                @include('admin.pages.warehouse.single.spi.spi-product')
                <!-- End Product Information -->
                @endif
            </div>
            <!-- Ppi Status -->
            <div class="col-md-2">


            <!-- PPi Product Step Information -->
            @if(isset($getSpiProduct) && count($getSpiProduct))
                <div class="done_this_action">
                    <h6>
                        <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0 fw-bold">
                            SPI Action
                        </div>
                    </h6>
                    <div class="py-2 alert-gray">
                        @include('admin.pages.warehouse.single.spi.spi-step-action-button')
                    </div>
                </div>
            @endif

            @if(!empty($spi))
                <?php $spi_id = $spi->id; ?>
                <h6>
                    <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0 fw-bold">
                        SPI Status
                    </div>
                </h6>
                @include('admin.pages.warehouse.single.spi.spi-status')
            @endif
            </div>
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
                   </div>
                    <!-- PPI Type -->
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
                    </div>
                </div>
            `;
            return html;
        }

        /**
         * Contact List Use ajax
         */
        function contactSource() {
            $.ajax({
                "url": "{{route('contact_api_source')}}",
                "type": "GET",
                "success": function (data) {
                    //console.log(data)
                }
            })
        }

        contactSource();
    </script>

    <style>
        .category-related-link {
            justify-content: flex-start;
        }
    </style>


    <!-- Import Product Modal -->
    @if(!empty($spi->id))
        {!! $Component::bootstrapModal('importProductFromSpi', [
            'modalHeader' => 'Import product from Spi',
            'position' => 'right',
            'backdrop' => true,
            'saveBtn' => 'import',
            'formAction' => route('spi_product_import_from_another_spi', request()->get('warehouse_code')),
            ]) !!}

        <script type="text/template" id="importProductTem">
            <div class="form-group">
                <label for="">Select SPI ID</label>
                <select name="from_spi_id" id="" class="form-control from-control-sm" required>
                    <option value=""></option>
                    @foreach($Model('PpiSpi')::where('action_format', 'Spi')->where('warehouse_id', request()->get('warehouse_id'))->get() as $data)
                        @if($spi->id == $data->id)
                        @else
                            <option value="{{$data->id}}">{{$data->id}}</option>
                        @endif
                    @endforeach
                </select>
                <input type="hidden" name="to_spi_id" value="{{$spi->id}}">
            </div>
        </script>
        <script>
            jQuery('#importProductFromSpiModalBody').html($('script#importProductTem').html())
        </script>
    @endif


    <script src="{{$viewDir}}/admin/pages/warehouse/single/spi/spi.js?{{rand(0,9999)}}"></script>





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
                header: "SPI ID {{$spi->id ?? null}}",               // prefix to html
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
            })
        }) //End


    </script>

@endsection
@php
    if(!empty($spi)){
       $getSatus = $Model('PpiSpiStatus')::where('ppi_spi_id', $spi->id)->get();
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
            $('#spi_content .spiProductEditBtn').remove();
        </script>
    @endsection
@endif
