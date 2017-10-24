<?php
//load cac file he thong

include_once APPDIR.'define.php';


    	
include_once SYSCLASSDIR.'App.php';
include_once SYSCLASSDIR.'Arr.php';
include_once SYSCLASSDIR.'Cache.php';
include_once SYSCLASSDIR.'Controller.php';
include_once SYSCLASSDIR.'Cube.php';
include_once SYSCLASSDIR.'cube_data.php';
include_once SYSCLASSDIR.'CubeException.php';
include_once SYSCLASSDIR.'datas.php';
include_once SYSCLASSDIR.'DB.php';
include_once SYSCLASSDIR.'DBTable.php';
include_once SYSCLASSDIR.'Files.php';
include_once SYSCLASSDIR.'FileUpload.php';
include_once SYSCLASSDIR.'FormData.php';
include_once SYSCLASSDIR.'Html.php';
include_once SYSCLASSDIR.'HTTPRequest.php';
include_once SYSCLASSDIR.'map.php';
include_once SYSCLASSDIR.'pathinfo.php';
include_once SYSCLASSDIR.'PDOdb.php';
include_once SYSCLASSDIR.'Route.php';
include_once SYSCLASSDIR.'Str.php';
include_once SYSCLASSDIR.'Template.php';
include_once SYSCLASSDIR.'vars.php';
include_once SYSCLASSDIR.'View.php';


include_once SYSFUNCDIR.'Controller.php';
include_once SYSFUNCDIR.'cubes.php';
include_once SYSFUNCDIR.'Extension.php';
include_once SYSFUNCDIR.'html.php';
//include_once SYSFUNCDIR.'language.php';
include_once SYSFUNCDIR.'map.php';
include_once SYSFUNCDIR.'Model.php';
include_once SYSFUNCDIR.'pathinfo.php';
include_once SYSFUNCDIR.'request.php';
include_once SYSFUNCDIR.'str.php';
include_once SYSFUNCDIR.'view.php';




App::config(get_config_var());

App::start();


$http = App::get_file_list(APPDIR.'http/','php',true);
if($http):
	foreach ($http as $f):
		if($f['type'] == 'file') include_once($f['path']);
	endforeach;
endif;
unset($http);


$common = App::get_file_list(APPDIR.'common/','php',true);
if($common):
	foreach ($common as $f):
		if($f['type'] == 'file') include_once($f['path']);
	endforeach;
endif;
unset($common);

include_once SYSTEMDIR.'run.php';
App::finish();
?>