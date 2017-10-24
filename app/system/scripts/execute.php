<?php
        $filename = $filename? $filename : $this->filename;
        $____cache____ = $cache ? $cache : $this->cache;
        $____cache_time____ = $cache_time ? $cache_time : $this->cache_time;
        $____pathinfo____ = $pathinfo ? $pathinfo : $this->pathinfo;
        $____query_string____ = $query_string ? $query_string: $this->query_string;
        //lay cac bien da dc khai bao
        //$filename = $this->parseFilename($filename);
        
        //lay danh sach dung dan cac file view da dc bien dich hoan toan sang php
        if($___fn___ = Template::getFileRun($filename, $this->currentPath)){
            
            $___d___ = self::getVar();
            $___f___ = $variable?$variable:$this->variable;
            if(is_array($___d___)){
                extract($___d___, EXTR_PREFIX_SAME, "CUBE_");
            }
            if(is_array($___f___)){
                extract($___f___, EXTR_PREFIX_SAME, "CUBE_");
            }
            $___c___ = self::getCacheFilename($___fn___, $____pathinfo____, $____query_string____);
            if($____cache____ && $____content____ = Cache::getCache($___c___,$____cache_time____)){//neu su dung cache va request khac post
                echo($____content____);
                return null;
            }

            try{
                $____oldPath____ = $this->currentPath;
                $this->currentPath = rtrim(dirname(str_replace(TPLRSPHP,VIEWDIR,$___fn___)),'/').'/';
                ob_start();
                $___inc_data___ = include($___fn___);
                $this->currentPath = $____oldPath____;
                
                if(!is_object($___inc_data___))
                {
                    $data = ob_get_clean();
                    if($this->_layout){
                        $data = $this->getLayout($data, $___f___);
                        $this->_layout = null;
                        
                    }
                    echo $data;
                    if($____cache____){
                        Cache::saveCache($___c___,$data);
                    }
                    
                }
                elseif(is_a($___inc_data___,'CubeException')){
                    throw new Exception($e->getFakeFileMsg(Template::getViewFilePath(str_replace(TPLRSPHP, VIEWDIR, $___fn___))), 1);
                }else{
                    throw new Exception(str_replace(TPLRSPHP, VIEWDIR, $e->getMessage()), 1);
                    
                }
            }catch(exception $e){
                $m = $e->getMessage();
                $m = str_replace(TPLRSPHP, VIEWDIR, $m);
                throw new Exception('loi ko xac dinh', 1);
            }
        }