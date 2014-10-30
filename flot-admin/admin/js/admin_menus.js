
var s_current_menu = "root";
var oa_menus = [];
var sa_item_name_look_up = [];
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
			$("#menu_order_area ul").append(html_menu_item($(ui.draggable).attr("menu_id"),$(ui.draggable).find("span[alt]").html()));


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


	oa_menus[s_current_menu] = sa_menu_ids.toString();
	serialise_entire_menu();
}
function serialise_entire_menu(){
	var sa_individual_menus = [];

	for (var key in oa_menus){
		sa_individual_menus.push(key+":"+oa_menus[key].toString());
	}

	s_full_serialisation = sa_individual_menus.join(";");
	$("#menu_order_serialised").val(s_full_serialisation);
}
function delete_menu_item(s_id){
	// remove from ui and re-serialse
	$("#menu_order_area ul li[menu_id="+s_id+"]").remove();
	serialise_menu_order();
}
function sub_menu(s_id){
	// set current menu to id
	s_current_menu = s_id;
	if(s_id !== "root")
		$("#menu_order_output").html('<a href="javascript:sub_menu(\'root\')">menu root</a> > <b>' + sa_item_name_look_up[s_id] + '</b>:');
	else
		$("#menu_order_output").html('');
	// recreate ui for current menu
	recreate_menu_ui_for_current();
}
function recreate_menu_ui_for_current(){
	var html_current_menu = "";

	if(oa_menus[s_current_menu] !== undefined){
		console.log(sa_item_name_look_up);
		oa_menus[s_current_menu].forEach(function(s_menu_item){
			// make a ui item
			//html_current_menu += html_menu_item(s_menu_item, sa_item_name_look_up['"'+s_menu_item+'"']);
			if(s_menu_item !== ""){
				html_current_menu += html_menu_item(s_menu_item, sa_item_name_look_up[s_menu_item]);
			}
		});
	}else{
		oa_menus[s_current_menu] = [];
	}
	$("#menu_order_area ul").html(html_current_menu);
}
function html_menu_item(s_id, s_name){
	return '<li class="menu_item clearer" menu_id="'+s_id+'"><i class="glyphicon glyphicon-resize-vertical resize-move-drag-icon"></i> '+s_name+'<a class="btn btn-sm btn-danger pull-right" href="javascript:delete_menu_item(\''+s_id+'\')"><i class="glyphicon glyphicon-remove"></i> remove</a><a class="btn btn-sm btn-default pull-right" href="javascript:sub_menu(\''+s_id+'\')"><i class="glyphicon glyphicon-th-list"></i> submenu</a></li>';
}