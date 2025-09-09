<table>
    <thead>
    <tr>
        <th>
        {{-- <input type="checkbox" id="checkAllCheckBox" class="h-auto mb-0" value=""> --}}
        </td>
        <th class="text-center">Product Name
        </th>
        @if($bundle_product)
            <th class="text-center">Size Of Bundle</th>
            <th class="text-center">Qty Of Bundle</th>
        @else
            <th class="text-center">Qty</th>
        @endif
        <th class="text-center" width="120px">Barcode Digit
        </th>
        <th class="text-center" width="200px">Action
        </th>
    </tr>
    </thead>
    <input type="hidden" name="spi_id" value="{{ $product->spi_id }}"/>
    <input type="hidden" name="spi_product_id" value="{{ $product->id }}"/>
    <input type="hidden" name="product_id" value="{{ $product->product_id }}"/>
    <input type="hidden" name="product_unique_key" value="{{ $unique_key }}"/>
    <input type="hidden" name="warehouse_id" value="{{ $product->warehouse_id }}"/>
    <input type="hidden" name="bundle_id" value="{{ $bundle_product ?? null }}"/>
    <tbody>

    @foreach ($getLineItem as $lineItem)
        @php
            //dump($getBarcode);
            $barCodeDigit = $lineItem->barcode;
            $orginalBarCodeDigit = $lineItem->original_barcode;
            //dump($lineItem);
            $thisProductId = $Model('Product')::getColumn($product->product_id, 'id');

            $checkExistingWithDB = $Model('ProductStock')::where('barcode', $barCodeDigit)
                                ->where('product_id', $thisProductId)
                                //->where('stock_type', 'Existing')
                                ->where('ppi_spi_id', $spi_id)
                                ->where('action_format', 'Spi')
                                ->where('ppi_spi_product_id', $product->id)
                                ->first();
        @endphp

        <tr style="background: {{ $checkExistingWithDB ? '#ffecb5' : null }}">
            <td>
                {{--                                                    @if ($checkExistingWithDB)--}}

                {{--                                                    @else--}}

                <input class="mb-0 d-none" id="barcode_product_line_item"
                       type="checkbox" name="barcode_product_line_item[]"
                       {{ $ppiLastStatusCode == 'spi_agreed_no_dispute' ? 'checked' : null }}
                       value="{{$orginalBarCodeDigit}}"/>

                <input class="mb-0 d-none" id=""
                       type="checkbox" name="barcode_product_unique_key[]"
                       {{ $ppiLastStatusCode == 'spi_agreed_no_dispute' ? 'checked' : null }}
                       value="{{$barCodeDigit}}"/>

                {{--                                                    @endif--}}
            </td>
            <!-- Product Name -->
            <td>
                {!! $Model('Product')::getColumn($product->product_id, 'name') !!}
            </td>
            <!-- End Product Name -->

            <!-- Product Qty -->
            @php
                if($barcode_format == 'Without-Tag'){
                    $qty =  $product->qty;
                }else{
                    $qty =  $lineItem->qty;
                }
            @endphp
            @if($bundle_product)
             <td class="text-center">{{$bundle_product}}</td>
            @endif
            <td class="text-center">{{$qty}}</td>

            <!-- Bundle -->
            <input type="hidden" name="qty[]" value="{{$qty}}">
            <!-- End Produt Qty -->

            <td class="{{ !empty($checkExistingWithDB) ? 'unselectable' : null }}">
                @php
                    /**
                    * For Print
                    * */
                    $forPrint []= $Query::barcodeGenerator($barCodeDigit, ['show_digit' => $orginalBarCodeDigit]);
                @endphp

                @if($barcode_format == 'Tag')
                    <p class="text-center">
                        {!! $Query::barcodeGenerator($barCodeDigit, ['show_digit' => $orginalBarCodeDigit]) !!}
                        {{--                                                            {{$barCodeDigit}}--}}
                    </p>
                @elseif($barcode_format == 'Bundle-Tag')
                    <p class="text-center">
                        {!! $Query::barcodeGenerator($barCodeDigit, ['show_digit' => $orginalBarCodeDigit]) !!}
                        {{--                                                            {{$barCodeDigit}}--}}
                    </p>
                @else
                    {{$barcode_format}}
                @endif

            </td>

            <td class="text-center">
                @if($checkExistingWithDB)
                    <span class="badge bg-success">
                     Stocked Out
                  </span>
                @else

                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
