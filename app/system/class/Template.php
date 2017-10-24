<?php 

/**
 * @author Doanln
 * @copyright 2017
 */


define('TPLRSCACHE', RESOURCESDIR.'cache/');
define('TPLRSFTIME', RESOURCESDIR.'filetime/');
define('TPLRSPHP', RESOURCESDIR.'php/');

define('TPLPOINT', '<!---------TPL Point[{n}] ---------->');
class Template{
	protected static $code;

	protected static $php_code = array();

	protected static $cache_time = 300;

	protected static $tplpoints = array(
		'find' => array(),
		'replace' => array()
	);

	protected static function reset()
	{
		self::$code=null;
		self::$php_code = array();
		self::$tplpoints = array(
			'find' => array(),
			'replace' => array()
		);
	}

	/**
     * kiem tra file truyen vao co ton tai hay ko?
     * @param String $file file hoac danh sach file, ngan cach bang dau phay (,)
     * @return Array tra ve mang danh sach file
     */
    
	public static function getViewFilePath($file=null,$dir=null){
        if(!is_string($file)&&!is_numeric($file)) return null;
        $d = is_string($dir)?rtrim($dir,'/').'/':rtrim(VIEWDIR,'/').'/';
        $f = null;
        $p = $d.$file;
        $b = explode(rtrim(BASEDIR,'/'),$file);
        if(count($b)>1){
            if(file_exists($file)) $f = $file;
            elseif(file_exists($file.'.php')) $f = $file.'.php';
            elseif(file_exists($file.'.inc')) $f = $file.'.inc';
            elseif(file_exists($file.'.tpl')) $f = $file.'.tpl';
        }
        elseif(!is_dir($p)){
            if(file_exists($p)) $f = $p;
            elseif(file_exists($p.'.php')) $f = $p.'.php';
            elseif(file_exists($p.'.html')) $f = $p.'.html';
            elseif(file_exists($p.'.inc')) $f = $p.'.inc';
            elseif(file_exists($p.'.tpl')) $f = $p.'.tpl';
            
        }else{
            if(file_exists($p.'.php')) $f = $p.'.php';
            elseif(file_exists($p.'/index.php')) $f = $p.'/index.php';
            elseif(file_exists($p.'/index.html')) $f = $p.'/index.html';
            elseif(file_exists($p.'/index.inc')) $f = $p.'/index.inc';
            elseif(file_exists($p.'/index.tpl')) $f = $p.'/index.tpl';
        }
        return $f;
    }
    /**
     * kiem tra file truyen vao co ton tai hay ko?
     * @param String $file file hoac danh sach file, ngan cach bang dau phay (,)
     * @return Array tra ve mang danh sach file
     */
    public static function getViewFileList($file=null,$dir=null){
        if(!is_string($file)&&!is_numeric($file)) return null;
        $a = array();
        if(is_string($file)){
            $b = explode(',',$file);
            $c = count($b);
            for($i=0;$i<$c;$i++){
                $d = self::getViewFilePath(trim($b[$i]),$dir);
                if($d) $a[] = $d;
            }
        }
        elseif(is_array($file)){
            foreach($file as $e){
                $f = self::getViewFilePath(trim($e),$dir);
                if($f) $a[] = $f;
            }
        }
        return $a;
    }


    public static function getFileRun($filename='',$dir=null)
    {
    	if(!is_string($filename) && !is_numeric($filename)) return null;
    	$rs = null;
    	if($f = self::getViewFilePath($filename,$dir)){
    		$file = new files(array('dir'=>BASEDIR));
    		$ft = filemtime($f);
    		$fs = explode('.', $f);
    		$ext = array_pop($fs);
    		$fn = implode('.', $fs);
    		$filetime = str_replace(VIEWDIR, TPLRSFTIME, $fn).'.time';
    		$filephp = str_replace(VIEWDIR, TPLRSPHP, $fn).'.php';
    		if(!file_exists($filetime) || $ft!=$file->get_contents($filetime) || !file_exists($filephp)){
    			if($code = self::compileFile($f)){
    				$code = '<?php try{ ?'.'>'.$code.'<'.'?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?'.'>';
    				if($file->save_contents($code, $filephp)){
    					if($file->save_contents($ft, $filetime)) $rs = true;
    					return $filephp;
    				}
    			}
    		}else{
    			return $filephp;
    		}
    	}
    	return null;
    }

    public static function getRunFileList($file=null,$dir=null){
        if(!is_string($file)&&!is_numeric($file)) return null;
        $a = array();
        if(is_string($file)){
            $b = explode(',',$file);
            $c = count($b);
            for($i=0;$i<$c;$i++){
                $d = self::getFileRun(trim($b[$i]),$dir);
                if($d) $a[] = $d;
            }
        }
        elseif(is_array($file)){
            foreach($file as $e){
                $f = self::getFileRun(trim($e),$dir);
                if($f) $a[] = $f;
            }
        }
        return $a;
    }


    
    public static function compileFile($filename=null)
    {
    	if($filename && file_exists($filename)){
    		return self::compile(file_get_contents($filename));
    	}
    	return null;
    }

    public static function compileAndSaveFile($filename=null,$newFile=null,$before = null,$after=null)
    {
    	if($filename && $newFile){
    		if($code = self::compileFile($filename)){
    			$code = $before.$code.$after;
    			if(App::save_file_contents($code, $newFile)) return true;
    		}
    	}
    	return false;
    }
	public static function compile($code=null)
	{
		self::reset();
		self::$code = $code;
		for($i=0; $i <5; $i++){
			self::hidePHP();
			self::escape();
			self::compileShortTag();
			self::compileOriginTag();
			self::compilePoints();
			self::optimizing();
			self::unescapeOnlyPHP();	
		}
		self::hidePHP();
		self::escape();
		self::showPHP();
		self::optimizing();
		$code = self::$code;
		self::$code = null;
		self::reset();
		return $code;
	}

	protected static function addPoint($find, $replace=null)
    {
    	if(!$replace) return null;
    	$n = count(self::$tplpoints['find']);
    	$f = str_replace('{n}', $n, TPLPOINT);
    	self::$code = str_replace($find, $f, self::$code);
    	self::$tplpoints['find'][$n] = $f;
    	self::$tplpoints['replace'][$n] = '<?php '.$replace.' ?'.'>';
    }

    protected static function compilePoints()
    {
    	if(count(self::$tplpoints['find'])>0){
    		self::$code = str_replace(self::$tplpoints['find'], self::$tplpoints['replace'], self::$code);
    		self::$tplpoints = array(
    			'find' => array(),
    			'replace' => array(),
    		);
    	}
    }

	protected static function compileOriginTag()
	{
		$code = self::$code;
		if(preg_match_all('/\{\{[^\{\}]*\}\}/', $code, $m)){
			foreach ($m[0] as $exp) {
				if(preg_match_all('/^\{\{(.*)?\}\}$/', trim($exp), $ma)){
					$c = trim($ma[1][0]);

					$c = self::parseVar($c);
					if($s = self::compileLoop($c)){
						self::addPoint($exp, $s);
					}
					elseif($s = self::compileIf($c)){
						self::addPoint($exp, $s);
					}
					elseif($s = self::compileSwitch($c)){
						self::addPoint($exp, $s);
					}
					elseif($s = self::compileClose($c)){
						self::addPoint($exp, $s);
					}
					elseif($s = self::compileCallSpecialFunc($c)){
						self::addPoint($exp, $s);
					}
					elseif($s = self::compilePrint($c)){
						self::addPoint($exp, $s);
					}
					elseif($s = self::compileAssign($c)){
						self::addPoint($exp, $s);
					}
					

				}
			}
		}

		return true;
	}

	protected static function compileAssign($st='')
	{
		$rs = null;
		
		if(preg_match_all('/^\$(\w+[A-z0-9_\[\-\]\'\"]*)\s*(=|-=|\+=|\.=|\*=\/=){1}\s*(.*)?$/', trim($st), $m)){
			if($name =  self::parseAssName($m[1][0])){
				$rs = '$'.$name.' '.$m[2][0] .' '.$m[3][0].';';

			}
		}
		elseif(preg_match_all('/^\$(\w+[A-z0-9_\[\-\]\'\"]*)\s*(\+\+|\-\-){1}$/', trim($st), $m)){
			if($name =  self::parseAssName($m[1][0])){
				$rs = '$'.$name.' '.$m[2][0].';';
			}
		}
		
		return $rs;
	}
	protected static function compilePrint($st=null)
	{
		$rs = null;
		if($var = self::parseAccessVar($st)){
			$rs = 'echoif('.$var.');';
		}
		return $rs;
	}
	protected static function compileCallSpecialFunc($st=null)
	{
		$rs = null;
		if(preg_match_all('/^(include|include_once|require_once|require)(\s*\(.*\)|\s+.*)+/si', $st, $m)){
			$f = $m[1][0];
			$fn = trim($m[2][0]);
			if(!$fn) return null;
			$filename = null;
			if(preg_match('/^\((.*)\)$/si', $fn, $ma)){
				$fn=$ma[1];
			}
			if($fnn = self::parseAccessVar($fn)){
				if(preg_match('/(^\"(.*)\"$|^\'(.*)\'$)/', $fnn,$mf)){
					if($mf[2]){
						$filename = $mf[2];
					}elseif(isset($mf[3])){
						$filename = $mf[3];
					}
					if(!preg_match('/\.(php|html|htm|inc|tpl)$/si', trim($filename))) $filename .= '.php';
					$filename = '"'.$filename.'"';
				}
				elseif(preg_match('/^[A-z_]+\w*$/', $fnn,$mf)){
					$filename = $fnn;
					if(!defined($filename) && !preg_match('/\.(php|html|htm|inc|tpl)$/si', trim($filename))) $filename = '"'.$filename.'.php"';
				}
				else{
					$filename = $fnn;
				}
				$rs = $f."($filename);";
			}

		}
		return $rs;
	}
	protected static function compileIf($st='')
	{
		$rs = null;
		$ifpt = '/^if\((.*)\)$/si';
		$elifpt = '/^else\s*if\((.*)\)$/si';
		$st = trim($st);
		if(preg_match_all($ifpt, $st, $m)){
			$rs = $st . ' {';
		}elseif(preg_match_all($elifpt, $st, $m)){
			$rs = '} elseif(' . $m[1][0] . ') {';
		}
		elseif(strtolower($st) == 'else'){
			$rs = '} else {';
		}
		return $rs;
	}

	protected static function compileSwitch($st='')
	{
		$rs = null;
		$pattern = '/^switch\((.*)\)$/si';
		$st = trim($st);
		$case = '/^case\s*((\$\w+|\w+\((.*\)){1})+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+)\s?(\.|\+|\-|\*|\/)?\s?((\$\w+|\w+\((.*\)){1})+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+|[A-Z0-9_]*)*$/';
		if(preg_match_all($pattern, $st, $m)){
			$rs = $st . ' {';
		}elseif(preg_match_all($case, $st, $m)){
			$rs = $st . ':';
		}
		elseif(preg_match('/break/si', $st, $m)){
			$rs = $st . ';';
		}
		elseif(preg_match('/default/si', $st, $m)){
			$rs = $st . ':';
		}

		return $rs;
	}
	protected static function compileLoop($st='')
	{
		$rs = null;
		$st = trim($st);
		if($l = self::compileLoopFor($st)){
			$rs = $l;
		}
		elseif($l = self::compileLoopForEach($st)){
			$rs = $l;
		}elseif($l = self::compileLoopForIf($st)){
			$rs = $l;
		}elseif($l = self::compileLoopWhile($st)){
			$rs = $l;
		}
		elseif(preg_match('/break/si', $st, $m)){
			$rs = $st . ';';
		}
		elseif(preg_match('/continue/si', $st, $m)){
			$rs = $st . ';';
		}
		
		//return true;
		return $rs;
	}

	protected static function compileLoopFor($st=null)
	{
		$rs = null;
		$pattern = '/^for\d?\(.*\)$/si';
		if(preg_match($pattern, trim($st))) $rs = $st.'{';
		return $rs;
	}

	protected static function compileLoopForEach($st=null)
	{
		$rs = null;
		$pattern = '/^foreach\d?\((.*)\)$/si';
		if(preg_match_all($pattern, trim($st), $m)){
			if($a = self::parseForEachParams($m[1][0])){
				$rs = 'foreach('.$a.'){';
			}
		}
		return $rs;
	}
	
	protected static function compileLoopForIf($st=null)
	{
		$rs = null;
		$pattern = '/^forif\d?\((.*)\)$/si';
		if(preg_match_all($pattern, trim($st), $m)){
			if($a = self::parseForEachParams($m[1][0])){
				$ap = explode('as', $a);
				$args = trim($ap[0]);
				$rs = 'if(is_array('.$args.') && count('.$args.') > 0) foreach('.$a.'){';
			}
		}
		return $rs;
	}

	protected static function compileLoopWhile($st=null)
	{
		$rs = null;
		$pattern = '/^while\d?\(.*\)$/si';
		if(preg_match($pattern, trim($st))) $rs = $st.'{';
		return $rs;
	}

	protected static function compileShortTag()
	{
		$rs = null;
		$st = trim(self::$code);
		$funcs = explode(' ', 'include include_once require require_once get_header get_footer get_sidebar layout template');
		$viewFuncs = explode(' ', 'include require get_header get_footer get_sidebar layout template');
		$stt = false;
		if(preg_match_all('/(?!<=\w)@([a-z_]+\w*)+\s*\(([^\r\n\t@]*)\)\s*(\:|\;|\s|\r|\n|\t|\W)?/si', $st, $m)){
			$t = count($m[0]);
			
			for($i = 0; $i < $t; $i++){
				$find = trim(preg_replace('/[\r\n\t]/', '', self::getSystax($m[0][$i],":")));
				
				preg_match_all('/@([a-z_]+\w*)+\s*\((.*)\)\:?/', $find, $matches);

				$f = strtolower(trim($matches[1][0]));
				$f = preg_replace('/\s{2,}/si', ' ', $f);
				$p = trim($matches[2][0]);
				$replace = null;
				if($f=='foreach'){
					$arg = self::parseForEachParams($p);
					$replace = $f.'('.$arg.'){';
				}
				elseif($f=='forif'){
					$arg = self::parseForEachParams($p);
					$ap = explode('as', $arg);
					$args = trim($ap[0]);
					$replace = 'if(is_array('.$args.') && count('.$args.') > 0) foreach('.$arg.'){';
				}
				elseif($f=='for' || $f =='while' || $f =='if'){
					$replace = $f.'('.$p.'){';
				}
				elseif($f == 'elseif' || $f == 'else if'){
					$replace = '}'.$f.'('.$p.'){';
				}
				elseif($f == 'case'){
					$replace = $f.' '.$p.':';
				}
				elseif($f=='switch'){
					$replace = $f.'('.$p.'){';
				}




				if(!$replace){
					$find = trim(preg_replace('/[\r\n\t]/', '', self::getSystax($m[0][$i],";")));
					preg_match_all('/@([a-z_]+[A-z0-9_]*)+\s*\((.*)\)\;?/', $find, $matches);
					$fr = trim($matches[1][0]);
					$f = strtolower($fr);
					$f = preg_replace('/\s{2,}/si', ' ', $f);
					$p = trim($matches[2][0]);
					
					if(in_array($f, $funcs)){
						if($p!='')
						{
							$mt = null;
							if(in_array($f, $viewFuncs)) $f = '$this->'.($f=='include'?'inc':($f=='require'?'req':$f));
							$px = explode(',',$p);
							$pp = '';
							if(count($px)>0){
								$pp = $px[0];
							}
							$pm = 'null';
							if(preg_match_all('/(\${1}[A-z_]+\w*)+/', trim($pp), $mt)){
								$pm = $pp;
							}
							elseif(preg_match_all('/([A-z]+[A-Z0-9_\-\.\/]*)+/si', trim($pp), $mt)){
								$ff = $mt[1][0];
								$pm = '"'.trim($ff).'"';
							}
							elseif(preg_match_all('/\"([^\"]*)+\"/si', trim($pp), $mt)){
								$ff = $mt[1][1];
								$pm = '"'.trim($ff).'"';
							}elseif(preg_match_all('/\'([^\']*)+\'/si', trim($pp), $mt)){
								$ff = $mt[1][0];
								$pm = '\''.trim($ff).'\'';
							}

							if($pm){
								$px[0] = $pm;
								$pt = implode(',',$px);
								$replace = $f.'('.$pt.');';
							}
						}elseif($f=='get_header' || $f=='get_footer' || $f=='get_sidebar' || $f=='layout' || $f=='template'){
							$f = '$this->'.$f;
							$replace = $f.'();';

						}
					}
					elseif($f=='return'){
						$replace = $f.' ('.$p.');';
					}
					elseif($f=='assign' || $f=='printif'){
						$replace = 'View::'.$f.' ('.$p.');';
					}
					elseif($f=='e'){
						$replace = 'echoif('.$p.');';
					}
					elseif($f=='isvar' || $f=='is_var'){
						$replace = 'View::isVar('.$p.');';
					}
					
					elseif($f=='view_content'){
						$replace = '$this->getViewContent('.$p.');';
					}
					elseif(function_exists($fr)){
						$replace = $fr.'('.$p.');';
					}
					else{
						$replace = 'cube_call_func("'.$fr.'"'.($p?', '.$p:'').');';
					}
				}
				if($replace){
					self::addPoint($find,$replace);
					$stt = true;
				}

			}
		}
		if(preg_match_all('/\W@([a-z_]+\w*)+\s*\:{0,1}\W/', $st, $m)){
			$t = count($m[0]);
			for($i = 0; $i < $t; $i++){
				
				preg_match_all('/@([a-z_]+\w*)+\s*\;{0,1}/', $m[0][$i], $matches);
				$cf = $matches[0][0];
				$find = trim(preg_replace('/[\r\n\t]/', '', $cf));
				$f = strtolower($m[1][$i]);
				$replace = null;
				if($f=='default'){
					$replace = $f.':';
				}
				
				if($replace){
					self::addPoint($find,$replace);
					$stt = true;
				}
			}
		}
		
		if(preg_match_all('/\W@([a-z_]+\w*)+(\;|\W|$)?/', $st, $m)){
			$t = count($m[0]);
			$arr_ends = explode(' ', 'endif endfor endforeach endwhile endforif end');
			for($i = 0; $i < $t; $i++){
				preg_match_all('/@([a-z_]+\w*)+\;{0,1}/', $m[0][$i], $matches);
				$cf = $matches[0][0];
				$find = trim(preg_replace('/[\r\n\t]/', '', $cf));
				$f = strtolower($m[1][$i]);
				$replace = null;
				if(in_array($f, $arr_ends)){
					$replace = '}';
				}elseif($f=='endswitch'){
					$replace = '}';
				}
				elseif($f=='else'){
					$replace = '} '.$f.' {';
				}elseif($f=='continue' || $f=='break'){
					$replace = $f.';';
				}
				if($replace){
					self::addPoint($find,$replace);
					$stt = true;
				}
			}
		}
		if(!$stt && preg_match_all('/(?!<=\w)@((\$\w+|\w+\((.*\)){1}|[A-z_]\w*)+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+|\w*|::[A-z_]\w*|::\$[A-z_]+\w*|::[A-z_]\w*\(.*\))\s?(\+\+|\-\-|==|\+=|\-=|\*=|\/=|\.=|<=|>=|<|>|!=|!|=|\.|\+|\-|\*|\/|\?|\:)?\s?((\$\w+|\w+\((.*\)){1}|new\s+\w+\((.*\)){1}|[A-z_]\w*)+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+|\w*|::[A-z_]\w*|::\$[A-z_]+\w*|::[A-z_]\w*\(.*\))*\;/i',self::$code,$m)){
			foreach($m[0] as $st){
				$syst = self::getSystax($st,';','@',';');
				if(preg_match_all('/@((\$\w+|\w+\((.*\)){1}|[A-z_]\w*)+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+|\w*|::[A-z_]\w*|::\$[A-z_]+\w*|::[A-z_]\w*\(.*\))\s?(\+\+|\-\-|==|\+=|\-=|\*=|\/=|\.=|<=|>=|<|>|!=|!|=|\.|\+|\-|\*|\/|\?|\:)?\s?((\$\w+|\w+\((.*\)){1}|new\s+\w+\((.*\)){1}|[A-z_]\w*)+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+|\w*|::[A-z_]\w*|::\$[A-z_]+\w*|::[A-z_]\w*\(.*\))*\;/i',$syst,$mm)){
					self::addPoint($syst,substr($syst,1));
				}
			}
			
		}elseif(!$stt && preg_match_all('/(?!<=\w)@((\$\w+|\w+\((.*\)){1}|[A-z_]\w*)+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+|\w*|::[A-z_]\w*|::\$[A-z_]+\w*|::[A-z_]\w*\(.*\))\s*=[\{\[].*[\}\]]\;/i',self::$code,$m)){
			foreach($m[0] as $st){
				$syst = self::getSystax($st,';','@',';');
				if(preg_match_all('/@((\$\w+|\w+\((.*\)){1}|[A-z_]\w*)+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+|\w*|::[A-z_]\w*|::\$[A-z_]+\w*|::[A-z_]\w*\(.*\))\s*=[\{\[].*[\}\]]\;/i',$syst,$mm)){
					
					//self::addPoint($syst,substr($syst,1));
				}
			}
			
		}
		
	}

	protected static function getSystax($str='',$aft = ';', $stch='(', $edch=null)
	{
		if(!$edch){
			$edch = ')';
			if($stch=='{') $edch = '}';
			elseif($stch=='[') $edch = ']';
		}
		$st = 0;
		$ed = 0;
		$len = strlen($str);
		$l = $len;
		$stop=false;
		for($i=0;$i<$len;$i++){
			$s = $str[$i];
			if(!$stop){
				if($s==$stch) $st++;
				elseif ($s==$edch) {
					$ed++;
					if($ed>0 && $ed==$st) $stop=true;
				}
			}else{
				if($s==$aft){
					$l=$i+1;
					break;
					$i+=$len;
				}else{
					$l=$i;
					break;
					$i+=$len;
				}
			}
		}
		return substr($str, 0, $l);
	}


	protected static function parseForEachParams($st=null)
	{
		$rs = null;
		$pattern = '/^(\$\w+|\w+\((.*\)){1})+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\->[A-z_]+\w*)*\s+as\s*\$[A-z]+\w*(\s*=>\s*\$[A-z]+\w*)?$/si';
		$pattern2 = '/^(\$\w+|\w+\((.*\)){1})+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\->[A-z_]+\w*)*\s*(,\s*[\$A-z_]+\w){1,2}$/si';
		if(preg_match_all($pattern, trim($st), $m)){
			$rs = trim($st);
		}elseif(preg_match($pattern2, trim($st), $m)){
			$pre = array('', ' as ', ' => ');
			$s = '';
			$ms = explode(',', $st);
			foreach ($ms as $key => $value) {
				$s.=$pre[$key].trim($value);
			}
			$rs = $s;
		}
		return $rs;
	}

	protected static function parseVar($varName=null)
	{
		return self::compileArray($varName);
	}
	
	protected static function parseAssName($varName=null)
	{
		$rs = null;
		if(preg_match_all('/^\w+(\[\]|\[[^\]]*\])*$/', trim($varName), $m)) $rs = $varName;
		return $rs;
	}

	
	protected static function parseAccessVar($st=null)
	{
		$rs = null;
		$pattern = '/^((\$\w+|\w+\((.*\)){1}|[A-z_]\w*)+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+|\w*|::[A-z_]\w*|::\$[A-z_]+\w*|::[A-z_]\w*\(.*\))\s?(\.|\+|\-|\*|\/|\?|\:)?\s?((\$\w+|\w+\((.*\)){1}|[A-z_]\w*)+(\[[^\]]*\]|\->[A-z_]+\w*\(.*\)|\->[A-z_]+\w*\[[^\]]*\]|\-\>[A-z_]+\w*)*|\'.*\'|\".*\"|\d+|\w*|::[A-z_]\w*|::\$[A-z_]+\w*|::[A-z_]\w*\(.*\))*$/';
		if(preg_match($pattern, trim($st), $m)){
			$rs = trim($st);
		}
		return $rs;
	}
	
	protected static function compileArray($st='')
	{
		$rs = $st;
		if(preg_match_all('/\$\w*(\[[A-z_]+\d*\]|\[\$[A-z_]+\d*\]\[\d+\]|\.\w+)+/si', $st, $m)){
			$m1 = $m[0][0];
			$m2 = preg_replace('/\[(\w*)\]/', '["\$$1"]', $m1);
			$m2 = preg_replace('/\.(\w*)/', '["$1"]', $m2);
			$rs = str_replace($m1, $m2, $st);
		}
		return $rs;
	}
	protected static function compileClose($st='')
	{
		$rs = null;
		$st = trim($st);
		if(preg_match('/^\/(for|foreach|while|if|switch)?$/si', $st)) $rs = '}';
		if(preg_match('/^end(for|foreach|while|if|switch)?$/si', $st)) $rs = '}';
		return $rs;
	}

	protected static function hidePHP()
	{
		$code = self::$code;
		if(preg_match_all('/(<\?php|<\?)([^\?][^>])*\?\>/si', $code, $m)){
			foreach ($m[0] as $v) {
				$key = 'Doandeptrai->>>>>>>>hahaha_'.count(self::$php_code).'______hihi';
				self::$php_code[$key] = $v;
				$code = str_replace($v, $key, $code);
			}
		}
		self::$code = $code;
		return $code;
	}

	protected static function showPHP()
	{
		foreach(self::$php_code as $k => $v){
			self::$code = str_replace($k, $v, self::$code);
		}
		return self::$code;
	}
	protected static function optimizing()
	{
		if(preg_match_all('/([\r\n\t\s\{\}\;\:]*)+(\s*)\?\>([\r\n\t\s]*)*(<\?php|<\?)([\r\n\t\s]*)*/si', self::$code, $m)){
			
			$t = count($m[0]);
			$arr_find = array();
			$arr_replace = array();
			for ($i=0; $i < $t; $i++) {
				$as = strtolower($m[0][$i]);

				//$mm = str_replace(array('<','?','p','h','p','>'), '', $as);
				$mm = str_replace('?'.'>', '', $as);
				$mm = str_replace('<'.'?php ', '', $mm);
				$mm = str_replace('<'.'?php', '', $mm);
				$mm = str_replace('<'.'?', '', $mm);
				self::$code = str_replace($m[0][$i], $mm, self::$code);
			}
			
		}
		// $code = preg_replace('/(\{|\}|\;|\:|\))+(\s*)\?\>([\r\n\t\s]*)*(<\?php|<\?)([\r\n\t\s]*)*/si', 
		// 	'$1$2$3$5',
		// 	$code);	
		return self::$code;	
	}


	protected static $escape_arr = array(
		'encode' => array(
			'find' => array('\\{', '\\}', '\\[', '\\]', '\\(', '\\)', "\\'", '\\"', '\\@', '\\\\'),
			'replace' => array('<!-- Cube_escape<cq 1> -->', '<!-- Cube_escape<cq 2> -->',
						  '<!-- Cube_escape<cq 3> -->', '<!-- Cube_escape<cq 4> -->', 
						  '<!-- Cube_escape<cq 5> -->', '<!-- Cube_escape<cq 6> -->', 
						  '<!-- Cube_escape<cq 7> -->', '<!-- Cube_escape<cq 8> -->', 
						  '<!-- Cube_escape<cq 9> -->', '<!-- Cube_escape<cq 10> -->')
		),
		'decode' => array(
			'find' => array('<!-- Cube_escape<cq 1> -->', '<!-- Cube_escape<cq 2> -->',
						  '<!-- Cube_escape<cq 3> -->', '<!-- Cube_escape<cq 4> -->', 
						  '<!-- Cube_escape<cq 5> -->', '<!-- Cube_escape<cq 6> -->', 
						  '<!-- Cube_escape<cq 7> -->', '<!-- Cube_escape<cq 8> -->', 
						  '<!-- Cube_escape<cq 9> -->', '<!-- Cube_escape<cq 10> -->'),
			'replace' => array('\\{', '\\}', '\\[', '\\]', '\\(', '\\)', "\\'", '\\"', '\\@', '\\\\')
		),
		'decodephp' => array(
			'find' => array('<!-- Cube_escape<cq 1> -->', '<!-- Cube_escape<cq 2> -->',
						  '<!-- Cube_escape<cq 3> -->', '<!-- Cube_escape<cq 4> -->', 
						  '<!-- Cube_escape<cq 5> -->', '<!-- Cube_escape<cq 6> -->', 
						  '<!-- Cube_escape<cq 7> -->', '<!-- Cube_escape<cq 8> -->', 
						  '<!-- Cube_escape<cq 9> -->', '<!-- Cube_escape<cq 10> -->'),
			'replace' => array('{', '}', '[', ']', '(', ')', "\'", '\"', '@', '\\')
		)
	);

	protected static function escape()
	{
		self::$code = str_replace(self::$escape_arr['encode']['find'], self::$escape_arr['encode']['replace'], self::$code);
	}

	protected static function unescapeOnlyPHP()
	{
		$code = self::$code;
		if(preg_match_all('/(<\?php|<\?)([^\?][^>])*\?\>/si', $code, $m)){
			foreach ($m[0] as $v) {
				$v2 = str_replace(self::$escape_arr['decodephp']['find'], self::$escape_arr['decodephp']['replace'], $v);
				$code = str_replace($v, $v2, $code);
			}
		}
		self::$code = $code;
	}
	protected static function unescape()
	{
		self::$code = str_replace(self::$escape_arr['decode']['find'], self::$escape_arr['decode']['replace'], self::$code);
	}
}

?>