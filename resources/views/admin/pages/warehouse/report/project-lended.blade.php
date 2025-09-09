@extends('admin.layouts.master')

@section('title')
    Project Report of {{$project_name}}
@endsection

@section('onlytitle')
    Project Report of <span class="text-primary">{{$project_name}}</span>

@endsection

@section('content')
    @php
        //$checkStockIn = $Model('PpiProduct')::leftjoin('ppi_spis', 'ppi_products.ppi_id', 'ppi_spis.id')
                        //->where('project', $project_name)
                        //->get();
        $sites = $Model('PpiSpiSource')::leftjoin('ppi_products', 'ppi_spi_sources.ppi_spi_id', 'ppi_products.ppi_id')
                            ->where('source_type', 'Site')
                            ->where('who_source', 'LIKE', '%Nipun Site%')
                            ->where('action_format',  'Ppi')
                            ->where('ppi_products.product_id', 1)
                            ->get()->groupBy('product_id');
        //dump($sites);
    @endphp
    <div class="content-wrapper" id="appVue">
        Product Name: {{$Model('Product'):: name(request()->product_id)}} | <span class="text-orange">Stock In hand: {{request()->stock_in_hand}}</span>
        <div class="row">
            <div class="col-md-6">
                @php
                    $lendTaken = $Model('SpiProductLoanFromProject')::where('original_project', $project_name);
                    if(request()->product_id){
                        $lendTaken = $lendTaken->where('product_id', request()->product_id);
                    }
                    $lendTaken = $lendTaken->get();
                    //dump($lendGiven);
                @endphp
                <h6 class="mb-2">
                    <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0 fw-bold">
                        <div class="d-inline">Lend Taken</div>
                        <span v-if="errorMsg" v-html="errorMsg" class="text-danger text-center float-end"></span>

                        <form action="{{route('report_lended_from_project_start_return')}}" method="post"  class="float-end" v-if="startreturnProcess">
                            @csrf
                            <input type="hidden" value="{{request()->stock_in_hand}}" name="stock_in_hand">
                            <input type="hidden" value="{{request()->product_id}}" name="product_id">
                            <input type="hidden" value="{{request()->product_id}}" name="project">

                            <input v-if="inputField" type="hidden" name="lended_id" :value="inputField">
                            <button type="submit" class="btn btn-primary" style="vertical-align: top; line-height: 18px; padding: 0px 5px;">
                                Return <span v-html="qty"></span> items
                            </button>
                        </form>
                    </div>
                </h6>

                @foreach($lendTaken as $data)
                        <div class="col-lg-12 mb-2 font-11 bw-1 border-gray p-2 shadow-sm tr selectedRowIdbw0">
                            <div class="">
                                 <span class="td">
                                    <div class="d-flex" style="justify-content: space-between;">
                                            @php
                                            $lastStatus = $Model('PpiSpiStatus')::where('status_for', 'Spi')->where('ppi_spi_product_id', $data->spi_product_id)->orderBy('id', 'desc')->first();
                                            @endphp
                                          <a
                                              title="Spi Product ID {{$data->spi_product_id}}"
                                              target="_blank" href="http://mtswarehouse.test/warehouse_1_af9b/ppi/edit/1492" class="d-inline-block">
                                            <span> <b>Spi ID:</b> {{$data->spi_id}} </span>
                                            <span class="text-dark fw-bold">.</span>
                                            <span><b>Product Name:</b> {{$Model('Product')::name($data->product_id)}}</span>
                                            <span class="text-dark fw-bold">.</span>
                                            <span><b>Taken From:</b> {{ $data->landed_project }}</span>
                                            <span class="text-dark fw-bold">.</span>
                                            <span><b>Qty:</b> {{ $data->qty }}</span>
                                            <span class="px-2 alert-warning">{{$data->status}}</span>
                                            <span class="text-green">{{$lastStatus->message}}</span>
                                        </a>
                                        @if($lastStatus->code == 'spi_product_out_from_stock' && $data->status != 'done')
                                            <label for="as{{$data->id}}" class="alert-primary p-2">
                                                <input class="text-right mb-0 h-auto"
                                                       style="box-shadow: none"
                                                       id="as{{$data->id}}"
                                                       type="checkbox" value="{{$data->id}}"
                                                       v-model="checkedItem"
                                                       v-on:change="qtyCalCulate(event, {{$data->qty}})">
                                            </label>
                                        @endif
                                    </div>
                                 </span>
                            </div>
                        </div>

                @endforeach

            </div>

            <div class="col-md-6">
                @php
                    $lendGiven = $Model('SpiProductLoanFromProject')::where('landed_project', $project_name);
                    if(request()->product_id){
                        $lendGiven = $lendGiven->where('product_id', request()->product_id);
                    }
                    $lendGiven = $lendGiven->get();
                    //dump($lendTaken);
                @endphp
                <h6 class="mb-2">
                    <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0 fw-bold"> Lend Given </div>
                </h6>
                @foreach($lendGiven as $data)

                    <div class="col-lg-12 mb-2 font-11 bw-1 border-gray p-2 shadow-sm tr selectedRowIdbw0">
                        <div class="">
                             <span class="td">
                                    <div class="d-inline-block">
                                          <a target="_blank" href="http://mtswarehouse.test/warehouse_1_af9b/ppi/edit/1492">
                                            <span> <b>Spi ID:</b> {{$data->spi_id}} </span>
                                            <span class="text-dark fw-bold">.</span>
                                            <span><b>Product Name:</b> {{$Model('Product')::name($data->product_id)}}</span>
                                            <span class="text-dark fw-bold">.</span>
                                            <span><b>Given To:</b> {{ $data->original_project }}</span>
                                            <span class="text-dark fw-bold">.</span>
                                            <span><b>Qty:</b> {{ $data->qty }}</span>
                                            <span class="px-2 alert-warning">{{$data->status}}</span>
                                            <br> &nbsp;
                                        </a>
                                    </div>

                                 </span>
                        </div>
                    </div>

                @endforeach

            </div>
        </div>
    </div>
@endsection


@section('cusjs')

    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>

    <script>
        new Vue({
            // Choosing the element to be controlled by Vue.JS by ID
            el: "#appVue",
            data() {
                return {
                    checkedItem : [],
                    qty : 0,
                    startreturnProcess : false,
                    inputField : false,
                    errorMsg : false,
                }
            },
            methods: {
                qtyCalCulate(event, qty){
                    if(event.target.checked){
                        this.qty = Math.round(this.qty+qty)
                    }else {
                        this.qty = Math.round(this.qty-qty)
                    }
                    this.inputField = this.checkedItem ? this.checkedItem.join(','): false;
                    let avaiableQty = '{{request()->stock_in_hand}}';
                    this.startreturnProcess = avaiableQty >= this.qty && this.qty > 0 ? true : false
                    this.errorMsg =   avaiableQty >= this.qty ? false : 'Maximum qty exceeded of stock in hand';
                }
            },
        })
    </script>

@endsection
