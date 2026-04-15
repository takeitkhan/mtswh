@php
    $mainQty = $product->qty;
    $alreadyPurchase = $Model('PurchaseVendor')::where('spi_id', $spi->id)->where('spi_product_id', $product->id)->first();
    $remainQty = $alreadyPurchase ? $alreadyPurchase->qty : 0;
    $remainQty = $mainQty-$remainQty;
@endphp
@if($remainQty)
<div class="card" style="border: 1px solid #ddd">
    <div class="card-header py-1 alert-info">Purchase From vendor</div>
    <di class="card-body">
        <form action="{{route('spi_buy_product_form_vendor', request()->get('warehouse_code'))}}" method="post">
            @csrf
            <input type="hidden" name="spi_id" value="{{$spi->id ?? null}}">
            <input type="hidden" name="spi_product_id" value="{{$product->id ?? null}}">
            <input type="hidden" name="product_id" value="{{$product->product_id ?? null}}">
            <input type="hidden" name="product_name" value="{{$product_name ?? null}}">
            <input type="hidden" name="unit" value="{{$productUnit ?? null}}">
            <input type="hidden" name="product_state" value="{{$productState ?? null}}">
            <input type="hidden" name="health_status" value="{{$healthStatus ?? null}}">
            <div class="form-group">
                @php
                    $getSources = $Model('PpiSpiSource')::where('ppi_spi_id', $spi->id)->first();
                @endphp
                <label for="">Sources</label>
                <input type="hidden" value="{{$getSources->who_source}}" name="vendor_name" id="">
                <input type="hidden" value="{{$getSources->who_source_id}}" name="vendor_id" id="">
                <label  for="">{{$getSources->who_source}}</label>
            </div>
            <div class="form-group">
                <label for="">Qty</label>
                <input required min="1" autocomplete="off" type="number"  name="qty" value="" class="from-control bg-white buy_qty" max="{{$remainQty }}">
            </div>
            <div class="form-group">
                <label for="">Price Per Unit</label>
                <input type="number" name="price" value="" class="from-control bg-white">
            </div>
            <div class="from-group">
                <button class="btn btn-sm btn-outline-info py-0">Ready to Buy</button>
            </div>
        </form>
    </di>
</div>

<script>
    let buyQty = document.querySelector('.buy_qty');
    buyQty.addEventListener('keyup', () => {
        if(buyQty.value > parseInt("{{$remainQty }}")){
            alert('Maximum Qty exceeded');
            buyQty.value = ''
        }
    })
</script>
@endif
