<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendmail($to,$subject='',$body='',$attachments = null){
    
    $mail = new PHPMailer(true);                              
    
    $mail->isSMTP();                                      
    $mail->Host = 'smtp.gmail.com';                       
    $mail->Port = 465;                                    
    $mail->SMTPSecure = 'ssl';                            
    $mail->SMTPAuth = true;                               
    $mail->Username = 'doanlnph04866@fpt.edu.vn';         
    $mail->Password = 'Doan.2016';                        
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('admin@thegioivuong.com', 'Thế Giới Vuông');
    
    $CC = null;
    $BCC = null;
    if(is_string($to) && filter_var($to, FILTER_VALIDATE_EMAIL)){
        $mail->addAddress($to);
    }elseif(is_array($to)){
        foreach($to as $key => $val){
            if(is_numeric($key)){
                if(filter_var($val, FILTER_VALIDATE_EMAIL)){
                    $mail->addAddress($val);
                }
            }elseif (strtolower($key) == '@cc') {//neu co CC
                $CC = $val;
            }elseif (strtolower($key) == '@bcc') {// neu co BCC
                $BCC = $val;
            }else{
                if(filter_var($val, FILTER_VALIDATE_EMAIL)){
                    $mail->addAddress($val,$key);
                }elseif(filter_var($key, FILTER_VALIDATE_EMAIL)){
                    $mail->addAddress($key,$val);
                }
            }
        }
    }
    if(is_string($CC) && filter_var($CC, FILTER_VALIDATE_EMAIL)){
        $mail->addCC($CC);
    }elseif(is_array($CC)){
        foreach($CC as $val){
            if(filter_var($val, FILTER_VALIDATE_EMAIL)){
                $mail->addCC($val);
            }
        }
    }
    if(is_string($BCC) && filter_var($BCC, FILTER_VALIDATE_EMAIL)){
        $mail->addBCC($BCC);
    }elseif(is_array($BCC)){
        foreach($BCC as $val){
            if(filter_var($val, FILTER_VALIDATE_EMAIL)){
                $mail->addBCC($val);
            }
        }
    }
    
    //$mail->addReplyTo('thegioivuong@@gmail.com', 'Information');
    
    //Attachments
    if(is_string($attachments) && file_exists($attachments)){
        $mail->addAttachment($attachments);
    }elseif(is_array($to)){
        foreach($to as $key => $val){
            if(is_numeric($key)){
                if(file_exists($val)){
                    $mail->addAttachment($attachments);
                }
            }else{
                if(file_exists($val)){
                    $mail->addAttachment($val,$key);
                }elseif(file_exists($key)){
                    $mail->addAttachment($key,$val);
                }
            }
        }
    }
    
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    
    //$mail->AltBody = $altBody;

    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }

}