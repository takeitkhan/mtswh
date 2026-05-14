$(document).ready(function() {
    var maxField = Infinity; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var fieldHTML = $('script[data-template="tem"]').html(); //New input field html 
    var x = parseInt($('.field_wrapper > div').length) || 0; //Get initial field counter from existing fields
    
    //Once add button is clicked
    $(addButton).click(function() {
        //Check maximum number of input fields
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
    });
    
    //Before form submission, validate and clean incomplete entries
    $('form').on('submit', function(e) {
        var hasError = false;
        var $form = $(this);
        var assignments = {}; // To track user-role combinations
        var processedIndices = {};
        
        // Collect all unique indices from user_id selects
        $('select[name^="assign_user"][name*="[user_id]"]').each(function() {
            var fieldName = $(this).attr('name');
            var match = fieldName.match(/\[(\d+)\]/);
            if (match) {
                var index = match[1];
                processedIndices[index] = true;
            }
        });
        
        // Check each pair for completeness and duplicates
        $.each(processedIndices, function(index, val) {
            var $userSelect = $('select[name="assign_user[' + index + '][user_id]"]');
            var $roleSelect = $('select[name="assign_user[' + index + '][role_id]"]');
            
            if ($userSelect.length && $roleSelect.length) {
                var userVal = $userSelect.val();
                var roleVal = $roleSelect.val();
                
                // If one is filled but not the other, it's incomplete
                if ((userVal && !roleVal) || (!userVal && roleVal)) {
                    alert('Please select both User and Role for each assignment, or remove the incomplete entry.');
                    hasError = true;
                    return false; // break
                }
                
                // If both are empty, remove the entire row
                if (!userVal && !roleVal) {
                    $userSelect.closest('div').remove();
                    return true; // continue
                }
                
                // Check for duplicate user-role combination
                var key = userVal + '_' + roleVal;
                if (assignments[key]) {
                    alert('Duplicate assignment detected! User "' + $userSelect.find('option:selected').text() + '" already has "' + $roleSelect.find('option:selected').text() + '" role in this warehouse.');
                    hasError = true;
                    return false; // break
                }
                assignments[key] = true;
            }
        });
        
        if (hasError) {
            e.preventDefault();
            return false;
        }
    });
});