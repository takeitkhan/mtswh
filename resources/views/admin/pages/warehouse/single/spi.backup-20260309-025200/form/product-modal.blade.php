<div id="spi_product_section" class="row">

</div>

<!-- SPI Product Insert / Edit Form Template -->
@php
    if(!empty($spi)){
        if($spi->tran_type == 'Without Money'){
            $productPrice = 0;
            $priceDisabled = 'readonly';
        }else {
            $productPrice = 0;
        }
    }
@endphp
<script>
    function SpiProductTemplate(id = ''){
        let html = `
        <!-- Products -->
        <div class="form-group">
        @php
        $getProducts = $Model('Product')::thisWarehouseProducts(['product_type', $spi->ppi_spi_type]);
        @endphp
        <label for="product">Select Product</label>
        <select name="product[${id}][product_id]" data-id="${id}" id="product_id" class="form-select select-box-modal" required>
            <option value=""></option>
            @foreach ($getProducts as $product)
            <option value="{{$product->id}}"
                {{ isset($spiEditProduct) && $spiEditProduct->product_id == $product->id ? 'selected' : ''  }}>
                {{$product->name}} - {{$product->product_type}}
            </option>
            @endforeach
        </select>
    </div>

    <div class="form-group pb-2">
        <label>&nbsp;</label>
        <button type="button" id="selectedProductInfo"  class="selectedProductInfo btn btn-sm btn-primary py-0" data-row-id="${id}">
            Check Stock
        </button>
    </div>
<?php /*
<!-- Product State -->
<div class="form-group">
    <label for="product_state">Product State</label>
@php $getProductState = $Query::getEnumValues('spi_products', 'product_state') @endphp
        <select name="product_state[]" id="product_state" class="form-select" data-id="${id}" required>
            <option value="" disabled="" selected="">Select</option>
            @foreach($getProductState as $value)
        <option value="{{$value}}"
                {{ isset($spiEditProduct) && $spiEditProduct->product_state == $value? 'selected' : ''  }}>
                {{$value}}
        </option>
@endforeach
        </select>
    </div>

    <!-- Health Status -->
    <div class="form-group">
        <label for="health_status">Health Status</label>
        @php $getHealthStatus = $Query::getEnumValues('spi_products', 'health_status') @endphp
        <select name="health_status[]" id="health_status" class="form-select" required>
            <option value="" disabled="" selected="">Select</option>
            @foreach($getHealthStatus as $value)
        <option value="{{$value}}"
            {{ isset($spiEditProduct) && $spiEditProduct->health_status == $value? 'selected' : ''  }}>
                {{$value}}
        </option>
@endforeach
        </select>
    </div>
*/ ?>
     <!-- Product QTY || Regular Qty -->
     <div class="form-group" id="regular_qty" data-id="${id}" style="display: {{isset($spiEditProductBundle) && count($spiEditProductBundle) > 0 ? 'none' : 'flex'}}">
        <label for="qty">QTY <span id="show_unit" class="text-danger"></span> </label>
        <input readonly type="number"  min="{{isset($spiEditProductBundle) && count($spiEditProductBundle) > 0 ? '0' : '1'}}" name="product[${id}][qty]" id="qty" class="form-control" value="{{ isset($spiEditProduct) ? $spiEditProduct->qty : '' }}" {{isset($spiEditProductBundle) && count($spiEditProductBundle) > 0 ? '' : 'required'}}>
    </div>

    <!-- Bundle Qty -->
    <?php /*
    <div class="form-group" id="bundle_qty" data-id="${id}" style="display: {{isset($spiEditProductBundle) && count($spiEditProductBundle) > 0 ? 'flex' : 'none'}}">
        <label for="qty">Bundle Size</label>
        <span id="bundle_breakdown" class="w-100 text-center">
        @if(isset($spiEditProductBundle) && count($spiEditProductBundle) > 0)
        @foreach($spiEditProductBundle as $key => $bundle)
        <div>
            <div class="w-25 d-inline-block me-4">
                <small>Qty</small> <br>
                <input type="number" min="1" name="bundle_size[{{$spiEditProduct->product_id}}][]" id="bundle_size" class="form-control" value="{{$bundle->bundle_size}}">
                            </div>
                    <div class="w-25 d-inline-block">
                        <small>Unit Price</small> <br>
                        <input type="number" min="1" name="bundle_price[{{$spiEditProduct->product_id}}][]" id="bundle_price" class="form-control bundle_price no-arrow" value="{{$bundle->bundle_price}}">
                    </div>
                @if($key == 0)
        <a href="javascript:void(0);" onclick=spiProductBundle('add', ${id})" data-id="${id}" class="bundle_add_button d-inline-block ms-3 valign-text-bottom" title="Remove field"><i class="fa fa-plus"></i></a>
                @else
        <a href="javascript:void(0);" onclick="spiProductBundle('remove', ${id})" class="bundle_remove_button d-inline-block ms-3 valign-text-bottom" title="Remove More"><i class="fa fa-times"></i></a>
                @endif
        </div>
        @endforeach
        @else
        <div class="cutpiece_breakdown" data-breakdown="0">
            <div class="w-25 d-inline-block me-4">
                <small>Qty</small> <br>
                <input type="number" data-id ="${id}" min="1" name="bundle_size[]" id="bundle_size" class="form-control" value="">
                        </div>
                        <div class="w-25 d-inline-block">
                            <small>Unit Price</small> <br>
                            <input type="number" data-id ="${id}"  name="bundle_price[]" id="bundle_price" class="form-control bundle_price no-arrow" value="">
                        </div>
                        <div class="w-25 d-inline-block">
                            <small>Sub Total</small> <br>
                            <input readonly type="number" data-id ="${id}" xname="bundle_subtotal_price[]" id="bundle_subtotal_price" class="form-control bundle_subtotal_price no-arrow" value="">
                        </div>

                        <a href="javascript:void(0);" onclick="spiProductBundle('add', ${id})" data-id="${id}" class="bundle_add_button d-inline-block ms-3 valign-text-bottom" title="Remove field">
                            <i class="fa fa-plus"></i>
                        </a>
                </div>
            @endif
        </span>
    </div>
 */ ?>
        <!-- Single Product Unit price -->
        @php $spiEditProductPrice = isset($spiEditProduct) ? $spiEditProduct->unit_price : 0  @endphp
        @if(isset($spiEditProductBundle) && count($spiEditProductBundle) > 0)
            @php $spiEditProductPrice = $spiEditProduct->price; @endphp
        @else
        <div class="form-group" id="single_product_unit_price" data-id="${id}">
            <label for="single_product_unit_price">Unit Price</label>
            <input step="any" {{$priceDisabled ?? null}} type="number" name="product[${id}][unit_price]" id="single_product_unit_price" class="form-control unit_price"
                value="{{isset($priceDisabled) ? $productPrice : $spiEditProductPrice }}">
        </div>
        @endif
        <div class="ppiInformation${id}">
            <div class="ppi_id_append">
                @if(isset($spiEditProduct) && !empty($spiEditProduct))
                    <input type="hidden" value="{{$spiEditProduct->ppi_id}}" name="product[${id}][ppi_id]" />
                    <input type="hidden" value="{{$spiEditProduct->ppi_product_id}}" name="product[${id}][ppi_product_id]" />
                    <input type="hidden" value="{{$spiEditProduct->from_warehouse}}" name="product[${id}][from_warehouse]" />
                    @if(!empty($spiEditProduct->bundle_id))
                    <input type="hidden" value="{{$spiEditProduct->bundle_id}}" name="product[${id}][bundle_id]" />
                    @endif

                    @php
                        $checkLendFromProject = $Model('SpiProductLoanFromProject')::where('spi_id', $spi->id)->where('spi_product_id', $spiEditProduct->id)->first();
                    @endphp

                        <input type="hidden" value="{{$checkLendFromProject->landed_project ?? null}}" name="product[${id}][landed_project]" />
                        <input type="hidden" value="{{$checkLendFromProject->original_project ?? $spi->project}}" name="product[${id}][originalProject]" />

                @endif
            </div>
        </div>

        <!-- Product price -->
        <div class="form-group" id="total_price" data-id="${id}">
            <label for="price">Total Price</label>
            @php
            if(!empty($spi)){
                if($spi->tran_type == 'Without Money'){
                    $productPrice = 0;
                    $priceDisabled = 'readonly';
                }else {
                    $productPrice = '';
                }
            }
            @endphp

            <input step="any" type="number" name="product[${id}][price]" id="price" class="form-control total_price"
            readonly
            {{ isset($spiEditProduct) && $spiEditProduct->product_state == 'Cut-Piece' ? 'readonly' : '' }}
            value="{{$productPrice ?? NULL}}{{ isset($spiEditProduct) ? $spiEditProduct->price : '' }}"
            {{$priceDisabled ?? 'required'}}>
        </div>

        <!-- Note -->
        <div class="form-group">
            <label for="note">Note</label>
            <textarea class="form-control" name="product[${id}][note]">{{ isset($spiEditProduct) ? $spiEditProduct->note : '' }}</textarea>
        </div>

        `;
        return html;
    }
</script>

<script type="text/template" xdata-template="spi_product_template">



</script>


@section('cusjs')
    @parent

    <script>
        /**
         * ===== JS Elements =================
         * ===== SPI Product =================
         * ===================================
         */
        /**
         *  Product State Select
         * Health Status Show Hide
         */
        $('#spi_product_section').on('change', 'select#product_id', function(){
            let colGroupId = $(this).data('id');
            $('.prb'+colGroupId+' select#product_state').val(null);
            $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input').val(null)
            $('#spi_product_section #total_price[data-id='+colGroupId+'] input').val(0)
            $('#spi_product_section #regular_qty[data-id='+colGroupId+'] input#qty').val(null)
            $('#spi_product_section #single_product_unit_price[data-id='+colGroupId+'] input#single_product_unit_price').val(0)
        });
        $('#spi_product_section').on('change', 'select#product_state', function(){
            let colGroupId = $(this).data('id');
            let productId = $('.prb'+colGroupId+' select#product_id').val();
            //alert(colGroupId)
            let hsv =$('#spi_product_section .colgroup[data-id='+colGroupId+'] select#health_status option[value="Scrapped"]');
            let psv = $('#spi_product_section .colgroup[data-id='+colGroupId+'] select#product_state').find(':selected').val();
            $('#spi_product_section .colgroup[data-id='+colGroupId+'] #total_price[data-id='+colGroupId+'] input.total_price').val(null)
            if( psv === 'New'){
                hsv.hide();
            }else if( psv === 'Used'){
                hsv.show();
            }

            if( psv === 'Cut-Piece'){
                //alert(productId)
                $('#spi_product_section #bundle_qty[data-id='+colGroupId+']').css('display', 'flex')
                $('#spi_product_section #regular_qty[data-id='+colGroupId+']').css('display', 'none')
                $('#spi_product_section #single_product_unit_price[data-id='+colGroupId+']').css('display', 'none')
                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').attr('required', 'required')
                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').attr('name', 'bundle_size['+productId+'][]')

                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').attr('required', 'required')
                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').attr('name', 'bundle_price['+productId+'][]')

                $('#spi_product_section #regular_qty[data-id='+colGroupId+'] input#qty').removeAttr('required').val(null)
                $('#spi_product_section #single_product_unit_price[data-id='+colGroupId+'] input#single_product_unit_price').removeAttr('required').val(0)

                $('#spi_product_section #regular_qty[data-id='+colGroupId+'] input#qty').val(null)
                $('#spi_product_section #single_product_unit_price[data-id='+colGroupId+'] input#single_product_unit_price').val(0)

                //$('#spi_product_section #total_price[data-id='+colGroupId+'] input.total_price').attr('readonly', true)
                //$('#spi_product_section #total_price[data-id='+colGroupId+'] label').html('Total Price')
            }else{
                $('#spi_product_section #bundle_qty[data-id='+colGroupId+']').css('display', 'none')
                $('#spi_product_section #regular_qty[data-id='+colGroupId+']').css('display', 'flex')
                $('#spi_product_section #single_product_unit_price[data-id='+colGroupId+']').css('display', 'flex')
                $('#spi_product_section #regular_qty[data-id='+colGroupId+'] input#qty').attr('required', 'required')
                $('#spi_product_section #single_product_unit_price[data-id='+colGroupId+'] input#single_product_unit_price').attr('required', 'required')

                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').removeAttr('required')
                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').val(null)
                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').attr('name', 'bundle_size[][]')

                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').removeAttr('required')
                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').val(0)
                $('#spi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').attr('name', 'bundle_price[][]')


                //$('#spi_product_section #total_price[data-id='+colGroupId+'] input.total_price').attr('readonly', true).val('')
                //$('#spi_product_section #total_price[data-id='+colGroupId+'] label').html('Total Price')

            }
        })
    </script>


    <script>
        /**
         * Select 2 for modal
         */
        function modelSelect2(){
            $('.select-box-modal').select2({
                dropdownParent: $('#spiProductModalLabel'),
                placeholder: "Select an Product",
                width: '100%'
            });
        }

        //Breakdown Add Button
        let addRowBtn = '@if(isset($spiEditProduct))\
                            Edit SPI Product\
                        @else\
                            <a href="javascript:void(0);" class="add_button d-block valign-text-bottom me-2" title="Add field"><i class="fa fa-plus"></i></a>\
                        @endif\
                        ';
        $('.modal-footer #add_btn').html('<a href="javascript:void(0);" class="add_button btn btn-sm btn-success valign-text-bottom me-2" title="Add field">Add More Product</a>');


        // Breakdown Remove Button
        function RemoveBtn(id){
            return  '<a href="javascript:void(0);" data-id = "'+id+'" class="remove_button d-inline-block valign-text-bottom me-2 float-end" title="Remove field"><i class="fa fa-times"></i></a>';
        }

        /** Product Breakdown Load After Window Load */
        let spiProductTemplate = $('script[data-template="spi_product_template"]').html();

        let spiProductPreload = '<div class="col-md-3 mb-2 colgroup prb0" data-id="0">\
                                        <div class="card border-1">\
                                            <div class="card-header p-1">'+addRowBtn+'</div>\
                                            <div class="card-body">'+SpiProductTemplate(0)+'</div>\
                                        </div>\
                                    </div>';

        $('#spi_product_section').empty().append(spiProductPreload);


        //Select2 Function Onload
        $(document).ready(function() {
            modelSelect2();
        });


        //Add New Row Button Action
        let x = 1;
        $('a.add_button').on('click', function(e) {
            e.preventDefault();
            let spiProductAppend = '<div class="col-md-3 mb-2 colgroup prb'+x+'" data-id="'+x+'">\
                                        <div class="card border-1">\
                                            <div class="card-header p-1">'+RemoveBtn(x)+'</div>\
                                            <div class="card-body">'+SpiProductTemplate(x)+'</div>\
                                        </div>\
                                    </div>';
            $('#spi_product_section').append(spiProductAppend);
            modelSelect2();
            x++; //Increment field counter
        })
        //Remove Row Button Action
        $('#spi_product_section').on('click', 'a.remove_button', function(e) {
            e.preventDefault();
            let deleteRowId = $(this).attr('data-id');
            //alert(deleteRowId);
            $('#spi_product_section .prb'+deleteRowId).remove(); //Remove field html
            modelSelect2();
        });


    </script>
    {{-- Edit SPI Product Modal Show --}}
    @if(isset($spiEditProduct))
        <script>
            setTimeout(function() {
                $("#spiProductModal").modal('show');
            }, 0);
        </script>
    @endif



    <script type="text/template" data-template="spi_product_bundle_template">
        <div class="w-25 d-inline-block me-4">
            <small>Qty</small> <br>
            <input type="number" min="1" name="bundle_size[][]" id="bundle_size" class="form-control" value="" required>
        </div>
        <div class="w-25 d-inline-block">
            <small>Unit Price</small> <br>
            <input step="any" type="number" min="0" name="bundle_price[][]" id="bundle_price" class="form-control bundle_price no-arrow" value="" required>
        </div>

        <div class="w-25 d-inline-block">
            <small>Sub Total</small> <br>
            <input step="any" type="number" min="0" xname="bundle_price[][]" id="bundle_subtotal_price" class="form-control bundle_subtotal_price no-arrow" value="" required>
        </div>

        <a href="javascript:void(0);" onclick="spiProductBundle('remove', 0)" class="bundle_remove_button d-inline-block ms-3 valign-text-bottom" title="Remove More">
            <i class="fa fa-times"></i>
        </a>
    </script>

    <script>
        function spiProductBundle(type, id){
            var maxField = Infinity; //Input fields increment limitation
            var addButton = $('.prb'+id+' .bundle_add_button'); //Add button selector
            var wrapper = $('.prb'+id+' #bundle_breakdown'); //Input field wrapper
            //var wrapperDataId = wrapper.attr('data-id');
            var fieldHTML = $('script[data-template="spi_product_bundle_template"]').html(); //New input field html
            var x = 1; //Initial field counter is 1
            // $(addButton).on('click', function(e) {
            //     e.preventDefault();
            //alert($(this).data('id'))
            //Check maximum number of input fields
            if(type == 'add'){
                if (x < maxField) {
                    let breakdownRowNumber = $('#spi_product_section .prb'+id+' .cutpiece_breakdown:last-child').attr('data-breakdown');
                    let breakRowNumberSum = parseInt(breakdownRowNumber) + parseInt(1);
                    let html = '<div class="cutpiece_breakdown" data-breakdown="'+breakRowNumberSum+'">';
                    html += fieldHTML;
                    html += '</div>';
                    $(wrapper).append(html); //Add field html
                    x++; //Increment field counter
                }
                // });
                let productId = $('.prb'+id+' select#product_id').val();
                $('#spi_product_section .prb'+id+' input#bundle_size').attr('name', 'bundle_size['+productId+'][]')
                $('#spi_product_section .prb'+id+' input#bundle_price').attr('name', 'bundle_price['+productId+'][]')
            }
            if(type == 'remove'){
                $('.colgroup').on('click', '.bundle_remove_button', function(e) {
                    e.preventDefault();
                    let pi = $(this).parent('div');
                    getRemovePrice = $(pi).attr('id', 're');

                    let amount = $('#re div input#bundle_price').val() ?? 0;
                    let totalAmount = $('.prb'+id+' input.total_price').val();
                    let sum = parseInt(totalAmount - amount);
                    $('.prb'+id+' input.total_price').val(sum)
                    //alert(amount);
                    $(this).parent('div').remove(); //Remove field html
                    //console.log($(this).parent('div').attr('id'))
                    x--; //Decrement field counter
                });
            }
        }
        //spiProductBundle();
    </script>


    <script>
        //Bundle Price Input Field Keyup Action
        $(document).on("keyup", "#bundle_price, #bundle_size", function() {
            let getDataId = $(this).parents('.colgroup').attr('data-id')
            let bundleBreakDownRow = $(this).parents('.cutpiece_breakdown').attr('data-breakdown');


            let bundlePrice = $('.prb'+getDataId+' .cutpiece_breakdown[data-breakdown="'+bundleBreakDownRow+'"] #bundle_price').val();
            let bundleSize = $('.prb'+getDataId+' .cutpiece_breakdown[data-breakdown="'+bundleBreakDownRow+'"] input#bundle_size').val();
            //Calculate Qty*Price
            $('.prb'+getDataId+' .cutpiece_breakdown[data-breakdown="'+bundleBreakDownRow+'"]  #bundle_subtotal_price').val(bundlePrice*bundleSize)

            //End
            var sum = 0;
            $(".prb"+getDataId+" #bundle_subtotal_price").each(function(){
                sum += +$(this).val();
            });
            $(".prb"+getDataId+" .total_price").val(sum);
        });
    </script>

    <script>
        //Single Product price Qty Keyup Action
        $(document).on('keyup', '#regular_qty #qty, #single_product_unit_price #single_product_unit_price', function(){
            let getDataId = $(this).parents('.colgroup').attr('data-id');
            let qty = parseFloat($(".prb"+getDataId+" #regular_qty #qty").val() ?? 0).toFixed(2);
            let price = parseFloat($(".prb"+getDataId+" #single_product_unit_price #single_product_unit_price").val() ?? 0).toFixed(2);

            let multiply = parseFloat(qty*price).toFixed(2);
            $(".prb"+getDataId+" .total_price").val(multiply);

        })
    </script>


    <script>
        /**
         * Product Select
         */
        $('#spi_product_section').on('click', 'button.selectedProductInfo', function(e){
            e.preventDefault()
            let rowId = $(this).data('row-id');
            let rowSelectProduct = $('select#product_id[data-id="'+rowId+'"]').find(':selected');
            let selectedProductName = rowSelectProduct.text();
            let selectedProductVal = rowSelectProduct.val();
            //alert(selectedProductVal)
            // console.log(selectedProductVal)
            if(selectedProductName){
                // alert(selectedProductVal);
                $('.selectedProductInfoOpenModal .modal-header').text(selectedProductName);
                $.ajax({
                    url : "{{route('spi_selected_product_details_info', request()->get('warehouse_code'))}}",
                    method : 'GET',
                    data : {
                        {{--'_token' : "{{csrf_token()}}",--}}
                        /*
                        'row_id' : rowId,
                        'product_id' : selectedProductVal,
                        'warehouse_id' : "{{request()->get('warehouse_id')}}",
                        'spi_type' : '{{$spi->ppi_spi_type ?? "Supply"}}',
                        'spi_project' : '{{$spi->project}}',
                        'spi_product_id' : '{{$spiEditProduct->id ?? null}}'
                        */
                        //'browse': dataBrowse,
                        //'spi_type': dataBrowse,
                        'row_id': rowId,
                        'spi_product_id' :  '{{$spiEditProduct->id ?? null}}',
                        'product_id': selectedProductVal,
                        'warehouse_id': "{{request()->get('warehouse_id')}}",
                        'spi_project': '{{$spi->project}}',
                        'original_project': '{{$spi->project ?? null}}'
                    },
                    success : function(response){
                        if(response) {
                            //console.log(response);
                            $('#selectedProductInfoModalBody').html(response);
                        }
                    }
                });
            }else{
                e.stopPropagation();
                alert('please select a product');
            }

        })


    </script>

    <?php
        // Show Modal  After Product Select
        $selectedProductInfo = [
            'modalSize' => 'xl modal-dialog-centered',
            'backdrop' => true,
            'backshadow' => true,
            'scrollable' => true,
            'saveBtn' => false,
//            'modalHeaderShow' => false
        ];
        echo $Component::bootstrapModal('selectedProductInfo', $selectedProductInfo);
    ?>


@endsection
