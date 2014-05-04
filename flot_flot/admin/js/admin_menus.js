
var s_current_menu = "root";
var oa_menus = [];
var s_full_serialisation = "";

$(function() {
	$("#available_pages").sortable();
	$("#menu_order_area ul").sortable({
		update: function() {
			serialise_menu_order(); 
		}
	});
	$("#available_pages li").draggable({
		helper: "clone",
		start: function(e, ui){
			$(ui.helper).addClass("menu_item_dragging");
		}
	});
	$("#menu_order_area ul").draggable();
	$("#available_pages").disableSelection();

	$("#menu_order_area").droppable({
		accept: "#available_pages li",
		hoverClass: "item_hovering",
      	drop: function( event, ui ) {
	      	// add to menu area
			$("#menu_order_area ul").append($(ui.draggable).clone());

	      	// serialise
			serialise_menu_order();    	
		}	
    });
});
function serialise_menu_order(){
	var sa_menu_ids = [];
	$("#menu_order_area ul li").each(function() {
		sa_menu_ids.push($(this).attr("menu_id"));
	});

	serialise_entire_menu();

	oa_menus[s_current_menu] = sa_menu_ids.toString();
}
function serialise_entire_menu(){
	var sa_individual_menus = [];

	for (var key in oa_menus){
		sa_individual_menus.push(key+":"+oa_menus[key].toString());
	}

	s_full_serialisation = sa_individual_menus.join(";");
	$("#menu_order_serialised").val(s_full_serialisation);
}