<?php 

use Common\getUrl;
use Common\CubeLoader;
/**
* TestController
*/

class TestController extends Controller
{
	
	function __construct()
	{
		# code...
	}

	public function index()
	{
		$loader = new CubeLoader('Doan');
		echo $loader;
	}
}
 ?>