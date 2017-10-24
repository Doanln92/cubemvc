<?php

function get_select_cat_parent($id=null){
	$Cat = get_model("Category");
	$catArgs = array('no'=>'không');

	$args = array();
	if($id && $c = $Cat->andWhere('id',$id)->first()){
		if($c->hasChild()){
			return $catArgs;
		}else{
			$args['id!='] = $c->id;
		}
	}
	
	$Cat->andWhere('parent_id','');

	
	if($cats = $Cat->get('id,name',$args)){

	    $catopt = arrToSelectOpts(convert_obj_to_arr($cats),'id','name');
	    $catArgs = arr_parse($catArgs,$catopt,true);
	}

	return $catArgs;
}


function get_select_post_cat_id(){
	$Cat = get_model("Category");
	$catopt = array('no' => 'Không');
	if($cats = $Cat->get('id,name')){

	    $catopt = arrToSelectOpts(convert_obj_to_arr($cats),'id','name');
	}

	return $catopt;
}
?>