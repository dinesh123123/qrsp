<?php
defined('BASEPATH') OR ('No direct script access allowed');
//define('FIREBASEKEY','AIzaSyC-yh9KH0RyyQa6MXSeezcY4h01RfNkMWA');


/*  Request For notification ...............  
    $data['message']= $this->content ;
    $data['register_id']= $this->row->fcm_id;
    $data['type'] = $this->title;
    $res = send_notification($data);*/
                
   	// function send_notification($data=array()){
     
    //     if(!isset($data['message']))
    //     {
    //         $data['message']='you have a new order';
    //     }
        
    //     if(!isset($data['fcm_id']))
    //     {
    //         $data['fcm_id']='xxsx';
    //     }
    //     if(!isset($data['type']))
    //     {
    //         $data['type']='xxsx';
    //     }
    //     $fields=array(
    //     "to"  => $data['fcm_id'],
    //     "notification"  => $data,
    //     "data"=> $data
    //     );
        
    //     $url = 'https://fcm.googleapis.com/fcm/send';
    //     $headers = array(
    //      'Content-Type:application/json',
    //      'Authorization: key=.AIzaSyC-yh9KH0RyyQa6MXSeezcY4h01RfNkMWA',

    //     'Content-Type: application/json'
    //     );
    //     // Open connection
    //     $ch = curl_init();
    //     // Set the url, number of POST vars, POST data
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     // Disabling SSL Certificate support temporarly
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    //     // Execute post
    //     $result = curl_exec($ch);
    //     //print_r($result);die;
    //     if ($result === FALSE) {
    //         die('Curl failed: ' . curl_error($ch));
    //     }
    //     // Close connection
    //     curl_close($ch);
    //     return $result;
    // }
    
    // Save Notification..
    
    // function save_notification($data=''){
    //     $obj = & get_instance();
	    
	   //  $obj->Api_model->add('tbl_notification', $data);
	    
    // }
    
    