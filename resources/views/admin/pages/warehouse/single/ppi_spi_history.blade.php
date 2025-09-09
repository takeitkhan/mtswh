@extends('admin.layouts.master')

@section('title')
    History ID {{$history->id}} | {{$history->action_format}} ID: {{$history->ppi_spi_id}}
@endsection

@section('filterleft')
    <div>
        <strong>Time of Action: </strong> {!! $Query::getDateTimeFormat($history->action_time) !!} |
        <strong>Action Performed By:</strong> {{$Model('User')::getColumn($history->action_performed_by, 'name')}}
    </div>


@endsection

@section('content')
    @php
        $getPpiSpiProductId = request()->get('ppi_spi_product_id') ?? null;
    @endphp
    <div class="content-wrapper">

            @php
                $oldData =  (object) json_decode($history->chunck_old_data, true);
                $newData =  (object) json_decode($history->chunck_new_data, true);
                //dump($oldData);
            @endphp
            <?php
            //Product template Function
           $productTemplate = function ($getData) use ($Model, $history, $getPpiSpiProductId) {
                    $getData = $getData;
                    $ppiProductGroup = [];
                    foreach ($getData->ppi_products as $products){
                        $ppiProductGroup[$products['id']] = $products;
                    }

                    $bundleGroup = [];
                    foreach ($getData->ppi_bundle_products as $bundle){
                        $bundleGroup[$bundle['ppi_product_id']][] = $bundle;
                    }

                    $setGroup = [];
                    foreach($getData->ppi_set_products as $set){
                        $ppi_product_set = explode(',', $set['ppi_product_id']);
                        foreach ($ppi_product_set as $product){
                            $setGroup[$product] = $set;
                        }
                    }
                ?>

                <div class="row font-14 py-1">
                    <div class="col">
                        <strong>{{$history->action_format}} Type:</strong> {{$getData->ppi_basic_info['ppi_spi_type']}}
                    </div>

                    <div class="col">
                        <strong>Project:</strong> {{$getData->ppi_basic_info['project']}}
                    </div>
                    <div class="col">
                        <strong>Transaction Type:</strong> {{$getData->ppi_basic_info['tran_type']}}
                    </div>
                </div>

                <div class="row font-14 py-1">
                    <p><strong>Product Source</strong></p>
                    <div class="col-md-12 mt-1">
                        <div class="crumbswrapper">
                            <div class="crumbs my-0" id="source_breadcrumb">
                                <?php $getSourceTree = $getData->ppi_source;
                                foreach($getSourceTree as $tree) {
                                   $tree = (object) $tree;?>
                                    <div class="innerwrap">
                                            <span class="innerItem">
                                                <span>{{$tree->source_type}}:</span> {{$tree->who_source}}
                                            </span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
                <h6>
                    <div class="title-with-border fw-bold">
                        Products
                    </div>
                </h6>
                <div class="py-1 font-12">
                    <table class="table table-bordered table-sm  thin-table">
                        <thead>
                        <tr style="background: #d1f4ff !important;" class="text-center">
                            <th>Product Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Product State</th>
                            <th>Health Status</th>
                            <th>Note</th>
                        </tr>
                        </thead>
                        <!-- Set Product -->
                        <?php
                        if($getData->ppi_set_products){
                        foreach($getData->ppi_set_products as $setProduct) : ?>
                            <tbody style="border: 3px solid #e1d36d">
                            <tr style="background-color: #fff3cd; !important;">
                                <td colspan="6" class="text-center">
                                    <b>{{$setProduct['set_name']}}</b>
                                </td>
                            </tr>

                            <?php
                            $exSet = explode(',', $setProduct['ppi_product_id']);
                            if($setProduct['ppi_product_id'] != ''):
                            foreach($exSet as $setP):
                                    $setP = (object) $ppiProductGroup[$setP];
                                    $product_unit_id = $setP->product_id ?  $Model('Product')::getColumn($setP->product_id, 'unit_id') : null;
                            ?>
                                <tr  class="{{ $getPpiSpiProductId == $setP->id ? 'bg-beige' :  null }}">
                                    <td>{{$Model('Product')::name($setP->product_id)}}</td>
                                    <td>
                                        <table class="table table-bordered mb-0 table-sm thin-table">
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
                                                    {{$setP->qty}}
                                                    {!! $Model('AttributeValue')::getValueById($product_unit_id) !!}
                                                </td>
                                                <td class="bundle-row text-center ppi_product_price_show">{!! $setP->unit_price !!}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td class="text-center">{{$setP->price}}</td>
                                    <td>{{$setP->product_state}}</td>
                                    <td>{{$setP->health_status}}</td>
                                    <td>{{$setP->note}}</td>
                                </tr>
                           <?php endforeach; endif;?>
                            </tbody>
                        <?php endforeach;}?>
                        <!--End Set Product -->

                        <!-- Single & Bundle Product -->
                        <tbody>
                        <?php foreach($getData->ppi_products as $product):
                                $product = (object) $product;
                                $product_unit_id =  $Model('Product')::getColumn($product->product_id, 'unit_id');
                                //dump($product);
                            if(array_key_exists($product->id, $setGroup) == false): ?>
                                <tr class="{{ $getPpiSpiProductId == $product->id ? 'bg-beige' :  null }}">
                                    <td title="product-id={{$product->product_id}} {{$history->action_format == 'Spi' ? 'spi_product_id='.$product->id : null }} ppi_product_id={{$product->ppi_product_id ?? $product->id}}">
                                        {{$Model('Product')::name($product->product_id)}}
                                    </td>
                                    <td>
                                        <table class="table table-bordered mb-0 table-sm thin-table">
                                        <?php if(isset($product->product_state) && $product->product_state == 'Cut-Piece'): ?>
                                            <!-- Bundle product -->
                                                <thead>
                                                <tr>
                                                    <th title="Bundle Size" class="bundle-row text-center">Size</th>
                                                    <th title="Bundle Unit Price"
                                                        class="bundle-row text-center ppi_product_price_show">Price
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                    $bundles = $bundleGroup[$product->id];
                                                    $bundleName = [];
                                                foreach( $bundles as $bundle):
                                                    $bundle = (object) $bundle; ?>
                                                    <tr>
                                                        <td style="color:#333" class="bundle-row text-center">
                                                            {!! $bundle->bundle_size !!} {!! $Model('AttributeValue')::getValueById($product_unit_id)  !!}
                                                        </td>
                                                        <td style="color:#333"
                                                            class="bundle-row text-center ppi_product_price_show"> {!! $bundle->bundle_price !!}</td>
                                                    <tr>
                                                        <td class="bundle-row text-center ppi_product_price_show"
                                                            colspan="3">
                                                            Total: {!! $bundle->bundle_size*$bundle->bundle_price !!}</td>
                                                    </tr>
                                                <?php endforeach;?>
                                                @php $bundleName = 'true'  @endphp
                                                </tbody>
                                                <!-- End Bundle Product -->
                                            <?php else: ?>
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
                                            <?php endif;?>
                                        </table>
                                    </td>
                                    <td class="text-center">{{$product->price}}</td>
                                    <td>{{$product->product_state ?? null}}</td>
                                    <td>{{$product->health_status ?? null}}</td>
                                    <td>{{$product->note}}</td>
                                </tr>
                            <?php endif;?>
                        <?php endforeach;?>
                        </tbody>

                        <!-- End Single & Bundle Product -->
                    </table>
                </div>
            <?php } ?>






        <div class="row">
            <div class="col-md-6">
                <h6>
                    <div class="title-with-border mb-2 alert-secondary px-2 text-dark border-0 fw-bold">
                        Before
                    </div>
                </h6>

                @if($history->chunck_old_data != 'null')
                {!! $productTemplate($oldData) !!}
                @endif

            </div>
            <div class="col-md-6">
                <h6>
                    <div class="title-with-border mb-2 alert-secondary px-2 text-dark border-0 fw-bold">
                        After
                    </div>
                </h6>

                @if($history->chunck_new_data != 'null')
                {!! $productTemplate($newData) !!}
                @endif
            </div>

        </div>


    </div>

@endsection


