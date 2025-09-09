<div id="ppi_product_section" class="row">

</div>

<!-- PPI Product Insert / Edit Form Template -->
@php
    if(!empty($ppi)){
        if($ppi->tran_type == 'Without Money'){
            $productPrice = 0;
            $priceDisabled = 'readonly';
        }else {
            $productPrice = 0;
        }
    }
@endphp
<script>
    function PpiProductTemplate(id = ''){
        let html = `
        <!-- Products -->
        <div class="form-group">
            <input type="hidden" class="breakdown_row_id" name="row_id[]" value="${id}">
            @php $getProducts = $Model('Product')::thisWarehouseProducts(['product_type' => $ppi->ppi_spi_type]);
            @endphp
            <label for="product">Select Product</label>
            <select name="product_id[]" data-id="${id}" id="product_id" class="form-select select-box-modal" required>
                <option value=""></option>
                @foreach ($getProducts as $product)
                    <option value="{{$product->id}}" data-unit="{{$Model('AttributeValue')::getValueById($product->unit_id)}}"
                        {{ isset($ppiEditProduct) && $ppiEditProduct->product_id == $product->id ? 'selected' : ''  }}
                        >{{$product->name}} </option>
                        {{--- {{$product->product_type}} - {{$Model('AttributeValue')::getValueById($product->unit_id)}}--}}
                @endforeach
            </select>
        </div>

        <!-- Product State -->
        <div class="form-group">
            <label for="product_state">Product State</label>
            @php $getProductState = $Query::getEnumValues('ppi_products', 'product_state') @endphp

            <select name="product_state[]" id="product_state" class="form-select" data-id="${id}" required>
                <option value="" disabled="" selected="">Select</option>
                @foreach($getProductState as $value)
                    <option value="{{$value}}"
                        {{ isset($ppiEditProduct) && $ppiEditProduct->product_state == $value? 'selected' : ''  }}
                        >{{$value}}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Health Status -->
        <div class="form-group">
            <label for="health_status">Health Status</label>
            @php $getHealthStatus = $Query::getEnumValues('ppi_products', 'health_status') @endphp
            <select name="health_status[]" id="health_status" class="form-select" required>
                <option value="" disabled="" selected="">Select</option>
                @foreach($getHealthStatus as $value)
                    <option value="{{$value}}"
                        {{ isset($ppiEditProduct) && $ppiEditProduct->health_status == $value? 'selected' : ''  }}
                        >{{$value}}
                    </option>
                @endforeach
            </select>
        </div>

         <!-- Product QTY || Regular Qty -->
         <div class="form-group" id="regular_qty" data-id="${id}" style="display: {{isset($ppiEditProductBundle) && count($ppiEditProductBundle) > 0 ? 'none' : 'flex'}}">
            <label for="qty">QTY  <span id="show_unit" class="text-danger"></span> </label>
            <input type="number"  min="{{isset($ppiEditProductBundle) && count($ppiEditProductBundle) > 0 ? '0' : '1'}}" name="qty[]" id="qty" class="form-control" value="{{ isset($ppiEditProduct) ? $ppiEditProduct->qty : '' }}" {{isset($ppiEditProductBundle) && count($ppiEditProductBundle) > 0 ? '' : 'required'}}>
        </div>

        <!-- Bundle Qty -->
        <div class="form-group" id="bundle_qty" data-id="${id}" style="display: {{isset($ppiEditProductBundle) && count($ppiEditProductBundle) > 0 ? 'flex' : 'none'}}">
            <label for="qty">Bundle Size</label>
            <span id="bundle_breakdown" class="w-100 text-center">
                @if(isset($ppiEditProductBundle) && count($ppiEditProductBundle) > 0)
                    @foreach($ppiEditProductBundle as $key => $bundle)
                        <div class="cutpiece_breakdown" data-breakdown="0{{$key}}">
                            <div class="w-25 d-inline-block me-4">
                                <small>Qty</small> <br>
                                <input type="number" min="1" name="bundle_size[${id}][]" xname="bundle_size[{{$ppiEditProduct->product_id}}][]" id="bundle_size" class="form-control" value="{{$bundle->bundle_size}}">
                            </div>
                            <div class="w-25 d-inline-block">
                                <small>Unit Price</small> <br>
                                <input {{$priceDisabled ?? null}} type="number" step="any" min="0" name="bundle_price[${id}][]" xname="bundle_price[{{$ppiEditProduct->product_id}}][]" id="bundle_price" class="form-control bundle_price no-arrow" value="{{isset($priceDisabled) ? $productPrice : $bundle->bundle_price}}">
                            </div>
                             <div class="w-25 d-inline-block">
                                <small>Sub Total</small> <br>
                                <input readonly type="number" min="0" xname="bundle_price[][]" id="bundle_subtotal_price" class="form-control bundle_subtotal_price no-arrow" value="{{$bundle->bundle_size*$bundle->bundle_price}}"" required>
                            </div>
                            @if($key == 0)
                                <a href="javascript:void(0);" onclick="ppiProductBundle('add', ${id})" data-id="${id}" class="bundle_add_button d-inline-block ms-3 valign-text-bottom" title="Remove field">
                                    <i class="fa fa-plus"></i>
                                </a>
                            @else
                                <a href="javascript:void(0);" onclick="ppiProductBundle('remove', ${id})" class="bundle_remove_button d-inline-block ms-3 valign-text-bottom" title="Remove More">
                                    <i class="fa fa-times"></i>
                                </a>
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
                            <input step="any"  {{$priceDisabled ?? null}} type="number" data-id ="${id}"  name="bundle_price[]" id="bundle_price" class="form-control bundle_price no-arrow" value="0">
                        </div>
                        <div class="w-25 d-inline-block">
                            <small>Sub Total</small> <br>
                            <input readonly type="number" data-id ="${id}" xname="bundle_subtotal_price[]" id="bundle_subtotal_price" class="form-control bundle_subtotal_price no-arrow" value="0">
                        </div>

                        <a href="javascript:void(0);" onclick="ppiProductBundle('add', ${id})" data-id="${id}" class="bundle_add_button d-inline-block ms-3 valign-text-bottom" title="Remove field">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                @endif
            </span>
        </div>
        <!-- Single Product Unit price -->
        @php $ppiEditProductPrice = isset($ppiEditProduct) ? $ppiEditProduct->unit_price : 0  @endphp
        @if(isset($ppiEditProductBundle) && count($ppiEditProductBundle) > 0)
            @php $ppiEditProductPrice = $ppiEditProduct->price; @endphp
        @else
        <div class="form-group" id="single_product_unit_price" data-id="${id}">
            <label for="single_product_unit_price">Unit Price</label>
            <input step="any" {{$priceDisabled ?? null}} type="number" name="unit_price[]" id="single_product_unit_price" class="form-control unit_price"
                value="{{isset($priceDisabled) ? $productPrice : $ppiEditProductPrice }}">
        </div>
        @endif
        <!-- Product price -->
        <div class="form-group" id="total_price" data-id="${id}">
            <label for="price">Total Price</label>
            <input step="any" type="number" name="price[]" id="price" class="form-control total_price"
                readonly
                //{{ isset($ppiEditProduct) && $ppiEditProduct->product_state == 'Cut-Piece' ? 'readonly' : '' }}
                value="{{isset($priceDisabled) ? $productPrice : $ppiEditProductPrice }}"
                {{$priceDisabled ?? 'required'}}>
        </div>

        <!-- Note -->
        <div class="form-group">
            <label for="note">Note</label>
            <textarea class="form-control" name="note[]">{{ isset($ppiEditProduct) ? $ppiEditProduct->note : '' }}</textarea>
        </div>

        `;
        return html;
    }
</script>

<script type="text/template" xdata-template="ppi_product_template">



</script>


@section('cusjs')
@parent

<script>
    /**
 * ===== JS Elements =================
 * ===== PPI Product =================
 * ===================================
 */
/**
 *  Product State Select
 * Health Status Show Hide
*/
$('#ppi_product_section').on('change', 'select#product_id', function(){
    let colGroupId = $(this).data('id');
    $('.prb'+colGroupId+' select#product_state').val('');
    let getUnit = $('.prb'+colGroupId+' select#product_id').find(':selected').attr('data-unit');
    $('.prb'+colGroupId+' span#show_unit').text('In ' +getUnit)
    $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input').val('')
    $('#ppi_product_section #total_price[data-id='+colGroupId+'] input').val(0)
    $('#ppi_product_section #regular_qty[data-id='+colGroupId+'] input#qty').val('')
    $('#ppi_product_section #single_product_unit_price[data-id='+colGroupId+'] input#single_product_unit_price').val(0)
});
$('#ppi_product_section').on('change', 'select#product_state', function(){
    let colGroupId = $(this).data('id');
    // let productId = $('.prb'+colGroupId+' select#product_id').val();
    let productId = $('.prb'+colGroupId+' input.breakdown_row_id').val();
    //alert(colGroupId)
    let hsv =$('#ppi_product_section .colgroup[data-id='+colGroupId+'] select#health_status option[value="Scrapped"]');
    let psv = $('#ppi_product_section .colgroup[data-id='+colGroupId+'] select#product_state').find(':selected').val();
    $('#ppi_product_section .colgroup[data-id='+colGroupId+'] #total_price[data-id='+colGroupId+'] input.total_price').val('')
    if( psv === 'New'){
        hsv.hide();
    }else if( psv === 'Used'){
        hsv.show();
    }

    if( psv === 'Cut-Piece'){
        //alert(productId)
        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+']').css('display', 'flex')
        $('#ppi_product_section #regular_qty[data-id='+colGroupId+']').css('display', 'none')
        $('#ppi_product_section #single_product_unit_price[data-id='+colGroupId+']').css('display', 'none')
        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').attr('required', 'required')
        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').attr('name', 'bundle_size['+productId+'][]')

        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').attr('required', 'required')
        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').attr('name', 'bundle_price['+productId+'][]').val(0)

        $('#ppi_product_section #regular_qty[data-id='+colGroupId+'] input#qty').removeAttr('required').val('')
        $('#ppi_product_section #single_product_unit_price[data-id='+colGroupId+'] input#single_product_unit_price').removeAttr('required').val(0)

        $('#ppi_product_section #regular_qty[data-id='+colGroupId+'] input#qty').val('')
        $('#ppi_product_section #single_product_unit_price[data-id='+colGroupId+'] input#single_product_unit_price').val(0)

        //$('#ppi_product_section #total_price[data-id='+colGroupId+'] input.total_price').attr('readonly', true)
        //$('#ppi_product_section #total_price[data-id='+colGroupId+'] label').html('Total Price')
    }else{
        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+']').css('display', 'none')
        $('#ppi_product_section #regular_qty[data-id='+colGroupId+']').css('display', 'flex')
        $('#ppi_product_section #single_product_unit_price[data-id='+colGroupId+']').css('display', 'flex')
        $('#ppi_product_section #regular_qty[data-id='+colGroupId+'] input#qty').attr('required', 'required')
        $('#ppi_product_section #single_product_unit_price[data-id='+colGroupId+'] input#single_product_unit_price').attr('required', 'required')

        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').removeAttr('required')
        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').val('')
        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_size').attr('name', 'bundle_size[][]')

        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').removeAttr('required')
        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').val(0)
        $('#ppi_product_section #bundle_qty[data-id='+colGroupId+'] input#bundle_price').attr('name', 'bundle_price[][]')


        //$('#ppi_product_section #total_price[data-id='+colGroupId+'] input.total_price').attr('readonly', true).val('')
        //$('#ppi_product_section #total_price[data-id='+colGroupId+'] label').html('Total Price')

    }
})
</script>


<script>
   /**
     * Select 2 for modal
     */
     function modelSelect2(){
        $('.select-box-modal').select2({
            dropdownParent: $('#exampleModalLabel')
        });
    }

    //Breakdown Add Button
    let addRowBtn = '@if(isset($ppiEditProduct))\
                        Edit PPI Product\
                    @else\
                    <a href="javascript:void(0);" class="add_button d-block valign-text-bottom me-2" title="Add field"><i class="fa fa-plus"></i></a>\
                    @endif\
                    ';
    $('.modal-footer #add_btn').html('<a href="javascript:void(0);" class="add_button btn btn-sm btn-success valign-text-bottom me-2" title="Add field">Add More Product</a>');


    // Breakdown Remove Button
    function RemoveBtn(id){
        return  '<a href="javascript:void(0);" data-id = "'+id+'" class="remove_button d-inline-block valign-text-bottom float-end me-2" title="Remove field"><i class="fa fa-times"></i></a>';
    }

    /** Product Breakdown Load After Window Load */
    let ppiProductTemplate = $('script[data-template="ppi_product_template"]').html();

    let ppiProductPreload = '<div class="col-md-3 mb-2 colgroup prb0" data-id="0">\
                                <div class="card">\
                                    <div class="card-header p-1">'+addRowBtn+'</div>\
                                    <div class="card-body">'+PpiProductTemplate(0)+'</div>\
                                </div>\
                            </div>';

    $('#ppi_product_section').empty().append(ppiProductPreload);


    //Select2 Function Onload
    $(document).ready(function() {
        modelSelect2();
    });


    //Add New Row Button Action
    let x = 1;
    $('a.add_button').click(function(e) {
        e.preventDefault();
        let ppiProductAppend = '<div class="col-md-3 mb-2 colgroup prb'+x+'" data-id="'+x+'">\
                                <div class="card">\
                                    <div class="card-header p-1">'+RemoveBtn(x)+'</div>\
                                    <div class="card-body">'+PpiProductTemplate(x)+'</div>\
                                </div>\
                            </div>';
        $('#ppi_product_section').append(ppiProductAppend);
        modelSelect2();
        x++; //Increament field counter
    })
    //Remove Row Button Action
    $('#ppi_product_section').on('click', 'a.remove_button', function(e) {
        e.preventDefault();
        let deleteRowId = $(this).attr('data-id');
        //alert(deleteRowId);
        $('#ppi_product_section .prb'+deleteRowId).remove(); //Remove field html
    });


</script>
{{-- Edit PPI Product Modal Show --}}
@if(isset($ppiEditProduct))
    <script>
        setTimeout(function() {
            jQuery("#exampleModal").modal('show');
        }, 0);
    </script>
@endif



<script type="text/template" data-template="ppi_product_bundle_template">
    <div class="w-25 d-inline-block me-4">
        <small>Qty</small> <br>
        <input type="number" min="1" name="bundle_size[][]" id="bundle_size" class="form-control" value="" required>
    </div>
    <div class="w-25 d-inline-block">
        <small>Unit Price</small> <br>
        <input step="any" {{$priceDisabled ?? null}} type="number" min="0" name="bundle_price[][]" id="bundle_price" class="form-control bundle_price no-arrow" value="{{$productPrice}}" required>
    </div>

    <div class="w-25 d-inline-block">
        <small>Sub Total</small> <br>
        <input  readonly type="number" min="0" xname="bundle_price[][]" id="bundle_subtotal_price" class="form-control bundle_subtotal_price no-arrow" value="" required>
    </div>

    <a href="javascript:void(0);" onclick="ppiProductBundle('remove', 0)" class="bundle_remove_button d-inline-block ms-3 valign-text-bottom" title="Remove More">
        <i class="fa fa-times"></i>
    </a>
</script>

<script>
      function ppiProductBundle(type, id){
        var maxField = Infinity; //Input fields increment limitation
        var addButton = $('.prb'+id+' .bundle_add_button'); //Add button selector
        var wrapper = $('.prb'+id+' #bundle_breakdown'); //Input field wrapper
        //var wrapperDataId = wrapper.attr('data-id');
        var fieldHTML = $('script[data-template="ppi_product_bundle_template"]').html(); //New input field html
        var x = 1; //Initial field counter is 1
        // $(addButton).on('click', function(e) {
        //     e.preventDefault();
            //alert($(this).data('id'))
            //Check maximum number of input fields
        if(type == 'add'){
            if (x < maxField) {
                let breakdownRowNumber = $('#ppi_product_section .prb'+id+' .cutpiece_breakdown:last-child').attr('data-breakdown');
                let breakRowNumberSum = parseInt(breakdownRowNumber) + parseInt(1);
                let html = '<div class="cutpiece_breakdown" data-breakdown="'+breakRowNumberSum+'">';
                    html += fieldHTML;
                    html += '</div>';
                $(wrapper).append(html); //Add field html
                x++; //Increment field counter
            }
        // });
        //     let productId = $('.prb'+id+' select#product_id').val();
            let productId = $('.prb'+id+' input.breakdown_row_id').val();
            $('#ppi_product_section .prb'+id+' input#bundle_size').attr('name', 'bundle_size['+productId+'][]')
            $('#ppi_product_section .prb'+id+' input#bundle_price').attr('name', 'bundle_price['+productId+'][]')
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
      //ppiProductBundle();
</script>


<script>
    //Bundle Price Input Field Keyup Action
    $(document).on("keyup", "#bundle_price, #bundle_size", function() {
            let getDataId = $(this).parents('.colgroup').attr('data-id')
            let bundleBreakDownRow = $(this).parents('.cutpiece_breakdown').attr('data-breakdown');


            let bundlePrice = $('.prb'+getDataId+' .cutpiece_breakdown[data-breakdown="'+bundleBreakDownRow+'"] #bundle_price').val();
            let bundleSize = $('.prb'+getDataId+' .cutpiece_breakdown[data-breakdown="'+bundleBreakDownRow+'"] input#bundle_size').val();
            //Calculate Qty*Price
            $('.prb'+getDataId+' .cutpiece_breakdown[data-breakdown="'+bundleBreakDownRow+'"]  #bundle_subtotal_price').val(parseFloat(bundlePrice*bundleSize).toFixed(2))

            //End
            var sum = 0;
            $(".prb"+getDataId+" #bundle_subtotal_price").each(function(){
                sum += +$(this).val();
            });
            $(".prb"+getDataId+" .total_price").val(parseFloat(sum).toFixed(2));
        });
</script>

<script>
    //Single Product price Qty Keyup Action
    $(document).on('keyup', '#regular_qty #qty, #single_product_unit_price #single_product_unit_price', function(){
        let getDataId = $(this).parents('.colgroup').attr('data-id');
        let qty = parseFloat($(".prb"+getDataId+" #regular_qty #qty").val() ?? 0);
        let price = parseFloat($(".prb"+getDataId+" #single_product_unit_price #single_product_unit_price").val() ?? 0);

        let multiply = parseFloat(qty*price).toFixed(2);
        $(".prb"+getDataId+" .total_price").val(multiply);

    })
</script>


@endsection
