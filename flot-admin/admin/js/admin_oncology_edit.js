// oncology edit document ready
$(document).ready(function() {
    $('#oncology_edit_submit').click(function(){
        $("#oncology_edit_form .oncology_element_editable").each(function(){
            if(!$(this).is(':checked')){
                $(this).val("false");
                $(this).prop('checked', true);
            }
        });

        // submit form
        $("#oncology_edit_form").submit();
    });

    $('#new_oncology_part').click(function(){
        // serialise form
        //_serialise_form();
        // submit form
        $("#oncology_edit_form").submit();
    });
});  




function _new_oncology_part(){
    $("form #full_element_parts").append($('#new_oncology_part_clone').html());
}
function _remove_part_from_oncology(s_id){
    $("div[id='"+s_id+"']").remove();
}