@extends('admin.layouts.master')

@section('title')
    Lended SPI
@endsection
@section('content')
    <div class="table-wrapper desktop-view mobile-view" id="lended_spi">
            <table class="" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th class="w-auto">Product Name</th>
                            <th>QTY</th>
                            <th class="ppi_product_price_show">Price</th>
                            <th>Product State</th>
                            <th>Health Status</th>
                            <th>Note</th>
                            <th>Warehouse</th>
                            <th width="135px">Physical Validation</th>
                        </tr>
                    </thead>
                <tbody>
                    @foreach($spis as $key => $product)
                        @php
                            $product_unit_id =  $Model('Product')::getColumn($product->product_id, 'unit_id');
                            $unique_key =  $Model('Product')::getColumn($product->product_id, 'unique_key');
                            /*
                            $checkExistingWithDB = $Model('ProductStock')::where('barcode', $barCodeDigit)
                                                    ->where('product_id', $thisProductId)
                                                    ->where('ppi_spi_id', $product->id)
                                                    ->where('action_format', 'Spi')
                                                    ->where('ppi_spi_product_id', $product->id)
                                                    ->first();
                            */
                            $ppiLastMainStatus = $Model('PpiSpiStatus')::getSpiLastMainStatus($product->spi_id);
                            $checkStockOutThisProduct = $Model('PpiSpiStatus')::checkSpiStatus($product->spi_id, 'spi_product_out_from_stock', ['ppi_spi_product_id' => $product->id]);
                        @endphp
{{--                    @if(($ppiLastMainStatus->code == 'spi_resent_to_wh_manager') || ($ppiLastMainStatus->code == 'spi_sent_to_wh_manager'))--}}
                    @if($checkStockOutThisProduct)
                    @else
                    <tr>
                        <td class="w-auto">
                            {!! $Model('Product')::name($product->product_id) !!}
                        </td>
                        <td>
                            <table class="thin-table" style="border-collapse: collapse;">
                                <thead>
                                <tr>
                                    <th title="Qty" class="bundle-row text-center">Qty</th>
                                    <th title="Unit Price"
                                        class="bundle-row text-center ppi_product_price_show">Price
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="bundle-row text-center">
                                        {{$product->qty}}
                                        {!! $Model('AttributeValue')::getValueById($product_unit_id) !!}
                                    </td>
                                    <td class="bundle-row text-center ppi_product_price_show">{!! $product->unit_price !!}</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="ppi_product_price_show">{{$product->price}}</td>
                        <td>  {!! $productState =  $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'product_state']) !!} </td>
                        <td>  {!! $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'health_status']) !!} </td>
                        <td> {!! $product->note !!} </td>
                        <td> <strong>{{ $Model('Warehouse')::name($product->from_warehouse) }}</strong> To <strong> {{$Model('Warehouse')::name($product->warehouse_id)}}</strong></td>
                        <td>

                            @if(auth()->user()->hasRoutePermission('spi_get_line_item'))
                                <?php if ($productState == 'Cut-Piece') {
                                    $bundleName = $product->bundle_id;
                                    $addBundleGetMethod = '?bundle=' . $bundleName;
                                } else {
                                    $addBundleGetMethod = null;
                                }

                                //$ppiLastPpiProductStatus = $Model('PpiSpiStatus')::getSpiLastStatus($spi->id, ['ppi_spi_product_id' => $product->spi_product_id]);
                                //$ppiLastPpiProductStatusCode = $ppiLastPpiProductStatus->code ?? null;
                                //dump($ppiLastPpiProductStatusCode);


                                if ($checkStockOutThisProduct) {
                                    $validationBgColor = 'green';
                                    $validationText = 'Validated';

                                } else {
                                    $validationBgColor = 'blue';
                                    $validationText = 'Vailidation';
                                }

                                if(auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action')){
                                    $validationText = $validationText;
                                }else {
                                    $validationText = 'Details';
                                }

                                ?>

                                <a class="btn btn-sm  py-0 btn-soft-{{$validationBgColor}}-gradient"
                                   href="{{ route('spi_get_line_item', [request()->get('warehouse_code')  , $product->id]) }}{{$addBundleGetMethod ?? null}}">
                                    <i style="font-size: 17px;"
                                       class="fas fa-barcode bg-transparent  m-auto d-inline-block"></i> {{$validationText}}
                                </a>
                            @endif

                            @if($checkStockOutThisProduct)
                                <p class="badge bg-success mt-2">Stocked out</p>
                            @endif


                            <?php /*
                            <form method="post" id="stockOutForm{{$key}}">
                                @csrf
                                <input type="hidden" name="spi_id" value="{{ $product->spi_id }}" />
                                <input type="hidden" name="spi_product_id" value="{{ $product->id }}" />
                                <input type="hidden" name="product_id" value="{{ $product->product_id }}" />
                                <input type="hidden" name="product_unique_key" value="{{ $unique_key }}" />
                                <input type="hidden" name="warehouse_id" value="{{ $product->from_warehouse }}" />
                                <input type="hidden" name="note" value="lend from {{$Model('Warehouse')::name($product->from_warehouse) }} To {{$Model('Warehouse')::name($product->warehouse_id)}}" />
                                <a type="button" id="stockOutFormBtn" data-row_id = {{$key}}  class="btn btn-sm py-0 btn-primary">Acceprt for Stock Out</a>
                            </form>
 */ ?>

                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>

        </div>

@endsection


@section('cusjs')
     @php
            /**
             * ppi Elements Setup
             * Show / Hide or Any Permission use for Button , row a
             */
            echo $PpiSpiPermission::elements();
     @endphp
    <script>
        $('div#lended_spi').on('click', 'a#stockOutFormBtn', function(e) {
            e.preventDefault();
            let barcodeRoute =
                "{{ route('spi_product_stock_out', [request()->get('warehouse_code')]) }}";
            let rowId = $(this).data('row_id');
            let stockOutForm = 'form#stockOutForm'+rowId;
            $(stockOutForm).attr('action', barcodeRoute)
            confirmAlert('Are you ready to stock out the product', '', '#ppiFormAction');
        })
    </script>

@endsection
