<?php

	/* 	oncolgoies class. 
		create object from json data stored in datastore.
		generate edit from for oncology
		permeate changes on edit form submission to datastore
	*/

	class Oncology {

		public $json_oncology_instance;
		public $datastore;

		public $sa_available_types = array('text' => 'text', 'html' => 'html', 'text_multiline' => 'text multiline');


		function __construct($json_oncology) {
			$this->json_oncology_instance = $json_oncology;
			# set a reference to datastore for persisting changes
			$this->datastore = new DataStore;
		}
		function s_part_html($s_name, $s_type, $s_editable, $s_order, $s_type_options){
			$html_part = '';

			$s_selected_type_options = str_replace('value="'.$s_type.'"', 'value="'.$s_type.'" selected ', $s_type_options);

			$html_part .= '<div class="well well-sm oncology_full_element" id="oncology_part_edit_'.$s_type.$s_name.'">';
			$html_part .= '<div class="row">';

			$html_part .= '<div class="col-xs-4"><input type="text" class="form-control element_name" placeholder="name" value="'.$s_name.'" name="name[]"></div>';
			$html_part .= '<div class="col-xs-4"><select class="form-control" placeholder="type" value="'.$s_type.'" name="type[]">'.$s_selected_type_options.'</select></div>';

			$s_checked = '';
			if($s_editable === "true"){
				$s_checked = ' checked';
			}
			$html_part .= '<div class="col-xs-2"><div class="checkbox"><label><input class="oncology_element_editable" type="checkbox" name="editable[]"'.$s_checked.' " value="true"> editable</label></div></div>';

			//$html_part .= '<div class="col-xs-1"><a class="disabled"><i class=""></i></a></div>';
			$html_part .= '<div class="col-xs-2"><a class="btn btn-danger" href="javascript:_remove_part_from_oncology(\'oncology_part_edit_'.$s_type.$s_name.'\');"><i class="glyphicon glyphicon-trash"></i></a></div>';

			$html_part .= '</div>';
			$html_part .= '</div>';

			return $html_part;
		}
		function html_edit_form(){
			$html_edit_form = '';

			$s_id = urldecode($this->json_oncology_instance->id);
			$s_title = urldecode($this->json_oncology_instance->title);

			$s_type_options = '';
			foreach ($this->sa_available_types as $key => $value) {
				$s_type_options .= '<option value="'.$key.'">'.$value.'</option>';
			}

			// top buttons
			$html_edit_form .= '<a class="btn btn-default btn-sm" href="javascript:_new_oncology_part();"><i class="glyphicon glyphicon-plus"></i><span class="small-hidden">&nbsp;add part</span></a>';

			$html_edit_form .= '<hr/>';

			$html_edit_form .= '<div class="alert alert-info">Add and remove Parts to a page type, choosing what kind of part you want. A page type can have many parts.</div>';

			// main form content

			$html_edit_form .= '<form id="oncology_edit_form" role="form" method="post" action="index.php"><div id="full_element_parts">';
			$html_edit_form .= '<h4>Name</h4>';

			// name
			$html_edit_form .= '<div class="form-group"><label for="page_type_name">Page type name</label><input type="text" class="form-control" id="page_type_name" placeholder="Page type name" name="page_type_name" value="'.$s_title.'"></div>';


			$html_edit_form .= '<h4>Parts</h4>';

			foreach ($this->json_oncology_instance->full_elements as $element) {
				$s_name = $element->name;
				$s_type = $element->type;
				$s_editable = $element->editable;
				$s_order = $element->position;

				$html_edit_form .= $this->s_part_html($s_name, $s_type, $s_editable, $s_order, $s_type_options);
			}

			$html_edit_form .= '<div class="alert alert-info">Click the "<i class="glyphicon glyphicon-plus"></i> add part" button above to add parts to this page type.</div>';

			$html_edit_form .= '</div>';

			//$html_edit_form .= '<input type="hidden" id="oncology_value" name="oncology_value">';
			$html_edit_form .= '<input type="hidden" name="oncology_id" value="'.$s_id.'">';
			$html_edit_form .= '<input type="hidden" name="section" value="oncologies">';

			// form save button
			$html_edit_form .= '<div class="form-group">';
			$html_edit_form .= '<button id="oncology_edit_submit" type="submit" class="form-control btn btn-success"><i class="glyphicon glyphicon-floppy-disk"></i> save</button>';
			$html_edit_form .= '</div>';

			$html_edit_form .= '</form>';

			$html_edit_form .= '<div id="new_oncology_part_clone" style="display:none;">'.$this->s_part_html('', '', '', '', $s_type_options).'</div>';

			return $html_edit_form;
		}

		function update_from_post(){
			$ufUF = new UtilityFunctions;

			$c_elements = count($_POST['name']);
			$oa_elements = array();
			//
			// general properties
			//
			$s_oncology_title = $ufUF->s_post_var('page_type_name', false);
			if($s_oncology_title){
				$this->json_oncology_instance->title = $s_oncology_title;
			}

			//
			// full elements
			//
			for($c_element = 0; $c_element < $c_elements; $c_element++){

				$s_name = $ufUF->s_post_array_var('name', $c_element, '');
				$s_type = $ufUF->s_post_array_var('type', $c_element, '');
				$s_editable = $ufUF->s_post_array_var('editable', $c_element, "false");
				$s_position = $ufUF->s_post_array_var('position', $c_element, 0);

				$oa_element = array(
					'name' => $s_name,
					'type' => $s_type,
					'editable' => $s_editable,
					'position' => $s_position
				);
				array_push($oa_elements, $oa_element);
			}
			$this->json_oncology_instance->full_elements = $oa_elements;
			
			$this->datastore->_set_oncology_data($this->json_oncology_instance);
			$this->datastore->b_save_datastore('oncologies');
		}
	}
?>