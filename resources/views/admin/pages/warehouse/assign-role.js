$(document).ready(function() {
    var maxField = Infinity; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var fieldHTML = $('script[data-template="tem"]').html(); //New input field html 
    var x = 1; //Initial field counter is 1
    //Once add button is clicked
    $(addButton).click(function() {
        //Check maximum number of input fields
        //alert(x);
        if (x < maxField) {
            let html = '<div class="f' + x + '">';
            html += fieldHTML;
            html += '</div>';
            $(wrapper).append(html); //Add field html
            $('.f' + x + ' select.assign_user_add_more').attr('name', 'assign_user[' + x + '][user_id]');
            $('.f' + x + ' select.assign_role_add_more').attr('name', 'assign_user[' + x + '][role_id]');
            select2Refresh();
            x++; //Increment field counter
        }
    });
    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e) {
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });
});