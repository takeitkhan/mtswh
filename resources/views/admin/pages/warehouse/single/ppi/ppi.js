/**
 * Show Project list
 * Based on PPI Type Select
 */
$('select#ppi_type').on('change', function(){
    $('#project_col').removeClass('d-none');
    let projectCol = $('#project_col');
    if($(this).val() == 'Supply'){
        let serviceProjectTemplate =$('script[data-template="supply_project_template"]').html();
        projectCol.empty().append(serviceProjectTemplate);
        select2Refresh();
    }else if($(this).val() == 'Service'){
        let supplyProjectTemplate = $('script[data-template="service_project_template"]').html();
        projectCol.empty().append(supplyProjectTemplate);
        select2Refresh();
    }
})

/**
 * ================
 *      Source
 * ================
 */


/** PPi Source Template when documents ready As Main Source */
// let sourceTemplate = $('script[data-template="source_template"]').html();
let sourceTemplate = source_template(0);
$("#ppi_source").empty().append('<div class="row ppiAppen ppiAppend0">'+sourceTemplate+'</div>');
// let nb = '<div class="row ppiAppen ppiAppend0">'+sourceTemplate.html()+'</div>';
// $(sourceTemplate).appendTo("#ppi_source");
/** Function of Remove A source Row */
function rem(id){
    let getMainSorceValue = $('.ppiAppend'+id+' select#main_source').find(':selected').val(); //get value selected row of Main Source

    $('.ppiAppen:nth-last-child(1) select#main_source option[value="'+getMainSorceValue+'"]').show();

    let getChildSourceValue = $('.ppiAppend'+id+' select#source_child').find(':selected').val(); //get value selected row of Source Child

    // IF Selected Row source child warehouse/Site
    if(getChildSourceValue === 'Warehouse' || getChildSourceValue === 'Site' || getChildSourceValue === 'Shop' ){
        $("#source_warehouse_site").empty();
    }

    $('.ppiAppend'+id).remove(); //Delete Selected Row
    $('.ppiAppen:nth-last-child(1) select').removeClass('readonly'); //Disabled selected  Option for last row
    $('.ppiAppen:nth-last-child(1) select#source_child').prop('selectedIndex', 'Select'); //Clear Selected

    //Remove From Breadcrumb
    $('#source_breadcrumb').empty();
    sourceBreadcrumb();

}


//LocalStorage catch Value Delete when window reload
window.localStorage.clear();

//Ppi Source Select option Chnage Functionality

$('#ppi_source .ppiAppend0').on('change', 'select#main_source', function(){
    let thisPpiAppendSelectedValue = $(this).val();
    localStorage.setItem('mainSourceData', thisPpiAppendSelectedValue);
})

let i= 0;

$("#ppi_source").on('change', 'select#source_child', function(){
    let id = ++i;
    let lst = localStorage.getItem('mainSourceData');

    //
    let sourceChild = $(this).val();
    if(sourceChild == 'Haschild'){

        let thisppiAppendMainSource = $('.ppiAppend'+(id-1)+' select#main_source');
        let thisPpiAppendSelectedValue = $(thisppiAppendMainSource).find(":selected").val();

        let appendHtml = '<div class="row ppiAppen ppiAppend'+id+'">'+source_template(id)+'\
                            <div class="col-md-1"><a class="text-danger" href="javascript:void()" onclick="rem('+id+')" id="rem'+id+'"><i class="far fa-trash-alt"></i></a></div>\
                            </div>';
        console.log(sourceTemplate)
        $("#ppi_source").append(appendHtml);


        $('.ppiAppen:nth-last-child(2) select').addClass('readonly');

        if(id === 1){
            localStorage.setItem('mainSourceData', thisPpiAppendSelectedValue);
            $('.ppiAppen:nth-last-child(1) select#main_source option[data-name="'+thisPpiAppendSelectedValue+'"]').hide();
        }else {
            localStorage.setItem('mainSourceData', lst+','+thisPpiAppendSelectedValue);
            let lstSplit = lst.split(',');
            $('.ppiAppen:nth-last-child(1) select#main_source option[data-name="'+thisPpiAppendSelectedValue+'"]').hide();
            for(c = 0; c < lstSplit.length; c++){
                $('.ppiAppen:nth-last-child(1) select#main_source option[data-name="'+lstSplit[c]+'"]').hide();
            }
        }
        //Breadcrumb Remove
        $('#source_breadcrumb .innerwrap.current').remove();

    }else if(sourceChild == 'Warehouse' || sourceChild == 'Site' || sourceChild == 'Shop'){
        let warehouseSiteInput = `<div class="col-md-10 form-group d-block">
                                <input class="form-control warehouse_name" type="text" value="" name="main_source[${id}][source]" placeholder = "${sourceChild} name" />
                                <input type="hidden" name="main_source[${id}][type]" value="${sourceChild}" />
                                <input type="hidden" name="main_source[${id}][source_level]" value="${sourceChild}" />
                                </div>`;
        $("#source_warehouse_site").empty().append(warehouseSiteInput);
         //Breadcrumb Remove
        $('#source_breadcrumb .innerwrap.current').remove();

    }

})


/**
 * =========
 * Source Breadcrumb
 * ==========
 */
 $(function(){
    $('#ppi_source').on('change','select', function(){
        sourceBreadcrumb();
    });
});

function sourceBreadcrumb(){
    var titles = [];
        $('select[name^=main_source]').each(function(){
            if($(this).val() != null){
                titles.push($(this).val());
            }
        });
        localStorage.setItem('bread', titles);
        let breas = localStorage.getItem('bread').split(',');
        // console.log(breas)
        $.each(breas, function(i, va) {
            va=va.split('|')[0]
            let abr = '<div class="innerwrap '+(i-1)+'b"><span class="innerItem">'+va+'</span></div>';
            $("#source_breadcrumb div."+(i-1)+"b").remove();
            $("#source_breadcrumb").append(abr);
        })
}


//Add Warehouse Name to Breadcrumb when keyup on  warehouse name input Box
$('#source_warehouse_site').on('keyup', 'input.warehouse_name', function(){
    let warehouseName = $('input.warehouse_name').val();
    let abr = '<div class="innerwrap current"><span class="innerItem current">'+warehouseName+'</span></div>';
    $("#source_breadcrumb").append(abr);
    $('#source_breadcrumb .innerwrap.current').remove();
    $("#source_breadcrumb").append(abr);
})



/**
 * ==================
 * ==PPI Product Table=====
 * ==================
 */
//Checkbox For Create set of product
function checkForSetProduct(routeUrl){
    $('#tbl_ppi_product table').on('click', 'input:checkbox#for_create_set', function(){
        let countCheck = $("#tbl_ppi_product input#for_create_set:checked").length;
        if(countCheck > 1){
            $('button#create_set_btn').removeProp('disabled');
            $('form#tbl_ppi_product_form_action').attr("action", routeUrl);
            $("a.delete input[name='_method']").attr('disabled', true);
            //alert(countCheck);
        }
        if(countCheck == 1){
            $('button#create_set_btn').attr('disabled', true);
            $('form#tbl_ppi_product_form_action').attr("action", 'javascript:void(0)');
            $("a.delete input[name='_method']").attr('disabled', false);
        }
    })
}

// Create Set Button Action
let setId;
$('button#create_set_btn').click(function(){
    //let getSetModalId = $(this).data('bs-target')
    //setId += getSetModalId;
    //$(getSetModalId+' input[name="set_name"]').attr('required', true);
    $('input#inputsetP').attr('required', true);
})
$('button#modalCloseBtnsetP').click(function(){
    $('input#inputsetP').attr('required', false);
})
//Product Delete Action
$('#tbl_ppi_product table').on('click', 'a.delete button', function(){
    $('input:checkbox#for_create_set').attr("checked", false);
    $("#tbl_ppi_product table a.delete input[name='_method']").attr('disabled', false);
})


/**========================
 * Ppi Dispute=============
 * ========================
 */
/** Correction Action */
$('#tbl_ppi_product').on('click', 'button#correction_button', function(){
    let url = $('#tbl_ppi_product button#correction_button').attr('data-url');
    $('#tbl_ppi_product form#tbl_ppi_product_form_action').prop('action', url);
})
/** Dispute Action */
//If Product Checkbox checked for Dispute
$('#tbl_ppi_product').on('click', 'button#dispute_button', function(){
    let url = $('#tbl_ppi_product button#dispute_button').attr('data-url');
    $('#tbl_ppi_product form#tbl_ppi_product_form_action').prop('action', url);
})

$('div.modal#dsiputeButton').on('click', 'button.modal-cancel', function(){
    $('#tbl_ppi_product form#tbl_ppi_product_form_action').prop('action', '');
})


function DisputeSubmitBtnDisableEnable(){
    let countCheck = $("#tbl_ppi_product input#disputeCheckBox:checked").length;
        if(countCheck >= 1){
            //console.log(countCheck);
            $('button#dispute_button').removeProp('disabled');
        }
        if(countCheck == 0){
            $('button#dispute_button').attr('disabled', true);
        }
}

$('#tbl_ppi_product table').on('click', 'input:checkbox#disputeCheckBox', function(){
    DisputeSubmitBtnDisableEnable();
});

$('#tbl_ppi_product input#disputeCheckBox').on('click', function(){

    //let Url = $(this).attr('data-url');
    let productId = $(this).attr('data-product-id');

    $('fieldset[disputeModal = '+productId+'] div.modal#ppiActionDisputeModal'+productId+' button.modal-cancel').attr('data-product-id', productId);
    $('fieldset[disputeModal = '+productId+'] div.modal#ppiActionDisputeModal'+productId+' button.modal-ok').attr('data-product-id', productId);

})


//Modal Submit Button Action
$('#tbl_ppi_product').on('click', 'fieldset div.modal button.modal-ok', function(){
    let productId = $(this).attr('data-product-id');
    let textareaId = $('div.modal#ppiActionDisputeModal'+productId+' textarea')
    //Textarea value length check
    if( !$.trim($(textareaId).val() ).length < 1 ){
        $('div.modal#ppiActionDisputeModal'+productId).modal('hide');
        $('#tbl_ppi_product table td input:checkbox[data-bs-target ="#ppiActionDisputeModal'+productId+'"]').prop('checked', true);

        //If td has alert danger class All are remove
        $('#tbl_ppi_product table tr.pr_row_'+productId+' td').removeClass('text-danger fw-bold');
        //Only Selected column added alert-danger class
        $('fieldset #ppiActionDisputeModal'+productId+' input:checked').each(function(){
            if($(this).is(':checked')){
                let ckval = $(this).val();
                $('#tbl_ppi_product table tr.pr_row_'+productId+' td.'+ckval).addClass('text-danger fw-bold')
                //console.log(ckval);
                //console.log('#tbl_ppi_product tr.pr_row_'+productId+' td.'+ckval);
            }
        });//End
    } else {
        alert('You have to type details of issue');
    }
})

//Modal Cancel Button Action
$('#tbl_ppi_product').on('click', 'button.modal-cancel', function(){
    let productId = $(this).attr('data-product-id');
    $('#tbl_ppi_product table td input:checkbox[data-bs-target ="#ppiActionDisputeModal'+productId+'"]').prop('checked', false);
    $('fieldset #ppiActionDisputeModal'+productId+' input:checkbox').prop('checked', false);
    $('fieldset #ppiActionDisputeModal'+productId+' input:radio').prop('checked', false);
    $('fieldset #ppiActionDisputeModal'+productId+' textarea').val('');
    //If td has alert danger class All are remove
    $('#tbl_ppi_product table tr.pr_row_'+productId+' td').removeClass('text-danger fw-bold');
    DisputeSubmitBtnDisableEnable();
})

/** End Dispute **/
