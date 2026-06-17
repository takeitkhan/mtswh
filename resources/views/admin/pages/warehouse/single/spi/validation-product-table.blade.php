<table>
    <thead>
    <tr>
        <th>
        {{-- <input type="checkbox" id="checkAllCheckBox" class="h-auto mb-0" value=""> --}}
        </td>
        <th class="text-center">Product Name
        </td>
        @if($bundle_product)
            <th class="text-center">Name Of Bundle</th>
            <th class="text-center">Qty Of Bundle</th>
        @else
            <th class="text-center">Qty</th>
        @endif
        <th class="text-center" width="120px">Barcode Digit
        </td>
        <th class="text-center" width="200px">Action
        </td>
    </tr>
    </thead>
    <input type="hidden" name="spi_id" value="{{ $product->spi_id }}"/>
    <input type="hidden" name="spi_product_id" value="{{ $product->id }}"/>
    <input type="hidden" name="product_id" value="{{ $product->product_id }}"/>
    <input type="hidden" name="product_unique_key" value="{{ $unique_key }}"/>
    <input type="hidden" name="warehouse_id" value="{{ $product->warehouse_id }}"/>
    <tbody>

    @foreach ($getLineItem as $lineItem)
        @php
            $barCodeDigit = $lineItem->barcode;
            $orginalBarCodeDigit = $lineItem->original_barcode;
            $thisProductId = $Model('Product')::getColumn($product->product_id, 'id');

            $checkStockOutThisProduct = $Model('ProductStock')::where('barcode', $barCodeDigit)
                                ->where('product_id', $thisProductId)
                                ->where('ppi_spi_id', $spi_id)
                                ->where('action_format', 'Spi')
                                ->where('ppi_spi_product_id', $product->id)
                                ->where('stock_action', 'Out')
                                ->first();

            if($barcode_format == 'Without-Tag'){
                $qty = $product->qty;
            }else{
                $qty = $lineItem->qty ?? 1;
            }
        @endphp

        <tr style="background: {{ $checkStockOutThisProduct ? '#ffecb5' : null }}">
            <td>
                <input class="mb-0 d-none" id="barcode_product_line_item"
                       type="checkbox" name="barcode_product_line_item[]"
                       {{ $ppiLastStatusCode == 'spi_agreed_no_dispute' ? 'checked' : null }}
                       value="{{$orginalBarCodeDigit}}"/>

                <input class="mb-0 d-none" id=""
                       type="checkbox" name="barcode_product_unique_key[]"
                       {{ $ppiLastStatusCode == 'spi_agreed_no_dispute' ? 'checked' : null }}
                       value="{{$barCodeDigit}}"/>
            </td>
            <!-- Product Name -->
            <td>
                {!! $Model('Product')::getColumn($product->product_id, 'name') !!}
            </td>
            <!-- End Product Name -->

            <!-- Product Qty -->
            @if($bundle_product)
                <td class="text-center">{{$bundle_product}}</td>
            @endif
            <td class="text-center">{{$qty}}</td>

            <!-- Bundle -->
            <input type="hidden" name="qty[]" value="{{$qty}}">
            <!-- End Produt Qty -->

            <td class="{{ !empty($checkStockOutThisProduct) ? 'unselectable' : null }}">
                @php
                    /**
                    * For Print
                    * */
                    $forPrint []= $Query::barcodeGenerator($barCodeDigit, ['show_digit_title' => $barCodeDigit, 'show_digit' => $orginalBarCodeDigit]);
                @endphp

                @if($barcode_format == 'Tag')
                    <p class="text-center">
                        {!! $Query::barcodeGenerator($barCodeDigit, ['show_digit' => $orginalBarCodeDigit]) !!}
                    </p>
                @elseif($barcode_format == 'Bundle-Tag')
                    <p class="text-center">
                        {!! $Query::barcodeGenerator($barCodeDigit, ['show_digit' => $orginalBarCodeDigit]) !!}
                    </p>
                @else
                    {{$barcode_format}}
                @endif

            </td>

            <td class="text-center">
                @if($checkStockOutThisProduct)
                    <span class="badge bg-success">
                        Stocked Out
                    </span>
                @else
                    @if(auth()->user()->hasRoutePermission('spi_ready_to_physical_validation_action'))
                        @if ($ppiLastStatusCode == 'spi_agreed_no_dispute')
                            @if($barcode_format == 'Tag' || $barcode_format == 'Bundle-Tag')
                                <button type="button" id=""
                                        class="btn btn-sm btn-outline-info py-0 existingProduct"
                                        data-barcode="{!! $barCodeDigit !!}"
                                        data-orginal_barcode="{!! $orginalBarCodeDigit !!}"
                                        data-spi_product_id="{{ $product->id }}"
                                        data-product_unique_key="{{ $unique_key }}"
                                        data-product_qty="{{$qty}}"
                                        data-product_id="{!! $thisProductId !!}">
                                    Verify?
                                </button>
                            @endif
                        @endif
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
