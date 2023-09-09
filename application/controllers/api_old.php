<?php

if(!defined('BASEPATH')) exit ('No direct script access allowed');




class Api extends MY_Controller

{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Api_Model');
    $this->load->model('CommanModel');
   /* $this->load->helper('custom_helper');*/
    $this->load->library('form_validation');
    $this->load->library('email');
    date_default_timezone_set('Asia/Calcutta');
  }





/*-----------------start sachin Qrsp 01 march 2023--------------------*/
 public function signup()
  {
    
    $name               = $this->input->post('name');
    $email              = $this->input->post('email');
    $password           = $this->input->post('password');
    $type               = $this->input->post('type');
    $fcm_id             = $this->input->post('fcm_id');
    
    if(isset($name) &&  
      isset($email) &&  
      isset($password) &&  
      isset($type) &&  
      isset($fcm_id))
    {
      if(!$this->Api_Model->is_record_exist('user','email',$email))
      {
        
     
          
            $otp = rand(1000,9999);
          
             $postdata = array(
          
            'name' => $name,
            'email' => $email,
            'otp' => $otp,
            'password' => md5($password),
            'plain_password' => $password,
            'type' => $type,
            'fcm_id' => $fcm_id,
            'created_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s'),
            'verify_otp' => "0",
            );
            
           $result = $this->Api_Model->insertAllData('user', $postdata);
           
           if($result)
           {
         
             $this->db->where("user.id",$result);
             $this->db->select("id,name,type,email,otp,verify_otp");
             $this->db->from("user");
             $user_result = $this->db->get()->row();
             
             $json['result']  = "true";
             $json['msg']     = "user signup successfully!";
             // $json['path']     = base_url()."assets/images/users/";
             $json['data'] =$user_result;
            
           }
           else
           {
             $json['result']  = "false";
             $json['msg']     = "something went wrong!";
           }           
        
        
      }
      else
      {
        $json['result']  = "false";
        $json['msg']     = "email already exist!";
      }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required name,email,password,fcm_id,type(user (or) admin)";      
    }    
    
    echo json_encode($json);
    
  }

  //....................login all user(user/admin).............//

  public function login()
   {
     $email   = $this->input->post('email');
     $password   = $this->input->post('password');
     $fcm_id   = $this->input->post('fcm_id');
     
     if(isset($email) && isset($password) && isset($fcm_id))
     {
       if($this->Api_Model->email_password($email,$password))
       {
         
         
         if(!empty($fcm_id))
         {
           $fcm_id = $fcm_id;
         }
         else
         {
           $fcm_id = "";
         }
         
        
        $otp = rand(1000,9999);
      
            $post_data = array(
              'otp'=>$otp,
              'fcm_id'=>$fcm_id,
              );
              
              $this->db->where('email',$email);
              $this->db->where('plain_password',$password);
        $result_1 = $this->db->update('user',$post_data);

          $wheredata = array('field'=>'*',

           'table'=>'user',

           'where'=>array('email' => $email,'plain_password'=>$password),
         );

        $result = $this->Api_Model->getAllDataRow($wheredata);
        
         
          $json['result'] = "true";
          $json['msg']    = "Login Successful";
          $json['data']   = $result;
          
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "email / password does not match";
       }

     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required email,fcm_id,password";
     }
   
     echo json_encode($json);
   }
 
//................. change password............................//

  public function change_password()
{

  extract($_POST);

  if(isset($user_id) && isset($old_password) && isset($new_password) && isset($confirm_password))
  {
   if($this->Api_Model->is_record_exists('user','id',"{$user_id}"))
   {
      $userdetail=$this->db->query('select * from user where id="'.$user_id.'"')->row();
      // $user_id=$userdetail->id;
      $old_encripted =  $this->Api_Model->select_single_rows('user','id',$user_id)->plain_password; 
      if(($old_password)==$old_encripted)
      {
        if ($new_password==$confirm_password) {
          $post_data['plain_password'] = ($new_password);
          $post_data['password'] = md5($new_password);
          $result = $this->Api_Model->updateData('user',$post_data,$user_id);
          if($result)
          {
            $json['result'] = "true";
            $json['msg'] = "Successfully updated password";
            $json['data'] =  $this->Api_Model->select_single_rows('user','id',$user_id);
          }else{
            $json['result'] = "false" ;
            $json['msg'] = "Something went wrong. Please try later.";
          }
        }else{
          $json['result'] = "false" ;
          $json['msg'] = "New password and confirm_password not matched.";
        }   
      }else{
        $json['result'] = "false";
        $json['msg']    = 'Invalid old Password';
      }   
    }else{
      $json['result'] = "false";
      $json['msg']    = 'User not exist';
    }
  }else
  {
    $json['result'] = "false";
    $json['msg'] = "parameter required user_id,old_password,new_password,confirm_password";
  }
  echo json_encode($json);
}
//.....................forget password..............................//
public function forget_password()
{
   $email = $this->input->post('email');
   
   if(isset($email))
   {
       if($this->Api_Model->is_record_exist('user','email',$email))
       {
           $otp = rand(1000,9999);
         
           $wheredata = array(
             'email' => $email
            );

          $data = array(
          'otp' => $otp,
          );

          $result = $this->Api_Model->update($wheredata,'user',$data);
          
          if($result)
          {
              $wheredata = array('field'=>'id,password,plain_password',

               'table'=>'user',

               'where'=>array('email' => $email),
       
               );

              $rows = $this->Api_Model->getAllDataRow($wheredata);
              $json['result'] = 'true';
              $json['msg']    = 'New Password sent successfully';
              $json['data']   = $rows;
          }
          else
          {
              $json['result'] = 'false';
              $json['msg']    = 'Something went wrong';
          }
           
       }
       else
       {
           $json['result'] = 'false';
           $json['msg']    = 'email Incorrect';
       }
   }
   else
   {
       $json['result'] = 'false';
       $json['msg']    = 'parameter required email';
   }
   echo json_encode($json);
}

//.......................logout..................................//
public function logout()
{
   $user_id = $this->input->post('user_id');
   if(isset($user_id))
   {
        extract($_POST);

      $wheredata = array(
            'id' => $user_id
        );

        $data = array(
            'fcm_id' => "",
            );

        $result = $this->Api_Model->update($wheredata,'user',$data);
      $result_user=  $this->Api_Model->is_record_exist('user','id',$user_id);

   if($result_user)
   {

     $json['result'] = 'true';
     $json['msg']    = 'Successfully logout.';

   }
   else
   {
       $json['result'] = 'false';
       $json['msg']    = 'user not found';
   }

   }
   else
   {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id";
   }

    echo json_encode($json);
}
//...........................sachin end....march.......................//
  public function customer_signup()
  {
    $vendor_name         = $this->input->post('vendor_name');
    $middle_name         = $this->input->post('middle_name');
    $last_name           = $this->input->post('last_name');
    $vendor_email        = $this->input->post('vendor_email');
    $vendor_password     = $this->input->post('vendor_password');
    $phone               = $this->input->post('phone');
    $state               = $this->input->post('state');
    $district            = $this->input->post('district');
    $pincode             = $this->input->post('pincode');
    $address             = $this->input->post('address');
    
    
    
    
    
    if(isset($vendor_name) && isset($last_name) && isset($phone) && isset($vendor_email)  && isset($vendor_password) && isset($state) && isset($district) && isset($pincode) && isset($address))
    {
      if(!$this->Api_Model->is_record_exist('vendor','phone',$phone))
      {
        
        if(!$this->Api_Model->is_record_exist('vendor','vendor_email',$vendor_email))
        {
        
         
          
            $otp = rand(1000,9999);
          
     
         
          
             $postdata = array(
            'vendor_name' => $vendor_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'phone' => $phone,
            'vendor_email' => $vendor_email,
            'vendor_password' => md5($vendor_password),
             // 'otp' => $otp,
            'state' => $state,
            
            'district' => $district,
            'pincode' => $pincode,
            'address' => $address,
            'type' => "customer",
            /*'status' => 0,*/
            'created_date' => date('Y-m-d H:i:s'),
            /*'updated_date' => date('Y-m-d H:i:s'),*/
            );
            
           $result = $this->Api_Model->insertAllData('vendor', $postdata);  
           
           
           if($result)
           {
             
         
             $this->db->where("vendor.vendor_id",$result);
             $this->db->select("vendor_id");
             $this->db->from("vendor");
             $vv = $this->db->get()->row();
         
             
             
             $json['result']  = "true";
             $json['msg']     = "signup successfully!";
             // $json['path']     = base_url()."assets/images/users/";
             $json['vendor_id'] = $vv->vendor_id;
            //  $json['otp']     = $otp;
            
           }
           else
           {
             $json['result']  = "false";
             $json['msg']     = "something went wrong!";
           }
           
           
           
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "Email already exist!";
        }
        
      }
      else
      {
        $json['result']  = "false";
        $json['msg']     = "Phone already exist!";
      }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required    vendor_name,middle_name,last_name, phone ,vendor_email,vendor_password,state,district,pincode,address";
     
      
    }
    
    
    echo json_encode($json);
    
  }





  // Api start deepesh



  
 public function users_signup()
{
  $name                = $this->input->post('name');
  $phone               = $this->input->post('phone');
  $email               = $this->input->post('email');
  $password            = $this->input->post('password');
  $lat                 = $this->input->post('lat');
  $lang                = $this->input->post('lang');
  $fcm_id              = $this->input->post('fcm_id');
  
  
  
  if(isset($name)  
    && isset($phone) 
    && isset($email)  
    && isset($password)
    && isset($lat) 
    && isset($lang)  
    && isset( $fcm_id))
  {
    if(!$this->Api_Model->is_record_exist('users','phone',$phone))
    {
      
      if(!$this->Api_Model->is_record_exist('users','email',$email))
      {
      
        
          $postdata = array(
          'name' => $name,
          'phone' => $phone,
          'email' => $email,
          'lat' => $lat,
          'lang' => $lang,
          'fcm_id' => $fcm_id,

          'password' => $password,
         
          
          );
          
         $result = $this->Api_Model->insertAllData('users', $postdata);  
         
         
         if($result)
         {
           
       
           $json['result']  = "true";
           $json['msg']     = "signup successfully!";
          /* $json['result'] =   $result;*/
      
      
      
        /*   
          $this->db->where("id",$result);
           $vv = $this->db->get('users')->row_array();  */         
           
           
         }
         else
         {
           $json['result']  = "false";
           $json['msg']     = "something went wrong!";
         }   
         
         
      }
      else
      {
        $json['result']  = "false";
        $json['msg']     = "Email already exist!";
      }
      
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "Phone already exist!";
    }
  }
  else
  {
    $json['result']  = "false";
    $json['msg']     = "parameter required name,phone,email,lat,lang,fcm_id,password";
   
    
  }
  
  
  echo json_encode($json);
  
}




public function user_login()
{
   $email      = $this->input->post('email');
   $password   = $this->input->post('password');
    $name   = $this->input->post('name');
     $image   = $this->input->post('image');
   
   if(isset($email) && isset($password))
   {
       if($this->Api_Model->is_record_exist('users','email',$email))
       {
           $wheredataaa = array('field'=>'id',

             'table'=>'users',

             'where'=>array('email' => $email,'password' => $password),
       

            );



          $rrows = $this->Api_Model->getAllDataRow($wheredataaa);
          
          
          if($rrows)
          {
              
              
              
               $otp = rand(1000,9999);
      
           $post_data = array(
               'otp'=>$otp,
               );
              
                     $this->db->where('email',$email);
         $result_1 = $this->db->update('users',$post_data);
      
      
      



            $wheredata = array('field'=>'id,email,password,image,name,otp,phone,verify_otp',

             'table'=>'users',

             'where'=>array('email' => $email),
            
       

            );



          $result = $this->Api_Model->getAllDataRow($wheredata);
          
         
              $json['result'] = "true";
              $json['msg']    = "Login Successful";
               $json['path']   = base_url().'assets/images/users/';
              $json['data']   = $result;
          }
          else
          {
              $json['result'] = "false";
              $json['msg']    = "Password Invalid";
          }
           


             
        
       }
       else
       {
           $json['result'] = "false";
           $json['msg']    = "Email Invalid";
       }
   }
   else
   {
       $json['result'] = "false";
       $json['msg']    = "parameter required email,password";
   }
   
   
   echo json_encode($json);
}













public function facebook_login()
{      

  extract($_POST);
  if (isset($name) && 
    isset($fcm_id) && 
    isset($facebook_id)
    ) 
  { 
      
      $otp = rand(1000,9999);
       //   $email = $this->input->post('email');
   
    
    

    
     
    $wheredata = array(    
    'facebook_id'  =>$facebook_id,
    );
    
    $res= $this->Api_Model->singleRowdata($wheredata,'users');
    if($res){
        
        
            $post_data = array(
                'otp' => $otp,
                'verify_otp' => 0,
                'fcm_id' => $fcm_id,
             );
     
      $this->db->where("facebook_id",$facebook_id);
      $this->db->update("users",$post_data); 
  
  
 
        
        
        
      $data_result['result'] = 'true';
      $data_result['msg']    = 'Facebook Login successfully!';
      $data_result['data']   = $this->Api_Model->singleRowdata($wheredata,'users');
    }else{
      $data = array( 
        //'email'  =>$email,
        'name'   =>$name,         
        'facebook_id'=>$facebook_id,           
        'fcm_id' =>$fcm_id,
        'otp' =>$otp,
        'verify_otp' =>0,
        'created_date' => date('Y-m-d H:i:s',time()),
        'updated_date' => date('Y-m-d H:i:s',time()),
      );
      $result    = $result = $this->Api_Model->insertAllDataa('users',$data);
      $wheredata = array(
        'id' => $result
      );
      $res1 = $this->Api_Model->singleRowdata($wheredata,'users');
      if($result){
        $data_result['result'] = 'true';
        $data_result['data']   = $res1;
        $data_result['msg']    = 'Facebook Login successfully!';
      }else{
        $data_result['result'] = 'false';
        $data_result['msg']    = 'Your record not insert!';
      }
    }
    

    
  }else{
    $data_result['result'] = 'false';
    $data_result['msg']    = 'parameter required name,fcm_id,facebook_id';
  }
  echo json_encode($data_result);

}


public function google_login()
{      

  extract($_POST);
  if (isset($email) && 
    isset($name) && 
    isset($fcm_id) && 
    isset($google_id)
    ) 
  {
      
    
    $otp = rand(1000,9999); 
      
      
      
      
    $wheredata = array(    
    'email'  =>$email                         
    );
    $res= $this->Api_Model->singleRowdata($wheredata,'users');
    if ($res){
        
      
      $post_data = array(
                'google_id' => $google_id,
                'otp' => $otp,
                'verify_otp' => 0,
                'fcm_id' => $fcm_id,
             );
             
             
     
      $this->db->where("email",$email);
      $this->db->update("users",$post_data);   
        
        
        
        
      $data_result['result'] = 'true';
      $data_result['msg']    = 'Google Login successfully!';
      $data_result['data']   = $this->Api_Model->singleRowdata($wheredata,'users');
    }else{
      $data = array( 
        'email'  =>$email,
        'name'   =>$name, 
        'otp'   =>$otp, 
        'verify_otp'  =>1, 
        'google_id'=>$google_id,           
        'fcm_id' =>$fcm_id
      );
      $result    = $this->Api_Model->insertAllDataa('users',$data);
      
      
      $wheredata = array(
        'id' => $result
      );
      
      
      $res1 = $this->Api_Model->singleRowdata($wheredata,'users');
      if($result){
        $data_result['result'] = 'true';
        $data_result['msg']    = 'Login google successfully!';
        $data_result['data']   = $res1;
        
      }else{
        $data_result['result'] = 'false';
        $data_result['msg']    = 'Your record not insert!';
      }
    }
  }else{
    $data_result['result'] = 'false';
    $data_result['msg']    = 'parameter required email,name,fcm_id,google_id';
  }
  echo json_encode($data_result);

}
    
    
    
    
public function vender_signup()
{
    $name                    = $this->input->post('name');
    $mname                   = $this->input->post('mname');
    $lname                   = $this->input->post('lname');    
    $email                   = $this->input->post('email');
    $mobile                  = $this->input->post('mobile');    
    $services_address        = $this->input->post('services_address');
    $service_abailble        = $this->input->post('service_abailble');
    
   
    if(isset($name) && isset($mname) && isset($lname) && isset($email) && isset($mobile)  && isset($services_address) && isset($service_abailble))
    {
        if(!$this->Api_Model->is_record_exist('users','mobile',$mobile))
        {
            
            if(!$this->Api_Model->is_record_exist('users','email',$email))
            {
            
             
                 if(!empty($_FILES['image']['name']))
            {
                $image = $this->imageUpload('image','assets/images/users/');
                
                $postdata['image'] = $image;
            }


             if(!empty($_FILES['cover_image']['name']))
            {
                $image = $this->imageUpload('cover_image','assets/images/cover/');
                
                $postdata['cover_image'] = $image;
            }


             if(!empty($_FILES['id_proof']['name']))
            {
                $image = $this->imageUpload('id_proof','assets/images/id_proof/');
                
                $postdata['id_proof'] = $image;
            }

                 
                
                   $postdata = array(
                    'name' => $name,
                    'mname' => $mname,
                    'lname' => $lname,
                    'email' => $email,
                    'image' => $image,
          
                    'mobile' => $mobile,
                    'services_address' => $services_address,
                    'service_abailble' => $service_abailble,
                    'profile' => "1",
                    'status' => 0,
                   
                    );
                    
                 $result = $this->Api_Model->insertAllData('users', $postdata);  
                 
                 
                 if($result)
                 {
                     
             
                     $this->db->where("users.id",$result);
                     $this->db->select("id");
                     $this->db->from("users");
                     $vv = $this->db->get()->row();
           
                     
                     
                     $json['result']  = "true";
                     $json['msg']     = "signup successfully!";
                     // $json['path']     = base_url()."assets/images/users/";
                     $json['user_id'] = $vv->id;
                    
                    
                 }
                 else
                 {
                     $json['result']  = "false";
                     $json['msg']     = "something went wrong!";
                 }
                 
                 
                 
            }
            else
            {
                $json['result']  = "false";
                $json['msg']     = "Email already exist!";
            }
            
        }
        else
        {
            $json['result']  = "false";
            $json['msg']     = "Mobile already exist!";
        }
    }
    else
    {
        $json['result']  = "false";
        $json['msg']     = "parameter required name,mname,lname,email,mobile,services_address,service_abailble,image,cover_image,id_proof";
     
      
    }
    
    
    echo json_encode($json);
    
}
    
    
public function venderget_profile()
{
 $user_id = $this->input->post('user_id');
 
 if(isset($user_id))
 {
  $wheredata = array('field'=>'users.*',

       'table'=>'users',

       'where'=>array('id' => $user_id),

      );



  $row = $this->Api_Model->getAllDataRow($wheredata); 
  
  if($row)
  {
    
    $wheredata_1 = array('field'=>'users.image',

       'table'=>'users',

       'where'=>array('id' => $user_id),

      );



    $row_1 = $this->Api_Model->getAllDataRow($wheredata_1); 
    
    
    if($row_1)
    {
      $row->image = $row_1->image;
    }
    else
    {
      $row->image = "";
    }
    
    
    
    $json['result'] = 'true';
    $json['msg']    = 'All Data';
    $json['path']   = base_url().'assets/images/users/';
    $json['data']   = $row;
  }
  else
  {
    $json['result'] = 'false';
    $json['msg']    = 'Invalid user_id';
  }
 }
 else
 {
   $json['result'] = 'false';
   $json['msg']    = 'parameter required user_id';
 }
 
 
 echo json_encode($json);
}




public function vender_update_profile()
{
  $user_id                = $this->input->post('user_id');
  $name                   = $this->input->post('name');
  $mname                  = $this->input->post('mname');
  $lname                  = $this->input->post('lname');
  $email                  = $this->input->post('email');
  $mobile                 = $this->input->post('mobile');
  $password               = $this->input->post('password');
  $services_address       = $this->input->post('services_address');
  $service_abailble       = $this->input->post('service_abailble');




  if(isset($user_id))
  {




    $post_data = array();

     if(!empty($name))
     {
      $post_data['name'] = $name;
     }


     if(!empty($mname))
     {
      $post_data['mname'] = $mname;
     }


     if(!empty($lname))
     {
      $post_data['lname'] = $lname;
     }

      if(!empty($email))
     {
      $post_data['email'] = $email;
     }

      if(!empty($mobile))
     {
      $post_data['mobile'] = $mobile;
     }

      if(!empty($password))
     {
      $post_data['password'] = $password;
     }

     if(!empty($services_address))
     {
      $post_data['services_address'] = $services_address;
     }

      if(!empty($service_abailble))
     {
      $post_data['service_abailble'] = $service_abailble;
     }





    if(!empty($_FILES['images']['name']))
    {


      $image = $this->Api_Model->select_single_row('users','id',$user_id);




      // if($image->image && file_exists('assets/images/users/'.$image->image))
     //  {
     //  unlink('assets/images/users/'.$image->image);


     //  }


      $image1  = $this->imageUpload('images','assets/images/users/');



      $post_data['images'] = $image1;


    }




     if(sizeof($post_data)>0){
       
       
       $wheredata = array(
         'id' => $user_id
         );

       $update = $this->Api_Model->update($wheredata,'users',$post_data);


      if($update)
      {
      $json['result'] = "true";
      $json['msg']    = "profile updated successfully";
      
      }else{
      $json['result']  = "false";
      $json['msg']     = "Something went wrong.";
      }


     }
     else{

      $json['result'] = "true";
      $json['msg']    = "profile updated successfully";
      


     }


  }
  else
  {
    $json['result'] = 'false';
    $json['msg']    = 'parameter required user_id,optional(name,mname,lname,email,mobile,password,services_address,service_abailble,image)';
  }



  echo json_encode($json);
}







public function get_profiles()
{
 $user_id = $this->input->post('user_id');     
 if(isset($user_id))
 {
  $wheredata = array('field'=>'users.*',
       'table'=>'users',
       'where'=>array('id' => $user_id),
      );
  $row = $this->Api_Model->getAllDataRow($wheredata);       
  if($row)
  {
    $json['result'] = 'true';
    $json['msg']    = 'All Data';
    $json['path']   = base_url().'assets/images/users/';
    $json['data']   = $row;
  }
  else
  {
    $json['result'] = 'false';
    $json['msg']    = 'Invalid user_id';
  }
 }
 else
 {
   $json['result'] = 'false';
   $json['msg']    = 'parameter required user_id';
 }   
 
 echo json_encode($json);
}
  
  
  
  
public function updates_profile()
{
  $user_id        = $this->input->post('user_id');
  $name           = $this->input->post('name');
  $email          = $this->input->post('email');
  $phone          = $this->input->post('phone');
  $dob            = $this->input->post('dob');
  $profession_id  = $this->input->post('profession_id');


  if(isset($user_id))
  {

    $post_data = array();
     if(!empty($name))
     {
      $post_data['name'] = $name;
     }

     if(!empty($email))
     {
      $post_data['email'] = $email;
     }

     if(!empty($phone))
     {
      $post_data['phone'] = $phone;
     }

     if(!empty($dob))
     {
      $post_data['dob'] = date('Y-m-d');
     }


     if(!empty($profession_id))
     {
      $post_data['profession_id'] = $profession_id;
     }

    if(!empty($_FILES['image']['name']))
    {

      $image = $this->Api_Model->select_single_row('users','id',$user_id);




      // if($image->image && file_exists('assets/images/users/'.$image->image))
     //  {
     //  unlink('assets/images/users/'.$image->image);


     //  }


      $image1  = $this->imageUpload('image','assets/images/users/');
      $post_data['image'] = $image1;
    }

     if(sizeof($post_data)>0){         
       
       $wheredata = array(
         'id' => $user_id
         );
       $update = $this->Api_Model->update($wheredata,'users',$post_data);
      if($update)
      {
      $json['result'] = "true";
      $json['msg']    = "profile updated successfully";
      
      }else{
      $json['result']  = "false";
      $json['msg']     = "Something went wrong.";
      }

     }
     else{

      $json['result'] = "true";
      $json['msg']    = "profile updated successfully";

     }

     }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required user_id,optional(name,email,phone,profession_id,dob,image)';
    }

  echo json_encode($json);
}
  


  public function add_product() 
  {  
 
    $title           =$this->input->post('title');
    $description     =$this->input->post('description');
    $price           =$this->input->post('price');
    $qty             =$this->input->post('qty');
    $unit            =$this->input->post('unit');
    $price_per_unit  =$this->input->post('price_per_unit');
    
    if(isset($title) && isset($description) && isset($price) && isset($qty) && isset($unit) && isset($price_per_unit)){

    
    $post_data = array(
     
      'title'=>$title,
      'description'=>$description,
      'price'=>$price,
      'qty'=>$qty,
      'unit'=>$unit,
      'price_per_unit'=>$price_per_unit,
      'type'=>"electronic",
      
    );
    
     $res = $this->Api_Model->insertAllData('products',$post_data);
     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'Products Added Successfully';
         }

     else{
       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';

         }     
    
         }
  
     else{
    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required title,description,price,qty,unit,price_per_unit';
       }
  
     echo json_encode($json);
  
   }



    public function get_my_product()
     {
        $user_id = $this->input->post('user_id');
        
        if(isset($user_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'products',
             'where'=>array('id' => $user_id),             
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All Data";
          $json['path']    = base_url()."assets/images/product/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No Data";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required user_id";
        }        
        
      echo json_encode($json);
    }



    public function delete_my_product()
     {
        $user_id = $this->input->post('user_id');
        
        if(isset($user_id))
        {
            $result = $this->Api_Model->deleteData('products','id',$user_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "Product Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "user_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required user_id";
        }
        
       echo json_encode($json);
    }




    public function update_product()
     {
        $user_id     = $this->input->post('user_id');
        $title          = $this->input->post('title');
        $description  = $this->input->post('description');
        $price       = $this->input->post('price');
        $qty        = $this->input->post('qty');
        $unit   = $this->input->post('unit');
        $price_per_unit      = $this->input->post('price_per_unit');
        
        
       if(isset($user_id))
        {
            $post_data = array();

             if(!empty($title))
             {
                $post_data['title'] = $title;
             }
             
             if(!empty($description))
             {
                $post_data['description'] = $description;
             }
             
             if(!empty($price))
             {
                $post_data['price'] = $price;
             }
             
             if(!empty($qty))
             {
                $post_data['qty'] = $qty;
             }
             
             if(!empty($unit))
             {
                $post_data['unit'] = $unit;
             }
             
             if(!empty($price_per_unit))
             {
                $post_data['price_per_unit'] = $price_per_unit;
             }
             
     

       if(sizeof($post_data)>0){

           $wheredata = array(
               'id' => $user_id
               );

       $update =  $this->Api_Model->update($wheredata,'products',$post_data);


        if($update)
        {
          $json['result'] = "true";
          $json['msg']    = "product updated successfully";
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "Something went wrong.";
        }

         }
         else
         {
          $json['result'] = "true";
          $json['msg']    = "product updated successfully";
         }

        }
        else
        {

        $json['result']  = "false";
        $json['msg']     = "parameter required user_id,optional(title,description,price,qty,unit,price_per_unit)";
        }        
        
      echo json_encode($json);        
    }



  public function add_services() 
  {  
 
    $title             =$this->input->post('title');
    $request_price     =$this->input->post('request_price');
    $equipment_used    =$this->input->post('equipment_used');
    $no_of_workers     =$this->input->post('no_of_workers');
    $working_time      =$this->input->post('working_time');
    $description       =$this->input->post('description');
    
    if(isset($title) && isset($request_price) && isset($equipment_used) && isset($no_of_workers) && isset($working_time) && isset($description)){

    
    $post_data = array(
     
      'title'=>$title,
      'request_price'=>$request_price,
      'equipment_used'=>$equipment_used,
      'no_of_workers'=>$no_of_workers,
      'working_time'=>$working_time,
      'description'=>$description,
      'type'=>"realstate",
      
    );                
    
     $res = $this->Api_Model->insertAllData('services',$post_data);
     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'services Added Successfully';
         }

     else{
       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';

         }     
    
         }
  
     else{
    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required title,request_price,equipment_used,no_of_workers,working_time,description';
       }
  
     echo json_encode($json);
  
   }


    public function get_my_services()
     {
        $user_id = $this->input->post('user_id');
        
        if(isset($user_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'services',
             'where'=>array('id' => $user_id),             
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All Data";
          // $json['path']    = base_url()."assets/images/product/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No Data";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required user_id";
        }        
        
      echo json_encode($json);
    }



    public function update_services()
     {
        $user_id     = $this->input->post('user_id');
        $title          = $this->input->post('title');
        $request_price  = $this->input->post('request_price');
        $equipment_used       = $this->input->post('equipment_used');
        $no_of_workers        = $this->input->post('no_of_workers');
        $working_time   = $this->input->post('working_time');
        $description      = $this->input->post('description');
        
        
       if(isset($user_id))
        {
            $post_data = array();

             if(!empty($title))
             {
                $post_data['title'] = $title;
             }
             
             if(!empty($request_price))
             {
                $post_data['request_price'] = $request_price;
             }
             
             if(!empty($equipment_used))
             {
                $post_data['equipment_used'] = $equipment_used;
             }
             
             if(!empty($no_of_workers))
             {
                $post_data['no_of_workers'] = $no_of_workers;
             }
             
             if(!empty($working_time))
             {
                $post_data['working_time'] = $working_time;
             }
             
             if(!empty($description))
             {
                $post_data['description'] = $description;
             }
             
     
       if(sizeof($post_data)>0){

           $wheredata = array(
               'id' => $user_id
               );

       $update =  $this->Api_Model->update($wheredata,'services',$post_data);


        if($update)
        {
          $json['result'] = "true";
          $json['msg']    = "services updated successfully";
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "Something went wrong.";
        }

         }
         else
         {
          $json['result'] = "true";
          $json['msg']    = "services updated successfully";
         }

        }
        else
        {

        $json['result']  = "false";
        $json['msg']     = "parameter required user_id,optional(title,request_price,equipment_used,no_of_workers,working_time,description)";
        }        
        
      echo json_encode($json);        
    }


       public function delete_my_service()
     {
        $user_id = $this->input->post('user_id');
        
        if(isset($user_id))
        {
            $result = $this->Api_Model->deleteData('services','id',$user_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "services Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "user_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required user_id";
        }
        
       echo json_encode($json);
    }



    public function add_package() 
  {  
 
    $package_name           =$this->input->post('package_name');
    $start_date     =$this->input->post('start_date');
    $end_date           =$this->input->post('end_date');
    $vanue_address             =$this->input->post('vanue_address');
    $guest_capycity            =$this->input->post('guest_capycity');
    $vanue_price  =$this->input->post('vanue_price');
    $offfer_price  =$this->input->post('offfer_price');
    $add_service  =$this->input->post('add_service');
    $add_product  =$this->input->post('add_product');
    $add_once_price  =$this->input->post('add_once_price');
    
    if(isset($package_name) && isset($start_date) && isset($end_date) && isset($vanue_address) && isset($guest_capycity) && isset($vanue_price) && isset($offfer_price) && isset($add_service) && isset($add_product) && isset($add_once_price)){

    
    $post_data = array(
     
      'package_name'=>$package_name,
      'start_date'=>$start_date,
      'end_date'=>$end_date,
      'vanue_address'=>$vanue_address,
      'guest_capycity'=>$guest_capycity,
      'vanue_price'=>$vanue_price,
      'offfer_price'=>$offfer_price,
      'add_service'=>$add_service,
      'add_product'=>$add_product,
      'add_once_price'=>$add_once_price,
      
      
    );


      if(!empty($_FILES['image']['name']))
       {

        $image1  = $this->imageUpload('1mage','assets/images/avatars/');
        $post_data['image'] = $image1;
       }

       if(!empty($_FILES['image2']['name']))
       {

        $image2  = $this->imageUpload('1mage2','assets/images/avatars/');
        $post_data['image2'] = $image2;
       }

       if(!empty($_FILES['image3']['name']))
       {

        $image3  = $this->imageUpload('1mage3','assets/images/avatars/');
        $post_data['image3'] = $image3;
       }

       if(!empty($_FILES['image4']['name']))
       {

        $image4  = $this->imageUpload('1mage4','assets/images/avatars/');
        $post_data['image4'] = $image4;
       }
       
    
     $res = $this->Api_Model->insertAllData('packages',$post_data);
     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'Package Create Successfully';
         }

     else{
       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';

         }     
    
         }
  
     else{
    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required package_name,start_date,end_date,vanue_address,guest_capycity,vanue_price,offfer_price,add_service,add_product,add_once_price,image,image2,image3,imgage4';
       }
  
     echo json_encode($json);
  
   }



    



  // Api end deepesh



/*start get profile*/


  public function vendor_get_profile()
   {
     $user_id = $this->input->post('user_id');
     
     if(isset($user_id))
     {
      $wheredata = array('field'=>'vendor.*',

           'table'=>'vendor',

           'where'=>array('vendor_id' => $user_id),

          );



      $row = $this->Api_Model->getAllDataRow($wheredata); 
      
      if($row)
      {
        
        $wheredata_1 = array('field'=>'kyc_doc.profile_pic',

           'table'=>'kyc_doc',

           'where'=>array('vendor_id' => $user_id),

          );



        $row_1 = $this->Api_Model->getAllDataRow($wheredata_1); 
        
        
        if($row_1)
        {
          $row->profile_pic = $row_1->profile_pic;
        }
        else
        {
          $row->profile_pic = "";
        }
        
        
        
        $json['result'] = 'true';
        $json['msg']    = 'All Data';
        $json['path']   = base_url().'assets/images/users/';
        $json['data']   = $row;
      }
      else
      {
        $json['result'] = 'false';
        $json['msg']    = 'Invalid user_id';
      }
     }
     else
     {
       $json['result'] = 'false';
       $json['msg']    = 'parameter required user_id';
     }
     
     
     echo json_encode($json);
   }



/*end get profile*/


   public function logout_old()
   {
     $user_id = $this->input->post('user_id');

     if(isset($user_id))
     {
      extract($_POST);

      $wheredata = array(
        'user_id' => $user_id
      );

      $data = array(
        'fcm_id' => "",
        'verify_otp' => 0
        );

      $result = $this->Api_Model->update($wheredata,'users',$data);

     if($result)
     {

     $json['result'] = 'true';
     $json['msg']    = 'Successfully logout.';

     }
     else
     {
       $json['result'] = 'false';
       $json['msg']    = 'something went wrong';
     }


     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id";
     }


    echo json_encode($json);
   }



 public function logouts(){
  extract($_POST);
  if(isset($userid))
  {
    $this->db->where('users.id',$userid);
    $this->db->update('users', array('fcm_id'=>''));
    $json['result'] = "true";
    $json['msg'] = "logout successfully.";
    echo json_encode($json);
    exit;
  }else{
    $json['result'] = "false";
    $json['msg'] = "required parameters: userid";
    echo json_encode($json);
    exit;
  }

  }


  public function get_category()
  {
    $wheredata = array('field'=>'id,name',

       'table'=>'category',

       'where'=>array(),

       'order_by'=>'id desc'

       );


    $result = $this->Api_Model->getAllData($wheredata);
    if($result)
    {
    foreach($result as $value)
    {
      $category_id=$value->id;
      $name=$value->name;
      // $icon=$value->icon;
     $query=  $this->db->select('COUNT(*) as count')
                ->where('category',$category_id)
                ->get('listing')
                ->row();
        $data=array('id'=>$category_id,
                'name'=>$name,
            'count'=>$query->count);

     $newdata[]=$data;

    }

     $json['result'] = "true";
      $json['msg']    = "All Category";

 $json['data']   = $newdata;
      echo json_encode($json);
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "No Category";
    }
  }
  
  
  
  
  
  
  
  
  
   public function get_brands_previous()
  {
    
  
   
      $wheredata = array('field'=>'id,maker,Logo',

       'table'=>'makes',

       'where'=>array(),

       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);

    if($result)
    {
      
      foreach($result as $value)
      {
        $id    = $value->id;
        $maker = $value->maker;
        $Logo  = $value->Logo;
        
     
        
        
        
        $resultnew[] = array(
          
          'id' => $id,
          'maker' => $maker,
          'Logo' => $Logo,
         
          
          
          );
      }
      
      
      
      $json['result'] = "true";
      $json['msg']    = "All Brands";
      $json['path']   = base_url()."assets/images/brand/";
      $json['data']   = $resultnew;
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "No Brands";
    }

    
    
    
    

    echo json_encode($json);
  }

  
  
  
  
  
  
   public function get_brands()
  {
    
  
   
      $wheredata = array('field'=>'id,maker,Logo',

       'table'=>'makes',

       'where'=>array(),

       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);

    
    $FINAL_RESULT = array();
     

    if($result)
    {
      
      foreach($result as $value)
      {
        $id    = $value->id;
        $maker = $value->maker;
        $Logo  = $value->Logo;
        
        
        
        $TEMP['id']    = $id;
        $TEMP['maker'] = $maker;
        $TEMP['Logo']  = $Logo;
        
        
        
        $this->db->where("vehicle_models.maker_id",$value->id);
        $this->db->select("vehicle_models.id,
                   vehicle_models.model_name");
        $this->db->from("vehicle_models");
        $dd = $this->db->get();
        
        
         $MODEL = array();
        
         if($dd->num_rows()>0)
         {
           foreach($dd->result() as $value)
           {
             $TEMP_USER['id']         = $value->id;
             $TEMP_USER['model_name'] = $value->model_name;
             
             
             array_push($MODEL,$TEMP_USER);
           }
         }
         else
         {
           $MODEL = array();
         }
         
         
         
         
          $TEMP['model'] = $MODEL;
          
          
          array_push($FINAL_RESULT,$TEMP);
        
     
       
      }
      
      
      
      $json['result'] = "true";
      $json['msg']    = "All Brands";
      $json['path']   = base_url()."assets/images/brand/";
      $json['data']   = $FINAL_RESULT;
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "No Brands";
    }

    
    
    
    

    echo json_encode($json);
  }
  
  
  
  
  
    public function get_home_brands()
  {
    
    $user_id = $this->input->post('user_id');
    
    
    if(isset($user_id))
    {
      $wheredata = array('field'=>'id,maker,Logo',

       'table'=>'makes',

       'where'=>array(),

       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);

    if($result)
    {
      
      foreach($result as $value)
      {
        $id    = $value->id;
        $maker = $value->maker;
        $Logo  = $value->Logo;
        
        
        
        $this->db->where("vendor_id !=",$user_id);
        $this->db->where("maker_id",$id);
        $this->db->select("id");
        $this->db->from("vehicles");
        $counts = $this->db->get();
        
        
        
        if($counts->num_rows() > 0)
        {
          $vehicle_count = $counts->num_rows();
        }
        else
        {
          $vehicle_count = 0;
        }
        
        
        
        
        $resultnew[] = array(
          
          'id' => $id,
          'maker' => $maker,
          'Logo' => $Logo,
          'vehicle_count' => $vehicle_count,
          
          
          );
      }
      
      
      
      $json['result'] = "true";
      $json['msg']    = "All Brands";
      $json['path']   = base_url()."assets/images/brand/";
      $json['data']   = $resultnew;
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "No Brands";
    }

    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id";
    }
    
    
    

    echo json_encode($json);
  }
  
  
  
  public function get_home_slider(){
    
    
    $wheredata = array('field' => '*',
    'table' => 'home_slider',
    'where' => array(),
    'order_by' => 'id desc'
    
    
    
    
    );
    
    $result = $this->Api_Model->getAllData($wheredata);
    
    if($result){
      
      $json['result'] = "true";
      $json['msg'] = "Get Successfully";
      $json['path'] = base_url()."assets/images/home_banner/";
      $json['data'] = $result;
      
      
      
    }
  
    else{
      
      $json['result'] = "false";
      $json['msg'] = "No data found";
    }
    
  
    echo json_encode($json);
   
    
     
  }
  
  
  
  



  
     public function home_banner()
  {
    $wheredata = array('field'=>'*',

       'table'=>'home_banner',

       'where'=>array('type' => 'distributor'),
       
       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);
    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['path']    = base_url()."assets/images/home_banner/";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    
    
    echo json_encode($json);
  }
  
  


   public function get_Models()
   {
     $brand_id = $this->input->post('brand_id');  /*make means brand*/
     
     if(isset($brand_id))
     {
       $wheredata = array('field'=>'id,model_name,model_image',

       'table'=>'vehicle_models',

       'where'=>array('maker_id' => $brand_id),

       'order_by'=>'id desc'

       );


       $result = $this->Api_Model->getAllData($wheredata);


       if($result)
      {
      $json['result'] = "true";
      $json['msg']    = "All model";
      $json['data']   = $result;
      }
      else
      {
      $json['result'] = "false";
      $json['msg']    = "No model avelable";
     }


     }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "parameter required brand_id";
     }

     echo json_encode($json);
   }
   
   
  public function get_Varient_by_models()
   {
     $model_id = $this->input->post('model_id');  /*make means brand*/
     $maker_id = $this->input->post('maker_id'); 
     
     if(isset($model_id) && isset($maker_id))
     {
       $wheredata = array('field'=>'id,variation_name',

       'table'=>'veriations',

       'where'=>array('maker_id' => $model_id),
        'where'=>array('model_id' => $model_id),

       'order_by'=>'id desc'

       );


       $result = $this->Api_Model->getAllData($wheredata);


       if($result)
      {
      $json['result'] = "true";
      $json['msg']    = "All varient";
      $json['data']   = $result;
      }
      else
      {
      $json['result'] = "false";
      $json['msg']    = "No varient avelable";
     }


     }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "parameter required maker_id,model_id";
     }

     echo json_encode($json);
   } 
   
   
   
   
   public function vendor_login()
  {   
  extract($_POST);
  if(isset($vendor_email) && isset($password))
  {
    $result =  $this->Api_Model->check_credentials($vendor_email);
    if($result)
    {

    

    if (md5($password) == $result->vendor_password) 
    {
    

       
        $wheredata=array('id'=>$result->vendor_id);
         
        // $res=$this->Api_Model->updates('vendor',$data,$wheredata);
        $result =  $this->Api_Model->check_credentials($vendor_email);

        $data['result'] = "true";

        $data['data'] = $result;

        $data['msg']    = 'Successfully logged in.';
       
      


    }else{

      $data['result'] = "false";

      $data['msg']    = 'Invalid Password';

    }

   



    }else{

    $data['result'] = "false";

    $data['msg']    = 'Invalid email';

    } 

  }else{

    $data['result'] = 'false';

    $data['msg']    = 'Please provide parameters(vendor_email,password)';            

  }          

 /*   echo json_encode($data);*/
  echo json_encode($data);
  exit;

  }

/*-----------------start anju dealrword--------------------*/













  public function followUnfollow(){

    extract($_POST);

    if(!empty($user_id) && !empty($to_user_id))
    {

    $follows = $this->db->query("SELECT * FROM follows WHERE follows. user_id='$user_id' AND follows.to_user_id='$to_user_id'");

    if($follows->num_rows()>0){ // already followed

      $this->db->where("follows.user_id", $user_id);
      $this->db->where("follows.to_user_id", $to_user_id);
      $this->db->delete("follows");

      $json['result'] = "true";
      $json['msg']    = "Un Followed";
      echo json_encode($json);
      exit;

    }
    else{ //add to new
      $this->db->insert("follows", array('user_id'=>$user_id, 'to_user_id'=>$to_user_id));
      $last_id = $this->db->insert_id();
      $json['result'] = "true";
      $json['msg']    = "Followed";
      $json['id']    = $last_id;
      echo json_encode($json);
      exit;

    }

    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "Req: user_id,to_user_id";
      echo json_encode($json);
      exit;

    }




  }
  

public function get_folloing_userslist(){
extract($_POST);

if(!empty($user_id)){

  $userdata = $this->db->query("SELECT * FROM users WHERE users.id='$user_id'");

  if($userdata->num_rows()==0){$json['result'] = "false"; $json['msg'] = "Invalid User Id";
 
  echo json_encode($json);exit;}

  /*  $data = $this->CommanModel->get_folloing_userslist($user_id);*/
   $data = $this->CommanModel->get_folloing_userslist($user_id);




  $json['result'] = "true";
  $json['data'] = $data->result();
  echo json_encode($json);
  exit;
}
else{

  $json['result'] = "false";
  $json['msg'] = "Please give parameters(user_id)";
  echo json_encode($json);
  exit;

}
}



public function get_follower_userslist(){
extract($_POST);

if(!empty($user_id)){

  $userdata = $this->db->query("SELECT * FROM users WHERE users.id='$user_id'");

  if($userdata->num_rows()==0){$json['result'] = "false"; $json['msg'] = "Invalid User Id";echo json_encode($json);exit;}

  $data = $this->CommanModel->get_follower_userslist_samsi($user_id);

  $json['result'] = "true";
  $json['data'] = $data->result();
  echo json_encode($json);
  exit;
}
else{

  $json['result'] = "false";
  $json['msg'] = "Please give parameters(user_id)";
  echo json_encode($json);
  exit;

}
}







  public function verify_otp()
  {
    $user_id    = $this->input->post('user_id');
    $otp        = $this->input->post('otp');

    if(isset($user_id) && isset($otp))
    {
      $wheredata = array('field'=>'id',

       'table'=>'users',

       'where'=>array('id'=>$user_id,'otp'=>$otp),

      );



       $result = $this->Api_Model->getAllDataRow($wheredata);


      if($result)
       {

        $verify_otp = array(
         'verify_otp' => 1,
        //  'form_status' => 1
         );

       $this->db->where("users.id",$user_id);
       $this->db->update("users",$verify_otp);


       $json['result'] = "true";
       $json['msg']    = "Otp verify Successfully.";
       $json['data']   = $this->Api_Model->select_single_row('users','id',$user_id);


       }
       else
       {

       $json['result'] = "false";
       $json['msg']    = "sorry otp not valid.";

      }


    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id,otp";
    }


    echo json_encode($json);
  }







//   public function resend_otp()
//   {
//     $mobile = $this->input->post('mobile');

//     if(isset($mobile))
//     {



//       $wheredata1 = array('field'=>['id','mobile'],

//           'table'=>'users',

//           'where'=>array('mobile'=>$mobile)

//       );


//       $is_exist = $this->Api_Model->getAllDataRow($wheredata1);


//       if($is_exist)
//       {
//          $otp = rand(1000,9999);


//       $post_data = array(
//         'otp'=>$otp,
//         );

//       $this->db->where('mobile',$mobile);
//       $result = $this->db->update('users',$post_data);

//       if($result)
//       {

//       //   $ccc =  $this->Driverapi_Model->select_single_row('drivers','phone',$mobile);





//         // if($ccc)
//         // {
//         //         $for_sms_mobile = $mobile;
//         //         sent_otp($ccc->name,$otp,$for_sms_mobile);


//         // }



//           $wheredata = array('field'=>'id,otp,mobile',

//           'table'=>'users',

//           'where'=>array('mobile'=>$mobile),

//           );



//           $row = $this->Api_Model->getAllDataRow($wheredata);

//          $json['result'] = "true";
//          $json['msg']    = "Resent successfully";
//          $json['data']   = $row;


//       }
//       else
//       {

//         $json['result'] = "false";
//         $json['msg']    = "Something went wrong. Please try later.";

//       }
//       }
//       else
//       {
//         $json['result'] = "false";
//         $json['msg']    = "Mobile number doesnt exist";
//       }







//     }
//     else
//     {
//       $json['result'] = "false";
//       $json['msg']    = "parameter required mobile";
//     }

//     echo json_encode($json);
//   }



 public function resend_otp_old2()
  {
    $phone= $this->input->post('phone');

    if(isset($phone))
    {



      $wheredata1 = array('field'=>['id','phone'],

           'table'=>'users',

           'where'=>array('phone'=>$phone)

      );


      $is_exist = $this->Api_Model->getAllDataRow($wheredata1);


      if($is_exist)
      {
         $otp = rand(1000,9999);


      $post_data = array(
        'otp'=>$otp,
        );

      $this->db->where('phone',$phone);
      $result = $this->db->update('users',$post_data);

      if($result)
      {
          
          
          

        //  $ccc =  $this->Driverapi_Model->select_single_row('drivers','phone',$mobile);

        // if($ccc)
        // {
        //         $for_sms_mobile = $mobile;
        //         sent_otp($ccc->name,$otp,$for_sms_mobile);


        // }
        
        
        
        
        
        



          $wheredata = array('field'=>'id,otp,phone',

          'table'=>'users',

          'where'=>array('phone'=>$phone),

           );



           $row = $this->Api_Model->getAllDataRow($wheredata);

         $json['result'] = "true";
         $json['msg']    = "Resent successfully";
         $json['data']   = $row;


      }
      else
      {

        $json['result'] = "false";
        $json['msg']    = "Something went wrong. Please try later.";

      }
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "phone number doesnt exist";
      }







    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required phone";
    }

    echo json_encode($json);
  }







  public function update_type()
  {
    $user_id = $this->input->post('user_id');
    $type    = $this->input->post('type');

    if(isset($user_id) && isset($type))
    {
      $wheredata = array(
        'id' => $user_id
      );

    

      $result = $this->Api_Model->update($wheredata,'users',$data);


      if($result)
      {
        $json['result'] = "true";
        $json['msg']    = "Type updated successfully";
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "type not update";
      }

    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id,type(home_owner or service_provider or material_supplier)";
    }


    echo json_encode($json);
  }







  



  public function get_subcategory()
  {

    $cat_id = $this->input->post('cat_id');

    if(isset($cat_id))
    {
       $category_id = 1;


      $wheredata = array('field'=>'id,name',

       'table'=>'signup_subcategory',

       'where'=>array('signup_subcategory.signup_cat_id' => $category_id),

       'order_by'=>'id desc'

       );



       $result = $this->Api_Model->getAllData($wheredata);

       if($result)
       {

         $this->db->where("users.id",$user_id);
         $this->db->select("users.signup_category_updated");
         $this->db->from("users");
         $datav = $this->db->get()->row();


         $json['result'] = "true";
         $json['msg']    = "All Data";
         $json['category_status']    = $datav;
         $json['data']   = $result;
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "No Data";
       }

    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id";
    }





    echo json_encode($json);
  }










  public function verify_profile()
  {


    $user_id  = $this->input->post('user_id');
    $fname    = $this->input->post('fname');
    $lname    = $this->input->post('lname');





    if(isset($user_id) && isset($fname) && isset($lname) && isset($_FILES['image']['name']))
     {

      $post_data = array();








       if(!empty($_FILES['image']['name']))
       {


        $image1  = $this->imageUpload('image','assets/images/users/');



        $post_data['image'] = $image1;


       }


       $post_data['fname']          = $fname;
       $post_data['lname']          = $lname;
       $post_data['verify_profile'] = 1;






       if(sizeof($post_data)>0){

         $wheredata = array(
        'id' => $user_id
         );



        $this->Api_Model->update($wheredata,'users',$post_data);




        $wheredata1 = array('field'=>['id','fname','lname','image'],

           'table'=>'users',

           'where'=>array('id'=>$user_id)

          );


        $row = $this->Api_Model->select_single_row_specific($wheredata1);




        $json['result'] = "true";
        $json['msg']    = "profile verified";
        $json['data']   = $row;

       }
       else{

         $wheredata1 = array('field'=>['id','fname','lname','image'],

           'table'=>'users',

           'where'=>array('id'=>$user_id)

          );


        $row = $this->Api_Model->select_single_row_specific($wheredata1);

        $json['result'] = "true";
        $json['msg']    = "profile verified";
        $json['data']   = $row;


       }






  }
  else
  {
    $json['result'] = "false";
    $json['msg']    = "parameter required user_id,fname,lname,image";
  }


  echo json_encode($json);

 }













  public function get_single_profile()
   {
     $own_id  = $this->input->post('own_id');
     $user_id = $this->input->post('user_id');

     if(isset($user_id) && isset($own_id))
     {
      $wheredata = array(
        'users.id'  => $user_id
        
      );

      //join
      // [
      //         'table'  =>'city',
      //         'on'  =>'city.id = users.city_id',
      //         'right_left' => 'left'
      // ],
      ///
      

      $followed_count  = $this->db->query("SELECT * FROM follows WHERE follows.user_id='$user_id'")->num_rows();
      $following_count = $this->db->query("SELECT * FROM follows WHERE follows.user_id='$user_id  '")->num_rows();

      $joins = array(
      [
          'table'  =>'category',
          'on'  =>'category.id = users.category_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'city',
          'on'  =>'city.id = users.city_id',
          'right_left' => 'left'
      ]);

             $select_fields = array(
            'users.id',
            'users.name',
            'users.fname',
            'users.lname',
            'ifnull(users.type,"") as type',
            'users.image',
            'ifnull(city.id,"") as city_id',
            'ifnull(city.name,"") as city_name',
            'users.email',
            'users.mobile',
            'users.pincode',
            'users.address',
            'users.language',
            'users.company',
            'users.experience',
            'users.about',
            'users.looking_for_workers',
            'ifnull(category.name,"") as category_name',
            'users.mobile_hidden',

            'about',
            'location',
            'social_media',
            'category_id',
            'second_category_id',
            'users.experience'
            );


       $row = $this->Api_Model->getSingleRowWithJoin('users',$select_fields,$joins,$wheredata);


  

     
      
      
      
      $this->db->where("follows.user_id",$user_id);
      $this->db->select("follows.id");
      $this->db->from("follows");
      $exist = $this->db->get();
      
      if($exist->num_rows()>0)
      {
        $total_following = $exist->num_rows();
      }
      else
      {
        $total_following = 0;
      }
      
      
      
      
      
      $this->db->where("follows.to_user_id",$user_id);
      $this->db->select("follows.id");
      $this->db->from("follows");
      $exist_2 = $this->db->get();
      
      if($exist_2->num_rows()>0)
      {
        $total_followers = $exist_2->num_rows();
      }
      else
      {
        $total_followers = 0;
      }
      
      
      
      
      
      $this->db->where("follows.user_id",$own_id);
      $this->db->where("follows.to_user_id",$user_id);
      $this->db->select("follows.id");
      $this->db->from("follows");
      $is_followst = $this->db->get()->row();
      
      
      if($is_followst)
      {
        $is_follows = "1";
      }
      else
      {
        $is_follows = "0";
      }
      
      
      
      

       if($row)
       {
         $result = array(
         'id' => $row->id,
         'name' => $row->name,
         'fname' => $row->fname,
         'lname' => $row->lname,
         'type' => $row->type,
         'image' => $row->image,
         'city_id' => $row->city_id,
         'city_name' => $row->city_name,
         'email' => $row->email,
         'mobile' => $row->mobile,
         'pincode' => $row->pincode,
         'address' => $row->address,
         'language' => $row->language,
         'company' => $row->company,
         'about' => $row->about,
         'looking_for_workers' => $row->looking_for_workers,
         'category_name' => $row->category_name,
         'about'=> $row->about,
         'mobile_hidden'=> $row->mobile_hidden,

         'experience'=> $row->experience,
         'location'=> $row->location,
         'social_media'=> $row->social_media,
         'language'=> $row->language,
         'category_id'=> $row->category_id,
         'second_category_id'=> $row->second_category_id,
         'followedCount' => $followed_count,
         'followingCount' => $following_count,
         'experience'=>$row->experience,
         'total_following'=>$total_following,
         'total_followers'=>$total_followers,
         'is_follows'=>$is_follows

         );


         $json['result'] = "true";
         $json['msg']    = "User Details";
         $json['path']   = base_url()."assets/images/users/";
         $json['data']   = $result;
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "user_id invalid";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,own_id";
     }

     echo json_encode($json);
   }










  public function update_details()
   {
     $user_id            = $this->input->post('user_id');
     $looking_for_worker = $this->input->post('looking_for_worker');
     $about              = $this->input->post('about');
     $company            = $this->input->post('company');
  //   $language           = $this->input->post('language');
     $address            = $this->input->post('address');
     $pincode            = $this->input->post('pincode');
     $email              = $this->input->post('email');
     $fb_link            = $this->input->post('fb_link');
     $insta_link         = $this->input->post('insta_link');
     $utube_link         = $this->input->post('utube_link');
     $experience         = $this->input->post('experience');
     $social_media       = $this->input->post('social_media');
     $service_provided   = $this->input->post('service_provided');

     if(isset($user_id))
     {
         $post_data = array();




         if(!empty($looking_for_worker))
         {
           $post_data['looking_for_workers'] = $looking_for_worker;
         }

         if(!empty($about))
         {
           $post_data['about'] = $about;
         }

         if(!empty($company))
         {
           $post_data['company'] = $company;
         }

        //  if(!empty($language))
        //  {
        //   $post_data['language'] = $language;
        //  }

         if(!empty($address))
         {
           $post_data['address'] = $address;
         }

         if(!empty($pincode))
         {
           $post_data['pincode'] = $pincode;
         }

         if(!empty($email))
         {
           $post_data['email'] = $email;
         }

         if(!empty($fb_link))
         {
           $post_data['fb_link'] = $fb_link;
         }

         if(!empty($insta_link))
         {
           $post_data['insta_link'] = $insta_link;
         }

         if(!empty($utube_link))
         {
           $post_data['youtube_link'] = $utube_link;
         }


          if(!empty($experience))
         {
           $post_data['experience'] = $experience;
         }



         if(!empty($social_media))
         {
           $post_data['social_media'] = $social_media;
         }


         if(!empty($service_provided))
         {
           $post_data['service_provided'] = $service_provided;
         }



         $post_data['updated_date'] = date('Y-m-d H:i:s',time());


       if(sizeof($post_data)>0)
       {



         $whereas = array(
           'id' => $user_id
           );




         $update =  $this->Api_Model->updateData_tt('users',$post_data,$whereas);




         if($update)
         {
          $json['result'] = "true";
          $json['msg']    = "User Detail updated!";
         }
         else
         {
          $json['result']  = "false";
          $json['msg']     = "Something went wrong.";
         }


       }
       else
       {

        $json['result'] = "true";
        $json['msg']    = "User Detail updated!";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,optional(looking_for_worker,about,company,address,pincode,email,fb_link,insta_link,utube_link,experience,social_media,service_provided)";
     }


     echo json_encode($json);

   }




















   public function update_extra_detail()
   {
     $user_id       = $this->input->post('user_id');
     $about       = $this->input->post('about');
     $location       = $this->input->post('location');
     $social_media       = $this->input->post('social_media');
     $language       = $this->input->post('language');
     $catid       = $this->input->post('catid');
     $subcat       = $this->input->post('subcat');


     if(isset($user_id))
     {
       $post_data = array();


       if(!empty($about))
       {
        $post_data['about'] = $about;
       }
       if(!empty($location))
       {
        $post_data['location'] = $location;
       }
       if(!empty($social_media))
       {
        $post_data['social_media'] = $social_media;
       }
       if(!empty($language))
       {
        $post_data['language'] = $language;
       }
       if(!empty($catid))
       {
        $post_data['city_id'] = $catid;
       }
       if(!empty($subcat))
       {
        $post_data['state_id'] = $subcat;
       }








       if(sizeof($post_data)>0){

         $wheredata = array(
           'id' => $user_id
           );

         $update =  $this->Api_Model->update($wheredata,'users',$post_data);


        if($update)
        {
        $json['result'] = "true";
        $json['msg']    = "Extra Detail updated successfully";
        }
        else
        {
        $json['result']  = "false";
        $json['msg']     = "Something went wrong.";
        }


       }
       else
       {

        $json['result'] = "true";
        $json['msg']    = "profile updated successfully";
       }
     }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id,optional(about,location,social_media,language,catid,subcat)";
     }


     echo json_encode($json);

   }






   public function get_main_category()
   {


     $user_id = $this->input->post('user_id');
     $type    = $this->input->post('type');


     if(isset($user_id) && isset($type))
     {
       
       
       if($type == "service_provider")
       {
         $wheredata = array('field'=>'id,name,image',

         'table'=>'category',

         'where'=>array('type' => $type),

         'order_by'=>'id desc'

        );
       }
       
       if($type == "home_owner")
       {
        $wheredata = array('field'=>'id,name,image',

        'table'=>'category',

        'where'=>array('type' => $type),

        'order_by'=>'id desc'

         );
       }
       
       if($type == "material_supplier")
       {
         $wheredata = array('field'=>'id,name,image',

        'table'=>'category',

        'where'=>array('type' => $type),

        'order_by'=>'id desc'

         );
       }
       
       
       if($type == "all")
       {
         $wheredata = array('field'=>'id,name,image',

        'table'=>'category',

        'where'=>array('type !=' => 'home_owner'),

        'order_by'=>'id desc'

         );
       }
       
       
       
        



    $result = $this->Api_Model->getAllData($wheredata);

     if($result)
     {
      $this->db->where("users.id",$user_id);
      $this->db->select("users.main_category_updated");
      $this->db->from("users");
      $datav = $this->db->get()->row();


      $json['result'] = "true";
      $json['msg']    = "All Category";
      $json['path']   = base_url()."assets/images/cat_image/";
      $json['category_status']    = $datav;
      $json['data']   = $result;
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "No Category";
     }
     }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "Parameter required user_id,type(service_provider or home_owner or material_supplier)";
     }





    echo json_encode($json);
   }






   public function get_main_subcat()
   {
     $cat_id = $this->input->post('cat_id');

     if(isset($cat_id))
     {
       $wheredata = array('field'=>'id,name',

       'table'=>'sub_category',

       'where'=>array('category_id' => $cat_id),

       'order_by'=>'id desc'

       );


       $result = $this->Api_Model->getAllData($wheredata);


       if($result)
      {
      $json['result'] = "true";
      $json['msg']    = "All Sub-Category";
      $json['data']   = $result;
      }
      else
      {
      $json['result'] = "false";
      $json['msg']    = "No Sub-Category";
     }


     }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "parameter required cat_id";
     }

     echo json_encode($json);
   }



   public function subCatArray()
   {
     $user_id    = $this->input->post('user_id');

     if(isset($user_id))
     {
     $sub_cats = $this->db->query("SELECT sub_category.id, sub_category.name FROM users_cat_subcat
                    JOIN sub_category ON sub_category.id=users_cat_subcat.subcategory_id
                     WHERE users_cat_subcat.user_id='$user_id' AND users_cat_subcat.subcategory_id!=0");
     if($sub_cats->num_rows()>0)
     {
       $json['result'] = "true";
       $json['msg']    = "Sub Categories";
       $json['data']   = $sub_cats->result();
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "No Sub Categories Found";
     }



     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "Req: user_id";
     }


     echo json_encode($json);
   }


   public function update_main_catsubcat_new()
   {
     $user_id    = $this->input->post('user_id');
     $cat_id     = $this->input->post('cat_id');
     $subcat_id  = $this->input->post('subcat_id');

     if(isset($user_id) && isset($cat_id) && isset($subcat_id))
     {
       $subcat_id            = $subcat_id;
       $slotidArray          = explode(',', $subcat_id);


     $wheredata = array(
         'id' => $user_id
       );

       $data = array(
         'category_id' => $cat_id,
         'main_category_updated' => 1,

         );
    //'form_status' => 3,
       $result = $this->Api_Model->update($wheredata,'users',$data);




     for($i = 0; $i < count($slotidArray); $i++)
      {



       $data11 = array(
      'user_id'          =>  $user_id,
      'category_id'      =>  $cat_id,
      'subcategory_id'   =>  $slotidArray[$i],
       //  'date'             =>  date('Y-m-d'),
       //  'created_date'     =>  date('Y-m-d H:i:s',time()),
       //  'updated_date'     =>  date('Y-m-d H:i:s',time()),
       );


      $res = $this->Api_Model->insertAllData('users_cat_subcat',$data11);


     }




     if($result)
     {
       $this->db->where("category.id",$cat_id);
       $this->db->select("category.name");
       $this->db->from("category");
       $rts = $this->db->get()->row();


       $json['result'] = "true";
       $json['msg']    = "Category Updated";
       $json['data']   = $rts;
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "something went wrong";
     }



     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,cat_id,subcat_id";
     }


     echo json_encode($json);
   }










  public function get_experience()
  {
    $wheredata = array('field'=>'id,name',

       'table'=>'experience',

       'where'=>array(),

       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);

    if($result)
    {
      $json['result'] = "true";
      $json['msg']    = "All Experience";
      $json['data']   = $result;
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "No Experience";
    }


    echo json_encode($json);
  }







   public function get_second_category()
  {


    $user_id = $this->input->post('user_id');

    if(isset($user_id))
    {
        $wheredata = array('field'=>'id,name,image',

       'table'=>'second_category',

       'where'=>array(),

       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);

    if($result)
    {

         $this->db->where("users.id",$user_id);
         $this->db->select("users.second_category_updated");
         $this->db->from("users");
         $datav = $this->db->get()->row();


        $json['result'] = "true";
        $json['msg']    = "All Category";
        $json['path']   = base_url()."assets/images/cat_image/";
        $json['category_status']    = $datav;
        $json['data']   = $result;
       }
       else
       {
        $json['result'] = "false";
        $json['msg']    = "No Category";
       }
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id";
    }





    echo json_encode($json);
  }







  public function get_second_subcategory()
   {
     $cat_id = $this->input->post('cat_id');

     if(isset($cat_id))
     {
       $wheredata = array('field'=>'id,name',

       'table'=>'second_subcategory',

       'where'=>array('second_category_id' => $cat_id),

       'order_by'=>'id desc'

       );


       $result = $this->Api_Model->getAllData($wheredata);


       if($result)
      {
      $json['result'] = "true";
      $json['msg']    = "All Sub-Category";
      $json['data']   = $result;
      }
      else
      {
      $json['result'] = "false";
      $json['msg']    = "No Sub-Category";
     }


     }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "parameter required cat_id";
     }

     echo json_encode($json);
   }






   public function add_bank() 
   {
  
  $vendor_id=$this->input->post('vendor_id');
  $bank_name=$this->input->post('bank_name');
  $type=$this->input->post('type');
  $card_holder_name=$this->input->post('card_holder_name');
  $ifsc_code=$this->input->post('ifsc_code');
  $account_number=$this->input->post('account_number');
  
  if(isset($vendor_id) && isset($bank_name) && isset($type) && isset($card_holder_name) && isset($ifsc_code) && isset($account_number)){
    
    $post_data=array('vendor_id'=>$vendor_id,'bank_name'=>$bank_name,'type'=>$type,'card_holder_name'=>$card_holder_name,'ifsc_code'=>$ifsc_code,'account_number'=>$account_number,'created_date'=>date('Y-m-d H:i:s',time()));
    
     $res = $this->Api_Model->insertAllData('vandor_bank_details',$post_data);
     
     if($res){
       $json['result'] = 'true';
       $json['msg'] = 'Bank Details Added Successfully';
     }
     else{
       
       $json['result'] = 'false';
       $json['msg'] = 'Somthing Went wrong';
     }
     
    
  }
  
  else{
    
    $json['result'] = 'false';
    $json['msg'] = 'parameter required vendor_id,bank_name,type,card_holder_name,ifsc_code,account_number';
  }
  
  echo json_encode($json);

  
  
}







public function get_bank_details(){
  
  $vendor_id=$this->input->post('vendor_id');
  
  if(isset($vendor_id)){
    
     $wheredata = array('field'=>'vendor_id,bank_name,card_holder_name,ifsc_code,account_number',

       'table'=>'vandor_bank_details',

       'where'=>array('vendor_id' => $vendor_id),

       'order_by'=>'id desc'

       );
       
      $result = $this->Api_Model->getAllData($wheredata);

    if($result)
    {
      $json['result'] = "true";
      $json['msg']    = "All Data";
      $json['data']   = $result;
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "No Data";
    }

    
  }
  
  else{
    
    $json['result'] = 'false';
    $json['msg'] = 'parameter required vendor_id';
  }
  
   echo json_encode($json);
  
  
}


public function update_bank(){
  
  $vendor_id=$this->input->post('vendor_id');
  $bank_name=$this->input->post('bank_name');
  $card_holder_name=$this->input->post('card_holder_name');
  $ifsc_code=$this->input->post('ifsc_code');
  $account_number=$this->input->post('account_number');
  
  
  if(isset($bank_name) && isset($card_holder_name) && isset($vendor_id) && isset($ifsc_code) && isset($account_number)){
  
  
  
   $wheredata = array(
        'vendor_id' => $vendor_id
      );

        
        
        $data['bank_name']=$bank_name;
         $data['card_holder_name']=$card_holder_name;
          $data['ifsc_code']=$ifsc_code;
           $data['account_number']=$account_number;
        
        
   $result = $this->Api_Model->updates($wheredata,'vandor_bank_details',$data);
   
   if($result){
     
     $json['result'] = 'true';
     $json['msg'] = 'Update Successfully';
   }
   
   else{
      $json['result'] = 'false';
     $json['msg'] = 'Somthing want wrong';
   }
  }
  
  else{
     $json['result'] = 'false';
     $json['msg'] = 'paramenter required vendor_id,optional(bank_name,card_holder_name,ifsc_code,account_number)';
    
  }
  
  echo json_encode($json);
  
}



   public function add_community()
   {
     $user_id     = $this->input->post('user_id');
     $description = $this->input->post('description');
     $category_id = $this->input->post('category_id');
     $type        = $this->input->post('type');
     $phone        = $this->input->post('phone');



     if(isset($user_id) && isset($description) && isset($type))
     {
       if(!empty($_FILES['image']['name']))
       {
        $image1  = $this->imageUpload('image','assets/images/community/');

        $post_data['image'] = $image1;
       }

       if(!empty($category_id))
       {
         $post_data['category_id']  = $category_id;
       }
       if(!empty($phone))
       {
         $post_data['phone']  = $phone;
       }



       $post_data['user_id']      = $user_id;
       $post_data['description']  = $description;

       $post_data['type']         = $type;
       $post_data['created_date'] = date('Y-m-d H:i:s',time());
       $post_data['updated_date'] = date('Y-m-d H:i:s',time());


       $res = $this->Api_Model->insertAllData('community',$post_data);

       if($res)
       {
         $json['result'] = "true";
         $json['msg']    = "Add successfully";
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "something went wrong";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,description,image,category_id,type(requirement or discussion)";
     }

     echo json_encode($json);
   }





   public function get_community()
   {

     $user_id     = $this->input->post('user_id');
     $type        = $this->input->post('type');
     $category_id = $this->input->post('category_id');
     $city_name   = $this->input->post('city_name');
     $mobile      = $this->input->post('mobile');

     $current_date_time = date('Y-m-d H:i:s',time());
     $current_date      = date('Y-m-d');

     if(isset($user_id) && isset($type))
     {


      if(!empty($category_id))
      {
        $where = array(
        'community.type' => $type,
        'community.category_id' => $category_id

        );
      }
      else
      {
        $where = array(

        'community.type' => $type,
        // 'users.city_name LIKE' => "'%$city_name%'"

        );
      }




      $res = $this->Api_Model->get_community("community",$where,"community.id desc");


      foreach($res as $value)
      {
        $id              = $value->id;
        $user_ids         = $value->user_id;
        $name            = $value->name;
        $user_image      = $value->user_image;
         $user_mobile      = $value->user_mobile;
        $required        = $value->required;
        $description     = $value->description;
        $community_image = $value->community_image;
        $created_date    = $value->created_date;
        $city_name = $value->city_name;
        $usertype    = $value->usertype;
        $phone = $value->phone;
        
         $user_iddd = $value->user_iddd;




        $date    = date('Y-m-d',strtotime($value->created_date));


        $this->db->where("like_community.user_id",$user_id);
        $this->db->where("like_community.community_id",$id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_liked = $this->db->get()->row();

        if($is_liked)
        {
          $liked = 1;
        }
        else
        {
          $liked = 0;
        }







        $this->db->where("comment_community.community_id",$id);
        $this->db->select("id");
        $this->db->from("comment_community");
        $comment_count = $this->db->get();

        if($comment_count->num_rows() >0)
        {
          $c_count = $comment_count->num_rows();
        }
        else
        {
          $c_count = 0;
        }







        $this->db->where("like_community.community_id",$id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_count = $this->db->get();

        if($is_count->num_rows()>0)
        {
          $like_count = $is_count->num_rows();
        }
        else
        {
          $like_count = 0;
        }




      $datetime1 = new DateTime();
      $datetime2 = new DateTime($created_date);
      $interval  = $datetime1->diff($datetime2);


      $years   = $interval->format('%y years');
      $days    = $interval->format('%a days');
      $hours   = $interval->format('%h hours');
      $minutes = $interval->format('%i minutes');
      $seconds = $interval->format('%s seconds');


      if($date == $current_date)
      {



        if($hours != '0 hours')
        {
          $time_to_show = $hours;
        }
        elseif($minutes != '0 minutes')
        {
          $time_to_show = $minutes;
        }
        else
        {
          $time_to_show = $seconds;
        }
      }



      $date1 = new DateTime($date);
      $date2 = new DateTime($current_date);

      $interval = $date1->diff($date2);

      $years     = $interval->y . " years";
      $months    = $interval->m . " months";
      $days      = $interval->d . " days";



      if($date != $current_date)
      {
        if($years != 0)
        {
          $time_to_show = $years;
        }
        elseif($months != 0)
        {
          $time_to_show = $months;
        }
        else
        {
          $time_to_show = $days;
        }
      }
      
      
      
      
        
        $this->db->where("users.id",$user_iddd);
        $this->db->select("category.name as post_user_category");
        $this->db->from("users");
        $this->db->join("category","category.id = users.category_id");
        $rey = $this->db->get()->row();

         
         if($rey)
         {
           $post_user_category = $rey->post_user_category;
         }
         else
         {
           $post_user_category = "";
         }



        $resultnew[] = array(
          'id' => $id,
          'user_id' => $user_ids,
          'name' => $name,
          
          'user_image' => $user_image,
           'user_mobile' => $user_mobile,
          'required' => $required,
          'description' => $description,
          'community_image' => $community_image,
          'created_date' => $created_date,
          'ago_time' => $time_to_show,
          'liked' => $liked,
          'like_count' => $like_count,
          'comment_count' => $c_count,
          'city_name'=>$city_name,
          'usertype'=>$usertype,
          'is_phone'=>$phone,
          'user_iddd' => $user_iddd,
          'post_user_category' => $post_user_category
          );

      }


      if($res)
      {
        $json['result']            = "true";
        $json['msg']               = "All Data";
        $json['user_path']         = base_url()."assets/images/users/";
        $json['community_path']    = base_url()."assets/images/community/";
        $json['data']              = $resultnew;
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "No Data";
      }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,type(requirement or discussion),optional(category_id)";
     }

     echo json_encode($json);
   }






  public function like_unlike_vehical()
   {
     $vehical_id   = $this->input->post('vehical_id');
     $vendor_id    = $this->input->post('vendor_id');
     
     if(isset($vehical_id) && isset($vendor_id))
     {
       $this->db->where("vehical_id",$vehical_id);
       $this->db->where("vendor_id",$vendor_id);
       $this->db->select("id");
       $this->db->from("like_unlike_vehicle");
       $already = $this->db->get()->row();
       
       if($already)
       {
              $this->db->where("vendor_id",$vendor_id);
              $this->db->where("vehical_id",$vehical_id);
         $deleted = $this->db->delete("like_unlike_vehicle");
         
         if($deleted)
         {
           $json['result']  = "true";
           $json['msg']     = "Unliked";
         }
         else
         {
           $json['result']  = "false";
           $json['msg']     = "something went wrong";
         }
       }
       else
       {
         $postdata = array(
           'vehical_id' => $vehical_id,
           'vendor_id' => $vendor_id
           );
         
         $result = $this->Api_Model->insertAllData('like_unlike_vehicle',$postdata); 
         
         
         if($result){
           
            $json['result']  = "true";
           $json['msg']     = "Liked";
           
         }
         
         
         
        //  if($result)
        //  {
        //      $this->db->where("sender_id",$user_id);
        //      $this->db->where("receiver_id",$vendor_id);
        //      $this->db->select("id");
        //      $this->db->from("notification");
        //      $uio = $this->db->get()->row();
           
           
        //      if(!$uio)
        //      {
        //          $this->db->where("id",$user_id);
        //          $this->db->select("name");
        //          $this->db->from("users");
        //          $rtst = $this->db->get()->row();
           
           
        //          $posts['sender_id']   = $user_id;
        //          $posts['receiver_id'] = $vendor_id;
        //          $posts['title']       = "Someone Liked profile";
        //          $posts['msg']         = $rtst->name." Liked your shop";;
           
        //          $posts['status']       = 0;
        //          $posts['created_date'] = date('Y-m-d H:i:s',time());
           
           
        //          $result_1 = $this->Api_Model->insertAllDataa('notification',$posts); 
        //      }
           
           
           
           
           
           
        //      $json['result']  = "true";
        //      $json['msg']     = "Liked";
        //  }
        //  else
        //  {
        //      $json['result']  = "false";
        //      $json['msg']     = "something went wrong";
        //  }
       }
     }
     else
     {
       $json['result']  = "false";
       $json['msg']     = "parameter required vehical_id,vendor_id";
     }
     
     
     echo json_encode($json);
   }







   public function like_unlike_community()
   {
     $user_id      = $this->input->post('user_id');
     $community_id = $this->input->post('community_id');

     if(isset($user_id) && isset($community_id))
     {
       $this->db->where("like_community.user_id",$user_id);
       $this->db->where("like_community.community_id",$community_id);
       $this->db->select("id");
       $this->db->from("like_community");
       $is_exist = $this->db->get()->row();

       if($is_exist)
       {
         $this->db->where("like_community.user_id",$user_id);
         $this->db->where("like_community.community_id",$community_id);
         $delete = $this->db->delete("like_community");

        $this->db->where("like_community.user_id",$user_id);
        $this->db->where("like_community.community_id",$community_id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_liked = $this->db->get()->row();

        if($is_liked)
        {
          $liked = 1;
        }
        else
        {
          $liked = 0;
        }
        $this->db->where("like_community.community_id",$community_id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_count = $this->db->get();

        if($is_count->num_rows()>0)
        {
          $like_count = $is_count->num_rows();
        }
        else
        {
          $like_count = 0;
        }
        $resultnew = array(
          'liked' => $liked,
          'like_count' => $like_count,
          );

         if($delete)
         {
          $json['result'] = "true";
          $json['msg']    = "Unliked successfully";
          $json['data']   = $resultnew;
         }
         else
         {
          $json['result'] = "false";
          $json['msg']    = "something went wrong";
         }

       }
       else
       {
         $post_data['user_id']      = $user_id;
         $post_data['community_id'] = $community_id;
         $post_data['created_date'] = date('Y-m-d H:i:s',time());

         $res = $this->Api_Model->insertAllData('like_community',$post_data);

        $this->db->where("like_community.user_id",$user_id);
        $this->db->where("like_community.community_id",$community_id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_liked = $this->db->get()->row();

        if($is_liked)
        {
          $liked = 1;
        }
        else
        {
          $liked = 0;
        }
        $this->db->where("like_community.community_id",$community_id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_count = $this->db->get();

        if($is_count->num_rows()>0)
        {
          $like_count = $is_count->num_rows();
        }
        else
        {
          $like_count = 0;
        }
        $resultnew = array(
          'liked' => $liked,
          'like_count' => $like_count,
          );
         if($res)
         {
          $json['result'] = "true";
          $json['msg']    = "Liked successfully";
          $json['data']   = $resultnew;
         }
         else
         {
          $json['result'] = "false";
          $json['msg']    = "something went wrong";
         }
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,community_id";
     }

     echo json_encode($json);
   }







   public function comment_community()
   {
     $user_id      = $this->input->post('user_id');
     $community_id = $this->input->post('community_id');
     $comment      = $this->input->post('comment');

     if(isset($user_id) && isset($community_id) && isset($comment))
     {
        $data11 = array(
          'user_id'          =>  $user_id,
          'community_id'     =>  $community_id,
          'comments'         =>  $comment,
          'created_date'     =>  date('Y-m-d H:i:s',time()),
         );


      $res = $this->Api_Model->insertAllData('comment_community',$data11);

      if($res)
      {
         $json['result'] = "true";
         $json['msg']    = "comment successfully";
      }
      else
      {
         $json['result'] = "false";
         $json['msg']    = "something went wrong";
      }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,community_id,comment";
     }


     echo json_encode($json);
   }








   public function load_comments()
   {
     $community_id = $this->input->post('community_id');

     if(isset($community_id))
     {
      $wheredata = array(
        'comment_community.community_id'  => $community_id
      );

      $joins = array([
          'table'  =>'users',
          'on'  =>'users.id = comment_community.user_id',
          'right_left' => 'left'
      ]);

             $select_fields = array(
            'comment_community.id',
            'comment_community.comments',
            'comment_community.created_date',
            'users.id as user_id',
            'users.name as user_name',
            'users.image as user_image'
            );


      $order_by = "comment_community.id DESC";


      $this->db->where("community.id",$community_id);
      $this->db->select("community.id,
                 community.description,
                 community.image as community_image,
                 users.id as user_id,
                 users.name as user_name,
                 users.image as user_image");
      $this->db->from("community");
      $this->db->join("users","users.id = community.user_id");
      $rsts = $this->db->get()->row();



       $result = $this->Api_Model->getSingleResultWithJoin('comment_community',$select_fields,$joins,$wheredata,$order_by);


       if($result)
       {
         foreach($result as $value)
       {
         $id           = $value->id;
         $comments     = $value->comments;
         $created_date = $value->created_date;
         $user_id      = $value->user_id;
         $user_name    = $value->user_name;
         $user_image   = $value->user_image;




         $this->db->where("like_community_comments.comment_community_id",$id);
         $this->db->select("id");
         $this->db->from("like_community_comments");
         $like_counts_r = $this->db->get();

         if($like_counts_r->num_rows()>0)
         {
           $like_counts = $like_counts_r->num_rows();
         }
         else
         {
           $like_counts = 0;
         }





         $resultnew[] = array(
           'id' => $id,
           'comments' => $comments,
           'created_date' => $created_date,
           'user_id' => $user_id,
           'user_name' => $user_name,
           'user_image' => $user_image,
           'like_counts' => $like_counts,
           );
       }
       }
       else
       {
         $resultnew = array();
       }




       if($rsts)
       {
         $json['result'] = "true";
         $json['msg']    = "All Comment";
         $json['path']   = base_url()."assets/images/users/";
         $json['community_path']   = base_url()."assets/images/community/";
         $json['datass'] = $rsts;
         $json['data']   = $resultnew;
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "No Comment";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required community_id";
     }


     echo json_encode($json);
   }




   public function like_unlike_community_comments()
   {
     $user_id    = $this->input->post('user_id');
     $comment_id = $this->input->post('comment_id');

     if(isset($user_id) && isset($comment_id))
     {
       $this->db->where("like_community_comments.user_id",$user_id);
       $this->db->where("like_community_comments.comment_community_id",$comment_id);
       $this->db->select("id");
       $this->db->from("like_community_comments");
       $is_exist = $this->db->get()->row();


       if($is_exist)
       {
         $this->db->where("like_community_comments.user_id",$user_id);
         $this->db->where("like_community_comments.comment_community_id",$comment_id);
         $res = $this->db->delete("like_community_comments");


         if($res)
       {
        $json['result'] = "true";
        $json['msg']    = "Unliked successfully";
       }
       else
       {
        $json['result'] = "false";
        $json['msg']    = "something went wrong";
       }

       }
       else
       {
        $data11 = array(
        'user_id' =>  $user_id,
        'comment_community_id' => $comment_id,
        'created_date' =>  date('Y-m-d H:i:s',time()),
         );


       $res = $this->Api_Model->insertAllData('like_community_comments',$data11);

       if($res)
       {
        $json['result'] = "true";
        $json['msg']    = "liked successfully";
       }
       else
       {
        $json['result'] = "false";
        $json['msg']    = "something went wrong";
       }
       }



     }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id,comment_id";
     }

     echo json_encode($json);
   }









  public function update_second_catsubcat()
  {
    $user_id    = $this->input->post('user_id');
    $cat_id     = $this->input->post('cat_id');
    $subcat_id  = $this->input->post('subcat_id');

    if(isset($user_id) && isset($cat_id) && isset($subcat_id))
    {
      $subcat_id            = $subcat_id;
      $slotidArray          = explode(',', $subcat_id);




      $wheredata = array(
        'id' => $user_id
      );

      $data = array(
        'second_category_id' => $cat_id,
        'second_category_updated' => 1
        );

      $result = $this->Api_Model->update($wheredata,'users',$data);




      for($i = 0; $i < count($slotidArray); $i++)
       {



      $data11 = array(
       'user_id'          =>  $user_id,
       'second_category_id'      =>  $cat_id,
       'second_subcategory_id'   =>  $slotidArray[$i],
      //  'date'             =>  date('Y-m-d'),
      //  'created_date'     =>  date('Y-m-d H:i:s',time()),
      //  'updated_date'     =>  date('Y-m-d H:i:s',time()),
      );


       $res = $this->Api_Model->insertAllData('users_second_catsubcat',$data11);


      }




      if($result)
      {
        $this->db->where("second_category.id",$cat_id);
        $this->db->select("second_category.name");
        $this->db->from("second_category");
        $rts = $this->db->get()->row();


        $json['result'] = "true";
        $json['msg']    = "Category Updated";
        $json['data']   = $rts;
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "something went wrong";
      }



    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id,cat_id,subcat_id";
    }


    echo json_encode($json);
  }









   public function update_profile_new_image()
   {
    $user_id       = $this->input->post('user_id');


    if(isset($user_id))
    {

      $wheredata1 = array('field'=>['id','image'],

           'table'=>'users',

           'where'=>array('id'=>$user_id)

          );


      $image = $this->Api_Model->select_single_row_specific($wheredata1);


      if(!empty($_FILES['image']['name']))
      {



          if($image->image && file_exists('assets/images/users/'.$image->image))
         {
           unlink('assets/images/users/'.$image->image);

          }


          $image1  = $this->imageUpload('image','assets/images/users/');

          $post_data['image'] = $image1;

        }









      $wheredata = array(
        'id' => $user_id
       );



      $result = $this->Api_Model->update($wheredata,'users',$post_data);

      if($result)
      {
        $json['result'] = "true";
        $json['msg']    = "Updated successfully";
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "something went wrong";
      }


    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id,image";
    }


    echo json_encode($json);


   }








   public function get_community_countss()
   {
     $user_id      = $this->input->post('user_id');
     $community_id = $this->input->post('community_id');

     if(isset($user_id) && isset($community_id))
     {
        $this->db->where("like_community.user_id",$user_id);
        $this->db->where("like_community.community_id",$community_id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_liked = $this->db->get()->row();

        if($is_liked)
        {
          $liked = 1;
        }
        else
        {
          $liked = 0;
        }







        $this->db->where("comment_community.community_id",$community_id);
        $this->db->select("id");
        $this->db->from("comment_community");
        $comment_count = $this->db->get();

        if($comment_count->num_rows() >0)
        {
          $c_count = $comment_count->num_rows();
        }
        else
        {
          $c_count = 0;
        }





        $this->db->where("like_community.community_id",$community_id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_count = $this->db->get();

        if($is_count->num_rows()>0)
        {
          $like_count = $is_count->num_rows();
        }
        else
        {
          $like_count = 0;
        }




        $this->db->where("users.id",$user_id);
        $this->db->select("ifnull(users.type,'') as type");
        $this->db->from("users");
        $tt = $this->db->get()->row();




        $resultnew = array(
          'liked' => $liked,
          'like_count' => $like_count,
          'comment_count' => $c_count,
          'type' => $tt->type,
          );






      $json['result'] = "true";
      $json['msg']    = "All Data";
      $json['data']   = $resultnew;

     }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id,community_id";
     }


     echo json_encode($json);
   }






  //  public function get_my_community()
  //  {
  //      $user_id = $this->input->post('user_id');

  //      if(isset($user_id))
  //      {
  //         $result = $this->Api_Model->get_my_community("community",$user_id);



  //         foreach($result as $value)
  //         {
  //             $id            = $value->id;
  //             $description   = $value->description;
  //             $image         = $value->image;
  //             $type          = $value->type;
  //             $created_date  = $value->created_date;
  //             $category_name = $value->category_name;


  //             $resultnew[] = array(
  //                 'id' => $id,
  //                 'description' => $description,
  //                 'image' => $image,
  //                 'type' => $type,
  //                 'created_date' => $created_date,
  //                 'category_name' => $category_name,
  //                 );

  //         }


  //         if($result)
  //         {
  //             $json['result'] = "true";
  //             $json['msg']    = "All Data";
  //             $json['path']   = base_url()."assets/images/community/";
  //             $json['data']   = $resultnew;
  //         }
  //         else
  //         {
  //             $json['result'] = "false";
  //             $json['msg']    = "No Data";
  //         }

  //      }
  //      else
  //      {
  //         $json['result'] = "false";
  //         $json['msg']    = "parameter required user_id";
  //      }


  //      echo json_encode($json);
  //  }






   public function get_my_community()
   {
     $user_id     = $this->input->post('user_id');
     $category_id = $this->input->post('category_id');
     $type        = $this->input->post('type');

     $current_date_time = date('Y-m-d H:i:s',time());
     $current_date      = date('Y-m-d');

     if(isset($user_id))
     {
         
         
         if(!empty($type))
         {
           if($type == "requirement")
           {
             $where = array(

            'community.user_id' => $user_id,
            'community.type' => 'requirement'
             );
           }
           
           if($type == "discussion")
           {
             $where = array(

             'community.user_id' => $user_id,
             'community.type' => 'discussion'
             );
           }
         }
         else
         {
           $where = array(

           'community.user_id' => $user_id
           );
         }


        





      $res = $this->Api_Model->get_community("community",$where,"community.id desc");


      foreach($res as $value)
      {
        $id              = $value->id;
        $user_ids         = $value->user_id;
        $name            = $value->name;
        $user_image      = $value->user_image;
        $required        = $value->required;
        $description     = $value->description;
        $community_image = $value->community_image;
        $created_date    = $value->created_date;

        $city_name = $value->city_name;
        $usertype    = $value->usertype;
        $phone    = $value->phone;
        $my_category_name    = $value->my_category_name;




        $date    = date('Y-m-d',strtotime($value->created_date));


        $this->db->where("like_community.user_id",$user_id);
        $this->db->where("like_community.community_id",$id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_liked = $this->db->get()->row();

        if($is_liked)
        {
          $liked = 1;
        }
        else
        {
          $liked = 0;
        }







        $this->db->where("comment_community.community_id",$id);
        $this->db->select("id");
        $this->db->from("comment_community");
        $comment_count = $this->db->get();

        if($comment_count->num_rows() >0)
        {
          $c_count = $comment_count->num_rows();
        }
        else
        {
          $c_count = 0;
        }







        $this->db->where("like_community.community_id",$id);
        $this->db->select("id");
        $this->db->from("like_community");
        $is_count = $this->db->get();

        if($is_count->num_rows()>0)
        {
          $like_count = $is_count->num_rows();
        }
        else
        {
          $like_count = 0;
        }




      $datetime1 = new DateTime();
      $datetime2 = new DateTime($created_date);
      $interval  = $datetime1->diff($datetime2);


      $years   = $interval->format('%y years');
      $days    = $interval->format('%a days');
      $hours   = $interval->format('%h hours');
      $minutes = $interval->format('%i minutes');
      $seconds = $interval->format('%s seconds');


      if($date == $current_date)
      {



        if($hours != '0 hours')
        {
          $time_to_show = $hours;
        }
        elseif($minutes != '0 minutes')
        {
          $time_to_show = $minutes;
        }
        else
        {
          $time_to_show = $seconds;
        }






      }













      $date1 = new DateTime($date);
      $date2 = new DateTime($current_date);

      $interval = $date1->diff($date2);

      $years     = $interval->y . " years";
      $months    = $interval->m . " months";
      $days      = $interval->d . " days";



      if($date != $current_date)
      {
        if($years != 0)
        {
          $time_to_show = $years;
        }
        elseif($months != 0)
        {
          $time_to_show = $months;
        }
        else
        {
          $time_to_show = $days;
        }
      }




        $resultnew[] = array(
          'id' => $id,
          'user_id' => $user_ids,
          'name' => $name,
          'user_image' => $user_image,
          'required' => $required,
          'description' => $description,
          'community_image' => $community_image,
          'created_date' => $created_date,
          'ago_time' => $time_to_show,
          'liked' => $liked,
          'like_count' => $like_count,
          'comment_count' => $c_count,
          'city_name'=>$city_name,
          'usertype'=>$usertype,
          'isphone'=>$phone,
          'my_category_name'=>$my_category_name,
          );

      }


      if($res)
      {
        $json['result']            = "true";
        $json['msg']               = "All Data";
        $json['user_path']         = base_url()."assets/images/users/";
        $json['community_path']    = base_url()."assets/images/community/";
        $json['data']              = $resultnew;
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "No Data";
      }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,optional(type = requirement or discussion)";
     }

     echo json_encode($json);
   }





   public function get_hashtag_list()
   {
     $wheredata = array('field'=>'id,name',

       'table'=>'hash_tag_list',

       'where'=>array(),

       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);

    if($result)
    {
      $json['result'] = "true";
      $json['msg']    = "All Data";
      $json['data']   = $result;
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "No Data";
    }


    echo json_encode($json);
   }






    public function get_reel_categories()
   {
     $wheredata = array('field'=>'id,name',

       'table'=>'reels_categories',

       'where'=>array(),

       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);

    if($result)
    {
      $json['result'] = "true";
      $json['msg']    = "All Data";
      $json['data']   = $result;
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "No Data";
    }


    echo json_encode($json);
   }









   public function add_reels()
   {
     $user_id          = $this->input->post('user_id');
     $description      = $this->input->post('description');
     $hash_tag_id      = $this->input->post('hash_tag_id'); // comma seperated value aa rahi hai
     $reel_category_id = $this->input->post('reel_category_id'); // comma seperated value aa rahi hai
     $image            = $this->input->post('image');
     $textcolor        = $this->input->post('textcolor');
     
     
     $image_text       = $this->input->post('image_text');
     
     
     $type             = $this->input->post('type');
      

      if(!$user_id  == "")
      {

       $data['result']= false;
       $data['msg']="Please give parameters(user_id)";
      }
         
         
         

     if(isset($user_id))
     {
       if(!empty($description))
       {
         $post_data['description'] = $description;
       }
       
       
       if(!empty($image_text))
       {
         $post_data['image_text'] = $image_text;
       }
       
       
       
       if(!empty($textcolor))
       {
         $post_data['textcolor'] = $textcolor;
       }
       
       
        if(!empty($type))
       {
         $post_data['type'] = $type;
       }



       if(!empty($_FILES['image']['name']))
       {


        $image1  = $this->imageUpload('image','assets/images/reels/');



        $post_data['file'] = $image1;


       }
       
       
       
       if(!empty($_FILES['image_2']['name']))
       {

        $image1  = $this->imageUpload('image_2','assets/images/reels/');
        $post_data['image_2'] = $image1;
       }
       
       
       
       if(!empty($_FILES['image_3']['name']))
       {

        $image1  = $this->imageUpload('image_3','assets/images/reels/');
        $post_data['image_3'] = $image1;
       }
       
       
       
       if(!empty($_FILES['image_4']['name']))
       {

        $image1  = $this->imageUpload('image_4','assets/images/reels/');
        $post_data['image_4'] = $image1;
       }
       
       
       
       if(!empty($_FILES['image_5']['name']))
       {

        $image1  = $this->imageUpload('image_5','assets/images/reels/');
        $post_data['image_5'] = $image1;
       }
       
       
       
       if(!empty($_FILES['image_6']['name']))
       {

        $image1  = $this->imageUpload('image_6','assets/images/reels/');
        $post_data['image_6'] = $image1;
       }
       
       
       
       if(!empty($_FILES['image_7']['name']))
       {

        $image1  = $this->imageUpload('image_7','assets/images/reels/');
        $post_data['image_7'] = $image1;
       }
       
       
       
       if(!empty($_FILES['image_8']['name']))
       {

        $image1  = $this->imageUpload('image_8','assets/images/reels/');
        $post_data['image_8'] = $image1;
       }
       
       
       
       if(!empty($_FILES['image_9']['name']))
       {

        $image1  = $this->imageUpload('image_9','assets/images/reels/');
        $post_data['image_9'] = $image1;
       }
       
       
       if(!empty($_FILES['image_10']['name']))
       {

        $image1  = $this->imageUpload('image_10','assets/images/reels/');
        $post_data['image_10'] = $image1;
       }
       
       



       $post_data['user_id'] = $user_id;
       $post_data['created_date'] = date('Y-m-d H:i:s',time());
       $post_data['updated_date'] = date('Y-m-d H:i:s',time());



       $inserdata = $this->db->insert("reels",$post_data);
       $insert_id = $this->db->insert_id();




       if($inserdata)
       {
         
         $subcat_id            = $reel_category_id;
         $slotidArray          = explode(',', $subcat_id);
         
         
         
         
         
         $subcat_id_2          = $hash_tag_id;
         $slotidArray_2        = explode(',', $subcat_id_2);



        for($i = 0; $i < count($slotidArray); $i++)
        {



          $data11 = array(
           'reels_id'          => $insert_id,
           'reels_categories_id' => $slotidArray[$i],
          );


         $res = $this->Api_Model->insertAllData('reels_categories_saved',$data11);


         }
         
         
        
        
        
        
         for($i = 0; $i < count($slotidArray_2); $i++)
        {



          $data11_2 = array(
           'reels_id'          => $insert_id,
           'hash_tag_list_id' => $slotidArray_2[$i],
          );


          $res_2 = $this->Api_Model->insertAllData('reels_hashtag_saved',$data11_2);


        }

     
         
         $json['result'] = "true";
         $json['msg']    = "Post Added";
         
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "something went wrong";
       }


    
         }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id,optional(image_text,description,textcolor,hash_tag_id,reel_category_id,image(for image or video)),type(image or text or video),image_2,image_3,image_4,image_5,image_6,image_7,image_8,image_9,image_10";
     }

  

     echo json_encode($json);

   }




   public function get_reels()
   {
     
    $user_id = $this->input->post('user_id');
    
    
    if(isset($user_id))
    {
        if($getResult = $this->Api_Model->get_my_reel())
        {
      
      
      foreach($getResult as $value)
      {
        $id           = $value->id;
        $user_id_1    = $value->user_id;
        $file         = $value->file;
        $image_2      = $value->image_2;
        $image_3      = $value->image_3;
        $image_4      = $value->image_4;
        $image_5      = $value->image_5;
        $image_6      = $value->image_6;
        $image_7      = $value->image_7;
        $image_8      = $value->image_8;
        $image_9      = $value->image_9;
        $image_10     = $value->image_10;
        $description  = $value->description;
        $image_text   = $value->image_text;
        $textcolor    = $value->textcolor;
        $type         = $value->type;
        $created_date = $value->created_date;
        $updated_date = $value->updated_date;
        $name         = $value->name;
         
        $city_name    = $value->city_name;
        $user_image   = $value->user_image;
        
        
        
        
        
        $this->db->where("comment_on_reels.reel_id",$id);
        $this->db->select("id");
        $this->db->from("comment_on_reels");
        $comm_c = $this->db->get();
        
        
        if($comm_c->num_rows() > 0)
        {
          $comment_count = $comm_c->num_rows();
        }
        else
        {
          $comment_count = 0;
        }
        
        
        
        
        $this->db->where("reel_id",$id);
        $this->db->select("id");
        $this->db->from("like_unlike_reel");
        $count = $this->db->get();
        
        
        
        if($count->num_rows() > 0)
        {
          $like_count = $count->num_rows();
        }
        else
        {
          $like_count = 0;
        }
        
        
        
        
        
        
        $this->db->where("user_id",$user_id);
        $this->db->where("reel_id",$id);
        $this->db->select("id");
        $this->db->from("like_unlike_reel");
        $is_my = $this->db->get()->row();
        
        
   
        
        
        if($is_my)
        {
          $i_liked = 1;
        }
        else
        {
          $i_liked = 0;
        }
        
        
        
        $resultnew[] = array(
          
          'id' => $id,
          'user_id' => $user_id_1,
          'file' => $file,
          'image_2' => $image_2,
          'image_3' => $image_3,
          'image_4' => $image_4,
          'image_5' => $image_5,
          'image_6' => $image_6,
          'image_7' => $image_7,
          'image_8' => $image_8,
          'image_9' => $image_9,
          'image_10' => $image_10,
          'description' => $description,
          'image_text' => $image_text,
          'textcolor' => $textcolor,
          'type' => $type,
          'created_date' => $created_date,
          'updated_date' => $updated_date,
          'name' => $name,
           
          'city_name' => $city_name,
          'user_image' => $user_image,
          'like_count' => $like_count,
          'comment_count' => $comment_count,
          'i_liked' => $i_liked,
          );
         }


        $data['result'] = "true";
        $data['msg']    = "Newsfeed list";
        $data['reel_path']   = base_url()."assets/images/reels/";
        $data['user_path']   = base_url()."assets/images/users/";
        $data['data']   = $resultnew;
      

       }
       else
       {
        $data['result'] = "false";
        $data['msg'] = "record not found";
       }
    }
    else
    {
      $data['result'] = "false";
      $data['msg']    = "parameter required user_id";
    }
     

    


    echo json_encode($data);



   }   
   
   
   
   public function apply_support(){
     
    $vendor_id = $this->input->post('vendor_id'); 
    $title = $this->input->post('title'); 
    $description = $this->input->post('description'); 
    
    
    if(isset($vendor_id) && isset($title) && isset($description)){
      
      $post_data = array('vendor_id' => $vendor_id,'title'=> $title,'description' => $description,'created_date'=>date('Y-m-d H:i:s',time()));
      
      
      $result = $this->Api_Model->insertAllData('technical_support',$post_data);
      
      if($result){
        
        $json['result'] = 'true';
        $json['msg'] = 'insert successfully';
      }
      else{
        
        $json['result'] = 'false';
        $json['msg'] = 'something went wrong';
      }
      
       
      
      
      
      
       
      }
      
      else{
        
        $json['result'] = 'false';
        $json['msg'] = 'parameter required vendor_id,title,description';
      }
       echo json_encode($json);
       
     
     
     
   }
   

   
   
   
   public function get_catwise_users()
   {
     $type = $this->input->post('type');
     
     if(isset($type))
     {
       $result = $this->Api_Model->get_catwise_users("users",$type);
       
       if($result)
       {
         $json['result'] = "true";
         $json['msg']    = "All Data";
         $json['path']   = base_url()."assets/images/users/";
         $json['data']   = $result;
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "No Data";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required type(home_owner or service_provider or material_supplier)";
     }
     
     
     echo json_encode($json);
   }
   
   
   
   
   
   
   
   
   
   public function get_all_categories_users_previous_latest()
   {
     $result = $this->Api_Model->get_all_cats("category","id DESC");
     
     if($result)
     {
       
       foreach($result as $val)
       {
         $id   = $val->id;
         $name = $val->name;
         
         
         
         
         
         $this->db->where("users.category_id",$id);
         $this->db->select("users.id");
         $this->db->from("users");
         $is = $this->db->get();
         
         if($is->num_rows()>0)
         {
           $is_user = "yes"; 
         }
         else
         {
           $is_user = "no";
         }
         
         
         
         
         
         $this->db->where("users.category_id",$id);
         $this->db->select("users.id as user_id,
                  users.name as user_name,
                  users.image as user_image,
                  category.name as category_name,
                  category.id as category_id");
         $this->db->from("users");
         $this->db->join("category","category.id = users.category_id");
         $this->db->order_by("users.id","DESC");
         $restttvv = $this->db->get();
         
         
         
         
         
         
         if($restttvv->num_rows()>0)
         {
           
           foreach($restttvv->result() as $value)
           {
            //  echo $id."<br>";
             
             
            //  $this->db->where("community.category_id",$id);
             $this->db->where("community.user_id",$value->user_id);
             $this->db->select("community.id");
             $this->db->from("community");
             $community_count = $this->db->get();
             
             if($community_count->num_rows()>0)
             {
               $community_counts = $community_count->num_rows();
             }
             else
             {
               $community_counts = 0;
             }
             
             
             
             
             $this->db->where("follows.user_id",$value->user_id);
             $this->db->select("follows.id");
             $this->db->from("follows");
             $fow = $this->db->get();
             
             if($fow->num_rows()>0)
             {
               $follow_counts = $fow->num_rows();
             }
             else
             {
               $follow_counts = 0;
             }
             
             
             
             
             
             $this->db->where("follows.to_user_id",$value->user_id);
             $this->db->select("follows.id");
             $this->db->from("follows");
             $fowing = $this->db->get();
             
             if($fowing->num_rows()>0)
             {
               $following_counts = $fowing->num_rows();
             }
             else
             {
               $following_counts = 0;
             }
             
             
             
             
             $users[] = array(
               'user_id' => $value->user_id,
               'category_id' => $value->category_id,
               'user_name' => $value->user_name,
               'user_image' => $value->user_image,
               'category_name' => $value->category_name,
               'post_counts' => $community_counts,
               'followers_count' => $following_counts,
               'following_count' => $follow_counts,
               );
           }
           
          //  $users = $restttvv->result();
         }
         else
         {
           $users = array();
         }
         
         
         
         $resultnew[] = array(
           'id' => $id,
           'name' => $name,
           'is_user' => $is_user,
           'users' => $users,
           );
       }
       
       
       $json['result'] = "true";
       $json['msg']    = "All Data";
       $json['path']   = base_url()."assets/images/users/";
       $json['data']   = $resultnew;
       
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "No Data";
     }
     
     
     echo json_encode($json);
   }
   
   
   
   
   
   
     public function add_hastags()

    {

     
      $name = (trim($this->input->post('name')));
       


        if($name  == "")



         {

       $data['result']= false;

     $data['msg']="Please give parameters(name)";

        }



    else{ 

    $updatedata['name']= $name;

    $result=  $this->db->insert("hash_tag_list",$updatedata);

    if($result)



     {

      

    $data['result'] = true;

    $data['msg'] = "successfully";

      }



      else{



       $data['result']= false;



      $data['msg']="something went wrong";



       }

     }



    echo json_encode($data);

  }
   
   
   
   
   
   public function get_all_categories_users()
   {
     $type = $this->input->post('type');
     
     if(isset($type))
     {
         $result = $this->Api_Model->get_all_cats("category","id DESC");
     
    
     
     
     $FINAL_RESULT = array();
     
     if($result)
     {
       
       foreach($result as $val)
       {
         $id   = $val->id;
         $name = $val->name;
         
         
          $TEMP['id'] = $id;
          $TEMP['name'] = $name;
         
          
          
         
         
         
         
         $this->db->where("users.category_id",$id);
         $this->db->select("users.id");
         $this->db->from("users");
         $is = $this->db->get();
         
         if($is->num_rows()>0)
         {
           $is_user = "yes"; 
         }
         else
         {
           $is_user = "no";
         }
         
         $TEMP['is_user'] = $is_user;

         
         
         
         
        
         
         $this->db->where("users.type",$type);
         $this->db->where("users.category_id",$id);
         $this->db->select("users.id as user_id,
                  users.name as user_name,
                  users.image as user_image,
                  category.name as category_name,
                  category.id as category_id,
                  ifnull(city.name,'') as city_name");
         $this->db->from("users");
         $this->db->join("category","category.id = users.category_id");
         $this->db->join("city","city.id = users.city_id","left");
         $this->db->order_by("users.id","DESC");
         $restttvv = $this->db->get();
         
         
         
         
         
        //  var_dump($restttvv->num_rows());
        //  die();
         
         
         $USERS = array();
         
         if($restttvv->num_rows()>0)
         {
           
           foreach($restttvv->result() as $value)
           {
             
             
             
            //  $this->db->where("community.category_id",$id);
             $this->db->where("community.user_id",$value->user_id);
             $this->db->select("community.id");
             $this->db->from("community");
             $community_count = $this->db->get();
             
             if($community_count->num_rows()>0)
             {
               $community_counts = $community_count->num_rows();
             }
             else
             {
               $community_counts = 0;
             }
             
             
             
             
             $this->db->where("follows.user_id",$value->user_id);
             $this->db->select("follows.id");
             $this->db->from("follows");
             $fow = $this->db->get();
             
             if($fow->num_rows()>0)
             {
               $follow_counts = $fow->num_rows();
             }
             else
             {
               $follow_counts = 0;
             }
             
             
             
             
             
             $this->db->where("follows.to_user_id",$value->user_id);
             $this->db->select("follows.id");
             $this->db->from("follows");
             $fowing = $this->db->get();
             
             if($fowing->num_rows()>0)
             {
               $following_counts = $fowing->num_rows();
             }
             else
             {
               $following_counts = 0;
             }
             
             
             
             
             $TEMP_USER['user_id'] = $value->user_id;
             $TEMP_USER['category_id'] = $value->category_id;
             $TEMP_USER['user_name'] = $value->user_name;
             $TEMP_USER['user_image'] = $value->user_image;
             $TEMP_USER['category_name'] = $value->category_name;
             $TEMP_USER['post_counts'] = $community_counts;
             $TEMP_USER['followers_count'] = $following_counts;
             $TEMP_USER['following_count'] = $follow_counts;
             $TEMP_USER['city_name']       = $value->city_name;
             
            array_push($USERS, $TEMP_USER);
               
               
               
               
           }
           
          
         }
         else
         {
          $USERS = array();
         }
         
         
         //var_dump($users);
         
        
        
          $TEMP['users'] = $USERS;
          
          
          array_push($FINAL_RESULT, $TEMP);
         
        //  $resultnew[] = array(
        //      'id' => $id,
        //      'name' => $name,
        //      'is_user' => $is_user,
        //      'users' => $users,
        //      );
       
         
         
         
         
         }
         
         
         
       
        
         $json['result'] = "true";
         $json['msg']    = "All Data";
         $json['path']   = base_url()."assets/images/users/";
         $json['data']   = $FINAL_RESULT;
       
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "No Data";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required type(home_owner or service_provider or material_supplier)";
     }
     
     
     
     
     
     echo json_encode($json);
   }
   
   
   
   
   
   
   
   
   
   
   public function like_reel()
   {
     $user_id = $this->input->post('user_id');
     $reel_id = $this->input->post('reel_id');
     
     if(isset($user_id) && isset($reel_id))
     {
       $wheredata1 = array('field'=>['id'],

           'table'=>'like_unlike_reel',

           'where'=>array('user_id'=>$user_id,'reel_id' => $reel_id)

          );


      $row = $this->Api_Model->select_single_row_specific($wheredata1);
      
      if($row)
      {
        // Delete
        
             $this->db->where("like_unlike_reel.user_id",$user_id);
             $this->db->where("like_unlike_reel.reel_id",$reel_id);
        $res = $this->db->delete("like_unlike_reel");
        
        
         if($res)
         {
           $json['result'] = "true";
           $json['msg']    = "Unliked";
         }
         else
         {
           $json['result'] = "false";
           $json['msg']    = "something went wrong";
         }
      }
      else
      {
        //Insert
         
         $post_data['user_id'] = $user_id;
         $post_data['reel_id'] = $reel_id;
        
         $res = $this->Api_Model->insertAllData('like_unlike_reel',$post_data);
         
         if($res)
         {
           
           $this->db->where("users.id",$user_id);
           $this->db->select("users.name,users.image,fcm_id");
           $this->db->from("users");
           $rtrt = $this->db->get()->row();
           
           $msgs = $rtrt->name." has liked your post";
           
           
           
           
           
           $this->db->where("reels.id",$reel_id);
           $this->db->select("reels.user_id as receiver_id");
           $this->db->from("reels");
           $reell = $this->db->get()->row();
           
           
           
           
           $post_data_1['sender_id']     = $user_id;
           
           $post_data_1['receiver_id']   = $reell->receiver_id;
           $post_data_1['msg']           = $msgs;
           $post_data_1['sender_image']  = $rtrt->image;
           $post_data_1['image']         = "";
           
           $post_data_1['post_id']       = $reel_id;
           $post_data_1['seen_status']   = 0;
           $post_data_1['created_date']  = date('Y-m-d H:i:s',time());
           
           
           $this->db->where("sender_id",$user_id);
           $this->db->where("receiver_id",$reell->receiver_id);
           $this->db->where("post_id",$reel_id);
           $this->db->select("id");
           $this->db->from("notification");
           $bb = $this->db->get()->row();
           
           
           if(!$bb)
           {
             
             
             
              $notification = array(
               'title' =>"Liked on reel",
               'body'  =>$msgs
             );
             
             
            send_notification($notification,$rtrt->fcm_id);
             
             
             
             $res_1 = $this->Api_Model->insertAllData('notification',$post_data_1);
           }
           
           
           
           
           
           
           $json['result'] = "true";
           $json['msg']    = "Liked";
         }
         else
         {
           $json['result'] = "false";
           $json['msg']    = "something went wrong";
         }
      }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,reel_id";
     }
     
     echo json_encode($json);
   }
   
   
   
   public function comment_on_reel()
   {
     $user_id = $this->input->post('user_id');
     $reel_id = $this->input->post('reel_id');
     $comment = $this->input->post('comment');
     
     if(isset($user_id) && isset($reel_id) && isset($comment))
     {
       $post_data['user_id'] = $user_id;
       $post_data['reel_id'] = $reel_id;
       $post_data['comment'] = $comment;
       
       
       $post_data['created_date'] = date('Y-m-d H:i:s',time());
       
       $res = $this->Api_Model->insertAllData('comment_on_reels',$post_data);
       
       
       if($res)
       {
         
         $this->db->where("users.id",$user_id);
         $this->db->select("users.name,users.image,fcm_id");
         $this->db->from("users");
         $rb = $this->db->get()->row();
         
         
         
         $this->db->where("reels.id",$reel_id);
         $this->db->select("reels.user_id");
         $this->db->from("reels");
         $reelvv = $this->db->get()->row();
         
         
         $msgs = $rb->name." commented on your post";
         
         $post_data_1['sender_id']    = $user_id;
         $post_data_1['receiver_id']  = $reelvv->user_id;
         $post_data_1['msg']          = $msgs;
         $post_data_1['sender_image'] = $rb->image;
         $post_data_1['post_id']      = $reel_id;
         $post_data_1['seen_status']  = 0;
         $post_data_1['created_date'] = date('Y-m-d H:i:s',time());
         
         
         
         
         
             $notification = array(
               'title' =>"Comment on reel",
               'body'  =>$msgs
             );
             
             
            send_notification($notification,$rb->fcm_id);
         
         
         
         $res_1 = $this->Api_Model->insertAllData('notification',$post_data_1);
         
         
         $json['result'] = "true";
         $json['msg']    = "commented";
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "something went wrong";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,reel_id,comment";
     }
     
     
     echo json_encode($json);
     
   }
   
   
   
   public function load_reel_comments()
   {
     $reel_id = $this->input->post('reel_id');
     
     if(isset($reel_id))
     {
       $res = $this->Api_Model->get_reel_comments('comment_on_reels',$reel_id);
       
       
       
       foreach($res as $value)
       {
         $id           = $value->id;
         $user_id      = $value->user_id;
         $reel_id      = $value->reel_id;
         $comment      = $value->comment;
         $created_date = $value->created_date;
         $user_name    = $value->user_name;
         $user_image   = $value->user_image;
         
         
         $resultnew[] = array(
           'id' => $id,
           'user_id' => $user_id,
           'reel_id' => $reel_id,
           'comment' => $comment,
           'created_date' => $created_date,
           'user_name' => $user_name,
           'user_image' => $user_image,
           );
         
       }
       
       
       if($res)
       {
         
         $this->db->where("reels.id",$reel_id);
         $this->db->select("reels.*,users.name as user_name,users.image as user_image");
         $this->db->from("reels");
         $this->db->join("users","users.id = reels.user_id");
         $reelss = $this->db->get()->row();
         
         
         
         
         
         $json['result']      = "true";
         $json['msg']         = "All Comment";
         $json['user_path']   = base_url()."assets/images/users/";
         $json['data_1']      = $reelss;
         $json['data']        = $resultnew;
       }
       else
       {
         $this->db->where("reels.id",$reel_id);
         $this->db->select("reels.*,users.name as user_name,users.image as user_image");
         $this->db->from("reels");
         $this->db->join("users","users.id = reels.user_id");
         $reelss = $this->db->get()->row();
         
         
       
         $json['result']      = "true";
         $json['msg']         = "All Comment";
         $json['user_path']   = base_url()."assets/images/users/";
         $json['data_1']      = $reelss;
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required reel_id"; 
     }
     
     
     echo json_encode($json);
   }
   
   
   
   
   
   
   
   public function report_community_post()
   {
     
     $user_id      = $this->input->post('user_id');
     $community_id = $this->input->post('community_id');
     $report       = $this->input->post('report');
     
     
     
     if(isset($user_id) && isset($community_id) && isset($report))
     {
       $post_data['user_id']      = $user_id;
       $post_data['community_id'] = $community_id;
       $post_data['report']       = $report;
       
       $post_data['created_date'] = date('Y-m-d H:i:s',time());
       
       $res = $this->Api_Model->insertAllData('community_report',$post_data);
       
       if(isset($res))
       {
         $json['result'] = "true";
         $json['msg']    = "Reported";
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "something went wrong";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,community_id,report"; 
     }
     
     
     
     echo json_encode($json);
     
     
     
   }
   
   
   
   
   
   
   
   
   
   
   
   
    public function report_reel_post()
   {
     
     $user_id      = $this->input->post('user_id');
     $reel_id      = $this->input->post('reel_id');
     $report       = $this->input->post('report');
     
     
     
     if(isset($user_id) && isset($reel_id) && isset($report))
     {
       $post_data['user_id']      = $user_id;
       $post_data['reel_id']      = $reel_id;
       $post_data['report']       = $report;
       
       $post_data['created_date'] = date('Y-m-d H:i:s',time());
       
       $res = $this->Api_Model->insertAllData('reel_report',$post_data);
       
       if(isset($res))
       {
         $json['result'] = "true";
         $json['msg']    = "Reported";
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "something went wrong";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id,reel_id,report"; 
     }
     
     
     
     echo json_encode($json);
     
     
     
   }
   
   
   
   
   
   
   
   
   
   public function get_my_reels()
   {
     $user_id = $this->input->post('user_id');
     
     
     if(isset($user_id))
     {
      //  $res = $this->Api_Model->get_my_reels('reels',$user_id);
       
       
       $res = $this->db->query("SELECT * FROM reels WHERE reels.user_id='$user_id' AND (reels.type='image' OR reels.type='video') ORDER BY id DESC")->result();
       
       
       if($res)
       {
         $json['result'] = "true";
         $json['msg']    = "All Data";
         $json['path']   = base_url()."assets/images/reels/";
         $json['data']   = $res;
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "No Data"; 
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id"; 
     }
     
     
      echo json_encode($json);
   }
   
   
   
   
   
   
   
   
   
   public function get_my_design()
   {
     $user_id = $this->input->post('user_id');
     
     if(isset($user_id))
     {
       
       $wheredata = array('field'=>'id,file',

       'table'=>'reels',

       'where'=>array('type' => 'image'),

       'order_by'=>'id desc'

       );



       $result = $this->Api_Model->getAllData($wheredata);
       
       
       if($result)
       {
         $json['result'] = "true";
         $json['msg']    = "All Data";
         $json['path']   = base_url()."assets/images/reels/";
         $json['data']   = $result;
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "No Data";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id";
     }
     
     
     echo json_encode($json);
   }
   
   
   
   
   
   
   
   
   public function get_notification_list()
   {
     $user_id = $this->input->post('user_id');
     
     if(isset($user_id))
     {
       $wheredata = array('field'=>'id,title,description,filepath',

       'table'=>'notifactions',

       'where'=>array(),

       'order_by'=>'id desc'

       );



      $result = $this->Api_Model->getAllData($wheredata);
      
      if($result)
      {
        $json['result'] = "true";
        $json['msg']    = "All Notification";
        $json['path']   = base_url()."/assets/images/image-gallery/thumb/";
        $json['data']   = $result;
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "No Notification";
      }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id";
     }
     
     
     echo json_encode($json);
   }
   
   



  public function test()
  {
    $this->db->where("id",31);
    $this->db->select("fcm_id");
    $this->db->from("users");
    $vv = $this->db->get()->row();
    
    
    
    $notification = array(
               'title' =>"Liked on reel",
               'body'  =>"ggg"
             );
    
     send_notification($notification,$vv->fcm_id);
  }





/*-------------start Anju---------------- */


  public function signup_old()
  {
    $name          = $this->input->post('name');
    $mobile        = $this->input->post('mobile');
    $email         = $this->input->post('email');
    $password         = $this->input->post('password');
    
    
    
    
    
    
    if(isset($name) && isset($mobile) && isset($email) && isset($password))
    {
      if(!$this->CommanModel->is_record_exist('users','mobile',$mobile))
      {
        
        if(!$this->CommanModel->is_record_exist('users','email',$email))
        {
        
         
          
      /*    if(!empty($_FILES['image']['name'])){
        $image=$this->imageUpload('image','assets/images/users/');
      }*/
      
      // else{
      //     $image = 'default.jpg';
      // }
            $otp = rand(1000,9999);
          
     
          
          
             $postdata = array(
            'name' => $name,
            'mobile' => $mobile,
            'email' => $email,
            'otp' => $otp,
            'password' => $password,
           
            'created_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s'),
            );
            
           $result = $this->Api_Model->insertAllData('users', $postdata);  
           
           
           if($result)
           {
             
         
             $this->db->where("users.id",$result);
             $this->db->select("id,otp");
             $this->db->from("users");
             $vv = $this->db->get()->row();
         
             
             
             $json['result']  = "true";
             $json['msg']     = "signup successfully!";
             // $json['path']     = base_url()."assets/images/users/";
             $json['user_id'] = $vv->id;
             $json['otp']     = $otp;
            
           }
           else
           {
             $json['result']  = "false";
             $json['msg']     = "something went wrong!";
           }
           
           
           
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "Email already exist!";
        }
        
      }
      else
      {
        $json['result']  = "false";
        $json['msg']     = "Mobile already exist!";
      }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required name,mobile,email,password";
     
      
    }
    
    
    echo json_encode($json);
    
  }
  
  
  
  
  public function loginold()
   {
     $mobile   = $this->input->post('mobile');
     
     if(isset($mobile))
     {
       if($this->CommanModel->is_record_exist('users','mobile',$mobile))
       {


           $otp = rand(1000,9999);
      
        $post_data = array(
          'otp'=>$otp,
          );
          
              $this->db->where('mobile',$mobile);
        $result_1 = $this->db->update('users',$post_data);



          $wheredata = array('field'=>'id,name,email,otp,status',

           'table'=>'users',

           'where'=>array('mobile' => $mobile),
       

          );



        $result = $this->Api_Model->getAllDataRow($wheredata);
        
         
          $json['result'] = "true";
          $json['msg']    = "Login Successful";
          $json['data']   = $result;
        
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "Mobile not register";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required mobile";
     }
     
     
     echo json_encode($json);
   }





  
  
  
  
  
  
  
   
   public function login_old2()
   {
     $mobile   = $this->input->post('mobile');
     $fcm_id   = $this->input->post('fcm_id');
     
     if(isset($mobile))
     {
       if($this->Api_Model->is_record_exist('users','mobile',$mobile))
       {
         
         
         if(!empty($fcm_id))
         {
           $fcm_id = $fcm_id;
         }
         else
         {
           $fcm_id = "";
         }
         
        
             $otp = rand(1000,9999);
      
        $post_data = array(
          'otp'=>$otp,
          'fcm_id'=>$fcm_id,
          );
          
              $this->db->where('mobile',$mobile);
        $result_1 = $this->db->update('users',$post_data);



          $wheredata = array('field'=>'id,name,email,otp,mobile',

           'table'=>'users',

           'where'=>array('mobile' => $mobile),
       

          );



        $result = $this->Api_Model->getAllDataRow($wheredata);
        
         
          $json['result'] = "true";
          $json['msg']    = "Login Successful";
          $json['data']   = $result;
         


           
        
       }
       else
       {
         $json['result'] = "false";
         $json['msg']    = "Mobile number not registered";
       }
     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required mobile,fcm_id";
     }
     
     
     echo json_encode($json);
   }
   






  
     public function update_profile()
  {
    $user_id        = $this->input->post('user_id');
    $name           = $this->input->post('name');
    $phone          = $this->input->post('phone');
    $profession            = $this->input->post('profession');
     $address            = $this->input->post('address');
    



    if(isset($user_id))
    {




      $post_data = array();

       if(!empty($name))
       {
        $post_data['name'] = $name;
       }


       if(!empty($phone))
       {
        $post_data['phone'] = $phone;
       }


       if(!empty($profession))
       {
        $post_data['profession'] = $profession;
       }


    if(!empty($address))
       {
        $post_data['address'] = $address;
       }







      if(!empty($_FILES['image']['name']))
      {


        $image = $this->Api_Model->select_single_row('users','id',$user_id);




        if($image->image && file_exists('assets/images/users/'.$image->image))
        {
        unlink('assets/images/users/'.$image->image);


        }


        $image1  = $this->imageUpload('image','assets/images/users/');



        $post_data['image'] = $image1;


      }




       if(sizeof($post_data)>0){
         
         
         $wheredata = array(
           'id' => $user_id
           );

         $update = $this->Api_Model->update($wheredata,'users',$post_data);


        if($update)
        {
        $json['result'] = "true";
        $json['msg']    = "profile updated successfully";
        
        }else{
        $json['result']  = "false";
        $json['msg']     = "Something went wrong.";
        }


       }
       else{

        $json['result'] = "true";
        $json['msg']    = "profile updated successfully";
        


       }


    }
    else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required user_id,optional(name,phone,profession,image,address)';
    }



    echo json_encode($json);
  }
   


 
    public function get_faq()
  {
    $wheredata = array('field'=>'*',

       'table'=>'faq',

       'where'=>array('user_type' => 'Provider'),
       
       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);
    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    
    echo json_encode($json);
  }
  
  
  
  

  
  
  
  
  
  
  
  
  
     public function get_district()
  {
    
    $state_id = $this->input->post('state_id');
    
    if(isset($state_id))
    {
      $wheredata = array('field'=>'id,district',

       'table'=>'districts',

       'where'=>array('state_id' => $state_id),
       
       'order_by'=>'id desc'

       );



    $result = $this->Api_Model->getAllData($wheredata);
    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    
    
    
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required state_id";
    }
    
    
    echo json_encode($json);
  }
  
  
  
  
  
  
  
  
  
  
  public function get_variant()
  {
    $model_id = $this->input->post('model_id');
    
    if(isset($model_id))
    {
      $wheredata = array('field'=>'id,variation_name',

       'table'=>'veriations',

       'where'=>array('model_id' => $model_id),

       'order_by'=>'id desc'

       );



       $result = $this->Api_Model->getAllData($wheredata);
       
       
       if($result)
       {
         $json['result']  = "true";
         $json['msg']     = "All Data";
         $json['data']    = $result;
       }
       else
       {
         $json['result']  = "false";
         $json['msg']     = "No Data";
       }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required model_id";
    }
    
    
    echo json_encode($json);
  }
  
  
  
  
  
  
  
  public function update_profile_sam()
  {
    
    $user_id              = $this->input->post('user_id');
    $fname                = $this->input->post('fname');
    $mname                = $this->input->post('mname');
    $lname                = $this->input->post('lname');
    $email                = $this->input->post('email');
    $mobile               = $this->input->post('mobile');
    $state_id             = $this->input->post('state_id');
    $city_id              = $this->input->post('city_id');
    $locality             = $this->input->post('locality');
    $address              = $this->input->post('address');
    $service_center_name  = $this->input->post('service_center_name');
    $specialized_brand    = $this->input->post('specialized_brand');
    
    $experience           = $this->input->post('experience');
    $garage_address       = $this->input->post('garage_address');
    
    $dealership_name      = $this->input->post('dealership_name');
    $dealership_address   = $this->input->post('dealership_address');
    $dealership_pincode   = $this->input->post('dealership_pincode');
    $dealership_state_id  = $this->input->post('dealership_state_id');
    $dealership_city_id   = $this->input->post('dealership_city_id');
    $pincode              = $this->input->post('pincode');
    
    
    
    
    if(isset($user_id))
    {
        $post_data = array();




         if(!empty($fname))
         {
           $post_data['vendor_name'] = $fname;
         }
         
         
         if(!empty($mname))
         {
           $post_data['middle_name'] = $mname;
         }
         
         
         if(!empty($lname))
         {
           $post_data['last_name'] = $lname;
         }
         
         
         if(!empty($email))
         {
           $post_data['vendor_email'] = $email;
         }
         
         
         if(!empty($mobile))
         {
           $post_data['phone'] = $mobile;
         }
         
         
         if(!empty($state_id))
         {
           $post_data['state'] = $state_id;
         }
         
         
         if(!empty($city_id))
         {
           $post_data['district'] = $city_id;
         }
         
         
         
         if(!empty($locality))
         {
           $post_data['locality'] = $locality;
         }
         
         
         
         if(!empty($address))
         {
           $post_data['address'] = $address;
         }
         
         
         
         if(!empty($service_center_name))
         {
           $post_data['service_centre'] = $service_center_name;
         }
         
         
         
         if(!empty($specialized_brand))
         {
           $post_data['specialized_brand'] = $specialized_brand;
         }


         if(!empty($experience))
         {
           $post_data['exp_vehicle_repair'] = $experience;
         }
         
         
         
         if(!empty($garage_address))
         {
           $post_data['garage_address'] = $garage_address;
         }
         
         
         
         if(!empty($dealership_name))
         {
           $post_data['dealership_name'] = $dealership_name;
         }
         
         
         if(!empty($dealership_address))
         {
           $post_data['address_dealership'] = $dealership_address;
         }



         if(!empty($dealership_pincode))
         {
           $post_data['pincode_dealership'] = $dealership_pincode;
         }
         
         
         
         if(!empty($dealership_state_id))
         {
           $post_data['state_dealership'] = $dealership_state_id;
         }
         
         
         
         if(!empty($dealership_city_id))
         {
           $post_data['district_dealership'] = $dealership_city_id;
         }
         
         
         
         if(!empty($pincode))
         {
           $post_data['pincode'] = $pincode;
         }

         


       if(sizeof($post_data)>0)
       {



         $whereas = array(
           'vendor_id' => $user_id
           );




         $update =  $this->Api_Model->updateData_tt('vendor',$post_data,$whereas);




         if($update)
         {
          $json['result'] = "true";
          $json['msg']    = "Updated!";
         }
         else
         {
          $json['result']  = "false";
          $json['msg']     = "Something went wrong.";
         }


       }
       else
       {

        $json['result'] = "true";
        $json['msg']    = "Updated!";
       }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id,optional(fname,mname,lname,email,mobile,state_id,city_id,locality,address,service_center_name,specialized_brand,experience,garage_address,dealership_name,dealership_address,dealership_pincode,dealership_state_id,dealership_city_id,pincode)";
    }
    
    
    
    echo json_encode($json);
  }
  
  
  
  
  
  
  
  
  
  
  
  public function add_vehicle()
  {
    $user_id                = $this->input->post('user_id');
    $brand_id               = $this->input->post('brand_id');
    $model_id               = $this->input->post('model_id');
    $variant_id             = $this->input->post('variant_id');
    $manufacture_year       = $this->input->post('manufacture_year');
    $vehicle_number         = $this->input->post('vehicle_number');
    $vehicle_color          = $this->input->post('vehicle_color');
    $vehicle_board          = $this->input->post('vehicle_board');
    $state_id               = $this->input->post('state_id');
    $city_id                = $this->input->post('city_id');
    $fuel                   = $this->input->post('fuel');
    $transmission           = $this->input->post('transmission');
    $no_of_owner            = $this->input->post('no_of_owner');
    $km_driven              = $this->input->post('km_driven');
    $set_a_price            = $this->input->post('set_a_price');
    $rc_status              = $this->input->post('rc_status');
    $insurance              = $this->input->post('insurance');
    $insurance_company_name = $this->input->post('insurance_company_name');
    $insurance_expiry_date  = $this->input->post('insurance_expiry_date');
    $insurance_type         = $this->input->post('insurance_type');
    $financier_noc          = $this->input->post('financier_noc');
    $duplicate_key          = $this->input->post('duplicate_key');
    $description            = $this->input->post('description');
    
    
    
    if(isset($user_id) && isset($brand_id) && isset($model_id) && isset($variant_id) && isset($manufacture_year) && isset($vehicle_number) && isset($vehicle_color) && isset($vehicle_board) && isset($state_id) && isset($city_id) && isset($fuel) && isset($transmission) && isset($no_of_owner) && isset($km_driven) && isset($set_a_price) && isset($rc_status) && isset($insurance) && isset($financier_noc) && isset($duplicate_key) && isset($description))
    {
      $post_data['vendor_id']            = $user_id;
      $post_data['maker_id']             = $brand_id;
      $post_data['model_id']             = $model_id;
      $post_data['veriant_id']           = $variant_id;
      $post_data['year']                 = $manufacture_year;
      $post_data['vehicle_number']       = $vehicle_number;
      $post_data['color']                = $vehicle_color;
      $post_data['vehicle_board']        = $vehicle_board;
      $post_data['state']                = $state_id;
      $post_data['district']             = $city_id;
      $post_data['fuel']                 = $fuel;
      $post_data['trans']                = $transmission;
      
      $post_data['no_owners']            = $no_of_owner;
      $post_data['km_driven']            = $km_driven;
      $post_data['set_a_price']          = $set_a_price;
      $post_data['rc_status']            = $rc_status;
      $post_data['insurance']            = $insurance;
      $post_data['financiar_noc_status'] = $financier_noc;
      $post_data['DuplicateKey']         = $duplicate_key;
      $post_data['desc']                 = $description;
      
      
      
      
      if(!empty($insurance_company_name))
      {
        $post_data['insurance_company_name'] = $insurance_company_name;
      }
      
      
      if(!empty($insurance_expiry_date))
      {
        $post_data['insurance_expiry_date'] = $insurance_expiry_date;
      }
      
      
      if(!empty($insurance_type))
      {
        $post_data['insurance_type'] = $insurance_type;
      }
      
      
      
      
      
      
      $result = $this->Api_Model->insertAllData('vehicles',$post_data); 
      
      
      if($result)
      {
        
       
           $df = array(
             'uniqueid' => 'DX'.$result
             
             );
           
           $this->db->where("id",$result);
           $this->db->update("vehicles",$df);
        
        
        
        
        $post_data_1 = array();
        
        if(!empty($_FILES['front_side']['name']))
        {

          $front_side  = $this->imageUpload('front_side','assets/images/brand/');

          $post_data_1['front_side'] = $front_side;

        }
        
        
        
        if(!empty($_FILES['left_side']['name']))
        {

          $left_side  = $this->imageUpload('left_side','assets/images/brand/');

          $post_data_1['left_side'] = $left_side;

        }
        
        
        
        if(!empty($_FILES['right_side']['name']))
        {

          $right_side  = $this->imageUpload('right_side','assets/images/brand/');

          $post_data_1['right_side'] = $right_side;

        }
        
        
        
        if(!empty($_FILES['back_side']['name']))
        {

          $back_side  = $this->imageUpload('back_side','assets/images/brand/');

          $post_data_1['back_side'] = $back_side;

        }
        
        
        
        if(!empty($_FILES['dicky']['name']))
        {

          $dicky = $this->imageUpload('dicky','assets/images/brand/');

          $post_data_1['dicky'] = $dicky;

        }
        
        
        
        if(!empty($_FILES['left_quarter_panel']['name']))
        {

          $left_quarter_panel = $this->imageUpload('left_quarter_panel','assets/images/brand/');

          $post_data_1['left_quarter_panel'] = $left_quarter_panel;

        }
        
        
        
        
        if(!empty($_FILES['right_quarter_panel']['name']))
        {

          $right_quarter_panel = $this->imageUpload('right_quarter_panel','assets/images/brand/');

          $post_data_1['right_quarter_panel'] = $right_quarter_panel;

        }
        
        
        
        if(!empty($_FILES['dashboard']['name']))
        {

          $dashboard = $this->imageUpload('dashboard','assets/images/brand/');

          $post_data_1['dashboard'] = $dashboard;

        }
        
        
        
        
        if(!empty($_FILES['interior']['name']))
        {

          $interior = $this->imageUpload('interior','assets/images/brand/');

          $post_data_1['interior'] = $interior;

        }
        
        
        
        
        if(!empty($_FILES['engine']['name']))
        {

          $engine = $this->imageUpload('engine','assets/images/brand/');

          $post_data_1['engine'] = $engine;

        }
        
        
        
        
        if(!empty($_FILES['spare_wheel']['name']))
        {

          $spare_wheel = $this->imageUpload('spare_wheel','assets/images/brand/');

          $post_data_1['spare_wheel'] = $spare_wheel;

        }
        
        
        
        if(!empty($_FILES['tool_kit']['name']))
        {

          $tool_kit = $this->imageUpload('tool_kit','assets/images/brand/');

          $post_data_1['tool_kit'] = $tool_kit;

        }
        
        
        
        if(!empty($_FILES['sun_roof']['name']))
        {

          $sun_roof = $this->imageUpload('sun_roof','assets/images/brand/');

          $post_data_1['sun_roof'] = $sun_roof;

        }
        
        
        
        
        if(!empty($_FILES['wheel_rim']['name']))
        {

          $wheel_rim = $this->imageUpload('wheel_rim','assets/images/brand/');

          $post_data_1['wheel_rim'] = $wheel_rim;

        }
        
        
        
        
        if(!empty($_FILES['front_wind_sheild']['name']))
        {

          $front_wind_sheild = $this->imageUpload('front_wind_sheild','assets/images/brand/');

          $post_data_1['front_wind_sheild'] = $front_wind_sheild;

        }
        
        
        
        
        
        if(!empty($_FILES['odo_meter_reading']['name']))
        {

          $odo_meter_reading = $this->imageUpload('odo_meter_reading','assets/images/brand/');

          $post_data_1['odo_meter_reading'] = $odo_meter_reading;

        }
        
        
        
        
        if(!empty($_FILES['front_rh_door_trim']['name']))
        {

          $front_rh_door_trim = $this->imageUpload('front_rh_door_trim','assets/images/brand/');

          $post_data_1['front_rh_door_trim'] = $front_rh_door_trim;

        }
        
        
        
        
        if(!empty($_FILES['other_image_1']['name']))
        {

          $other_image_1 = $this->imageUpload('other_image_1','assets/images/brand/');

          $post_data_1['other_image_1'] = $other_image_1;

        }
        
        
        
        if(!empty($_FILES['other_image_2']['name']))
        {

          $other_image_2 = $this->imageUpload('other_image_2','assets/images/brand/');

          $post_data_1['other_image_2'] = $other_image_2;

        }
        
        
        
        if(!empty($_FILES['other_image_3']['name']))
        {

          $other_image_3 = $this->imageUpload('other_image_3','assets/images/brand/');

          $post_data_1['other_image_3'] = $other_image_3;

        }
        
        
        
        
        $post_data_1['vehicle_id'] = $result;
        $post_data_1['status']     = "active";
        $post_data_1['created_at'] = date('Y-m-d H:i:s',time());
        $post_data_1['updated_at'] = date('Y-m-d H:i:s',time());
        
        
         if(sizeof($post_data_1)>0)
         {
         
           $this->Api_Model->insertAllData('vehicleimages',$post_data_1); 
         
         

         }
        
        
        
        $json['result'] = "true";
        $json['msg']    = "vehicle added";
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "something went wrong";
      }
      
      
      
      
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required user_id,brand_id,model_id,variant_id,manufacture_year,vehicle_number,vehicle_color,vehicle_board(white_board or yellow_board or black_board),state_id,city_id,fuel(cng_hybrids or diesel or electric or lpg or petrol),transmission(automatic or manual),no_of_owner(1st or 2nd or 3rd or 4th or 4th+),km_driven,set_a_price,rc_status(original_rc or duplicate_rc or not_confirmed),insurance(available or not_available or expired),insurance_company_name,insurance_expiry_date,insurance_type(Comprehensive or Zero Depreciation or Third Party),financier_noc(available or not_available or expired),duplicate_key(not_available or available),description,front_side,left_side,right_side,back_side,dicky,left_quarter_panel,right_quarter_panel,dashboard,interior,engine,spare_wheel,tool_kit,sun_roof,wheel_rim,front_wind_sheild,odo_meter_reading,front_rh_door_trim,other_image_1,other_image_2,other_image_3";
    }
    
    
    echo json_encode($json);
    
  }
  
  
  
  
  
  
  
  
  
  public function update_vehicle()
  {
    $vehicle_id             = $this->input->post('vehicle_id');
    $brand_id               = $this->input->post('brand_id');
    $model_id               = $this->input->post('model_id');
    $variant_id             = $this->input->post('variant_id');
    $manufacture_year       = $this->input->post('manufacture_year');
    $vehicle_number         = $this->input->post('vehicle_number');
    $vehicle_color          = $this->input->post('vehicle_color');
    $vehicle_board          = $this->input->post('vehicle_board');
    $state_id               = $this->input->post('state_id');
    $city_id                = $this->input->post('city_id');
    $fuel                   = $this->input->post('fuel');
    $transmission           = $this->input->post('transmission');
    $no_of_owner            = $this->input->post('no_of_owner');
    $km_driven              = $this->input->post('km_driven');
    $set_a_price            = $this->input->post('set_a_price');
    $rc_status              = $this->input->post('rc_status');
    $insurance              = $this->input->post('insurance');
    $insurance_company_name = $this->input->post('insurance_company_name');
    $insurance_expiry_date  = $this->input->post('insurance_expiry_date');
    $insurance_type         = $this->input->post('insurance_type');
    $financier_noc          = $this->input->post('financier_noc');
    $duplicate_key          = $this->input->post('duplicate_key');
    $description            = $this->input->post('description');
    
    
    
    
    if(isset($vehicle_id))
    {
      
       $post_data = array();

     
     
    
   
 
    
     
      
       
     

      
      
      
      
      
      
      
      if(!empty($brand_id))
      {
        $post_data['maker_id'] = $brand_id;
      }
      
      
      if(!empty($model_id))
      {
        $post_data['model_id'] = $model_id;
      }
      
      
      if(!empty($variant_id))
      {
        $post_data['veriant_id'] = $variant_id;
      }
      
      
      
      if(!empty($manufacture_year))
      {
        $post_data['year'] = $manufacture_year;
      }
      
      
      if(!empty($vehicle_number))
      {
        $post_data['vehicle_number'] = $vehicle_number;
      }
      
      
      if(!empty($vehicle_color))
      {
        $post_data['color'] = $vehicle_color;
      }
      
      
      if(!empty($vehicle_board))
      {
        $post_data['vehicle_board'] = $vehicle_board;
      }
      
      
      if(!empty($state_id))
      {
        $post_data['state'] = $state_id;
      }
      
      
      
      if(!empty($city_id))
      {
        $post_data['district'] = $city_id;
      }
      
      
      if(!empty($fuel))
      {
        $post_data['fuel'] = $fuel;
      }
      
      
      
      if(!empty($transmission))
      {
        $post_data['trans'] = $transmission;
      }
      
      
      
      if(!empty($no_of_owner))
      {
        $post_data['no_owners'] = $no_of_owner;
      }
      
      
      
      if(!empty($km_driven))
      {
        $post_data['km_driven'] = $km_driven;
      }
      
      
      
      if(!empty($set_a_price))
      {
        $post_data['set_a_price'] = $set_a_price;
      }
      
      
      
      if(!empty($rc_status))
      {
        $post_data['rc_status'] = $rc_status;
      }
      
      
      
      if(!empty($insurance))
      {
        $post_data['insurance'] = $insurance;
      }
      
      
      
      if(!empty($financier_noc))
      {
        $post_data['financiar_noc_status'] = $financier_noc;
      }
      
      
      if(!empty($duplicate_key))
      {
        $post_data['DuplicateKey'] = $duplicate_key;
      }
      
      
      
      if(!empty($description))
      {
        $post_data['desc'] = $description;
      }
      
      
      //
      
      
      
      if(!empty($insurance_company_name))
      {
        $post_data['insurance_company_name'] = $insurance_company_name;
      }
      
      
      if(!empty($insurance_expiry_date))
      {
        $post_data['insurance_expiry_date'] = $insurance_expiry_date;
      }
      
      
      if(!empty($insurance_type))
      {
        $post_data['insurance_type'] = $insurance_type;
      }
      
 

       
       
       $post_data['updated_at'] = date('Y-m-d H:i:s',time());



       if(sizeof($post_data)>0){
         
         
         $wheredata = array(
           'id' => $vehicle_id
           );

         $update = $this->Api_Model->updates($wheredata,'vehicles',$post_data);


        if($update)
        {
        $json['result'] = "true";
        $json['msg']    = "vehicle updated";
        
        }else{
        $json['result']  = "false";
        $json['msg']     = "Something went wrong.";
        }


       }
       else
       {

        $json['result'] = "true";
        $json['msg']    = "vehicle updated";
        


       }
       
       
       
       
       
    }
    else
    {
       $json['result'] = "false";
       $json['msg']    = "parameter required vehicle_id,brand_id,model_id,variant_id,manufacture_year,vehicle_number,vehicle_color,vehicle_board(white_board or yellow_board or black_board),state_id,city_id,fuel(cng_hybrids or diesel or electric or lpg or petrol),transmission(automatic or manual),no_of_owner(1st or 2nd or 3rd or 4th or 4th+),km_driven,set_a_price,rc_status(original_rc or duplicate_rc or not_confirmed),insurance(available or not_available or expired),insurance_company_name,insurance_expiry_date,insurance_type(Comprehensive or Zero Depreciation or Third Party),financier_noc(available or not_available or expired),duplicate_key(not_available or available),description";
    }
    
    
    
    echo json_encode($json);
  }
  
   
  

  
  
    
    
    public function update_vehicle_images()
    {
      $vehicle_id = $this->input->post('vehicle_id');
      
      if(isset($vehicle_id))
      {
         $post_data = array();

        
        
        
        $image = $this->Api_Model->select_single_row_ss('vehicleimages','vehicle_id',$vehicle_id);


        if(!empty($_FILES['front_side']['name']))
        {

        if($image->front_side && file_exists('assets/images/brand/'.$image->front_side))
        {
          unlink('assets/images/brand/'.$image->front_side);

        }


        $image1  = $this->imageUpload('front_side','assets/images/brand/');

        $post_data['front_side'] = $image1;
        
        }
        
        
        
        
        if(!empty($_FILES['left_side']['name']))
        {

        if($image->left_side && file_exists('assets/images/brand/'.$image->left_side))
        {
          unlink('assets/images/brand/'.$image->left_side);

        }


        $left_side  = $this->imageUpload('left_side','assets/images/brand/');

        $post_data['left_side'] = $left_side;
        
        }
        
        
        
        
        if(!empty($_FILES['right_side']['name']))
        {

        if($image->right_side && file_exists('assets/images/brand/'.$image->right_side))
        {
          unlink('assets/images/brand/'.$image->right_side);

        }


        $right_side  = $this->imageUpload('right_side','assets/images/brand/');

        $post_data['right_side'] = $right_side;
        
        }
        
        
        
        
        
        if(!empty($_FILES['back_side']['name']))
        {

        if($image->back_side && file_exists('assets/images/brand/'.$image->back_side))
        {
          unlink('assets/images/brand/'.$image->back_side);

        }


        $back_side  = $this->imageUpload('back_side','assets/images/brand/');

        $post_data['back_side'] = $back_side;
        
        }
        
        
        
        
        if(!empty($_FILES['dicky']['name']))
        {

        if($image->dicky && file_exists('assets/images/brand/'.$image->dicky))
        {
          unlink('assets/images/brand/'.$image->dicky);

        }


        $dicky  = $this->imageUpload('dicky','assets/images/brand/');

        $post_data['dicky'] = $dicky;
        
        }
        
        
        
        
        
        if(!empty($_FILES['left_quarter_panel']['name']))
        {

        if($image->left_quarter_panel && file_exists('assets/images/brand/'.$image->left_quarter_panel))
        {
          unlink('assets/images/brand/'.$image->left_quarter_panel);

        }


        $left_quarter_panel = $this->imageUpload('left_quarter_panel','assets/images/brand/');

        $post_data['left_quarter_panel'] = $left_quarter_panel;
        
        }
        
        
        
        
        if(!empty($_FILES['right_quarter_panel']['name']))
        {

        if($image->right_quarter_panel && file_exists('assets/images/brand/'.$image->right_quarter_panel))
        {
          unlink('assets/images/brand/'.$image->right_quarter_panel);

        }


        $right_quarter_panel = $this->imageUpload('right_quarter_panel','assets/images/brand/');

        $post_data['right_quarter_panel'] = $right_quarter_panel;
        
        }
        
        
        
        
        
        if(!empty($_FILES['dashboard']['name']))
        {

        if($image->dashboard && file_exists('assets/images/brand/'.$image->dashboard))
        {
          unlink('assets/images/brand/'.$image->dashboard);

        }


        $dashboard = $this->imageUpload('dashboard','assets/images/brand/');

        $post_data['dashboard'] = $dashboard;
        
        }
        
        
        
        
        
        if(!empty($_FILES['interior']['name']))
        {

        if($image->interior && file_exists('assets/images/brand/'.$image->interior))
        {
          unlink('assets/images/brand/'.$image->interior);

        }


        $interior = $this->imageUpload('interior','assets/images/brand/');

        $post_data['interior'] = $interior;
        
        }
        
        
        
        
        
        if(!empty($_FILES['engine']['name']))
        {

        if($image->engine && file_exists('assets/images/brand/'.$image->engine))
        {
          unlink('assets/images/brand/'.$image->engine);

        }


        $engine = $this->imageUpload('engine','assets/images/brand/');

        $post_data['engine'] = $engine;
        
        }
        
        
        
        
        
        
        if(!empty($_FILES['spare_wheel']['name']))
        {

        if($image->spare_wheel && file_exists('assets/images/brand/'.$image->spare_wheel))
        {
          unlink('assets/images/brand/'.$image->spare_wheel);

        }


        $spare_wheel = $this->imageUpload('spare_wheel','assets/images/brand/');

        $post_data['spare_wheel'] = $spare_wheel;
        
        }
        
        
        
        
        
        if(!empty($_FILES['tool_kit']['name']))
        {

        if($image->tool_kit && file_exists('assets/images/brand/'.$image->tool_kit))
        {
          unlink('assets/images/brand/'.$image->tool_kit);

        }


        $tool_kit = $this->imageUpload('tool_kit','assets/images/brand/');

        $post_data['tool_kit'] = $tool_kit;
        
        }
        
        
        
        
        
        if(!empty($_FILES['sun_roof']['name']))
        {

        if($image->sun_roof && file_exists('assets/images/brand/'.$image->sun_roof))
        {
          unlink('assets/images/brand/'.$image->sun_roof);

        }


        $sun_roof = $this->imageUpload('sun_roof','assets/images/brand/');

        $post_data['sun_roof'] = $sun_roof;
        
        }
        
        
        
        
        
         if(!empty($_FILES['wheel_rim']['name']))
        {

        if($image->wheel_rim && file_exists('assets/images/brand/'.$image->wheel_rim))
        {
          unlink('assets/images/brand/'.$image->wheel_rim);

        }


        $wheel_rim = $this->imageUpload('wheel_rim','assets/images/brand/');

        $post_data['wheel_rim'] = $wheel_rim;
        
        }
        
        
        
        
        
         if(!empty($_FILES['front_wind_sheild']['name']))
        {

        if($image->front_wind_sheild && file_exists('assets/images/brand/'.$image->front_wind_sheild))
        {
          unlink('assets/images/brand/'.$image->front_wind_sheild);

        }


        $front_wind_sheild = $this->imageUpload('front_wind_sheild','assets/images/brand/');

        $post_data['front_wind_sheild'] = $front_wind_sheild;
        
        }
        
        
        
        
        
        
         if(!empty($_FILES['odo_meter_reading']['name']))
        {

        if($image->odo_meter_reading && file_exists('assets/images/brand/'.$image->odo_meter_reading))
        {
          unlink('assets/images/brand/'.$image->odo_meter_reading);

        }


        $odo_meter_reading = $this->imageUpload('odo_meter_reading','assets/images/brand/');

        $post_data['odo_meter_reading'] = $odo_meter_reading;
        
        }
        
        
        
        
        
         if(!empty($_FILES['front_rh_door_trim']['name']))
        {

        if($image->front_rh_door_trim && file_exists('assets/images/brand/'.$image->front_rh_door_trim))
        {
          unlink('assets/images/brand/'.$image->front_rh_door_trim);

        }


        $front_rh_door_trim = $this->imageUpload('front_rh_door_trim','assets/images/brand/');

        $post_data['front_rh_door_trim'] = $front_rh_door_trim;
        
        }
        
        
        
        
        
        
         if(!empty($_FILES['other_image_1']['name']))
        {

        if($image->other_image_1 && file_exists('assets/images/brand/'.$image->other_image_1))
        {
          unlink('assets/images/brand/'.$image->other_image_1);

        }


        $other_image_1 = $this->imageUpload('other_image_1','assets/images/brand/');

        $post_data['other_image_1'] = $other_image_1;
        
        }
        
        
        
         if(!empty($_FILES['other_image_2']['name']))
        {

        if($image->other_image_2 && file_exists('assets/images/brand/'.$image->other_image_2))
        {
          unlink('assets/images/brand/'.$image->other_image_2);

        }


        $other_image_2 = $this->imageUpload('other_image_2','assets/images/brand/');

        $post_data['other_image_2'] = $other_image_2;
        
        }
        
        
        
        
         if(!empty($_FILES['other_image_3']['name']))
        {

        if($image->other_image_3 && file_exists('assets/images/brand/'.$image->other_image_3))
        {
          unlink('assets/images/brand/'.$image->other_image_3);

        }


        $other_image_3 = $this->imageUpload('other_image_3','assets/images/brand/');

        $post_data['other_image_3'] = $other_image_3;
        
        }




       if(sizeof($post_data)>0){
         
         
         $wheredata = array(
           'vehicle_id' => $vehicle_id
           );

         $update = $this->Api_Model->updates($wheredata,'vehicleimages',$post_data);


        if($update)
        {
        $json['result'] = "true";
        $json['msg']    = "vehicle updated";
        
        }else{
        $json['result']  = "false";
        $json['msg']     = "Something went wrong.";
        }


       }
       else{

        $json['result'] = "true";
        $json['msg']    = "vehicle updated";
        


       }
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "parameter required vehicle_id,front_side,left_side,right_side,back_side,dicky,left_quarter_panel,right_quarter_panel,dashboard,interior,engine,spare_wheel,tool_kit,sun_roof,wheel_rim,front_wind_sheild,odo_meter_reading,front_rh_door_trim,other_image_1,other_image_2,other_image_3";
      }
      
      
      echo json_encode($json);
    }
    
    
    
    
    
    
    
    
    
    
    public function get_my_vehicle_list()
    {
      $user_id = $this->input->post('user_id');
      
      if(isset($user_id))
      {
         $wheredata = array(
        'vehicles.vendor_id'  => $user_id
        );

        $joins = array([
          'table'  =>'makes',
          'on'  =>'makes.id = vehicles.maker_id',
          'right_left' => 'left'
        ],
        [
          'table'  =>'vehicle_models',
          'on'  =>'vehicle_models.id = vehicles.model_id',
          'right_left' => 'left'
        ],
        [
          'table'  =>'vehicleimages',
          'on'  =>'vehicleimages.vehicle_id = vehicles.id',
          'right_left' => 'left'
        ]);

             $select_fields = array(
            'vehicles.id',
            'vehicleimages.front_side as front_side_image',
            'makes.maker as brand_name',
            'makes.Logo as brand_image',
            'vehicles.color',
            'vehicles.fuel',
            'vehicle_models.model_name',
            'vehicles.set_a_price',
            'vehicles.year',
            'vehicles.vehicle_number',
            'vehicles.uniqueid',
            );


      $order_by = "vehicles.id DESC";





       $result = $this->Api_Model->getSingleResultWithJoin('vehicles',$select_fields,$joins,$wheredata,$order_by);
       
       
       if($result)
       {
        $json['result'] = "true";
        $json['msg']    = "All Added vehicles";
        $json['path']   = base_url()."assets/images/brand/";
        $json['data']   = $result;
       }
       else
       {
        $json['result'] = "false";
        $json['msg']    = "No Vehicle Added";
       }
       
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "parmeter required user_id";
      }
      
      
       echo json_encode($json);
    }
    
    
    
    
    
    
    
    
    
    public function single_vehicle_detail()
    {
      
      $user_id    = $this->input->post('user_id');
      $vehicle_id = $this->input->post('vehicle_id');
      
      if(isset($vehicle_id) && isset($user_id))
      {
         $wheredata = array(
        'vehicles.id'  => $vehicle_id
        
        );

   

      $joins = array(
      [
          'table'  =>'makes',
          'on'  =>'makes.id = vehicles.maker_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicle_models',
          'on'  =>'vehicle_models.id = vehicles.model_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'veriations',
          'on'  =>'veriations.id = vehicles.veriant_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'states',
          'on'  =>'states.id = vehicles.state',
          'right_left' => 'left'
      ],
      [
          'table'  =>'districts',
          'on'  =>'districts.id = vehicles.district',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicleimages',
          'on'  =>'vehicleimages.vehicle_id = vehicles.id',
          'right_left' => 'left'
      ]);

             $select_fields = array(
            'vehicles.*',
            'makes.maker as brand_name',
            'vehicle_models.model_name',
            'veriations.variation_name',
            'states.name as state_name',
            'districts.district as district_name',
            'vehicleimages.front_side',
            'vehicleimages.left_side',
            'vehicleimages.right_side',
            'vehicleimages.back_side',
            'vehicleimages.dicky',
            'vehicleimages.left_quarter_panel',
            'vehicleimages.right_quarter_panel',
            'vehicleimages.dashboard',
            'vehicleimages.interior',
            'vehicleimages.engine',
            'vehicleimages.spare_wheel',
            'vehicleimages.tool_kit',
            'vehicleimages.sun_roof',
            'vehicleimages.wheel_rim',
            'vehicleimages.front_wind_sheild',
            'vehicleimages.odo_meter_reading',
            'vehicleimages.front_rh_door_trim',
            'vehicleimages.other_image_1',
            'vehicleimages.other_image_2',
            'vehicleimages.other_image_3',
            
           
            );


        $row = $this->Api_Model->getSingleRowWithJoin('vehicles',$select_fields,$joins,$wheredata);
        
        
        
        if($row)
        {
          
          $this->db->where("like_unlike_vehicle.vendor_id",$user_id);
          $this->db->where("like_unlike_vehicle.vehical_id",$vehicle_id);
          $this->db->select("id");
          $this->db->from("like_unlike_vehicle");
          $rty = $this->db->get()->row();
          
          if($rty)
          {
            $row->i_liked = 1;
          }
          else
          {
            $row->i_liked = 0;
          }
          
          
          $json['result'] = "true";
          $json['msg']    = "All Detail";
          $json['path']   = base_url()."assets/images/brand/";
          $json['data']   = $row;
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "vehicle_id Invalid";
        }
        
        
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "parmeter required user_id,vehicle_id";
      }
      
      
      echo json_encode($json);
    }
    
    
    
    
    
    
    
    
    
    
    
    public function delete_vehicle()
    {
      $vehicle_id = $this->input->post('vehicle_id');
      
      if(isset($vehicle_id))
      {
         $wheredata1 = array('field'=>['*'],

           'table'=>'vehicleimages',

           'where'=>array('vehicle_id'=>$vehicle_id)

        );


        $is_exist = $this->Api_Model->getAllDataRow($wheredata1);
        
        
         if($is_exist->front_side && file_exists('assets/images/brand/'.$is_exist->front_side))
          {
           unlink('assets/images/brand/'.$is_exist->front_side);

          }
          
          
          if($is_exist->left_side && file_exists('assets/images/brand/'.$is_exist->left_side))
          {
           unlink('assets/images/brand/'.$is_exist->left_side);

          }
          
          
          if($is_exist->right_side && file_exists('assets/images/brand/'.$is_exist->right_side))
          {
           unlink('assets/images/brand/'.$is_exist->right_side);

          }
          
          
          if($is_exist->back_side && file_exists('assets/images/brand/'.$is_exist->back_side))
          {
           unlink('assets/images/brand/'.$is_exist->back_side);

          }
          
          
          if($is_exist->dicky && file_exists('assets/images/brand/'.$is_exist->dicky))
          {
           unlink('assets/images/brand/'.$is_exist->dicky);

          }
          
          
          if($is_exist->left_quarter_panel && file_exists('assets/images/brand/'.$is_exist->left_quarter_panel))
          {
           unlink('assets/images/brand/'.$is_exist->left_quarter_panel);

          }
          
          
          if($is_exist->right_quarter_panel && file_exists('assets/images/brand/'.$is_exist->right_quarter_panel))
          {
           unlink('assets/images/brand/'.$is_exist->right_quarter_panel);

          }
          
          
          if($is_exist->dashboard && file_exists('assets/images/brand/'.$is_exist->dashboard))
          {
           unlink('assets/images/brand/'.$is_exist->dashboard);

          }
          
          
          if($is_exist->interior && file_exists('assets/images/brand/'.$is_exist->interior))
          {
           unlink('assets/images/brand/'.$is_exist->interior);

          }
          
          
          if($is_exist->engine && file_exists('assets/images/brand/'.$is_exist->engine))
          {
           unlink('assets/images/brand/'.$is_exist->engine);

          }
          
          
          if($is_exist->spare_wheel && file_exists('assets/images/brand/'.$is_exist->spare_wheel))
          {
           unlink('assets/images/brand/'.$is_exist->spare_wheel);

          }
          
          
          if($is_exist->tool_kit && file_exists('assets/images/brand/'.$is_exist->tool_kit))
          {
           unlink('assets/images/brand/'.$is_exist->tool_kit);

          }
          
          
          if($is_exist->sun_roof && file_exists('assets/images/brand/'.$is_exist->sun_roof))
          {
           unlink('assets/images/brand/'.$is_exist->sun_roof);

          }
          
          
          if($is_exist->wheel_rim && file_exists('assets/images/brand/'.$is_exist->wheel_rim))
          {
           unlink('assets/images/brand/'.$is_exist->wheel_rim);

          }
          
          
          if($is_exist->front_wind_sheild && file_exists('assets/images/brand/'.$is_exist->front_wind_sheild))
          {
           unlink('assets/images/brand/'.$is_exist->front_wind_sheild);

          }
          
          
          if($is_exist->odo_meter_reading && file_exists('assets/images/brand/'.$is_exist->odo_meter_reading))
          {
           unlink('assets/images/brand/'.$is_exist->odo_meter_reading);

          }
          
          
          if($is_exist->front_rh_door_trim && file_exists('assets/images/brand/'.$is_exist->front_rh_door_trim))
          {
           unlink('assets/images/brand/'.$is_exist->front_rh_door_trim);

          }
          
          
          if($is_exist->other_image_1 && file_exists('assets/images/brand/'.$is_exist->other_image_1))
          {
           unlink('assets/images/brand/'.$is_exist->other_image_1);

          }
          
          
          if($is_exist->other_image_2 && file_exists('assets/images/brand/'.$is_exist->other_image_2))
          {
           unlink('assets/images/brand/'.$is_exist->other_image_2);

          }
          
          
          if($is_exist->other_image_3 && file_exists('assets/images/brand/'.$is_exist->other_image_3))
          {
           unlink('assets/images/brand/'.$is_exist->other_image_3);

          }
          
          
          
          
          
          $this->db->where("vehicleimages.vehicle_id",$vehicle_id);
          $this->db->delete("vehicleimages");
          
          
          
               $this->db->where("vehicles.id",$vehicle_id);
          $deleted = $this->db->delete("vehicles");
          
          
          
          if($deleted)
          {
            $json['result'] = "true";
            $json['msg']    = "vehicle deleted";
          }
          else
          {
            $json['result'] = "false";
            $json['msg']    = "something went wrong";
          }
        
        
        
      }
      else
      {
        $json['result'] = "false";
        $json['msg']    = "parmeter required vehicle_id";
      }
      
      
      echo json_encode($json);
    }
    
    
    
    
    
    
    
    
      public function car_across_state()
      {
        
        
        $user_id = $this->input->post('user_id');
        
        
        if(isset($user_id))
        {
          $wheredata = array('field'=>'id,name,image',

        'table'=>'states',

        'where'=>array(),
       
        'order_by'=>'id desc'

        );



    $result = $this->Api_Model->getAllData($wheredata);
    
    if($result)
    {
      
      
      foreach($result as $value)
      {
        $id    = $value->id;
        $name  = $value->name;
        $image = $value->image;
        
        
        
        $this->db->where("vendor_id !=",$user_id);
        $this->db->where("state",$id);
        $this->db->select("id");
        $this->db->from("vehicles");
        $counts = $this->db->get();
        
        
        
        if($counts->num_rows() > 0)
        {
          $vehicle_count = $counts->num_rows();
        }
        else
        {
          $vehicle_count = 0;
        }
        
        
        
        
        
        $resultnew[] = array(
          
          'id' => $id,
          'name' => $name,
          'image' => $image,
          'vehicle_count' => $vehicle_count,
          
          );
      }
      
      
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['path']    = base_url()."assets/images/state/";
      $json['data']    = $resultnew;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required user_id";
        }
        
        
        
         
    
       echo json_encode($json);
      }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  public function get_live_auction()
  {
    $user_id = $this->input->post('user_id');
    
    if(isset($user_id))
    { 
        $wheredata = array(
        'vehicles.vendor_id !='  => $user_id
        );

      $joins = array([
          'table'  =>'makes',
          'on'  =>'makes.id = vehicles.maker_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicle_models',
          'on'  =>'vehicle_models.id = vehicles.model_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'states',
          'on'  =>'states.id = vehicles.state',
          'right_left' => 'left'
      ],
      [
          'table'  =>'districts',
          'on'  =>'districts.id = vehicles.district',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicleimages',
          'on'  =>'vehicleimages.vehicle_id = vehicles.id',
          'right_left' => 'left'
      ]);

             $select_fields = array(
            'vehicles.id',
            'vehicles.desc',
            'vehicles.trans',
            'makes.maker as brand_name',
            'vehicle_models.model_name',
            'vehicles.km_driven',
            'vehicles.set_a_price',
            'states.name as state_name',
            'districts.district as city_name',
            'vehicleimages.front_side as vehicle_image',
            );


      $order_by = "vehicles.id DESC";




       $result = $this->Api_Model->getSingleResultWithJoin('vehicles',$select_fields,$joins,$wheredata,$order_by);
       
       
       
       if($result)
       {
         $json['result']  = "true";
         $json['msg']     = "All vehicle";
         $json['path']    = base_url()."assets/images/brand/";
         $json['data']    = $result;
       }
       else
       {
         $json['result']  = "false";
         $json['msg']     = "No vehicle";
       }
       
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id";
    }
    
    
    echo json_encode($json);
  }
  
  
  
  
  
  
  
  
  
  
  public function bid_vehicle()
  {
    $user_id    = $this->input->post('user_id');
    $vehicle_id = $this->input->post('vehicle_id');
    $bid_amt    = $this->input->post('bid_amt');
    
    
    if(isset($user_id) && isset($vehicle_id) && isset($bid_amt))
    {
      
      $this->db->where("vehicle_id",$vehicle_id);
      $this->db->where("vendor_id",$user_id);
      $this->db->select("id");
      $this->db->from("bids");
      $already = $this->db->get()->row();
      
      
      
      if($already)
      {
        $json['result']  = "false";
        $json['msg']     = "Already Bid";
      }
      else
      {
        $post_data['vehicle_id']    = $vehicle_id;
        $post_data['vendor_id']     = $user_id;
        $post_data['user_type']     = "vendor";
        $post_data['bidAmount']     = $bid_amt;
        $post_data['BidStatus']     = "Placed";
        $post_data['created_date']  = date('Y-m-d H:i:s',time());
        $post_data['updated_date']  = date('Y-m-d H:i:s',time());
      
        $result = $this->Api_Model->insertAllData('bids',$post_data);
        
        if($result)
        {
          
          $post_data_1['vehicle_id']    = $vehicle_id;
          $post_data_1['vendor_id']     = $user_id;
          $post_data_1['bidAmount']     = $bid_amt;
          $post_data_1['user_type']     = "vendor";
          $post_data_1['created_date']  = date('Y-m-d H:i:s',time());
          
          
      
      
          $this->Api_Model->insertAllData('bid_history',$post_data_1);
          
          
          
          $json['result']  = "true";
          $json['msg']     = "Bid Successfully";
        }
      }
      
      
      
       
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id,vehicle_id,bid_amt";
    }
    
    
    echo json_encode($json);
    
  }
  
  
  
  
  
  
  public function get_home_single_vehicle()
  {
    $user_id  = $this->input->post('user_id');
    $state_id = $this->input->post('state_id');
    $brand_id = $this->input->post('brand_id');
    
    
    if(isset($user_id) && isset($state_id) && isset($brand_id))
    {
      
         if(!empty($state_id))
         {
          $wheredata = array(
            
            'vehicles.vendor_id !='  => $user_id,
            'vehicles.state'  => $state_id,
        
          );
         }
         
         
         if(!empty($brand_id))
         {
           $wheredata = array(
            
            'vehicles.vendor_id !='  => $user_id,
            'vehicles.maker_id'  => $brand_id,
        
          );
         }
      
      
      


      $joins = array([
          'table'  =>'makes',
          'on'  =>'makes.id = vehicles.maker_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicle_models',
          'on'  =>'vehicle_models.id = vehicles.model_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'states',
          'on'  =>'states.id = vehicles.state',
          'right_left' => 'left'
      ],
      [
          'table'  =>'districts',
          'on'  =>'districts.id = vehicles.district',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicleimages',
          'on'  =>'vehicleimages.vehicle_id = vehicles.id',
          'right_left' => 'left'
      ]);

             $select_fields = array(
            'vehicles.id',
            'vehicles.desc',
            'vehicles.trans',
            'makes.maker as brand_name',
            'vehicle_models.model_name',
            'vehicles.km_driven',
            'vehicles.set_a_price',
            'states.name as state_name',
            'districts.district as city_name',
            'vehicleimages.front_side as vehicle_image',
            );


      $order_by = "vehicles.id DESC";




       $result = $this->Api_Model->getSingleResultWithJoin_gg('vehicles',$select_fields,$joins,$wheredata,$order_by);
        
        
        
        if($result)
        {
           $json['result']  = "true";
           $json['msg']     = "All vehicle";
           $json['path']    = base_url()."assets/images/brand/";
           $json['data']    = $result;
         }
         else
         {
           $json['result']  = "false";
           $json['msg']     = "No vehicle";
         }
        
        
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id,state_id,brand_id";
    }
    
    
    echo json_encode($json);
  }
  
  
  
  
  
  
  
  public function get_my_bid_vehicle()
  {
    $user_id = $this->input->post('user_id');
    
    if(isset($user_id))
    {
      
           $wheredata = array(
            
            'bids.vendor_id'  => $user_id,
        
          );
  
      
      
      


      $joins = array([
          'table'  =>'vehicles',
          'on'  =>'vehicles.id = bids.vehicle_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'makes',
          'on'  =>'makes.id = vehicles.maker_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicleimages',
          'on'  =>'vehicleimages.vehicle_id = vehicles.id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicle_models',
          'on'  =>'vehicle_models.id = vehicles.model_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vendor',
          'on'  =>'vendor.vendor_id = vehicles.vendor_id',
          'right_left' => 'left'
      ]
      );

             $select_fields = array(
            'vehicles.id as vehicle_id',
            'makes.maker as brand_name',
            'ifnull(vehicleimages.front_side,"") as vehicle_image',
            'vehicle_models.model_name',
            'vehicles.vehicle_number',
            'vehicles.year as manufacture_year',
            'vendor.vendor_name as owner_name',
            'vehicles.set_a_price',
            'bids.id as bid_id',
            'bids.bidAmount',
            'bids.BidStatus'
            
            );


      $order_by = "bids.id DESC";




       $result = $this->Api_Model->getSingleResultWithJoin_gg('bids',$select_fields,$joins,$wheredata,$order_by);
       
       
       
       if($result)
       {
         
         
         foreach($result as $value)
         {
           
       
           
           $vehicle_id       = $value->vehicle_id;
           $brand_name       = $value->brand_name;
           $vehicle_image    = $value->vehicle_image;
           $model_name       = $value->model_name;
           $vehicle_number   = $value->vehicle_number;
           $manufacture_year = $value->manufacture_year;
           $set_a_price      = $value->set_a_price;
           $bid_id           = $value->bid_id;
           $bidAmount        = $value->bidAmount;
           $BidStatus        = $value->BidStatus;
           $owner_name       = $value->owner_name;
           
           
           
           $this->db->where("vehicle_id",$vehicle_id);
           $this->db->select("id");
           $this->db->from("bids");
           $cc = $this->db->get();
           
           
           if($cc->num_rows() > 0)
           {
             $total_bid = $cc->num_rows();
           }
           else
           {
             $total_bid = 0;
           }
           
           
           
           
           
           $resultnew[] = array(
             
             'vehicle_id' => $vehicle_id,
             'brand_name' => $brand_name,
             'vehicle_image' => $vehicle_image,
             'model_name' => $model_name,
             'vehicle_number' => $vehicle_number,
             'manufacture_year' => $manufacture_year,
             'set_a_price' => $set_a_price,
             'bid_id' => $bid_id,
             'bidAmount' => $bidAmount,
             'BidStatus' => $BidStatus,
             'total_bid' => $total_bid,
             'owner_name' => $owner_name,
             
             );
         }
         
         
         
         $json['result']  = "true";
         $json['msg']     = "All Vehicle";
         $json['path']    = base_url()."assets/images/brand/";
         $json['data']    = $resultnew;
       }
       else
       {
         $json['result']  = "false";
         $json['msg']     = "No Vehicle";
       }
       
       
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id";
    }
    
    
    echo json_encode($json);
  }
  
  
  
  
  
  
  
  
  
  public function get_my_favourite_vehicle()
  {
    $user_id = $this->input->post('user_id');
    
    if(isset($user_id))
    {
       $wheredata = array(
        'like_unlike_vehicle.vendor_id'  => $user_id
        );

      $joins = array([
          'table'  =>'vehicles',
          'on'  =>'vehicles.id = like_unlike_vehicle.vehical_id',
          'right_left' => 'left'
      ],
        [
          'table'  =>'makes',
          'on'  =>'makes.id = vehicles.maker_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicle_models',
          'on'  =>'vehicle_models.id = vehicles.model_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'states',
          'on'  =>'states.id = vehicles.state',
          'right_left' => 'left'
      ],
      [
          'table'  =>'districts',
          'on'  =>'districts.id = vehicles.district',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicleimages',
          'on'  =>'vehicleimages.vehicle_id = vehicles.id',
          'right_left' => 'left'
      ]);

             $select_fields = array(
            'vehicles.id',
            'vehicles.desc',
            'vehicles.trans',
            'makes.maker as brand_name',
            'vehicle_models.model_name',
            'vehicles.km_driven',
            'vehicles.set_a_price',
            'states.name as state_name',
            'districts.district as city_name',
            'vehicleimages.front_side as vehicle_image',
            );


      $order_by = "like_unlike_vehicle.id DESC";




       $result = $this->Api_Model->getSingleResultWithJoin('like_unlike_vehicle',$select_fields,$joins,$wheredata,$order_by);
       
       
       
       if($result)
       {
         $json['result']  = "true";
         $json['msg']     = "All vehicle";
         $json['path']    = base_url()."assets/images/brand/";
         $json['data']    = $result;
       }
       else
       {
         $json['result']  = "false";
         $json['msg']     = "No vehicle";
       }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id";
    }
    
    
    echo json_encode($json);
  }
  
  
  
  
  
  
  
  
  public function view_vehicle()
  {
    $user_id    = $this->input->post('user_id');
    $vehicle_id = $this->input->post('vehicle_id');
    
    if(isset($user_id) && isset($vehicle_id))
    {
      $this->db->where("user_id",$user_id);
      $this->db->where("vehicle_id",$vehicle_id);
      $this->db->select("id");
      $this->db->from("view_vehicle");
      $rr = $this->db->get()->row();
      
      
      $postdata['user_id']    = $user_id;
      $postdata['vehicle_id'] = $vehicle_id;
      
      
      if($rr)
      {
              $this->db->where("user_id",$user_id);
              $this->db->where("vehicle_id",$vehicle_id);
         $deleted = $this->db->delete("view_vehicle");
         
         
         $result = $this->Api_Model->insertAllData('view_vehicle', $postdata);
         
      }
      else
      {
        $result = $this->Api_Model->insertAllData('view_vehicle', $postdata);
      }
      
      
      if($result)
      {
        $json['result']  = "true";
        $json['msg']     = "view successfully";
      }
      else
      {
        $json['result']  = "false";
        $json['msg']     = "something went wrong";
      }
      
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id,vehicle_id";
    }
    
    
    echo json_encode($json);
    
  }
  
  
  
  
  
  
  
  
  public function get_recent_view_vehicle()
  {
    $user_id    = $this->input->post('user_id');
    $vehicle_id = $this->input->post('vehicle_id');
    
    if(isset($user_id) && isset($vehicle_id))
    {
      $wheredata = array(
        'view_vehicle.user_id'  => $user_id,
        'view_vehicle.vehicle_id !=' => $vehicle_id
        );

      $joins = array([
          'table'  =>'vehicles',
          'on'  =>'vehicles.id = view_vehicle.vehicle_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'makes',
          'on'  =>'makes.id = vehicles.maker_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicle_models',
          'on'  =>'vehicle_models.id = vehicles.model_id',
          'right_left' => 'left'
      ],
      [
          'table'  =>'states',
          'on'  =>'states.id = vehicles.state',
          'right_left' => 'left'
      ],
      [
          'table'  =>'districts',
          'on'  =>'districts.id = vehicles.district',
          'right_left' => 'left'
      ],
      [
          'table'  =>'vehicleimages',
          'on'  =>'vehicleimages.vehicle_id = vehicles.id',
          'right_left' => 'left'
      ]);

             $select_fields = array(
            'vehicles.id',
            'vehicles.desc',
            'vehicles.trans',
            'makes.maker as brand_name',
            'vehicle_models.model_name',
            'vehicles.km_driven',
            'vehicles.set_a_price',
            'states.name as state_name',
            'districts.district as city_name',
            'vehicleimages.front_side as vehicle_image',
            );


      $order_by = "view_vehicle.id DESC";
      
      
      $limit = 5;




       $result = $this->Api_Model->getSingleResultWithJoin_limit('view_vehicle',$select_fields,$joins,$wheredata,$order_by,$limit);
       
       
       
       if($result)
       {
         $json['result']  = "true";
         $json['msg']     = "All vehicle";
         $json['path']    = base_url()."assets/images/brand/";
         $json['data']    = $result;
       }
       else
       {
         $json['result']  = "false";
         $json['msg']     = "No vehicle";
       }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id,vehicle_id";
    }
    
    
    echo json_encode($json);
  }
  
  
  
  
  
  
  
  
  public function update_bid()
  {
    $user_id    = $this->input->post('user_id');
    $vehicle_id = $this->input->post('vehicle_id');
    $amount     = $this->input->post('amount');
    
    if(isset($user_id) && isset($vehicle_id) && isset($amount))
    {
      $wheredata = array(
        
        'vendor_id' => $user_id,
        'vehicle_id' => $vehicle_id,
        );
        
      $post_data['bidAmount']    = $amount;
      $post_data['updated_date'] = date('Y-m-d H:i:s',time());
      
      
      $result = $this->Api_Model->updates($wheredata,'bids',$post_data);
      
      
      if($result)
      {
        
        $post_data_2['vendor_id']     = $user_id;
        $post_data_2['vehicle_id']    = $vehicle_id;
        $post_data_2['bidAmount']     = $amount;
        $post_data_2['user_type']     = "vendor";
        $post_data_2['created_date']  = date('Y-m-d H:i:s',time());
        
        $this->Api_Model->insertAllData('bid_history',$post_data_2);
        
        
        $json['result']  = "false";
        $json['msg']     = "Bid updated";
      }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id,vehicle_id,amount";
    }
    
    
    echo json_encode($json);
  }
  
  
  
  
  
  
  public function update_profile_pics()
  {
    $user_id = $this->input->post('user_id');
    
    if(isset($user_id) && isset($_FILES['profile_pic']['name']))
    {


        $image1  = $this->imageUpload('profile_pic','assets/images/brand/');

        $post_data['profile_pic'] = $image1;
        
        $wheredata = array(
          
          'vendor_id' => $user_id
          );
        
        
      //   $result = $this->Api_Model->update($wheredata,'kyc_doc',$post_data);
        
        
        $result = $this->Api_Model->updateData_tt('kyc_doc',$post_data,$wheredata);
        
        
        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "profile updated";
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "something went wrong";
        }

    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required user_id,profile_pic";
    }
    
    
    echo json_encode($json);
  }
    
    
    
    
  public function add_contact() 
  {  
 
    $Title             =$this->input->post('Title');
    $name     =$this->input->post('name');
    $mobile    =$this->input->post('mobile');
    $description     =$this->input->post('description');
    $working_time      =$this->input->post('working_time');
    $description       =$this->input->post('description');
    
    if(isset($Title) && isset($name) && isset($mobile) && isset($description)){

    
    $post_data = array(
     
      'Title'=>$Title,
      'name'=>$name,
      'mobile'=>$mobile,
      'description'=>$description,
     
      
      
    );        
     $post_data_2['created_date']  = date('Y-m-d H:i:s',time());
    
     $res = $this->Api_Model->insertAllData('contacts',$post_data);
     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'contacts Added Successfully';
         }

     else{
       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';

         }     
    
         }
  
     else{
    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required Title,name,mobile,description';
       }
  
     echo json_encode($json);
  
   }
   
   
   
   
   
      public function how_kerla_exp()
  {
     
    $this->db->select("*");
    $this->db->from("how_kerla_willwork");
    $result = $this->db->get()->row();
    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    
    echo json_encode($json);
  }
   
   
   
   
   
   
   
   
   
   
   
    public function get_my_Home_listing()
     {
         $user_id = $this->input->post('user_id'); 
          $category = $this->input->post('category'); 
    
    
     if(isset($user_id) && isset($category)){
    
       
          $wheredata = array('field'=>'*',
             'table'=>'listing',
             'where'=>array('category' => $category),               
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All Data";
          $json['path']    = base_url()."assets/images/cover/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No Data";
        }
}
      else
        {
          $json['result']  = "false";
          $json['msg']     = " required category";
        }
               
        
      echo json_encode($json);
    }
   
   
   
   
   
  
   
   
   



public function  serviceoffer_by_hotel()
{
  $listing_id=$this->input->post('listing_id');


  if(isset($listing_id))
  {
    $where = array(
      'field'      =>'*',
      'table'      =>'serviceoffer_by_hotel',
      'where'      =>array('listing_id' => $listing_id),
      'order_by'   =>'id asc'
    );

    $result=$this->CommanModel->fetch_Account($where);
    if($result){


      foreach($result as $val)
      {
       $serviceoffer_id    = $val->serviceoffer_id;


      $this->db->where("serviceoffer.id",$serviceoffer_id);
      $this->db->select("serviceoffer.*");
      $this->db->from("serviceoffer_by_hotel");
      $this->db->join("serviceoffer","serviceoffer.id = serviceoffer_by_hotel.serviceoffer_id");
      $restjoin = $this->db->get()->row();


$resultnew[]=$restjoin;
}


     $json['result']    = "true";
     $json['msg']       = "All Data";
     $json['data']      = $resultnew;
   }else
   {
    $json['result'] = "false";
    $json['msg']    = "no favourite list data";
  }

}else{
       $json['result'] = "false";
       $json['msg']    = "parameter required listing_id";
     }
echo json_encode($json);
}
    
    
    
    
    
    public function favourite_listing()
{
  $user_id                   =$this->input->post('user_id');
  $listing_id                =$this->input->post('listing_id');
  $action                    =$this->input->post('action');
  if(isset($listing_id) && isset($user_id) && isset($action))
  {
    
    $data=array(
              'user_id'         =>$user_id,
              'listing_id'      =>$listing_id,
              'action'          =>$action,
              );

    if($action==1){

      
      
      /*$wheredata=array(
              'field'         =>'id,i_favourite',
              'where'      =>array('id'=>$listing_id),
              'table'          =>'listing'
              );
      
      $value=$this->Api_Model->getAllDataRow($wheredata);*/
      
    //   $this->Api_Model->update(array('id'=>$listing_id),'listing',array('i_favourite'=>1));
    
    $this->db->where('id',$listing_id);
    $result_1 = $this->db->update('listing',array('i_favourite'=>'1'));
      
    //   print_r($result_1);die;
      
      $result=$this->Api_Model->All_data_insert('listing_user_favourite',$data);

          if($result)
          { 
            $json['result']  = "true";
            $json['msg']     = "favourite list added ";
          }
          else
          {
            $json['result']  = "false";
            $json['msg']     = "something went wrong";
          }
    }else{
      $where = array(
                  'user_id'   =>$user_id,
                  'listing_id'=>$listing_id
                );
                
                $this->db->where('id',$listing_id);
    $result_1 = $this->db->update('listing',array('i_favourite'=>'0'));
    
      $result=$this->Api_Model->All_data_delete('listing_user_favourite',$where);
      

   if($result)
    {
     $json['result'] = "true";
     $json['msg']    = "favourite list remove";
   }
   else
   {
     $json['result'] = "false";
     $json['msg']    = "Invalid listing_id";
   }

    }

  }else{
   $json['result'] = "false";
    $json['msg']    = "parameter required user_id,listing_id,action";
  }
  echo json_encode($json);

}

public function get_Favourite_listing()
{
  $user_id=$this->input->post('user_id');


  if(isset($user_id))
  {
    $where = array(
      'field'      =>'*',
      'table'      =>'listing_user_favourite',
      'where'      =>array('user_id' => $user_id),
      'order_by'   =>'id desc'
    );

    $result=$this->Api_Model->get_All_data_fetch($where);
    // print_r($result);die;
    if($result){


      foreach($result as $val)
      {
       $listing_id    = $val->listing_id;
       $user_id        = $val->user_id;

       $res=$this->Api_Model->listfavourite_user($listing_id);

       $resultnew[]=$res;

     }

     $json['result']    = "true";
     $json['msg']       = "All Data";
     $json['path']    = base_url()."assets/images/cover/";
     $json['data']      = $resultnew;
   }else
   {
    $json['result'] = "false";
    $json['msg']    = "no favourite list data";
  }

}else{
       $json['result'] = "false";
       $json['msg']    = "parameter required user_id";
     }
echo json_encode($json);
}
    
public function listing_details()
 {
  $user_id=$this->input->post('user_id');
  $listing_id=$this->input->post('listing_id');
  if(isset($user_id) && isset($listing_id)){

    $where = array(
      'field'      =>'*',
      'table'      =>'listing',
      'where'      =>array('id' => $listing_id),
      'order_by'   =>'id asc'
    );

    $result=$this->Api_Model->get_single_data_fetch($where);

    $ser=$result->service;
   
    $service=(explode(",", $ser));


foreach($service as $x=>$x_value){
  
         $service_id=$x_value;

         $result_1=$this->Api_Model->service_listing($service_id);

         $resultnew[]=$result_1;
  
     $result->service     = $resultnew;
   
}
     $json['result']    = "true";
     $json['msg']       = "All listing Detail";
     $json['path']    = base_url()."assets/images/cover/";
     $json['service_path']    = base_url()."uploads/serviceoffer/";
     $json['data']      = $result;
   }else
   {
    $json['result'] = "false";
    $json['msg']    = "parameter required user_id,listing_id";
  }
  echo json_encode($json);
 } 
    
    public function listing_review()
 {
  $user_id=$this->input->post('user_id');
  $listing_id=$this->input->post('listing_id');
  $feedback=$this->input->post('feedback');
  $rating=$this->input->post('rating');
  if(isset($user_id) && isset($rating) && isset($feedback) && isset($listing_id))
  {
    $data=array('user_id'=>$user_id,
                 'listing_id'=>$listing_id,
                 'rating'=>$rating,
                 'feedback'=>$feedback);
    $result=$this->Api_Model->All_data_insert('listing_review',$data);

   $query = $this->db->select("AVG(rating) as avg_rating")
                  
                  ->where('listing_id',$listing_id)
                  ->get('listing_review')
                  ->row();

  $avg_rating=$query->avg_rating;


  $data_4 = array( 'rating'    =>$avg_rating );
   
    $where_4 = array(
             'field'   =>'*',

             'table'   =>'listing',

             'where'   =>array( 
                               'id'  =>$listing_id
                              )
              );

    $update =  $this->Api_Model->update_All_data($where_4,$data_4);

    $json['result']    = "true";
     $json['msg']       = "user review successfully";
   }else
   {
    $json['result'] = "false";
    $json['msg']    = "parameter required user_id,listing_id,feedback,rating";
  }
  echo json_encode($json);
 }


 public function get_review_listing()
 {
  $listing_id=$this->input->post('listing_id');
  if(isset($listing_id)){

    $where = array(
      'field'      =>'*',
      'table'      =>'listing_review',
      'where'      =>array('listing_id' => $listing_id),
      'order_by'   =>'id desc'
    );

    $result=$this->Api_Model->get_All_data_fetch($where);

    if($result){

      foreach($result as $val)
      {
       $user_id=$val->user_id;

      $this->db->where("users.id",$user_id);
      $this->db->select("users.id as user_id,users.image as user_image,users.name as user_name,listing_review.date_time");
      $this->db->from("listing_review");
      $this->db->join("users","users.id = listing_review.user_id");
      $restjoin = $this->db->get()->row();
      $resultnew[]=array(
                          
                          'user_id'=>$restjoin->user_id,
                          'user_name'=>$restjoin->user_name,
                          'user_image'=>$restjoin->user_image,
                          'rating'=>$val->rating,
                         'feedback'=>$val->feedback,
                         'date_time'=>$val->date_time,
                       );
      }


     $json['result']    = "true";
     $json['msg']       = "All Data";
     $json['path']   = base_url().'assets/images/users/';
     $json['data']      = $resultnew;
   }else
   {
    $json['result'] = "false";
    $json['msg']    = "no review list";
  }


   }else
   {
    $json['result'] = "false";
    $json['msg']    = "parameter required listing_id";
  }
  echo json_encode($json);
 }
  
  public function cancellation_Policy()
 {
  $listing_id=$this->input->post('listing_id');
  if(isset($listing_id)){

    $where = array(
      'field'      =>'id,listing_id,content,terms',
      'table'      =>'cancellation',
      'where'      =>array('listing_id' => $listing_id),
      'order_by'   =>'id desc'
    );

    $result=$this->Api_Model->get_All_data_fetch($where);

   $json['result'] = "true";
    $json['msg']    = "cancellation policy Details";
    $json['data']   = $result;

  }else{
    $json['result'] = "false";
    $json['msg']    = "parameter required listing_id";
  }
  echo json_encode($json);
 }
 
 public function health_saftey()
 {
  $listing_id=$this->input->post('listing_id');
  if(isset($listing_id)){
    
    

    $query=$this->db->get('health_safety')->row();

  $result=array('listing_id'=>$listing_id,'id'=>$query->id,
                  'title'=>$query->title,
                  'description'=>$query->description,);

    $json['result'] = "true";
    $json['msg']    = "Health and saftey Details";
    $json['data']   = $result;

  }else{
    $json['result'] = "false";
    $json['msg']    = "parameter required listing_id";
  }
  echo json_encode($json);
 }
 
public function house_Rules()
 {
   $listing_id=$this->input->post('listing_id');
  if(isset($listing_id)){
    
    

    $query=$this->db->get('house_rules')->row();

  $result=array('listing_id'=>$listing_id,
                 'id'=>$query->id,
                  'title'=>$query->title,
                  'check_in'=>$query->check_in,
                  'check_out'=>$query->check_out,
                  'max_guest'=>$query->max_guest,
                  'description'=>$query->description,);

    $json['result'] = "true";
    $json['msg']    = "House rules guideline";
    $json['data']   = $result;

  }else{
    $json['result'] = "false";
    $json['msg']    = "parameter required listing_id";
  }
  echo json_encode($json);
 }


public function add_booking()
  {
    $listing_id=$this->input->post('listing_id');
    $user_id=$this->input->post('user_id');
    $payment_id=$this->input->post('payment_id');
    $no_adults=$this->input->post('no_adults');
    $no_children=$this->input->post('no_children');
    $no_infants=$this->input->post('no_infants');
    $no_pets=$this->input->post('no_pets');
    $total_guest=$this->input->post('total_guest');
    $room=$this->input->post('room');
    $total_amount=$this->input->post('total_amount');
    $start_date=$this->input->post('start_date');
    $end_date=$this->input->post('end_date');
    $payment_method=$this->input->post('payment_method');
    if(isset($listing_id) && isset($user_id) && isset($payment_id)
      && isset($no_adults) && isset($no_children) && isset($no_infants)
      && isset($no_pets) && isset($total_guest) && isset($start_date)
      && isset($end_date) && isset($total_amount) && isset($payment_method) && isset($room)){

$code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
      $data=array(
                  'listing_id'=>$listing_id,
                  'user_id'=>$user_id,
                  'payment_id'=>$payment_id,
                  'no_children'=>$no_children,
                  'no_adults'=>$no_adults,
                  'no_infants'=>$no_infants,
                  'no_pets'=>$no_pets,
                  'total_guest'=>$total_guest,
                  'room'=>$room,
                  'total_amount'=>$total_amount,
                  'start_date'=>$start_date,
                  'end_date'=>$end_date,
                //   'booking_status'=>'pending',
                  'package_uniq_id'=>$code,
                  'payment_method'=>$payment_method,

      );

    $result=$this->Api_Model->All_data_insert('booking',$data);
    
    $result_guest=$this->db->where('id',$listing_id)->get('listing')->row();
    $current_guest=$result_guest->current_guest;

    $data_guest = array( 'current_guest'    =>($current_guest +$total_guest) );
   
    $where_guest = array(
             'field'   =>'*',

             'table'   =>'listing',

             'where'   =>array( 
                               'id'  =>$listing_id
                              )
              );

    $update =  $this->Api_Model->update_All_data($where_guest,$data_guest);


       if($result)
         {
            $json['result']  = "true";
            $json['msg']     = "booking successfully!";   
         }
         else
         {
           $json['result']  = "false";
           $json['msg']     = "something went wrong!";
         }
      
    }
    else
    {
      $json['result'] = "false";
      $json['msg']     = "parameter required  listing_id,user_id,payment_id,no_adults,no_children,no_infants,no_pets,total_guest,total_amount,start_date,end_date,payment_method,room";
    }
    echo json_encode($json);
  }
  
  
public function get_booking_list()
  {
    $user_id=$this->input->post('user_id');
    if(isset($user_id)){


      $where = array(
        'field'      =>'*',
        'table'      =>'booking',
        'where'      =>array('user_id' => $user_id),
        'order_by'   =>'id asc'
      );

      $result=$this->Api_Model->get_All_data_fetch($where);

      if($result){
        foreach($result as $value)
        {
          $listing_id=$value->listing_id;
          $userid=$value->user_id;
          $booking_id=$value->id;
// print_r($booking_id);

      $this->db->where("users.id",$userid);
      $this->db->where("listing.id",$listing_id);
      $this->db->where('booking.id',$booking_id);
      $this->db->select("booking.id as booking_id,booking.package_uniq_id,booking.payment_id,
      booking.no_adults,booking.no_infants,
      booking.no_pets,booking.no_children,booking.total_guest,booking.room,booking.total_amount,
      booking.start_date,booking.end_date,booking.booking_status,booking.payment_method,

        users.id as user_id,users.name as user_name,users.image as user_image,

        listing.*");
      $this->db->from("booking");
      $this->db->join("users","users.id = booking.user_id");
      $this->db->join("listing","listing.id=booking.listing_id");
     
      $restjoin[] = $this->db->get()->row();

        }
        // die();

       $json['result'] = "true";
        $json['msg']    = "booking list show";
        $json['user_path']   = base_url().'assets/images/users/';
        $json['listing_path']    = base_url()."assets/images/cover/";
        $json['data']   = $restjoin;

      }else{
        $json['result'] = "false";
        $json['msg']    = "No booking list";
      }
    }else{
     $json['result'] = "false";
     $json['msg']    = "parameter required user_id";

   }
   echo json_encode($json);


 }
 
 
 public function get_payment_list()
{
  $user_id=$this->input->post('user_id');
  if(isset($user_id)){

    $where = array(
        'field'      =>'*',
        'table'      =>'booking',
        'where'      =>array('user_id' => $user_id,'payment_method'=>'online'),
        'order_by'   =>'id asc'
      );

      $result=$this->Api_Model->get_All_data_fetch($where);
      if($result){
      foreach($result as $value){
        $id=$value->id;
        $listing_id=$value->listing_id;
        $user_id=$value->user_id;

           // $this->db->where('booking.booking_status !=','pending' );

      $this->db->where('booking.id',$id);
      $this->db->where("listing.id",$listing_id);
      
   
      $this->db->select("booking.id as booking_id,booking.package_uniq_id,booking.payment_id,
      booking.user_id,booking.booking_date,booking.total_amount,
      booking.booking_status,booking.payment_method,booking.listing_id,listing.hotel ,listing.helpline_no ");

      $this->db->from("booking");
      $this->db->join("listing","listing.id=booking.listing_id");
     
      $restjoin[] = $this->db->get()->row();

}

       $json['result'] = "true";
        $json['msg']    = "payment list show";
        $json['data']   = $restjoin;

      }else{
        $json['result'] = "false";
        $json['msg']    = "No payment list";
      }
    }else{
     $json['result'] = "false";
     $json['msg']    = "parameter required user_id";

   }
   echo json_encode($json);


 }
 public function get_listing_booking()

 {
  $listing_id=$this->input->post('listing_id');
  if(isset($listing_id)){
    $where = array(
        'field'      =>'id as booking_id,user_id,listing_id,total_guest,room,start_date,end_date',
        'table'      =>'booking',
        'where'      =>array('listing_id' => $listing_id),
        'order_by'   =>'id asc'
      );
    $result=$this->Api_Model->get_All_data_fetch($where);
    if($result){

       $json['result'] = "true";
        $json['msg']    = "listing booking show";
        $json['data']   = $result;


    }else{
        $json['result'] = "false";
        $json['msg']    = "No listing booking";
      }


  }else{
     $json['result'] = "false";
     $json['msg']    = "parameter required listing_id";

   }
   echo json_encode($json);
 }
 public function cancel_user_booking()
 {
  $user_id=$this->input->post('user_id');
  $booking_id=$this->input->post('booking_id');
if(isset($user_id) && isset($booking_id)){
$data = array( 'booking_status'    =>'cancelled');
   
    $where = array(
             'field'   =>'*',

             'table'   =>'booking',

             'where'   =>array( 
                               'id'  =>$booking_id,
                               'user_id'=>$user_id,
                              )
              );
    

    $update =  $this->Api_Model->update_All_data($where,$data);
    $json['result']    = "true";
     $json['msg']       = "user booking cancelled successfully";


}else{

$json['result'] = "false";
     $json['msg']    = "parameter required user_id,booking_id";

   }
   echo json_encode($json);

 }
  
  
    public function get_about_us()
  {
     
    $this->db->select("*");
    $this->db->from("abouts");
    $result = $this->db->get()->row();
    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    
    echo json_encode($json);
  }
  //.............................................................//
  // public function get_terms_condition()
  // {      
  //   $this->db->select("*");
  //   $this->db->from("termsconditions");
  //   $result = $this->db->get()->row();
    
  //   if($result)
  //   {
  //     $json['result']  = "true";
  //     $json['msg']     = "All Data";
  //     $json['data']    = $result;
  //   }
  //   else
  //   {
  //     $json['result']  = "false";
  //     $json['msg']     = "No Data";
  //   }    
  //   echo json_encode($json);
  // }
//..................................//
  public function get_privacy_policy()
  {

    $this->db->select("*");
    $this->db->from("privacy");
    $result = $this->db->get()->row();

    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    
    echo json_encode($json);
  }

public function get_states()
  {

    $wheredata = array('field'=>'*',
       'table'=>'states',
       'where'=>array(),       
       'order_by'=>'id asc'
       );

    $result = $this->Api_Model->getAllData($wheredata);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    
    echo json_encode($json);
  }

public function get_cities()
  {
    $state_id=$this->input->post('state_id');
    if(isset($state_id)){

    $this->db->select('cities.*,states.name as state_name');
    $this->db->from('cities');
    $this->db->join('states','cities.state_id=states.id');
    $this->db->order_by('cities.id asc');
    $this->db->where('cities.state_id',$state_id);
    $result = $this->db->get()->result();

    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
  }else{
     $json['result'] = "false";
     $json['msg']    = "parameter required state_id";

   }
    
    echo json_encode($json);
  }   

public function get_pharma_company()
  {

    $wheredata = array('field'=>'*',
       'table'=>'company_user',
       'where'=>array(),       
       'order_by'=>'id desc'
       );

    $result = $this->Api_Model->getAllData($wheredata);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    
    echo json_encode($json);
  }

  // public function get_company_profile()
  // {
  //   $company_id=$this->input->post('company_id');
  //   if(isset($company_id)){

  //   // $this->db->select('company_user.*,states.name as state_name,cities.name as city_name');
  //   $this->db->select('company_user.*,states.name as state_name,cities.name as city_name');
  //   $this->db->from('company_user');
  //   $this->db->join('states','company_user.state_id=states.id');
  //   $this->db->join('cities','company_user.city_id=cities.id');
  //   $this->db->order_by('company_user.id asc');
  //   $this->db->where('company_user.id',$company_id);
  //   $result = $this->db->get()->result();



  //   if($result)
  //   {
    
  //     $json['result']  = "true";
  //     $json['msg']     = "All Data";
  //     $json['data']    = $result;
  //   }
  //   else
  //   {

  //     $json['result']  = "false";
  //     $json['msg']     = "No Data";
  //   }
  // }else{
  //    $json['result'] = "false";
  //    $json['msg']    = "parameter required company_id";

  //  }
    
  //   echo json_encode($json);
  // }

public function update_company_profile()
{
  $company_id        = $this->input->post('company_id');
  $name           = $this->input->post('name');
  $company_name          = $this->input->post('company_name');
  $email          = $this->input->post('email');
  $mobile            = $this->input->post('mobile');
  $address            = $this->input->post('address');
  $pin_code            = $this->input->post('pin_code');
  $state            = $this->input->post('state');
  $buyerc_ontact_number            = $this->input->post('buyerc_ontact_number');
  // $drugs_licence_image            = $this->input->post('drugs_licence_image');
  $gst_no            = $this->input->post('gst_no');
  $ceiling_in            = $this->input->post('ceiling_in');
  $state_id            = $this->input->post('state_id');
  $city_id            = $this->input->post('city_id');

  if(isset($company_id))
  {

    $post_data = array();
     if(!empty($name))
     {
      $post_data['name'] = $name;
     }

     if(!empty($company_name))
     {
      $post_data['company_name'] = $company_name;
     }

     if(!empty($email))
     {
      $post_data['email'] = $email;
     }

     if(!empty($mobile))
     {
      $post_data['mobile'] = $mobile;
     }

     if(!empty($state))
     {
      $post_data['state'] = $address;
     }

     if(!empty($address))
     {
      $post_data['address'] = $address;
     }

      if(!empty($pin_code))
     {
      $post_data['pin_code'] = $pin_code;
     }

      if(!empty($buyerc_ontact_number))
     {
      $post_data['buyerc_ontact_number'] = $buyerc_ontact_number;
     }

     if(!empty($gst_no))
     {
      $post_data['gst_no'] = $gst_no; 
     }


      if(!empty($ceiling_in))
     {
      $post_data['ceiling_in'] = $ceiling_in;   
     }

      if(!empty($state_id))
     {
      $post_data['state_id'] = $state_id;   
     }

     if(!empty($city_id))
     {
      $post_data['city_id'] = $city_id;   
     }


    if(!empty($_FILES['image']['name']))
    {
      $image = $this->Api_Model->select_single_rows('company_user','id',$company_id);

      if($image->image && file_exists('assets/images/users/'.$image->image))
      {
      unlink('assets/images/users/'.$image->image);
      }

      $image1  = $this->imageUpload('image','assets/images/users/');
      $post_data['image'] = $image1;
    }

     if(sizeof($post_data)>0){         
       
       $wheredata = array(
         'id' => $company_id
         );
       $update = $this->Api_Model->update($wheredata,'company_user',$post_data);
      if($update)
      {
      $json['result'] = "true";
      $json['msg']    = "profile updated successfully";
      
      }else{
      $json['result']  = "false";
      $json['msg']     = "Something went wrong.";
      }

     }
     else{
      $json['result'] = "true";
      $json['msg']    = "profile updated successfully";
     }

     }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required company_id,optional(name,email,company_name,mobile,address,pin_code,buyerc_ontact_number,gst_no,ceiling_in,state_id,city_id,state,image)';
    }

  echo json_encode($json);
}

public function delete_company_profile()
     {
        $company_id = $this->input->post('company_id');
        
        if(isset($company_id))
        {
            $result = $this->Api_Model->deleteData('company_user','id',$company_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "profile Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "company_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required company_id";
        }
        
       echo json_encode($json);
    }


// public function pharma_company_signup()
//   {
//     $name                 = $this->input->post('name');
//     $company_name         = $this->input->post('company_name');
//     $email                = $this->input->post('email');
//     $password             = $this->input->post('password');
//     $mobile               = $this->input->post('mobile');
    
//     if(isset($name) && isset($company_name) && isset($password) && isset($mobile) && isset($email))
//     {
//       if(!$this->Api_Model->is_record_exist('company_user','mobile',$mobile))
//       {
        
//         if(!$this->Api_Model->is_record_exist('company_user','email',$email))
//         {
//           /*if(!empty($_FILES['image']['name']))
//             {
//                 $image = $this->imageUpload('image','assets/images/users/');
//                 $postdata['image'] = $image;
//             }*/
          
//             $otp = rand(1000,9999);
          
//              $postdata = array(
//             'name' => $name,
//             'company_name' => $company_name,
//             'email' => $email,
//             'password' => md5($password),
//             'mobile' => $mobile,
//             'otp' => $otp,
//             /*'image' => $image,*/
//             'created_date' => date('Y-m-d H:i:s'),
//             'updated_date' => date('Y-m-d H:i:s'),
//             'verify_otp' => 1,
//             );
            
//           $result = $this->Api_Model->insertAllData('company_user', $postdata);
           
//           if($result)
//           {
         
//              $this->db->where("company_user.id",$result);
//              $this->db->select("id");
//              $this->db->from("company_user");
//              $vv = $this->db->get()->row();
             
//              $json['result']  = "true";
//              $json['msg']     = "signup successfully!";
//              $json['path']     = base_url()."assets/images/users/";
//              $json['id'] = $vv->id;
//              $json['otp']     = $otp;
            
//           }
//           else
//           {
//              $json['result']  = "false";
//              $json['msg']     = "something went wrong!";
//           }           
//         }
//         else
//         {
//           $json['result']  = "false";
//           $json['msg']     = "Email already exist!";
//         }
        
//       }
//       else
//       {
//         $json['result']  = "false";
//         $json['msg']     = "mobile already exist!";
//       }
//     }
//     else
//     {
//       $json['result']  = "false";
//       $json['msg']     = "parameter required name, company_name, email, password, mobile";      
//     }    
    
//     echo json_encode($json);
    
//   }
  
  
  public function pharma_company_signup()
  {
    $name                 = $this->input->post('name');
    $company_name         = $this->input->post('company_name');
    $email                = $this->input->post('email');
    $password             = $this->input->post('password');
    $mobile               = $this->input->post('mobile');
    $fcm_id               = $this->input->post('fcm_id');
    
    if(isset($name) && isset($company_name) && isset($password) && isset($mobile) && isset($email) && isset($fcm_id))
    {
      if(!$this->Api_Model->is_record_exist('company_user','mobile',$mobile))
      {
        
        if(!$this->Api_Model->is_record_exist('company_user','email',$email))
        {
          /*if(!empty($_FILES['image']['name']))
            {
                $image = $this->imageUpload('image','assets/images/users/');
                $postdata['image'] = $image;
            }*/
          
            $otp = rand(1000,9999);
            $fcm_id = rand(1000,9999);
          
             $postdata = array(
            'name' => $name,
            'company_name' => $company_name,
            'email' => $email,
            'password' => md5($password),
            'mobile' => $mobile,
            'fcm_id' => $fcm_id,
            'otp' => $otp,
          'type' => "pharma",
            'created_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s'),
            'verify_otp' => 0,
            );
            
           $result = $this->Api_Model->insertAllData('company_user', $postdata);
           
           if($result)
           {
         
             $this->db->where("company_user.id",$result);
             $this->db->select("id");
             $this->db->from("company_user");
             $vv = $this->db->get()->row();
             
             $json['result']  = "true";
             $json['msg']     = "signup successfully!";
             $json['path']     = base_url()."assets/images/users/";
             $json['id'] = $vv->id;
             $json['otp']     = $otp;
            
           }
           else
           {
             $json['result']  = "false";
             $json['msg']     = "something went wrong!";
           }           
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "Email already exist!";
        }
        
      }
      else
      {
        $json['result']  = "false";
        $json['msg']     = "mobile already exist!";
      }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required name, company_name, email, password, mobile,fcm_id";      
    }    
    
    echo json_encode($json);
    
  }
  

public function company_verify_otp()
	{
		$company_id    = $this->input->post('company_id');
		$otp        = $this->input->post('otp');

		if(isset($company_id) && isset($otp))
		{
			$wheredata = array('field'=>'id',

			 'table'=>'company_user',

			 'where'=>array('id'=>$company_id,'otp'=>$otp),

			);

			 $result = $this->Api_Model->getAllDataRow($wheredata);

			if($result)
			 {

				$verify_otp = array(
				 'verify_otp' => 1,
				 );

			 $this->db->where("company_user.id",$company_id);
			 $this->db->update("company_user",$verify_otp);

			 $json['result'] = "true";
			 $json['msg']    = "Otp verify Successfully.";
			 $json['data']   = $this->Api_Model->select_single_row_ss('company_user','id',$company_id);
			 }
			 else
			 {
			 $json['result'] = "false";
			 $json['msg']    = "sorry otp not valid.";
			}
		}
		else
		{
			$json['result'] = "false";
			$json['msg']    = "parameter required company_id,otp";
		}

		echo json_encode($json);
	}
  




	public function company_login()
	 {
		 $mobile   = $this->input->post('mobile');
		 $fcm_id   = $this->input->post('fcm_id');
		 
		 if(isset($mobile) && isset($fcm_id))
		 {
			 if($this->Api_Model->is_record_exist('company_user','mobile',$mobile))
			 {
					 $otp = rand(1000,9999);
			
				$post_data = array(
					'otp'=>$otp,
					'fcm_id'=>$fcm_id,
					);
					
							$this->db->where(array('mobile'=>$mobile,'verify_otp'=>'1'));
						
				$result_1 = $this->db->update('company_user',$post_data);



					$wheredata = array('field'=>'id,otp',

					 'table'=>'company_user',

					 'where'=>array('mobile' => $mobile,'verify_otp' => '1'),      
					 
					);

				$result = $this->Api_Model->getAllDataRow($wheredata);
	
				if($result){        
				 
					$json['result'] = "true";
					$json['msg']    = "Login Successful";
					$json['data']   = $result;
					}
				else
			 {
				 $json['result'] = "false";
				 $json['msg']    = "Something went wrong";
			 }    
			 }
			 else
			 {
				 $json['result'] = "false";
				 $json['msg']    = "Mobile not register";
			 }
		 }
		 else
		 {
			 $json['result'] = "false";
			 $json['msg']    = "parameter required mobile,fcm_id";
		 }
		 
		 echo json_encode($json);
	 }

   public function company_logout()
   {
     $company_id = $this->input->post('company_id');

     if(isset($company_id))
     {
      extract($_POST);

      $wheredata = array(
        'id' => $company_id
      );

      $data = array(
        'verify_otp' => 1
        );

      $result = $this->Api_Model->update($wheredata,'company_user',$data);

     if($result)
     {

     $json['result'] = 'true';
     $json['msg']    = 'Successfully logout.';

     }
     else
     {
       $json['result'] = 'false';
       $json['msg']    = 'something went wrong';
     }

     }
     else
     {
       $json['result'] = "false";
       $json['msg']    = "parameter required company_id";
     }

    echo json_encode($json);
   }



public function company_forget_password()
{
   $email = $this->input->post('email');
   
   if(isset($email))
   {
       if($this->Api_Model->is_record_exist('company_user','email',$email))
       {
           $otp = rand(1000,9999);           
           
           $wheredata = array(
             'email' => $email
            );

          $data = array(
          'otp' => $otp,
          );

          $result = $this->Api_Model->update($wheredata,'company_user',$data);
          
          if($result)
          {
              $wheredata = array('field'=>'id,password',

               'table'=>'company_user',

               'where'=>array('email' => $email),
       
               );

              $rows = $this->Api_Model->getAllDataRow($wheredata);
              
              $json['result'] = 'true';
              $json['msg']    = 'New Password sent successfully';
              $json['data']   = $rows;
          }
          else
          {
              $json['result'] = 'false';
              $json['msg']    = 'Something went wrong';
          }
           
       }
       else
       {
           $json['result'] = 'false';
           $json['msg']    = 'email Incorrect';
       }
   }
   else
   {
       $json['result'] = 'false';
       $json['msg']    = 'parameter required email';
   }   
   
   echo json_encode($json);
}


public function company_change_password()
{

  extract($_POST);

  if(isset($company_id) && isset($old_password) && isset($new_password) && isset($confirm_password))
  {
   if($this->Api_Model->is_record_exists('company_user','id',"{$company_id}"))
   {
      $userdetail=$this->db->query('select * from company_user where id="'.$company_id.'"')->row();
      // $company_id=$userdetail->id;
      $old_encripted =  $this->Api_Model->select_single_rows('company_user','id',$company_id)->password; 
      if(md5($old_password)==$old_encripted)
      {
        if ($new_password==$confirm_password) {
          $post_data['password'] = md5($new_password);
          $result = $this->Api_Model->updateData('company_user',$post_data,$company_id);
          if($result)
          {
            $json['result'] = "true";
            $json['msg'] = "Successfully updated password";
            $json['data'] =  $this->Api_Model->select_single_rows('company_user','id',$company_id);
          }else{
            $json['result'] = "false" ;
            $json['msg'] = "Something went wrong. Please try later.";
          }
        }else{
          $json['result'] = "false" ;
          $json['msg'] = "New password and confirm_password not matched.";
        }   
      }else{
        $json['result'] = "false";
        $json['msg']    = 'Invalid current Password';
      }   
    }else{
      $json['result'] = "false";
      $json['msg']    = 'User not exist';
    }
  }else
  {
    $json['result'] = "false";
    $json['msg'] = "Please give parameters(company_id,old_password,new_password,confirm_password)";
  }
  echo json_encode($json);
}

public function get_company_billing_address()
  {
    $company_id          = $this->input->post('company_id');

    if(isset($company_id))
  {

    $wheredata = array('field'=>'*',
       'table'=>'company_billing_address',
       'where'=>array('company_id'=>$company_id),       
       'order_by'=>'id desc'
       );

    $result = $this->Api_Model->getAllData($wheredata);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required company_id';
    }
    
    echo json_encode($json);
  }

  public function company_billing_address_detail()
  {
    $bill_id          = $this->input->post('bill_id');

    if(isset($bill_id))
  {

    $result = $this->Api_Model->select_single_rows('company_billing_address','id',$bill_id);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required bill_id';
    }
    
    echo json_encode($json);
  }
  

public function add_company_billing_address() 
  {  
 
    $company_id       = $this->input->post('company_id');
    $mobile           = $this->input->post('mobile');
    $email           = $this->input->post('email');
    $address          = $this->input->post('address');
    $gst_no           = $this->input->post('gst_no');
    $drug_license_no  = $this->input->post('drug_license_no');
    $state_id         = $this->input->post('state_id');
    $city_id          = $this->input->post('city_id');
    
    if(isset($company_id) && isset($mobile) && isset($address) && isset($state_id) && isset($city_id) && isset($email)){
    
    $post_data = array(     
      'company_id'      =>  $company_id,
      'mobile'          =>  $mobile,
      'email'           =>  $email,
      'address'         =>  $address,
      'gst_no'          =>  $gst_no,
      'drug_license_no' =>  $drug_license_no,
      'state_id'        =>  $state_id,
      'city_id'         =>  $city_id,
      'created_date'    =>  date('Y-m-d H:i:s')
    );
    
     $res = $this->Api_Model->insertAllData('company_billing_address',$post_data);     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'Billing Address Added Successfully';
         }

     else{       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';
         }         
         }  
     else{    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required company_id, mobile,email, address, state_id, city_id, optional(gst_no, drug_license_no)';
       }
  
     echo json_encode($json);
  
   }



public function update_company_billing_address()
{
  $bill_id          = $this->input->post('bill_id');
  $mobile           = $this->input->post('mobile');
  $address          = $this->input->post('address');
  $gst_no           = $this->input->post('gst_no');
  $drug_license_no  = $this->input->post('drug_license_no');
  $state_id         = $this->input->post('state_id');
  $city_id          = $this->input->post('city_id');

  if(isset($bill_id))
  {

    $post_data = array();
     if(!empty($mobile))
     {
      $post_data['mobile'] = $mobile;
     }

     if(!empty($address))
     {
      $post_data['address'] = $address;
     }

     if(!empty($gst_no))
     {
      $post_data['gst_no'] = $gst_no;
     }

     if(!empty($state_id))
     {
      $post_data['state_id'] = $state_id;
     }

     if(!empty($city_id))
     {
      $post_data['city_id'] = $city_id;
     }

     if(!empty($drug_license_no))
     {
      $post_data['drug_license_no'] = $drug_license_no;
     }

     if(sizeof($post_data)>0){         
       
       $wheredata = array(
         'id' => $bill_id
         );
       $update = $this->Api_Model->update($wheredata,'company_billing_address',$post_data);
      if($update)
      {
      $json['result'] = "true";
      $json['msg']    = "billing address updated successfully";
      
      }else{
      $json['result']  = "false";
      $json['msg']     = "Something went wrong.";
      }

     }
     else{
      $json['result'] = "true";
      $json['msg']    = "billing address updated successfully";
     }

     }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required bill_id,optional(mobile,address,gst_no,state_id,city_id,drug_license_no)';
    }

  echo json_encode($json);
}

public function delete_company_billing_address()
     {
        $bill_id = $this->input->post('bill_id');
        
        if(isset($bill_id))
        {
            $result = $this->Api_Model->deleteData('company_billing_address','id',$bill_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "billing address Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "bill_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required bill_id";
        }
        
       echo json_encode($json);
    }

public function get_post_job()
  {
    $company_id          = $this->input->post('company_id');

    if(isset($company_id))
  {

    $wheredata = array('field'=>'*',
       'table'=>'post_job',
       'where'=>array('company_id'=>$company_id),       
       'order_by'=>'id desc'
       );

    $result = $this->Api_Model->getAllData($wheredata);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required company_id';
    }
    
    echo json_encode($json);
  }

  public function post_job_detail()
  {
    $post_id          = $this->input->post('post_id');

    if(isset($post_id))
  {

    $result = $this->Api_Model->select_single_rows('post_job','id',$post_id);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required post_id';
    }
    
    echo json_encode($json);
  }




public function add_post_job() 
  {  
 
    $company_id       = $this->input->post('company_id');
    $name          = $this->input->post('name');
    $skills          = $this->input->post('skills');
    
    $job_type           = $this->input->post('job_type');
    $number_of_opening           = $this->input->post('number_of_opening');
    $job_responsibilities           = $this->input->post('job_responsibilities');
    $ctc_breakup           = $this->input->post('ctc_breakup');
    $fixed_pay           = $this->input->post('fixed_pay');
    $variable_pay           = $this->input->post('variable_pay');
    $other_incentives           = $this->input->post('other_incentives');
    $provision           = $this->input->post('provision');
    $provision_mounth           = $this->input->post('provision_mounth');
   
    $salary       = $this->input->post('salary');
    $price_from      = $this->input->post('price_from');
    $price_to     = $this->input->post('price_to');
   
    $perks           = $this->input->post('perks');
   
    $about          = $this->input->post('about');
   
    $questions          = $this->input->post('questions');
    $website          = $this->input->post('website');

    $questions_1          = $this->input->post('questions_1');
    $questions_2          = $this->input->post('questions_2');
    $questions_3          = $this->input->post('questions_3');
    $questions_4          = $this->input->post('questions_4');
    $questions_5          = $this->input->post('questions_5');

    $expiry_date          = $this->input->post('expiry_date');
    $experience          = $this->input->post('experience');
    
    if(isset($company_id)){
    
    $post_data = array(     
      
      'company_id'      =>  $company_id,
      'job_type'         =>  $job_type,
      'number_of_opening'         =>  $number_of_opening,
      'job_responsibilities'         =>  $job_responsibilities,
      'ctc_breakup'         =>  $ctc_breakup,
      'fixed_pay'         =>  $fixed_pay,
      'price_from'         =>  $price_from,
      'price_to'         =>  $price_to,
      'variable_pay'         =>  $variable_pay,
      'other_incentives'         =>  $other_incentives,
      'provision'         =>  $provision,
      'provision_mounth'         =>  $provision_mounth,
      'name'         =>  $name,
     'salary'      =>  $salary,
      'skills'      =>  $skills,
      
      'perks'          =>  $perks,
      
      'about'         =>  $about,
     
      'questions'         =>  $questions,
      'website'         =>  $website,

      'questions_1'         =>  $questions_1,
      'questions_2'         =>  $questions_2,
      'questions_3'         =>  $questions_3,
      'questions_4'         =>  $questions_4,
      'questions_5'         =>  $questions_5,
      'expiry_date'         =>  $expiry_date,
      'experience'         =>  $experience,

      'created_date'    =>  date('Y-m-d H:i:s')

    );
   // echo "<pre>"; print_r($post_data); die;
    
     $res = $this->Api_Model->insertAllData('post_job',$post_data);     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'Post Added Successfully';
         }

     else{       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';
         }         
         }  
     else{    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required company_id,optional(job_type(office,remote),number_of_opening,job_responsibilities,ctc_breakup,skills,fixed_pay,variable_pay,other_incentives,provision,(yes,no),provision_mounth, name, salary(Fixed, Negotiable), perks ,questions,price_from,price_to,website,questions_1,questions_2,questions_3,questions_4,questions_5,expiry_date,experience)';
       }
  
     echo json_encode($json);
  
   }


   public function update_post_job()
{
  $post_id          = $this->input->post('post_id');
    $name          = $this->input->post('name');
    
    $work_type           = $this->input->post('work_type');
    $location  = $this->input->post('location');
    $start_from          = $this->input->post('start_from');
    $salary       = $this->input->post('salary');
    $skills          = $this->input->post('skills');
    $perks           = $this->input->post('perks');
    $price_from  = $this->input->post('price_from');
    $price_to         = $this->input->post('price_to');
    $about          = $this->input->post('about');
    $day_to_day_work         = $this->input->post('day_to_day_work');
    $questions          = $this->input->post('questions');

  if(isset($post_id))
  {

    $post_data = array();
     if(!empty($name))
     {
      $post_data['name'] = $name;
     }

     if(!empty($work_type))
     {
      $post_data['work_type'] = $work_type;
     }

     if(!empty($location))
     {
      $post_data['location'] = $location;
     }

     if(!empty($start_from))
     {
      $post_data['start_from'] = $start_from;
     }

     if(!empty($salary))
     {
      $post_data['salary'] = $salary;
     }

     if(!empty($skills))
     {
      $post_data['skills'] = $skills;
     }

     if(!empty($perks))
     {
      $post_data['perks'] = $perks;
     }

     if(!empty($price_from))
     {
      $post_data['price_from'] = $price_from;
     }

     if(!empty($price_to))
     {
      $post_data['price_to'] = $price_to;
     }

     if(!empty($about))
     {
      $post_data['about'] = $about;
     }

     if(!empty($day_to_day_work))
     {
      $post_data['day_to_day_work'] = $day_to_day_work;
     }

     if(!empty($questions))
     {
      $post_data['questions'] = $questions;
     }

     if(sizeof($post_data)>0){         
       
       $wheredata = array(
         'id' => $post_id
         );
       $update = $this->Api_Model->update($wheredata,'post_job',$post_data);
      if($update)
      {
      $json['result'] = "true";
      $json['msg']    = "post updated successfully";
      
      }else{
      $json['result']  = "false";
      $json['msg']     = "Something went wrong.";
      }

     }
     else{
      $json['result'] = "true";
      $json['msg']    = "post updated successfully";
     }

     }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required post_id,optional(name, work_type(Parmanent, Contractual), location, start_from(Immediately, Select date), salary(Fixed, Negotiable), skills, perks(Commission, Certificates, Flexible Time, Other), price_from, price_to, about, day_to_day_work, questions)';
    }

  echo json_encode($json);
}

public function delete_post_job()
     {
        $post_id = $this->input->post('post_id');
        
        if(isset($post_id))
        {
            $result = $this->Api_Model->deleteData('post_job','id',$post_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "post Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "post_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required post_id";
        }
        
       echo json_encode($json);
    }

   public function get_post_internship_o()
  {
    $company_id          = $this->input->post('company_id');

    if(isset($company_id))
  {

    $wheredata = array('field'=>'*',
       'table'=>'post_internship',
       'where'=>array('company_id'=>$company_id),       
       'order_by'=>'id desc'
       );

    $result = $this->Api_Model->getAllData($wheredata);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required company_id';
    }
    
    echo json_encode($json);
  }

  public function post_internship_detail()
  {
    $post_id          = $this->input->post('post_id');

    if(isset($post_id))
  {

    $result = $this->Api_Model->select_single_rows('post_internship','id',$post_id);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required post_id';
    }
    
    echo json_encode($json);
  }



 public function add_post_internship() 
  {  
  
    $company_id	                   = $this->input->post('company_id');
    $part_time_allowed                   = $this->input->post('part_time_allowed');
    $internship_title	                   = $this->input->post('internship_title');
    $allow_women	                   = $this->input->post('allow_women');
    $skills_required           = $this->input->post('skills_required');
    $internship_types          = $this->input->post('internship_types');
    $city	                      = $this->input->post('city');
    $part_time_allowed         = $this->input->post('part_time_allowed');
    $number_of_opening         = $this->input->post('number_of_opening');
    $internship_start_date     = $this->input->post('internship_start_date');
    $internship_duration       = $this->input->post('internship_duration');
    $internship_responsibilities = $this->input->post('internship_responsibilities');
    $pop                       = $this->input->post('pop');
    $price_from                      = $this->input->post('price_from');
    $price_to                       = $this->input->post('price_to');
    $websites                       = $this->input->post('websites');
    $skills                       = $this->input->post('skills');
    
    
    $minimum_assured           = $this->input->post('minimum_assured');
    $maximum_assured           = $this->input->post('maximum_assured');
    $scale                     = $this->input->post('scale');
    
    
    $stipend                     = $this->input->post('stipend');
    
    $perks                  = $this->input->post('perks');
   
    $about                 = $this->input->post('about');
    
    $questions                 = $this->input->post('questions');

    $questions_1          = $this->input->post('questions_1');
    $questions_2          = $this->input->post('questions_2');
    $questions_3          = $this->input->post('questions_3');
    $questions_4          = $this->input->post('questions_4');
    $questions_5          = $this->input->post('questions_5');

    $expiry_date          = $this->input->post('expiry_date');
    $experience          = $this->input->post('experience');
   
    
    if(isset($company_id)){
    
    $post_data = array(     
       
      'internship_title'         =>  $internship_title,
      'skills_required'          =>  $skills_required,
      'internship_types' =>  $internship_types,
      'allow_women' =>  $allow_women,
      'part_time_allowed'        =>  $part_time_allowed,
       'city'                      =>  $city,
       'part_time_allowed'                      =>  $part_time_allowed,
       'company_id'                      =>  $company_id,
       'price_from'                      =>  $price_from,
       'price_to'                      =>  $price_to,
      'number_of_opening'          =>  $number_of_opening,
      'internship_start_date'         =>  $internship_start_date,
       'internship_duration'          =>  $internship_duration,
      'internship_responsibilities' =>  $internship_responsibilities,
      'pop'        =>  $pop,
      'websites'         =>  $websites,
      'minimum_assured'        =>  $minimum_assured,
      'maximum_assured'         =>  $maximum_assured,
      'scale'         =>  $scale,
       'skills'         =>  $skills,
      
     
      'stipend'         =>  $stipend,
      'perks'         =>  $perks,
      
      'about'         =>  $about,
    
      'questions'         =>  $questions,
      'created_date'    =>  date('Y-m-d H:i:s'),
      'questions_1'         =>  $questions_1,
      'questions_2'         =>  $questions_2,
      'questions_3'         =>  $questions_3,
      'questions_4'         =>  $questions_4,
      'questions_5'         =>  $questions_5,
      'expiry_date'         =>  $expiry_date,
      'experience'         =>  $experience,
    );
    
     $res = $this->Api_Model->insertAllData('post_internship',$post_data);     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'Post Added Successfully';
         }

     else{       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';
         }         
         }  
     else{    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required company_id ,optional(internship_title,skills_required,internship_types,(in-office,remote),part_time_allowed,(yes,no),part_time_allowed,(yes,no),city,skills,number_of_opening,internship_start_date,(immediately,latter),internship_duration,internship_responsibilities,pop,(yes,no),websites,minimum_assured,maximum_assured,scale,stipend,(fixed,negotiable,performance_based,unpaid),perks,about,questions,price_from,price_to,allow_women),(questions_1,questions_2,questions_3,questions_4,questions_5,expiry_date,experience)';
       }
  
     echo json_encode($json);
  
   }

   public function update_post_internship()
{
  $post_id          = $this->input->post('post_id');
    $name          = $this->input->post('name');
    $work_type           = $this->input->post('work_type');
    $location  = $this->input->post('location');
    $duration         = $this->input->post('duration');
    $start_from          = $this->input->post('start_from');
    $stipend           = $this->input->post('stipend');
    $skills          = $this->input->post('skills');
    $perks           = $this->input->post('perks');
    $price_from  = $this->input->post('price_from');
    $price_to         = $this->input->post('price_to');
    $about          = $this->input->post('about');
    $day_to_day_work         = $this->input->post('day_to_day_work');
    $questions          = $this->input->post('questions');

  if(isset($post_id))
  {

    $post_data = array();
     if(!empty($name))
     {
      $post_data['name'] = $name;
     }

     if(!empty($work_type))
     {
      $post_data['work_type'] = $work_type;
     }

     if(!empty($location))
     {
      $post_data['location'] = $location;
     }

     if(!empty($duration))
     {
      $post_data['duration'] = $duration;
     }

     if(!empty($start_from))
     {
      $post_data['start_from'] = $start_from;
     }

     if(!empty($stipend))
     {
      $post_data['stipend'] = $stipend;
     }

     if(!empty($salary))
     {
      $post_data['salary'] = $salary;
     }

     if(!empty($skills))
     {
      $post_data['skills'] = $skills;
     }

     if(!empty($perks))
     {
      $post_data['perks'] = $perks;
     }

     if(!empty($price_from))
     {
      $post_data['price_from'] = $price_from;
     }

     if(!empty($price_to))
     {
      $post_data['price_to'] = $price_to;
     }

     if(!empty($about))
     {
      $post_data['about'] = $about;
     }

     if(!empty($day_to_day_work))
     {
      $post_data['day_to_day_work'] = $day_to_day_work;
     }

     if(!empty($questions))
     {
      $post_data['questions'] = $questions;
     }

     if(sizeof($post_data)>0){         
       
       $wheredata = array(
         'id' => $post_id
         );
       $update = $this->Api_Model->update($wheredata,'post_internship',$post_data);
      if($update)
      {
      $json['result'] = "true";
      $json['msg']    = "post updated successfully";
      
      }else{
      $json['result']  = "false";
      $json['msg']     = "Something went wrong.";
      }

     }
     else{
      $json['result'] = "true";
      $json['msg']    = "post updated successfully";
     }

     }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required post_id,optional(name, work_type(Work from home, From Office), location, duration, start_from(Immediately, Select date), stipend, skills, perks(Commission, Certificates, Flexible Time, Offer letter), price_from, price_to, about, day_to_day_work, questions)';
    }

  echo json_encode($json);
}

   public function delete_post_internship()
     {
        $post_id = $this->input->post('post_id');
        
        if(isset($post_id))
        {
            $result = $this->Api_Model->deleteData('post_internship','id',$post_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "post Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "post_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required post_id";
        }
        
       echo json_encode($json);
    }

   public function get_company_members()
  {
    $company_id          = $this->input->post('company_id');

    if(isset($company_id))
  {

    $wheredata = array('field'=>'*',
       'table'=>'company_member',
       'where'=>array('company_id'=>$company_id),       
       'order_by'=>'id desc'
       );

    $result = $this->Api_Model->getAllData($wheredata);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required company_id';
    }
    
    echo json_encode($json);
  }

  public function company_members_detail()
  {
    $member_id          = $this->input->post('member_id');

    if(isset($member_id))
  {

    $result = $this->Api_Model->select_single_rows('company_members','id',$member_id);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required member_id';
    }
    
    echo json_encode($json);
  }

//   public function add_company_members() 
//   {  
 
//     $company_id       = $this->input->post('company_id');
//     $name             = $this->input->post('name');
//     $department       = $this->input->post('department');
//     $location         = $this->input->post('location');
//     $work             = $this->input->post('work');
    
//     if(isset($company_id) && isset($name) && isset($department) && isset($location) && isset($work)){
    
//     $post_data = array(     
//       'company_id'      =>  $company_id,
//       'name'            =>  $name,
//       'department'      =>  $department,
//       'location'        =>  $location,
//       'work'            =>  $work,
//       'created_date'    =>  date('Y-m-d H:i:s')
//     );
    
//      $res = $this->Api_Model->insertAllData('company_members',$post_data);     
//          if($res){
//           $json['result'] = 'true';
//           $json['msg'] = 'company members Added Successfully';
//          }

//      else{       
//          $json['result'] = 'false';
//          $json['msg'] = 'Somthing Went wrong';
//          }         
//          }  
//      else{    
//         $json['result'] = 'false';
//         $json['msg'] = 'parameter required company_id, name, department, location, work';
//       }
  
//      echo json_encode($json);
  
//   }


public function add_company_members() 
  {  
 
    $company_id       = $this->input->post('company_id');
    $name             = $this->input->post('name');
    $department       = $this->input->post('department');
    $location         = $this->input->post('location');
    $work             = $this->input->post('work');
    
    if(isset($company_id) && isset($name) && isset($department) && isset($location) && isset($work)){
    
   


    

    if(!empty($_FILES['image']['name']))
      {
          $image = $this->imageUpload('image','assets/images/users/');
          
          $postdata['image'] = $image;
      }

       $post_data = array(     
      'company_id'      =>  $company_id,
      'name'            =>  $name,
      'department'      =>  $department,
      'location'        =>  $location,
      'work'            =>  $work,
      'image'            =>  $image,
      // 'created_date'    =>  date('Y-m-d H:i:s')
    );
        
     $res = $this->Api_Model->insertAllData('company_member',$post_data);     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'company members Added Successfully';
         }

     else{       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';
         }         
         }  
     else{    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required company_id, name, department, location, work,image';
       }
  
     echo json_encode($json);
  
   }
public function update_company_members()
  {
   $company_id      = $this->input->post('company_id');
    $name           = $this->input->post('name');
    $department     = $this->input->post('department');
    $location       = $this->input->post('location');
    $work           = $this->input->post('work');

    if(isset($company_id))
    {

      $post_data = array();

      if(!empty($company_id))
      {
        $post_data['company_id'] = $company_id;
      }

     if(!empty($name))
      {
        $post_data['name'] = $name;
      }

      if(!empty($department))
      {
        $post_data['department'] = $department;
      }

      if(!empty($location))
      {
        $post_data['location'] = $location;
      }

      if(!empty($work))
      {
        $post_data['work'] = $location;
      }

     
      if(!empty($_FILES['image']['name']))
      {
       
        $image1  = $this->imageUpload('image','assets/images/users/');
        $post_data['image'] = $image1;
      }


      if(sizeof($post_data)>0){         

        $wheredata = array(
          'company_id' => $company_id
        );
        $update = $this->Api_Model->update($wheredata,'company_member',$post_data);
        if($update)
        {
          $json['result'] = "true";
          $json['msg']    = "Company Members Image updated successfully";

        }else{
          $json['result']  = "false";
          $json['msg']     = "Something went wrong.";
        }

      }

    }
    else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required company_id,optional(name,department,location,work,image)';
    }

    echo json_encode($json);
  }
public function delete_company_members()
     {
        $member_id = $this->input->post('member_id');
        
        if(isset($member_id))
        {
            $result = $this->Api_Model->deleteData('company_members','id',$member_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "members Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "member_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required member_id";
        }
        
       echo json_encode($json);
    }
//......................................................................//
  //   public function get_company_products()
  // {
  //   $company_id          = $this->input->post('company_id');

  //   if(isset($company_id))
  // {

  //   $wheredata = array('field'=>'*',
  //      'table'=>'company_products',
  //      'where'=>array('company_id'=>$company_id),       
  //      'order_by'=>'id desc'
  //      );

  //   $result = $this->Api_Model->getAllData($wheredata);    
  //   if($result)
  //   {
  //     $json['result']  = "true";
  //     $json['msg']     = "All Data";
  //     $json['data']    = $result;
  //   }
  //   else
  //   {
  //     $json['result']  = "false";
  //     $json['msg']     = "No Data";
  //   }
  //   }
  //    else
  //   {
  //     $json['result'] = 'false';
  //     $json['msg']    = 'parameter required company_id';
  //   }
    
  //   echo json_encode($json);
  // }

 //................................................................//   
  public function company_products_detail()
  {
    $product_id          = $this->input->post('product_id');

    if(isset($product_id))
  {

    $result = $this->Api_Model->select_single_rows('company_products','id',$product_id);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required product_id';
    }
    
    echo json_encode($json);
  }

  public function add_company_products() 
  {  
 
    $company_id       = $this->input->post('company_id');
    $name             = $this->input->post('name');
    $desciption       = $this->input->post('desciption');
    $batch_no         = $this->input->post('batch_no');
    $expiry           = $this->input->post('expiry');
    $mrp              = $this->input->post('mrp');
    $tax_class        = $this->input->post('tax_class');
    $category_kapsul_id        = $this->input->post('category_kapsul_id');
    $hsn_code        = $this->input->post('hsn_code');
    
    if(isset($company_id) && isset($name) && isset($desciption) && isset($batch_no) && isset($expiry) && isset($mrp) && isset($tax_class) && isset($category_kapsul_id)){
    
    $post_data = array(     
      'company_id'      =>  $company_id,
      'name'            =>  $name,
      'desciption'      =>  $desciption,
      'batch_no'        =>  $batch_no,
      'expiry'          =>  $expiry,
      'mrp'             =>  $mrp,
      'tax_class'       =>  $tax_class,
      'category_kapsul_id'       =>  $category_kapsul_id,
      'hsn_code'       =>  $hsn_code,
      'created_date'    =>  date('Y-m-d H:i:s')
    );
    
     $res = $this->Api_Model->insertAllData('company_products',$post_data);     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'products Added Successfully';
         }

     else{       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';
         }         
         }  
     else{    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required company_id, name, desciption, batch_no, expiry, hsn_code,category_kapsul_id, mrp, tax_class(0,5,12,18,28),optional(hsn_code)';
       }
  
     echo json_encode($json);
  
   }

   public function update_company_products()
{
    $product_id       = $this->input->post('product_id');
    $name             = $this->input->post('name');
    $desciption       = $this->input->post('desciption');
    $batch_no         = $this->input->post('batch_no');
    $expiry           = $this->input->post('expiry');
    $mrp              = $this->input->post('mrp');
    $tax_class        = $this->input->post('tax_class');

  if(isset($product_id))
  {

    $post_data = array();
     if(!empty($name))
     {
      $post_data['name'] = $name;
     }

     if(!empty($desciption))
     {
      $post_data['desciption'] = $desciption;
     }

     if(!empty($batch_no))
     {
      $post_data['batch_no'] = $batch_no;
     }

     if(!empty($expiry))
     {
      $post_data['expiry'] = $expiry;
     }

     if(!empty($mrp))
     {
      $post_data['mrp'] = $mrp;
     }

     if(!empty($tax_class))
     {
      $post_data['tax_class'] = $tax_class;
     }

     if(sizeof($post_data)>0){         
       
       $wheredata = array(
         'id' => $product_id
         );
       $update = $this->Api_Model->update($wheredata,'company_products',$post_data);
      if($update)
      {
      $json['result'] = "true";
      $json['msg']    = "products updated successfully";
      
      }else{
      $json['result']  = "false";
      $json['msg']     = "Something went wrong.";
      }

     }
     else{
      $json['result'] = "true";
      $json['msg']    = "products updated successfully";
     }

     }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required product_id,optional(name,desciption,batch_no,expiry,mrp,tax_class(0,5,12,18,28))';
    }

  echo json_encode($json);
}

public function delete_company_products()
     {
        $product_id = $this->input->post('product_id');
        
        if(isset($product_id))
        {
            $result = $this->Api_Model->deleteData('company_products','id',$product_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "products Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "product_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required product_id";
        }
        
       echo json_encode($json);
    }

    public function get_company_bills()
  {
    $company_id          = $this->input->post('company_id');

    if(isset($company_id))
  {

    $wheredata = array('field'=>'*',
       'table'=>'company_bills',
       'where'=>array('company_id'=>$company_id),       
       'order_by'=>'id desc'
       );

    $result = $this->Api_Model->getAllData($wheredata);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required company_id';
    }
    
    echo json_encode($json);
  }

  public function company_bills_detail()
  {
    $bill_id          = $this->input->post('bill_id');

    if(isset($bill_id))
  {

    $result = $this->Api_Model->select_single_rows('company_bills','id',$bill_id);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required bill_id';
    }
    
    echo json_encode($json);
  }

  public function create_bills_basic() 
  {  
 
    $company_id         = $this->input->post('company_id');
    $name_of_party      = $this->input->post('name_of_party');
    $address_of_party   = $this->input->post('address_of_party');
    $state_id           = $this->input->post('state_id');
    $pin_code           = $this->input->post('pin_code');
    $buyers_contact     = $this->input->post('buyers_contact');
    $drug_license       = $this->input->post('drug_license');
    $gst                = $this->input->post('gst');
    $gst_no             = $this->input->post('gst_no');
    $selling_in         = $this->input->post('selling_in');
    
    if(isset($company_id) && isset($name_of_party) && isset($address_of_party) && isset($state_id) && isset($pin_code) && isset($buyers_contact) && isset($gst) && isset($selling_in)){
    
    $post_data = array(     
      'company_id'        =>  $company_id,
      'name_of_party'     =>  $name_of_party,
      'address_of_party'  =>  $address_of_party,
      'state_id'          =>  $state_id,
      'pin_code'          =>  $pin_code,
      'buyers_contact'    =>  $buyers_contact,
      'drug_license'      =>  $drug_license,
      'gst'               =>  $gst,
      'gst_no'            =>  $gst_no,
      'selling_in'        =>  $selling_in,
      'created_date'      =>  date('Y-m-d H:i:s')
    );
    
     $res = $this->Api_Model->insertAllData('company_bills',$post_data);     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'company bill Added Successfully';
           $json['id'] = $res;
         }

     else{       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';
         }         
         }  
     else{    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required company_id, name_of_party, address_of_party, state_id, pin_code, buyers_contact, selling_in(Same State, Other State), gst(applicable, not applicable), optional(gst_no, drug_license)';
       }
  
     echo json_encode($json);
  
   }

   public function create_bills_product() 
  {
    $bill_id            = $this->input->post('bill_id');
    $product_id         = $this->input->post('product_id');
    $total_quantity     = $this->input->post('total_quantity');
    $selling_price      = $this->input->post('selling_price');
    
    if(isset($bill_id) && isset($product_id) && isset($total_quantity) && isset($selling_price)){

      $product = $this->Api_Model->select_single_rows('company_products','id',$product_id);
    
    $post_data = array(
      'product_id'        =>  $product_id,
      'total_quantity'    =>  $total_quantity,
      'mrp'               =>  $product->mrp,
      'batch_no'          =>  $product->batch_no,
      'selling_price'     =>  $selling_price,
      'total_amount'      =>  $selling_price*$total_quantity
    );

    $wheredata = array('id'=>$bill_id);
     $res = $this->Api_Model->update($wheredata,'company_bills',$post_data);     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'company bill Added Successfully';
           $json['id'] = $bill_id;
           $json['data'] = $post_data;
         }

     else{       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';
         }         
         }  
     else{    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required bill_id, product_id, total_quantity, selling_price';
       }
  
     echo json_encode($json);
  
   }

   public function create_bills_gst() 
  {
    $bill_id            = $this->input->post('bill_id');
    
    if(isset($bill_id)){      

      $total = $this->Api_Model->select_single_rows('company_bills','id',$bill_id);

      $product = $this->Api_Model->select_single_rows('company_products','id',$total->product_id);
    
    $post_data = array(
      'total_with_gst'  =>  $total->total_amount+($product->tax_class*$total->total_amount/100),
    );

    $wheredata = array('id'=>$bill_id);
     $res = $this->Api_Model->update($wheredata,'company_bills',$post_data);

     $total_data = $this->Api_Model->select_single_rows('company_bills','id',$bill_id);
          
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'company bill Added Successfully';
           $json['total'] = $total_data;
         }

     else{       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';
         }         
         }  
     else{    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required bill_id';
       }
  
     echo json_encode($json);
  
   }
 
/*-------------start Anju---------------- */

//...................................creats coupon..................................//
public function create_coupons() 
  {  
 
    $product             =$this->input->post('product');
    $corporation     =$this->input->post('corporation');
    $offer    =$this->input->post('offer');
    $discount     =$this->input->post('discount');
    $percentage      =$this->input->post('percentage');
    $discount_price       =$this->input->post('discount_price');
    $coupon_validity       =$this->input->post('coupon_validity');
    $available       =$this->input->post('available');
    $company_id       =$this->input->post('company_id');
    
    if(isset($product) && isset($corporation) && isset($offer) && isset($discount) && isset($percentage) && isset($discount_price) && isset($coupon_validity) && isset($available)){

    
    $post_data = array(
     
      'product'=>$product,
      'corporation'=>$corporation,
      'offer'=>$offer,
      'discount'=>$discount,
      'percentage'=>$percentage,
      'discount_price'=>$discount_price,
      'coupon_validity'=>$coupon_validity,
      'available'=>$available,
      'company_id'=>$company_id,
      
    );                
    
     $res = $this->Api_Model->insertAllData('create_coupons',$post_data);
     
         if($res){
           $json['result'] = 'true';
           $json['msg'] = 'coupons Added Successfully';
         }

     else{
       
         $json['result'] = 'false';
         $json['msg'] = 'Somthing Went wrong';

         }     
    
         }
  
     else{
    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required product,corporation,offer,discount,percentage,discount_price,coupon_validity,available,company_id';
       }
  
     echo json_encode($json);
  
   }

   //..........................get coupons ................................//
   public function get_coupons()
  {

    $company_id = $this->input->post('company_id');

  if(isset($company_id)){

      $result=$this->Api_Model->get_coupons($company_id);

      if($result)
      {
        $json['result']  = "true";
        $json['msg']     = "All Data";
        $json['data']    = $result;
      }
      else
      {
        $json['result']  = "false";
        $json['msg']     = "No Data";
      }   

  }else{
        $json['result']  = "false";
        $json['msg']     = "parameter required company_id";
  }


    echo json_encode($json);
  }

  //......................update coupons..............................//

  public function update_coupons() 
  {  
 
    $company_id             =$this->input->post('company_id');
    $product             =$this->input->post('product');
    $corporation     =$this->input->post('corporation');
    $offer    =$this->input->post('offer');
    $discount     =$this->input->post('discount');
    $percentage      =$this->input->post('percentage');
    $discount_price       =$this->input->post('discount_price');
    $coupon_validity       =$this->input->post('coupon_validity');
    $available       =$this->input->post('available');
    $coupon_id       =$this->input->post('coupon_id');
   
    
    if(isset($company_id) ){


    $post_data = array();

     if(!empty($company_id))
     {
      $post_data['company_id'] = $company_id;
     }

     if(!empty($product))
     {
      $post_data['product'] = $product;
     }


     if(!empty($corporation))
     {
      $post_data['corporation'] = $corporation;
     }


     if(!empty($offer))
     {
      $post_data['offer'] = $offer;
     }

      if(!empty($discount))
     {
      $post_data['discount'] = $discount;
     }

      if(!empty($percentage))
     {
      $post_data['percentage'] = $percentage;
     }

      if(!empty($discount_price))
     {
      $post_data['discount_price'] = $discount_price;
     }

     if(!empty($coupon_validity))
     {
      $post_data['coupon_validity'] = $coupon_validity;
     }

      if(!empty($available))
     {
      $post_data['available'] = $available;
     }  
$post_data['update_date'] = date('Y-m-d H:i:s');


    
     $result = $this->db->where('id',$coupon_id)
                      ->update('create_coupons',$post_data);

      $result_company=$this->db->where('id',$coupon_id)->get('create_coupons')->row();
                      
     
         if($result_company){
           $json['result'] = 'true';
           $json['msg'] = 'coupons update Successfully';
         }

     else{
       
         $json['result'] = 'false';
         $json['msg'] = 'No Company found';

         }     
    
         }
  
     else{
    
        $json['result'] = 'false';
        $json['msg'] = 'parameter required coupon_id,optional(company_id,product,corporation,offer,discount,percentage,discount_price,coupon_validity,available)';
       }
  
     echo json_encode($json);
  
   }

//.............................................................................//

// public function get_my_skills()
//      {
//         $skill_id  = $this->input->post('skill_id');
        
//         if(isset($skill_id))
//         {
//             $wheredata = array('field'=>'*',
//              'table'=>'skills',
//              'where'=>array('id' => $skill_id),             
//              'order_by'=>'id desc'
//           );
//         $result=$this->Api_Model->getAllData($wheredata);

//         if($result)
//         {
//           $json['result']  = "true";
//           $json['msg']     = "All Data";
//           // $json['path']    = base_url()."assets/images/product/";
//           $json['data']    = $result;
//         }
//         else
//         {
//           $json['result']  = "false";
//           $json['msg']     = "No Data";
//         }

//         }
//         else
//         {
//           $json['result']  = "false";
//           $json['msg']     = "parameter required skill_id";
//         }        
        
//       echo json_encode($json);
//     }


public function get_my_skills()
  {

    $id = $this->input->post('id');

    {
      $wheredata = array('field'=>'id,skill_name,status',
        'table'=>'skills',

        'where'=>array(),

        'order_by'=>'id desc',

      );
      $result=$this->Api_Model->getAllData($wheredata);

      if($result)
      {
        $json['result']  = "true";
        $json['msg']     = "All Data";
        // $json['path']    = base_url()."assets/images/avatars/";
        $json['data']    = $result;
      }
      else
      {
        $json['result']  = "false";
        $json['msg']     = "No Data";
      }

    }


    echo json_encode($json);
  }
   
//.....................sachin start..........................................//

public function get_post_internship()
  {
    $company_id          = $this->input->post('company_id');

    if(isset($company_id))
  {

    $wheredata = array('field'=>'*',
       'table'=>'post_internship',
       'where'=>array('company_id'=>$company_id),       
       'order_by'=>'id desc'
       );
    $where = array('field'=>'*',
       'table'=>'post_job',
       'where'=>array('company_id'=>$company_id),       
       'order_by'=>'id desc'
       );

    

    // $result['post_internship'] = $this->Api_Model->getAllData($wheredata); 
    $result['post_internship'] = $this->Api_Model->get_post_internship($company_id); 
    
    if($result['post_internship']){

    }else{
      $result['post_internship']=null;

    }

     // $result['post_job'] = $this->Api_Model->getAllData($where);  
     $result['post_job'] = $this->Api_Model->get_post_job($company_id);  





       if($result['post_job']){

    }else{
      $result['post_job']=null;

    } 
    if($result['post_internship'] !=null ||  $result['post_job'] !=null  )
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required company_id';
    }
    
    echo json_encode($json);
  }


  //..................sachin end.............................................//
public function get_company_profile()
  {
    $company_id=$this->input->post('company_id');
    if(isset($company_id)){

      $result_company=$this->db->where('id',$company_id)->get('company_user')->row();

    // $this->db->select('company_user.*,states.name as state_name,cities.name as city_name');
    $this->db->select('company_user.*,states.name as state_name,cities.name as city_name');
    $this->db->from('company_user');
    $this->db->join('states','company_user.state_id=states.id');
    $this->db->join('cities','company_user.city_id=cities.id');
    $this->db->order_by('company_user.id asc');
    $this->db->where('company_user.id',$company_id);
    $result = $this->db->get()->result();

    

    if($result)
    {
    
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['data']    = $result;
    }
    else
    {

      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
  }else{
     $json['result'] = "false";
     $json['msg']    = "parameter required company_id";

   }
    
    echo json_encode($json);
  }
//................................get_coupons....................//
 public function add_category()
 {
  $cate_name=$this->input->post('cate_name');
  
  if(isset($cate_name) )
  {
    $data=array('cate_name' =>$cate_name,
      'created_date' =>date('Y-m-d H:i:s'),
       'updated_date' =>date('Y-m-d H:i:s'),);


                 if(!empty($_FILES['cate_image']['name']))
            {
                $image = $this->imageUpload('cate_image','assets/images/category/');
                
                $data['cate_image'] = $image;
            }

            $this->db->insert('category_kapsul',$data);
            $insert_id=$this->db->insert_id();
            if($insert_id){
              $json['result']='true';
              $json['msg']='successfully add data';
               $json['path'] = base_url()."assets/images/category/";
              $json['data']=$insert_id;

            }else{
              $json['result']='false';
              $json['msg']='no data added';
            }

  }else{
    $json['result']='false';
    $json['msg']='parameter required cate_name,cate_image';
  }
  echo json_encode($json);
 }
//.....................................................//

 public function get_category_kapsul()
 {
  $cate_id=$this->input->post('cate_id');
  if(isset($cate_id)){
    $result=$this->db->where('id',$cate_id)->get('category_kapsul')->row();
       if($result){
              $json['result']='true';
              $json['msg']='successfully data';
               $json['path'] = base_url()."assets/images/category/";
              $json['data']=$result;

            }else{
              $json['result']='false';
              $json['msg']='no data found';
            }
  }else{
    $json['result']='false';
    $json['msg']='parameter required cate_id';
  }
  echo json_encode($json);
 }
  /*-------------start Divya---------------- */ 
public function get_category_kapsul_list()
 {
  
    $result=$this->db->get('category_kapsul')->result();
       if($result){
              $json['result']='true';
              $json['msg']='successfully data';
               $json['path'] = base_url()."assets/images/category/";
              $json['data']=$result;

            }else{
              $json['result']='false';
              $json['msg']='no data found';
            }
 
  echo json_encode($json);
 }

 //......................sachin .................................//
 public function get_company_products()
  {
    $company_id          = $this->input->post('company_id');

    if(isset($company_id))
  {

    $wheredata = array('field'=>'*',
       'table'=>'company_products',
       'where'=>array('company_id'=>$company_id),       
       'order_by'=>'id desc'
       );

    // $result = $this->Api_Model->getAllData($wheredata);    
    $result = $this->Api_Model->company_products($company_id);    
    if($result)
    {
      $json['result']  = "true";
      $json['msg']     = "All Data";
      $json['path'] = base_url()."assets/images/category/";
      $json['data']    = $result;
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "No Data";
    }
    }
     else
    {
      $json['result'] = 'false';
      $json['msg']    = 'parameter required company_id';
    }
    
    echo json_encode($json);
  }
//........................Resume......................................//
//....................personal details...............................//

public function add_education()
 {
    $medical_id                   =$this->input->post('medical_id');
    $education                    =$this->input->post('education');
    $school_collage               =$this->input->post('school_collage');
    $start_date                   =$this->input->post('start_date');
    $end_date                     =$this->input->post('end_date');
    $degree                       =$this->input->post('degree');
    $stream                       =$this->input->post('stream');
    $performance_scale            =$this->input->post('performance_scale');
    $performance                  =$this->input->post('performance');
    $board                        =$this->input->post('board');
  
     if(isset($medical_id)
     && isset($education) 
     && isset($school_collage)
     // && isset($start_date) 
     // && isset($end_date) 
     // && isset($degree) 
     // && isset($stream) 
     && isset($performance_scale) 
     && isset($performance) 
     // && isset($board) 
   )
  {
    $data=array(
      'medical_id'       =>$medical_id,
      'education'        =>$education,
      'school_collage'   =>$school_collage,
      'start_date'       =>$start_date,
      'end_date'         =>$end_date,
      'degree'           =>$degree,
      'stream'           =>$stream,
      'performance_scale'=>$performance_scale,
      'performance'      =>$performance,
      'board'            =>$board,
      'created_date'     =>date('Y-m-d H:i:s'),
       'updated_date'    =>date('Y-m-d H:i:s'),
     );


            $this->db->insert('education',$data);
            $insert_id=$this->db->insert_id();
            if($insert_id){
              $json['result']='true';
              $json['msg']='successfully add data';
              $json['data']=$insert_id;

            }else{
              $json['result']='false';
              $json['msg']='something went wrong';
            }

  }else{
    $json['result']='false';
    $json['msg']='parameter required medical_id,education,school_collage,performance_scale,performance,optional(start_date,end_date,degree,stream,board)';
  }
  echo json_encode($json);
 }

//...............................update_education...........................................//

 public function update_education() 
 {  

  $education_id                 =$this->input->post('education_id');
  $medical_id                   =$this->input->post('medical_id');
  $education                    =$this->input->post('education');
  $school_collage               =$this->input->post('school_collage');
  $start_date                   =$this->input->post('start_date');
  $end_date                     =$this->input->post('end_date');
  $degree                       =$this->input->post('degree');
  $stream                       =$this->input->post('stream');
  $performance_scale            =$this->input->post('performance_scale');
  $performance                  =$this->input->post('performance');
  $board                        =$this->input->post('board');
  


  if(isset($education_id) ){


    $post_data = array();

    if(!empty($medical_id))
    {
      $post_data['medical_id'] = $medical_id;
    }

    if(!empty($education))
    {
      $post_data['education'] = $education;
    }


    if(!empty($school_collage))
    {
      $post_data['school_collage'] = $school_collage;
    }


    if(!empty($start_date))
    {
      $post_data['start_date'] = $start_date;
    }

    if(!empty($end_date))
    {
      $post_data['end_date'] = $end_date;
    }

    if(!empty($degree))
    {
      $post_data['degree'] = $degree;
    }

    if(!empty($stream))
    {
      $post_data['stream'] = $stream;
    }

    if(!empty($performance_scale))
    {
      $post_data['performance_scale'] = $performance_scale;
    }

    if(!empty($performance))
    {
      $post_data['performance'] = $performance;
    }  
    $post_data['updated_date'] = date('Y-m-d H:i:s');



    $result_educ=$this->db->where('id',$education_id)->get('education')->row();

    if($result_educ){
      $result = $this->db->where('id',$education_id)
      ->update('education',$post_data);
    }

    $result_education=$this->db->where('id',$education_id)->get('education')->row();            

    if($result_education){
     $json['result'] = 'true';
     $json['msg'] = 'education update Successfully';
     $json['data'] = $result_education;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'No education data found';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required education_id,optional(medical_id,education,school_collage,start_date,end_date,degree,stream,performance_scale,performance,board)';
}

echo json_encode($json);

}

//................................job_internship....................................//

public function add_job_internship()
 {
    $medical_id                   =$this->input->post('medical_id');
    $job_profile                  =$this->input->post('job_profile');
    $organization                 =$this->input->post('organization');
    $work_from_home               =$this->input->post('work_from_home');
    $location                     =$this->input->post('location');
    $start_date                   =$this->input->post('start_date');
    $end_date                     =$this->input->post('end_date');
    $currently_working            =$this->input->post('currently_working');
    $work_discription             =$this->input->post('work_discription');
    $type                         =$this->input->post('type');
  
     if(isset($medical_id)
     && isset($job_profile) 
     && isset($organization)
     && isset($work_from_home) 
     && isset($location) 
     && isset($start_date) 
     && isset($end_date) 
     && isset($currently_working) 
     && isset($work_discription) 
     && isset($type) 
   )
  {
    $data=array(
      'medical_id'          =>$medical_id,
      'job_profile'         =>$job_profile,
      'organization'        =>$organization,
      'work_from_home'      =>$work_from_home,
      'location'            =>$location,
      'start_date'          =>$start_date,
      'end_date'            =>$end_date,
      'currently_working'   =>$currently_working,
      'work_discription'    =>$work_discription,
      'type'                =>$type,
      'created_date'        =>date('Y-m-d H:i:s'),
       'updated_date'       =>date('Y-m-d H:i:s'),
     );


            $this->db->insert('job_internship',$data);
            $insert_id=$this->db->insert_id();
            if($insert_id){
              $json['result'] ='true';
              $json['msg']    ='successfully add data';
              $json['data']   =$insert_id;

            }else{
              $json['result'] ='false';
              $json['msg']    ='something went wrong';
            }

  }else{
             $json['result']  ='false';
             $json['msg']     ='parameter required medical_id,job_profile,organization,work_from_home (yes / no) ,location,start_date,end_date,currently_working (yes / no) , work_discription ,type(job / internship)';
  }
  echo json_encode($json);
 }

//.................................update job internship..........................................//
  public function update_job_internship() 
 {  

    $job_internship_id            =$this->input->post('job_internship_id');
    $medical_id                   =$this->input->post('medical_id');
    $job_profile                  =$this->input->post('job_profile');
    $organization                 =$this->input->post('organization');
    $work_from_home               =$this->input->post('work_from_home');
    $location                     =$this->input->post('location');
    $start_date                   =$this->input->post('start_date');
    $end_date                     =$this->input->post('end_date');
    $currently_working            =$this->input->post('currently_working');
    $work_discription             =$this->input->post('work_discription');
    $type                         =$this->input->post('type');
  


  if(isset($job_internship_id) ){


    $post_data = array();

    if(!empty($medical_id))
    {
      $post_data['medical_id'] = $medical_id;
    }

    if(!empty($job_profile))
    {
      $post_data['job_profile'] = $job_profile;
    }


    if(!empty($organization))
    {
      $post_data['organization'] = $organization;
    }


    if(!empty($work_from_home))
    {
      $post_data['work_from_home'] = $work_from_home;
    }

    if(!empty($location))
    {
      $post_data['location'] = $location;
    }

    if(!empty($start_date))
    {
      $post_data['start_date'] = $start_date;
    }

    if(!empty($end_date))
    {
      $post_data['end_date'] = $end_date;
    }

    if(!empty($currently_working))
    {
      $post_data['currently_working'] = $currently_working;
    }

    if(!empty($work_discription))
    {
      $post_data['work_discription'] = $work_discription;
    }

    if(!empty($type))
    {
      $post_data['type'] = $type;
    }

    $post_data['updated_date'] = date('Y-m-d H:i:s');



    $result_job=$this->db->where('id',$job_internship_id)->get('job_internship')->row();

    if($result_job){
      $result = $this->db->where('id',$job_internship_id)
      ->update('job_internship',$post_data);
    }

    $result_result_job_internship=$this->db->where('id',$job_internship_id)->get('job_internship')->row();            

    if($result_result_job_internship){
     $json['result'] = 'true';
     $json['msg'] = 'job internship update Successfully';
     $json['data'] = $result_result_job_internship;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'No job internship data found';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required job_internship_id,optional(medical_id,job_profile,organization,work_from_home (yes / no) ,location,start_date,end_date,currently_working (yes / no) , work_discription, type(job / internship))';
}

echo json_encode($json);

}
//............................medical signup..................................//
public function medical_company_signup()
  {
    
    $mobile               = $this->input->post('mobile');
    $fcm_id               = $this->input->post('fcm_id');
    
    if(isset($mobile) &&  isset($fcm_id))
    {
      if(!$this->Api_Model->is_record_exist('company_user','mobile',$mobile))
      {
        
     
          
            $otp = rand(1000,9999);
          
             $postdata = array(
          
            'mobile' => $mobile,
            'fcm_id' => $fcm_id,
            'otp' => $otp,
            'type' => "medical",
            'created_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s'),
            'verify_otp' => "0",
            );
            
           $result = $this->Api_Model->insertAllData('company_user', $postdata);
           
           if($result)
           {
         
             $this->db->where("company_user.id",$result);
             $this->db->select("id,mobile,type,fcm_id,verify_otp,otp");
             $this->db->from("company_user");
             $vv = $this->db->get()->row();
             
             $json['result']  = "true";
             $json['msg']     = "Medical signup successfully!";
             $json['path']     = base_url()."assets/images/users/";
             $json['id'] = $vv->id;
             $json['otp']     = $otp;
             $json['data'] =$vv;
            
           }
           else
           {
             $json['result']  = "false";
             $json['msg']     = "something went wrong!";
           }           
        
        
      }
      else
      {
        $json['result']  = "false";
        $json['msg']     = "mobile already exist!";
      }
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required mobile,fcm_id";      
    }    
    
    echo json_encode($json);
    
  }

//.............................chemist signup.................................//

//...................................get education.......................//
   public function get_education()
     {
        $medical_id = $this->input->post('medical_id');
        
        if(isset($medical_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'education',
             'where'=>array('medical_id' => $medical_id),             
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All Education Data ";
          // $json['path']    = base_url()."assets/images/product/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No Education Data";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required medical_id";
        }        
        
      echo json_encode($json);
    }
  //....................get job_internship.......................//
   public function get_job_internship()
     {
        $medical_id = $this->input->post('medical_id');
        
        if(isset($medical_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'job_internship',
             'where'=>array('medical_id' => $medical_id),             
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All job_internship Data ";
          // $json['path']    = base_url()."assets/images/product/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No job_internship Data";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required medical_id";
        }        
        
      echo json_encode($json);
    }

   //.................delete education...................................//

    public function delete_education()
     {
        $education_id = $this->input->post('education_id');
        
        if(isset($education_id))
        {
            $result = $this->Api_Model->deleteData('education','id',$education_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "successfully education Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "education_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required education_id";
        }
        
       echo json_encode($json);
    } 

    //.................delete job_internship...................................//
    
    public function delete_job_internship()
     {
        $job_internship_id = $this->input->post('job_internship_id');
        
        if(isset($job_internship_id))
        {
            $result = $this->Api_Model->deleteData('job_internship','id',$job_internship_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "successfully job_internship Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "job_internship_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required job_internship_id";
        }
        
       echo json_encode($json);
    }
//..............................add skills........................//
public function add_skills_company()
 {
    $medical_id                   =$this->input->post('medical_id');
    $skill_id                     =$this->input->post('skill_id');
    $skill_name                   =$this->input->post('skill_name');
    $skill_rate                   =$this->input->post('skill_rate');
    
     if(isset($medical_id)
     && isset($skill_name) 
     && isset($skill_id)
     && isset($skill_rate) 
   
   )

  {
    $data=array(
      'medical_id'          =>$medical_id,
      'skill_name'          =>$skill_name,
      'skill_id'            =>$skill_id,
      'skill_rate'          =>$skill_rate,
      'created_date'        =>date('Y-m-d H:i:s'),
       'updated_date'       =>date('Y-m-d H:i:s'),
     );


            $this->db->insert('skills_company',$data);
            $insert_id=$this->db->insert_id();
            if($insert_id){
              $json['result'] ='true';
              $json['msg']    ='successfully add skills_company';
              $json['data']   =$insert_id;

            }else{
              $json['result'] ='false';
              $json['msg']    ='something went wrong';
            }

  }else{
             $json['result']  ='false';
             $json['msg']     ='parameter required medical_id,skill_id,skill_name,skill_rate (beginner / intermediate / advanced)';
  }
  echo json_encode($json);
 }

 //.....................get skills.....................................//
   public function get_skills_company()
     {
        $medical_id = $this->input->post('medical_id');
        
        if(isset($medical_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'skills_company',
             'where'=>array('medical_id' => $medical_id),             
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All skills_company Data ";
          // $json['path']    = base_url()."assets/images/product/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No skills_company Data";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required medical_id";
        }        
        
      echo json_encode($json);
    }
 //...........................delete skills............................//  

 public function delete_skills_company()
     {
        $skills_company_id = $this->input->post('skills_company_id');
        
        if(isset($skills_company_id))
        {
            $result = $this->Api_Model->deleteData('skills_company','id',$skills_company_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "successfully skills_company Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "skills_company_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required skills_company_id";
        }
        
       echo json_encode($json);
    } 
//....................update_skills................................//
  public function update_skills_company() 
 {  

    $skills_company_id            =$this->input->post('skills_company_id');
    $medical_id                   =$this->input->post('medical_id');
    $skill_id                     =$this->input->post('skill_id');
    $skill_name                   =$this->input->post('skill_name');
    $skill_rate                   =$this->input->post('skill_rate');
  


  if(isset($skills_company_id) ){


    $post_data = array();

    if(!empty($medical_id))
    {
      $post_data['medical_id'] = $medical_id;
    }

    if(!empty($skill_id))
    {
      $post_data['skill_id'] = $skill_id;
    }


    if(!empty($skill_name))
    {
      $post_data['skill_name'] = $skill_name;
    }


    if(!empty($skill_rate))
    {
      $post_data['skill_rate'] = $skill_rate;
    }

    
    $post_data['updated_date'] = date('Y-m-d H:i:s');



    $result_job=$this->db->where('id',$skills_company_id)->get('skills_company')->row();

    if($result_job){
      $result = $this->db->where('id',$skills_company_id)
      ->update('skills_company',$post_data);
    }

    $result_result_skills=$this->db->where('id',$skills_company_id)->get('skills_company')->row();            

    if($result_result_skills){
     $json['result'] = 'true';
     $json['msg'] = 'skills update Successfully';
     $json['data'] = $result_result_skills;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'No skills data found';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required skills_company_id,optional(medical_id,skill_id,skill_name,skill_rate (beginner / intermediate / advanced))';
}

echo json_encode($json);

}
//.........................portfolio.......................................//
public function add_portfolio_old()
 {
    $medical_id                     =$this->input->post('medical_id');
    $blog_link                      =$this->input->post('blog_link');
    $github_profile                 =$this->input->post('github_profile');
    $play_store_link                =$this->input->post('play_store_link');
    $portfolio_link                 =$this->input->post('portfolio_link');
    $other_work                     =$this->input->post('other_work');
    
     if(isset($medical_id)
     && isset($blog_link) 
     && isset($github_profile) 
     && isset($play_store_link)
     && isset($portfolio_link) 
     && isset($other_work) 
   
   )

  {
    $data=array(
      'medical_id'           =>$medical_id,
      'blog_link'            =>$blog_link,
      'github_profile'       =>$github_profile,
      'play_store_link'      =>$play_store_link,
      'portfolio_link'       =>$portfolio_link,
      'other_work'           =>$other_work,
      'created_date'         =>date('Y-m-d H:i:s'),
       'updated_date'        =>date('Y-m-d H:i:s'),
     );


            $this->db->insert('portfolio',$data);
            $insert_id=$this->db->insert_id();
            if($insert_id){
              $json['result'] ='true';
              $json['msg']    ='successfully add portfolio';
              $json['data']   =$insert_id;

            }else{
              $json['result'] ='false';
              $json['msg']    ='something went wrong';
            }

  }else{
             $json['result']  ='false';
             $json['msg']     ='parameter required medical_id,blog_link,github_profile,play_store_link,portfolio_link,other_work';
  }
  echo json_encode($json);
 }

 public function get_portfolio()
     {
        $medical_id = $this->input->post('medical_id');
        
        if(isset($medical_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'portfolio',
             'where'=>array('medical_id' => $medical_id),             
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All portfolio Data ";
          // $json['path']    = base_url()."assets/images/product/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No portfolio Data";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required medical_id";
        }        
        
      echo json_encode($json);
    }

    public function delete_portfolio()
     {
        $portfolio_id = $this->input->post('portfolio_id');
        
        if(isset($portfolio_id))
        {
            $result = $this->Api_Model->deleteData('portfolio','id',$portfolio_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "successfully portfolio Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "portfolio_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required portfolio_id";
        }
        
       echo json_encode($json);
    }

  public function update_portfolio() 
 {  

    $portfolio_id                   =$this->input->post('portfolio_id');
    $medical_id                     =$this->input->post('medical_id');
    $blog_link                      =$this->input->post('blog_link');
    $github_profile                 =$this->input->post('github_profile');
    $play_store_link                =$this->input->post('play_store_link');
    $portfolio_link                 =$this->input->post('portfolio_link');
    $other_work                     =$this->input->post('other_work');
  


  if(isset($portfolio_id) ){


    $post_data = array();

    if(!empty($medical_id))
    {
      $post_data['medical_id'] = $medical_id;
    }

    if(!empty($blog_link))
    {
      $post_data['blog_link'] = $blog_link;
    }


    if(!empty($github_profile))
    {
      $post_data['github_profile'] = $github_profile;
    }


    if(!empty($play_store_link))
    {
      $post_data['play_store_link'] = $play_store_link;
    }
    
    if(!empty($portfolio_link))
    {
      $post_data['portfolio_link'] = $portfolio_link;
    }

    if(!empty($other_work))
    {
      $post_data['other_work'] = $other_work;
    }
    
    $post_data['updated_date'] = date('Y-m-d H:i:s');



    $result_job=$this->db->where('id',$portfolio_id)->get('portfolio')->row();

    if($result_job){
      $result = $this->db->where('id',$portfolio_id)
      ->update('portfolio',$post_data);
    }

    $result_result_skills=$this->db->where('id',$portfolio_id)->get('portfolio')->row();            

    if($result_result_skills){
     $json['result'] = 'true';
     $json['msg'] = 'portfolio update Successfully';
     $json['data'] = $result_result_skills;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'No portfolio data found';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required portfolio_id,optional(medical_id,blog_link,github_profile,play_store_link,portfolio_link,other_work)';
}

echo json_encode($json);

}
//......................good cart.........................................//
     //...............good cart  controller ..............................//
public function getProductByCategory()

  {    

    $filter = "";

    extract($_POST);


    if(isset($user_id))
    {
      
    }else{
      $total_item_count = '0';
    }

    if(isset($category_id) && isset($user_id))
    {

    //   if($category_id == '0')
    //   {

    //     $result =  $this->ProductModel->getProducts($filter,$user_id);

    //   }else{

        $result =  $this->ProductModel->getProductByCate($category_id,$filter,$user_id);
        
    //   }

      if($result)

      {

        $data['result'] = "true";
        $data['total_item_count'] = count($this->ProductModel->get_cart_product($user_id));
        $data['data'] = $result;

        $data['msg']    = 'Products';

      }else{

        $data['result'] = "false";

        $data['msg']    = 'No Products available';            

      }  

    }else{

      $data['result'] = 'false';

      $data['msg']    = 'Please provide parameters(category_id,user_id)';            

    }       

    echo json_encode($data);

  }
//................good cart model..........................//
   public function getProductByCate($category_id,$filter,$user_id)
  {
     $custom_query = "SELECT
                        products.id,
                        products.url_slug,
                        products.product_id,
                        products.name,
                        products.description,
                        products.is_subscription,
                        CONCAT(products.quantity,' ',products.unit) as quantity,
                        ROUND(products.mrp) as mrp,
                        ROUND(products.price) as price,
                         ROUND((mrp-price) * 100/mrp) AS Percent,
                        categories.name as cate_name,

                        (SELECT image FROM product_images WHERE product_id = products.product_id LIMIT 1 ) as image,
                        (SELECT COUNT(id) FROM wishlist WHERE product_id = products.id AND user_id ='$user_id') as wishlist_status,
                        (SELECT COUNT(id) FROM carts WHERE product_id = products.id AND user_id ='$user_id') as cart_status,
                        (SELECT SUM(quantity) FROM carts WHERE product_id = products.id AND user_id ='$user_id') as cart_quantity

                      FROM products
                      LEFT JOIN categories ON products.cate_id =  categories.id
                      WHERE products.cate_id = '$category_id' AND products.product_status='Instock'";
    if(!empty($filter))
    {
      if($filter == 'low_to_high')
      {
        $custom_query .= " ORDER BY products.price ASC ";
      }

      if($filter == 'high_to_low')
      {
        $custom_query .= " ORDER BY products.price DESC ";
      }

      if($filter == 'alphabetical')
      {
        $custom_query .= " ORDER BY products.name ASC";
      }
    }else{
      $custom_query .= "GROUP BY products.id ORDER BY products.name ASC";
    }

       $query = $this->db->query($custom_query);

    return $query->result_array();
  }

//.....................................................................//

  public function add_portfolio()
  {
    $medical_id                     =$this->input->post('medical_id');
    $blog_link                      =$this->input->post('blog_link');
    $github_profile                 =$this->input->post('github_profile');
    $play_store_link                =$this->input->post('play_store_link');
    $portfolio_link                 =$this->input->post('portfolio_link');
    $other_work                     =$this->input->post('other_work');
    
    if(isset($medical_id))

    {

      if(!$this->Api_Model->is_record_exist('portfolio','medical_id',$medical_id))
      {


        $data=array(
          'medical_id'           =>$medical_id,
          'blog_link'            =>$blog_link,
          'github_profile'       =>$github_profile,
          'play_store_link'      =>$play_store_link,
          'portfolio_link'       =>$portfolio_link,
          'other_work'           =>$other_work,
          'created_date'         =>date('Y-m-d H:i:s'),
          'updated_date'        =>date('Y-m-d H:i:s'),
        );


        $this->db->insert('portfolio',$data);
        $insert_id=$this->db->insert_id();
        if($insert_id){
          $json['result'] ='true';
          $json['msg']    ='successfully add portfolio';
          $json['data']   =$insert_id;

        }else{
          $json['result'] ='false';
          $json['msg']    ='something went wrong';
        }



      }else{

       $post_data = array();

       if(!empty($medical_id))
       {
        $post_data['medical_id'] = $medical_id;
      }

      if(!empty($blog_link))
      {
        $post_data['blog_link'] = $blog_link;
      }


      if(!empty($github_profile))
      {
        $post_data['github_profile'] = $github_profile;
      }


      if(!empty($play_store_link))
      {
        $post_data['play_store_link'] = $play_store_link;
      }

      if(!empty($portfolio_link))
      {
        $post_data['portfolio_link'] = $portfolio_link;
      }

      if(!empty($other_work))
      {
        $post_data['other_work'] = $other_work;
      }

      $post_data['updated_date'] = date('Y-m-d H:i:s');



      $result_job=$this->db->where('medical_id',$medical_id)->get('portfolio')->row();

      if($result_job){
        $result = $this->db->where('medical_id',$medical_id)
        ->update('portfolio',$post_data);
      }

      $result_result_skills=$this->db->where('medical_id',$medical_id)->get('portfolio')->row();            

      if($result_result_skills){
       $json['result'] = 'true';
       $json['msg'] = 'portfolio update Successfully';
       $json['data'] = $result_result_skills;
     }

     else{
       $json['result'] = 'false';
       $json['msg'] = 'No portfolio data found';

     }


   }       

 }else{
   $json['result']  ='false';

   $json['msg'] = 'parameter required medical_id,optional(blog_link,github_profile,play_store_link,portfolio_link,other_work)';
 }
 echo json_encode($json);
}

  //..........................project details................................//

public function add_projects_details()
 {
    $medical_id                 =$this->input->post('medical_id');
    $title                      =$this->input->post('title');
    $start_date                 =$this->input->post('start_date');
    $end_date                   =$this->input->post('end_date');
    $currently_going            =$this->input->post('currently_going');
    $discription                =$this->input->post('discription');
    $project_link               =$this->input->post('project_link');
    
     if(isset($medical_id)
     && isset($title) 
     && isset($start_date) 
     && isset($end_date)
     && isset($currently_going) 
     && isset($discription) 
     && isset($project_link) 
   
   )

  {
    $data=array(
      'medical_id'           =>$medical_id,
      'title'                =>$title,
      'start_date'           =>$start_date,
      'end_date'             =>$end_date,
      'currently_going'      =>$currently_going,
      'discription'          =>$discription,
      'project_link'         =>$project_link,
      'created_date'         =>date('Y-m-d H:i:s'),
       'updated_date'        =>date('Y-m-d H:i:s'),
     );


            $this->db->insert('projects_details',$data);
            $insert_id=$this->db->insert_id();
            if($insert_id){
              $json['result'] ='true';
              $json['msg']    ='successfully add projects_details';
              $json['data']   =$insert_id;

            }else{
              $json['result'] ='false';
              $json['msg']    ='something went wrong';
            }

  }else{
             $json['result']  ='false';
             $json['msg']     ='parameter required medical_id,title,start_date,end_date,currently_going( yes / no ),discription,project_link';
  }
  echo json_encode($json);
 }

 public function get_projects_details()
     {
        $medical_id = $this->input->post('medical_id');
        
        if(isset($medical_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'projects_details',
             'where'=>array('medical_id' => $medical_id),             
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All projects_details Data ";
          // $json['path']    = base_url()."assets/images/product/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No projects_details Data";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required medical_id";
        }        
        
      echo json_encode($json);
    }

    public function delete_projects_details()
     {
        $projects_details_id = $this->input->post('projects_details_id');
        
        if(isset($projects_details_id))
        {
            $result = $this->Api_Model->deleteData('projects_details','id',$projects_details_id);
            
        if($result)
        {
          $json['result'] = "true";
          $json['msg']    = "successfully projects_details Deleted";
        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "projects_details_id Invalid";
        }

        }
        else
        {
          $json['result'] = "false";
          $json['msg']    = "parameter required projects_details_id";
        }
        
       echo json_encode($json);
    }

  public function update_projects_details() 
 {  

    $projects_details_id        =$this->input->post('projects_details_id');
    $medical_id                 =$this->input->post('medical_id');
    $title                      =$this->input->post('title');
    $start_date                 =$this->input->post('start_date');
    $end_date                   =$this->input->post('end_date');
    $currently_going            =$this->input->post('currently_going');
    $discription                =$this->input->post('discription');
    $project_link               =$this->input->post('project_link');
  


  if(isset($projects_details_id) ){


    $post_data = array();

    if(!empty($medical_id))
    {
      $post_data['medical_id'] = $medical_id;
    }

    if(!empty($title))
    {
      $post_data['title'] = $title;
    }


    if(!empty($start_date))
    {
      $post_data['start_date'] = $start_date;
    }


    if(!empty($end_date))
    {
      $post_data['end_date'] = $end_date;
    }
    
    if(!empty($currently_going))
    {
      $post_data['currently_going'] = $currently_going;
    }

    if(!empty($discription))
    {
      $post_data['discription'] = $discription;
    }

    if(!empty($project_link))
    {
      $post_data['project_link'] = $project_link;
    }
    
    $post_data['updated_date'] = date('Y-m-d H:i:s');



    $result_job=$this->db->where('id',$projects_details_id)->get('projects_details')->row();

    if($result_job){
      $result = $this->db->where('id',$projects_details_id)
      ->update('projects_details',$post_data);
    }

    $result_projects_details=$this->db->where('id',$projects_details_id)->get('projects_details')->row();            

    if($result_projects_details){
     $json['result'] = 'true';
     $json['msg'] = 'projects_details update Successfully';
     $json['data'] = $result_projects_details;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'No projects_details data found';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required projects_details_id,optional(medical_id,title,start_date,end_date,currently_going( yes / no ),discription,project_link)';
}

echo json_encode($json);

}
    
    //.....................resend_otp.......................//
    
    public function resend_otp_company_user()

    {
        $user_id = $this->input->post('user_id');

        if(isset($user_id))
        {
        $wheredata1 = array('field'=>['id','mobile'],

                     'table'=>'company_user',

                     'where'=>array('id'=>$user_id)

            );


          $is_exist = $this->Api_Model->getAllDataRow($wheredata1);


          if($is_exist)
          {
                 $otp = rand(1000,9999);


            $post_data = array(
                'otp'=>$otp,
                );

            $this->db->where('id',$user_id);
            $result = $this->db->update('company_user',$post_data);

            if($result)
            {
                  $wheredata = array('field'=>'id,name,company_name,otp,mobile,email,type,verify_otp,fcm_id',

                    'table'=>'company_user',

                    'where'=>array('id'=>$user_id),

                   );



                   $row = $this->Api_Model->getAllDataRow($wheredata);

                 $json['result'] = "true";
                 $json['msg']    = "Resent successfully";
                 $json['data']   = $row;


            }
            else
            {

              $json['result'] = "false";
              $json['msg']    = "Something went wrong. Please try later.";

            }
          }
          else
          {
              $json['result'] = "false";
              $json['msg']    = "user_id doesnt exist";
          }

        }
        else
        {
            $json['result'] = "false";
            $json['msg']    = "parameter required user_id";
        }

        echo json_encode($json);
    }

   

  
  
  //........................get my apply job..................//

  public function get_my_apply_job_internship()
  {
    $user_id=$this->input->post('user_id');
    $type=$this->input->post('type');
    if(isset($user_id) && isset($type))
    {

      $result=$this->Api_Model->apply_job_internship($user_id,$type);

      if($result){

        if($type=='job'){

         $json['result'] = "true";
         $json['msg']    = "All My Apply job data";
         $json['data']   = $result;

         }else{

         $json['result'] = "true";
         $json['msg']    = "All My Apply internship data";
         $json['data']   = $result;

        }

     }
     else
     {
      $json['result'] = "false";
      $json['msg']    = "No appy job_internship.";
    }
  }
  else
  {
    $json['result'] = "false";
    $json['msg']    = "parameter required user_id,type( job (or) internship )";
  }

  echo json_encode($json);
}
 //......................15 feb 2023.................................//

 //..........................get all job .......................//

    public function get_all_jobs()
    {
      $medical_id=$this->input->post('medical_id');
      if(isset($medical_id))
      {
        $result_apply=$this->Api_Model->appy_already($medical_id);

        if($result_apply){

         $json['result'] = "true";
         $json['msg']    = "All job data";
         $json['path']     = base_url()."assets/images/users/";
         $json['data']   = $result_apply;
       }
       else
       {
        $json['result'] = "false";
        $json['msg']    = "Something went wrong. Please try later.";
      }
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required medical_id";
    }

    echo json_encode($json);
  }

  //.......................get all internship.................//

     public function get_all_internship()
    {
      $medical_id=$this->input->post('medical_id');
      if(isset($medical_id))
      {
        
        $result_apply=$this->Api_Model->appy_already_internship($medical_id);

        if($result_apply){

         $json['result'] = "true";
         $json['msg']    = "All internship data";
         $json['path']     = base_url()."assets/images/users/";
         $json['data']   = $result_apply;
       }
       else
       {
        $json['result'] = "false";
        $json['msg']    = "Something went wrong. Please try later.";
      }
    }
    else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required medical_id";
    }

    echo json_encode($json);
  }


//.................apply jobs..............................//

  public function apply_job_internship()
  {
    $medical_id=$this->input->post('medical_id');
    $job_internship_id=$this->input->post('job_internship_id');
    $company_id=$this->input->post('company_id');
    $type=$this->input->post('type');
    $answer_1=$this->input->post('answer_1');
    $answer_2=$this->input->post('answer_2');
    $answer_3=$this->input->post('answer_3');
    $answer_4=$this->input->post('answer_4');
    $answer_5=$this->input->post('answer_5');
    if(isset($medical_id) && isset($job_internship_id) &&  isset($company_id) && isset($type))
    {
                                    
      $data=array('medical_id'=>$medical_id,
                  'job_internship_id'=>$job_internship_id,
                   'type'=>$type,
                   'company_id'=>$company_id,
                   'apply_status'=>'applied',
                   'apply_date'=>date('Y-m-d H:i:s'),
                   'answer_1'=>$answer_1,
                   'answer_2'=>$answer_2,
                   'answer_3'=>$answer_3,
                   'answer_4'=>$answer_4,
                   'answer_5'=>$answer_5,
                 );
      $this->db->insert('apply_job_internship',$data);
      $insert_id=$this->db->insert_id();


if($type=='job'){ 
  
$fetch_data=$this->db->select('id,company_id,count')
                     ->where('id',$job_internship_id)
                     ->get('post_job')
                     ->row();

$count=$fetch_data->count;
$new_count=($count+1);

$update_count=array('count'=>$new_count);
$this->db->where('id',$job_internship_id)
          ->update('post_job',$update_count);

}

if($type=='internship'){ 
  
$fetch_data=$this->db->select('id,company_id,count')
                     ->where('id',$job_internship_id)
                     ->get('post_internship')
                     ->row();

$count=$fetch_data->count;
$new_count=($count+1);

$update_count=array('count'=>$new_count);
$this->db->where('id',$job_internship_id)
          ->update('post_internship',$update_count);               
}




      if($insert_id)
      {
        $json['result']="true";
        $json['msg']="successfully apply job_internship";
        $json['data']=$insert_id;
      }else{
        $json['result']="false";
        $json['msg']="No apply job_internship";
      }

    }else
    {
      $json['result'] = "false";
      $json['msg']    = "parameter required medical_id,job_internship_id,company_id,type( job (or) internship ),optional(answer_1,answer_2,answer_3,answer_4,answer_5)";
    }

    echo json_encode($json);
  }

//...............................medical...............................//

public function get_my_apply_job_internship_medical()
  {
    $medical_id=$this->input->post('medical_id');
    
    if(isset($medical_id) )
    {

      $result=$this->Api_Model->apply_job_internship_medical($medical_id);

      if($result){

         $json['result'] = "true";
         $json['msg']    = "All My Apply job_internship data";
         $json['data']   = $result;

         }else{
      $json['result'] = "false";
      $json['msg']    = "No appy job_internship";
    }
  }
  else
  {
    $json['result'] = "false";
    $json['msg']    = "parameter required medical_id";
  }

  echo json_encode($json);
}
//............................company................................//
     public function get_my_apply_job_internship_company()
  {
    $company_id=$this->input->post('company_id');
    
    if(isset($company_id) )
    {

      $result=$this->Api_Model->apply_job_internship_company($company_id);

      if($result){

         $json['result'] = "true";
         $json['msg']    = "All My Apply job_internship data";
         $json['data']   = $result;

         }else{
      $json['result'] = "false";
      $json['msg']    = "No appy job_internship";
    }
  }
  else
  {
    $json['result'] = "false";
    $json['msg']    = "parameter required company_id";
  }

  echo json_encode($json);
}


//.................. 16 feb 2023..............................//
public function get_company_user_profile_medical()
{

     $medical_id = $this->input->post('medical_id');
        
        if(isset($medical_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'company_user',
             'where'=>array('id' => $medical_id),             
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All  Medical Data show ";
          $json['path']     = base_url()."assets/images/users/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No Medical Data show";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required medical_id";
        }        
        
      echo json_encode($json);
    } 

   //......................update medical image...............//
    public function update_medical_image()
    {
      $medical_id=$this->input->post('medical_id');
      if($medical_id){

        $config['upload_path'] = './assets/images/users/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $this->load->library('upload', $config);


        if(!$this->upload->do_upload('image'))
        {
      
        }else{
          $this->load->library('image_lib');


          $image_data =   $this->upload->data();

          if($image_data['file_size'] <= 2400 ){
            $configer =  array(
              'image_library'   => 'gd2',
              'source_image'    =>  $image_data['full_path'],
              'maintain_ratio'  =>  TRUE,
                    // 'width'           =>  2000,
                    // 'height'          =>  2500,
              'master_dim'          =>  'width',
              'quality'          =>  '50%',

            );

          }else{
            $configer =  array(
              'image_library'   => 'gd2',
              'source_image'    =>  $image_data['full_path'],
              'maintain_ratio'  =>  TRUE,
              'width'           =>  2000,
              'height'          =>  2500,
              'master_dim'          =>  'width',
              'quality'          =>  '50%',

            );

          }
          $this->image_lib->clear();
          $this->image_lib->initialize($configer);
          $this->image_lib->resize();
          if($image_data['file_name']){
            $image = $image_data['file_name'];
            $postdata['image'] = $image;
          }else{
            $image = '';
            $postdata['image'] = $image;
          }

        }

$postdata['id'] = $medical_id;
        $result = $this->Api_Model->updateData('company_user',$postdata,$medical_id);
        
            if($result)
            {
              $json['result'] = "true";
              $json['msg'] = "Successfully updated image ";
              
            }else{
              $json['result'] = "false" ;
              $json['msg'] = "Something went wrong. Please try later.";
            }

      }

      else{
                 $json['result']  = "false";
          $json['msg']     = "parameter required medical_id,optional(image)";
        }        
        
      echo json_encode($json);
    } 
//......................medical update profile................//

 public function update_medical_profile() 
 {  

    $medical_id                 =$this->input->post('medical_id');
    $name                       =$this->input->post('name');
    $state                      =$this->input->post('state');
    $state_id                   =$this->input->post('state_id');
    $city                       =$this->input->post('city');
    $city_id                    =$this->input->post('city_id ');
    $email                      =$this->input->post('email');
    $address                    =$this->input->post('address');
  


  if(isset($medical_id) ){


    $post_data = array();

    if(!empty($name))
    {
      $post_data['name'] = $name;
    }

    if(!empty($state))
    {
      $post_data['state'] = $state;
    }


    if(!empty($state_id))
    {
      $post_data['state_id'] = $state_id;
    }


    if(!empty($city))
    {
      $post_data['city'] = $city;
    }
    
    if(!empty($city_id))
    {
      $post_data['city_id'] = $city_id;
    }

    if(!empty($email))
    {
      $post_data['email'] = $email;
    }

    if(!empty($address))
    {
      $post_data['address'] = $address;
    }
    
    $post_data['updated_date'] = date('Y-m-d H:i:s');

      $result = $this->db->where('id',$medical_id)
      ->update('company_user',$post_data);
   

    $result_profile=$this->db->where('id',$medical_id)->get('company_user')->row();            

    if($result_profile){
     $json['result'] = 'true';
     $json['msg'] = 'Medical profile update Successfully';
     $json['data'] = $result_profile;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'No company_user data found';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required medical_id,optional(name,state,state_id,city,city_id,email,address)';
}

echo json_encode($json);

}

//........................21 feb 2023................................//

public function save_job_internship()
{
  $medical_id          =$this->input->post('medical_id');
  $job_internship_id   =$this->input->post('job_internship_id');
  $company_id          =$this->input->post('company_id');
  $type                =$this->input->post('type');
  $save                =$this->input->post('save');
  if(isset($medical_id) 
    && isset($job_internship_id)
    && isset($company_id) 
    && isset($type) 
    && isset($save) )
  {
    $data=array(
      'medical_id'         =>$medical_id,
      'job_internship_id'  =>$job_internship_id,
      'company_id'         =>$company_id,
      'type'               =>$type,
      'save'               =>$save,
      'save_date'          =>date('Y-m-d H:i:s'),

    );

    if($save==1){
      $this->db->insert('save_job_internship',$data);
      $insert_id=$this->db->insert_id();

      if($insert_id){
        $json['result']='true';
        $json['msg']=' successfully job internship saved';

      }else{
        $json['result']='false';
        $json['msg']='something went wrong';
      }
    }
    if($save==0){
      $result_saved=$this->db->select('*')
      ->where('medical_id',$medical_id)
      ->where('job_internship_id',$job_internship_id)
      ->where('company_id',$company_id)
      ->where('type',$type)
      ->get('save_job_internship')
      ->row();
      if($result_saved){
        $saved_id=$result_saved->id;
        $this->db->where('id',$saved_id)->delete('save_job_internship');

        $json['result']='true';
        $json['msg']='job internship not saved';
      }else{
        $json['result']='false';
        $json['msg']='something went wrong';
      }                  

    }
    
  }else{
    $json['result']='false';
    $json['msg']='parameter required medical_id,job_internship_id,company_id,save(1=>saved,0=>not saved),type( job (or) internship )';
  }
  echo json_encode($json);
}
//...........................21 feb 2023....................//

    public function get_saved_job_internship_list()
    {
      $medical_id=$this->input->post('medical_id');
    
    if(isset($medical_id) )
    {

      $result=$this->Api_Model->saved_job_internship($medical_id);

      if($result){

         $json['result'] = "true";
         $json['msg']    = "All My Saved job_internship data";
          $json['path']     = base_url()."assets/images/users/";
         $json['data']   = $result;

         }else{
      $json['result'] = "false";
      $json['msg']    = "No saved job_internship";
    }
  }
  else
  {
    $json['result'] = "false";
    $json['msg']    = "parameter required medical_id";
  }

  echo json_encode($json);
  }

  //................get company user list..................//
  public function get_company_user_list()
  {
    $type=$this->input->post('type');
    if(isset($type))
    {
      $wheredata = array('field'=>'*',
             'table'=>'company_user',
             'where'=>array('type' => $type),             
             'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllData($wheredata);
        if($result){
          $json['result']='true';
          $json['msg']='successfully user list';
          $json['data']=$result;

        }else{
          $json['result'] = 'false';
          $json['msg'] ='no user data available';
        }

    }else{
      $json['result']='false';
      $json['msg']='parameter required type( pharma (or) medical (or) chemist )';

    }
    echo json_encode($json);

  }

//.......................get Applicants list.....................//

    public function get_applicants_list()
  {
    $company_id         = $this->input->post('company_id');
    $job_internship_id  = $this->input->post('job_internship_id');
    
    if(isset($company_id) && isset($job_internship_id))
    {

      $result=$this->Api_Model->applicants_list($company_id,$job_internship_id);

      if($result){

         $json['result'] = "true";
         $json['msg']    = "All My Apply job_internship data";
         $json['data']   = $result;

         }else{
      $json['result'] = "false";
      $json['msg']    = "No appy job_internship";
    }
  }
  else
  {
    $json['result'] = "false";
    $json['msg']    = "parameter required company_id,job_internship_id";
  }

  echo json_encode($json);
}

//....................22 feb 2023............................//

public function applicants_apply_status()
{
  $apply_id     =$this->input->post('apply_id');
  $company_id     =$this->input->post('company_id');
  $apply_status =$this->input->post('apply_status');

  if(isset($apply_id) && isset($apply_status) && isset($company_id))
  {
    if(!empty($apply_status))
    {
      $post_data['apply_status'] = $apply_status;
    }
    if(!empty($company_id))
    {
      $post_data['company_id'] = $company_id;
    }



$fetch=$this->db->where('id',$apply_id)
                    ->where('company_id',$company_id)
                    ->get('apply_job_internship')
                    ->row();

if($fetch){
   $update=$this->db->where('id',$apply_id)
                    ->where('company_id',$company_id)
                    ->update('apply_job_internship',$post_data);
}


    if($fetch)
    {

     $json['result'] = 'true';
     $json['msg']    = 'Successfully updated apply_status.';

   }
   else
   {
     $json['result'] = 'false';
     $json['msg']    = 'something went wrong';
   }


 }else
 {
  $json['result'] = "false";
  $json['msg']    = "parameter required apply_id,company_id,apply_status( accepted (or) rejected (or) view )";
}

echo json_encode($json);

}


//..................nibba nibbbi.........................//
   public function update_image_new()
   {
    $user_id=$this->input->post('user_id');
    if(isset($user_id))
    {

     $config['upload_path'] = './assets/images/profile';
     $config['allowed_types'] = 'gif|jpg|png|jpeg';
     $this->load->library('upload', $config);


     if(!$this->upload->do_upload('image1'))
     {
  
      $post_data['id']=$user_id;

     }else{
   
      $this->load->library('image_lib');
   

      $image_data =   $this->upload->data();

      if($image_data['file_size'] <= 2400 ){
       $configer =  array(
        'image_library'   => 'gd2',
        'source_image'    =>  $image_data['full_path'],
        'maintain_ratio'  =>  TRUE,
              // 'width'           =>  2000,
              // 'height'          =>  2500,
        'master_dim'          =>  'width',
        'quality'          =>  '50%',

      );

     }else{
       $configer =  array(
        'image_library'   => 'gd2',
        'source_image'    =>  $image_data['full_path'],
        'maintain_ratio'  =>  TRUE,
        'width'           =>  2000,
        'height'          =>  2500,
        'master_dim'          =>  'width',
        'quality'          =>  '50%',

      );

     }
     $this->image_lib->clear();
     $this->image_lib->initialize($configer);
     $this->image_lib->resize();
     if($image_data['file_name']){
      $image = $image_data['file_name'];
      $post_data['image1'] = $image;
    }else{
      $image = '';
      $post_data['image1'] = $image;
    }

  }
  

  $result=$this->db->where('id',$user_id)->update('users_nibba',$post_data);
  if($result){
    $json['result']='true';
    $json['msg']='successfully update image1';
    $json['path']    = base_url()."assets/images/profile/";
    $json['data']=$this->db->select('id,image1')
    ->where('id',$user_id)
    ->get('users_nibba')
    ->row();
  }else{
    $json['result']='false';
    $json['msg']='something went wrong';
  }

}else{
  $json['result']='false';
  $json['msg']='parameter required user_id,image1';
}
echo json_encode($json);
}
//.................applicants status list..............................//

    public function get_applicants_apply_status_company()
  {
    $company_id=$this->input->post('company_id');
    $apply_status =$this->input->post('apply_status');
    
    if(isset($company_id) && isset($apply_status) )
    {

      $result=$this->Api_Model->applicants_apply_status_company($company_id,$apply_status);

      if($result){

         $json['result'] = "true";
         $json['msg']    = "All My Apply job_internship data";
         $json['data']   = $result;

         }else{
      $json['result'] = "false";
      $json['msg']    = "No appy job_internship";
    }
  }
  else
  {
    $json['result'] = "false";
    $json['msg']    = "parameter required company_id,apply_status( accepted (or) rejected (or) view (or) applied )";
  }

  echo json_encode($json);
}

//...........................help support...............................//

 public function add_help_support()
 {
    $company_user_id           =$this->input->post('company_user_id');
    $user_type                 =$this->input->post('user_type');
    $title                     =$this->input->post('title');
    $query                     =$this->input->post('query');
   
  
     if(isset($company_user_id)
     && isset($user_type) 
     && isset($title)
     && isset($query) 
   )
  {
    $data=array(
      'company_user_id'       =>$company_user_id,
      'user_type'                  =>$user_type,
      'title'                 =>$title,
      'query'                 =>$query,
      
      'created_date'          =>date('Y-m-d H:i:s'),
       'updated_date'         =>date('Y-m-d H:i:s'),
     );


            $this->db->insert('help_support',$data);
            $insert_id=$this->db->insert_id();
            if($insert_id){
              $json['result']='true';
              $json['msg']='successfully add help_support';
              $json['data']=$insert_id;

            }else{
              $json['result']='false';
              $json['msg']='something went wrong';
            }

  }else{
    $json['result']='false';
    $json['msg']='parameter required company_user_id,title,query,user_type( pharma (or) medical (or) chemist )';
  }
  echo json_encode($json);
 }

//......................chemist get profile......................//

   public function chemist_get_profile()
{

     $chemist_id = $this->input->post('chemist_id');
        
        if(isset($chemist_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'company_user',
             'where'=>array('id' => $chemist_id),             
             'order_by'=>'id desc'
           );
                     $result=$this->Api_Model->getAllData($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All  chemist Data show ";
          $json['path']     = base_url()."assets/images/users/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No chemist Data show";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required chemist_id";
        }        
        
      echo json_encode($json);
    } 
//.................update chemist profile...................//

     public function chemist_update_profile() 
 {  

    $chemist_id                 =$this->input->post('chemist_id');
    $name                       =$this->input->post('name');
    $state                      =$this->input->post('state');
    $state_id                   =$this->input->post('state_id');
    $city                       =$this->input->post('city');
    $city_id                    =$this->input->post('city_id ');
    $email                      =$this->input->post('email');
    $address                    =$this->input->post('address');
  


  if(isset($chemist_id) ){


    $post_data = array();

    if(!empty($name))
    {
      $post_data['name'] = $name;
    }

    if(!empty($state))
    {
      $post_data['state'] = $state;
    }


    if(!empty($state_id))
    {
      $post_data['state_id'] = $state_id;
    }


    if(!empty($city))
    {
      $post_data['city'] = $city;
    }
    
    if(!empty($city_id))
    {
      $post_data['city_id'] = $city_id;
    }

    if(!empty($email))
    {
      $post_data['email'] = $email;
    }

    if(!empty($address))
    {
      $post_data['address'] = $address;
    }
    
    $post_data['updated_date'] = date('Y-m-d H:i:s');

      $result = $this->db->where('id',$chemist_id)
      ->update('company_user',$post_data);
   

    $result_profile=$this->db->where('id',$chemist_id)->get('company_user')->row();            

    if($result_profile){
     $json['result'] = 'true';
     $json['msg'] = 'chemist profile update Successfully';
     $json['data'] = $result_profile;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'No company_user data found';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required chemist_id,optional(name,state,state_id,city,city_id,email,address)';
}

echo json_encode($json);

}


   
   //.................update pharma profile...................//

    public function pharma_update_profile() 
 {  

    $pharma_id                  =$this->input->post('pharma_id');
    $name                       =$this->input->post('name');
    $state                      =$this->input->post('state');
    $state_id                   =$this->input->post('state_id');
    $city                       =$this->input->post('city');
    $city_id                    =$this->input->post('city_id ');
    $email                      =$this->input->post('email');
    $address                    =$this->input->post('address');
  


  if(isset($pharma_id) ){


    $post_data = array();

    if(!empty($name))
    {
      $post_data['name'] = $name;
    }

    if(!empty($state))
    {
      $post_data['state'] = $state;
    }


    if(!empty($state_id))
    {
      $post_data['state_id'] = $state_id;
    }


    if(!empty($city))
    {
      $post_data['city'] = $city;
    }
    
    if(!empty($city_id))
    {
      $post_data['city_id'] = $city_id;
    }

    if(!empty($email))
    {
      $post_data['email'] = $email;
    }

    if(!empty($address))
    {
      $post_data['address'] = $address;
    }
    
    $post_data['updated_date'] = date('Y-m-d H:i:s');

      $result = $this->db->where('id',$pharma_id)
      ->update('company_user',$post_data);
   

    $result_profile=$this->db->where('id',$pharma_id)->get('company_user')->row();            

    if($result_profile){
     $json['result'] = 'true';
     $json['msg'] = 'pharma profile update Successfully';
     $json['data'] = $result_profile;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'No pharma_user data found';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required pharma_id,optional(name,state,state_id,city,city_id,email,address)';
}

echo json_encode($json);

}
//.....................24 feb 2024...google login...................//

  public function google_login_company()
{      

  extract($_POST);
  if (isset($email) && 
    isset($name) && 
    isset($fcm_id) && 
    isset($type) && 
    isset($google_id)
    ) 
  {

    $otp = rand(1000,9999); 
  
    $wheredata = array(    
    'email'  =>$email                         
    );
    $res= $this->Api_Model->singleRowdata($wheredata,'company_user');
    if ($res){
     
      
      $post_data = array(
                'google_id' => $google_id,
                'otp' => $otp,
                'verify_otp' => 0,
                'fcm_id' => $fcm_id,
             );
 
      $this->db->where("email",$email);
      $this->db->update("company_user",$post_data);   

      $data_result['result'] = 'true';
      $data_result['msg']    = 'Google Login successfully!';
      $data_result['data']   = $this->Api_Model->singleRowdata($wheredata,'company_user');
    }else{ 
       
      $data = array( 
        'email'  =>$email,
        'name'   =>$name, 
        'type'   =>$type, 
        'otp'   =>$otp, 
        'verify_otp'  =>1, 
        'google_id'=>$google_id,           
        'fcm_id' =>$fcm_id
      );
      $result    = $this->Api_Model->insertAllDataa('company_user',$data);
      
      
      $wheredata = array(
        'id' => $result
      );
      
      
      $res1 = $this->Api_Model->singleRowdata($wheredata,'company_user');
      if($result){
        $data_result['result'] = 'true';
        $data_result['msg']    = 'Login google successfully!';
        $data_result['data']   = $res1;
        
      }else{
        $data_result['result'] = 'false';
        $data_result['msg']    = 'Your record not insert!';
      }
    }
  }else{
    $data_result['result'] = 'false';
    $data_result['msg']    = 'parameter required email,name,fcm_id,google_id,type';
  }
  echo json_encode($data_result);

}

//....................pharma update image.........................//

   public function update_pharma_image()
    {
      $pharma_id=$this->input->post('pharma_id');
      if($pharma_id){

        $config['upload_path'] = './assets/images/users/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $this->load->library('upload', $config);


        if(!$this->upload->do_upload('image'))
        {
      
        }else{
          $this->load->library('image_lib');


          $image_data =   $this->upload->data();

          if($image_data['file_size'] <= 2400 ){
            $configer =  array(
              'image_library'   => 'gd2',
              'source_image'    =>  $image_data['full_path'],
              'maintain_ratio'  =>  TRUE,
                    // 'width'           =>  2000,
                    // 'height'          =>  2500,
              'master_dim'          =>  'width',
              'quality'          =>  '50%',

            );

          }else{
            $configer =  array(
              'image_library'   => 'gd2',
              'source_image'    =>  $image_data['full_path'],
              'maintain_ratio'  =>  TRUE,
              'width'           =>  2000,
              'height'          =>  2500,
              'master_dim'      =>  'width',
              'quality'         =>  '50%',

            );

          }
          $this->image_lib->clear();
          $this->image_lib->initialize($configer);
          $this->image_lib->resize();
          if($image_data['file_name']){
            $image = $image_data['file_name'];
            $postdata['image'] = $image;
          }else{
            $image = '';
            $postdata['image'] = $image;
          }

        }

$postdata['id'] = $pharma_id;
        $result = $this->Api_Model->updateData('company_user',$postdata,$pharma_id);
        
            if($result)
            {
              $json['result'] = "true";
              $json['msg'] = "Successfully updated image ";
              $json['path'] = base_url()."assets/images/users/";
              $json['data']=$this->db->select('id,image')
                                      ->where('id',$pharma_id)
                                      ->get('company_user')
                                      ->row();
              
              
            }else{
              $json['result'] = "false" ;
              $json['msg'] = "Something went wrong. Please try later.";
            }

      }

      else{
          $json['result']  = "false";
          $json['msg'] = "parameter required pharma_id,optional(image)";
        }        
        
      echo json_encode($json);
    } 

//....................chemist update image.........................//

   public function update_chemist_image()
    {
      $chemist_id=$this->input->post('chemist_id');
      if($chemist_id){

        $config['upload_path'] = './assets/images/users/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $this->load->library('upload', $config);


        if(!$this->upload->do_upload('image'))
        {
      
        }else{
          $this->load->library('image_lib');


          $image_data =   $this->upload->data();

          if($image_data['file_size'] <= 2400 ){
            $configer =  array(
              'image_library'   => 'gd2',
              'source_image'    =>  $image_data['full_path'],
              'maintain_ratio'  =>  TRUE,
                    // 'width'           =>  2000,
                    // 'height'          =>  2500,
              'master_dim'          =>  'width',
              'quality'          =>  '50%',

            );

          }else{
            $configer =  array(
              'image_library'   => 'gd2',
              'source_image'    =>  $image_data['full_path'],
              'maintain_ratio'  =>  TRUE,
              'width'           =>  2000,
              'height'          =>  2500,
              'master_dim'      =>  'width',
              'quality'         =>  '50%',

            );

          }
          $this->image_lib->clear();
          $this->image_lib->initialize($configer);
          $this->image_lib->resize();
          if($image_data['file_name']){
            $image = $image_data['file_name'];
            $postdata['image'] = $image;
          }else{
            $image = '';
            $postdata['image'] = $image;
          }

        }

$postdata['id'] = $chemist_id;
        $result = $this->Api_Model->updateData('company_user',$postdata,$chemist_id);
        
            if($result)
            {
              $json['result'] = "true";
              $json['msg'] = "Successfully updated image ";
              $json['path'] = base_url()."assets/images/users/";
              $json['data']=$this->db->select('id,image')
                                      ->where('id',$chemist_id)
                                      ->get('company_user')
                                      ->row();
              
              
            }else{
              $json['result'] = "false" ;
              $json['msg'] = "Something went wrong. Please try later.";
            }

      }

      else{
          $json['result']  = "false";
          $json['msg'] = "parameter required chemist_id,optional(image)";
        }        
        
      echo json_encode($json);
    } 


   //................sachin end...............................//


  //...................................................//



}


?>
