<?php try{ ?><?php $this->layout("fullwidth", array('a','b')); 

$this->template("home-slideshow",null,true,180); 

$this->template("home-layout",null,true,180); ?>
<?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>