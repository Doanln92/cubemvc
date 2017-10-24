<?php

function df($obj=null)
{
    echo '<pre>';
    var_dump($obj);
    echo '</pre>';
    die;
}

function getUrl($path = "/"){

    $Url = str_replace($_SERVER["DOCUMENT_ROOT"], "http://$_SERVER[HTTP_HOST]", dirname($_SERVER['SCRIPT_FILENAME']));

    return $path != '/' ? rtrim($Url,'/').'/'.ltrim($path,'/') : $Url;
}

function getCurrentUrl(){
    return PROTOCOL."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}


function getPathInfo(){
    return ltrim(str_replace(getUrl(), '', strtok(getCurrentUrl(), '?')),'/');
}