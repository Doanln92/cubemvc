<?php

class CubeException extends Exception {
	protected $_errNsg = null;
	public function errorMessage() {
		//error message
		$this->_errNsg = 'Error on line '.$this->getLine().' in {$file}: <b>'.$this->getMessage().'</b> ';
		$errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
		.': <b>'.$this->getMessage().'</b> ';
		return $errorMsg;
	}

	public function getMaskFileMsg($file=null)
	{
		if(!$file) $file = $this->getFile();
		return str_replace('{$file}', $file, $this->_errNsg);
	}
}
?>