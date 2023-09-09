<?php

if(!defined('BASEPATH')) exit ('No direct script access allowed');




class Api extends MY_Controller

{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Api_Model');
   /* $this->load->helper('custom_helper');*/
    $this->load->library('form_validation');
    $this->load->library('email');
    $this->load->helper('url');
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
    $mobile              = $this->input->post('mobile');
    
    if(isset($name) &&  
      isset($email) &&  
      isset($password) &&  
      isset($type) &&  
      isset($mobile) &&  
      isset($fcm_id))
    {
      if(!$this->Api_Model->is_record_exist('user','email',$email))
      {
        
     
          
            $otp = rand(100000,999999);
          
             $postdata = array(
          
            'name' => $name,
            'email' => $email,
            'otp' => $otp,
            'password' => md5($password),
            'plain_password' => $password,
            'mobile' => $mobile,
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
      $json['msg']     = "parameter required name,email,password,fcm_id,mobile,type(user (or) admin)";      
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
         
        
        $otp = rand(100000,999999);
      
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
           $otp = rand(100000,999999);
         
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

//...........................02 march 2023........................//

  public function signup_login()
    {
        $mobile = $this->input->post('mobile');
        $fcm_id = $this->input->post('fcm_id');

        if(isset($mobile) && isset($fcm_id))
        {

            $otp = rand(100000,999999);

            $this->db->select("id");
            $this->db->from("user");
            $this->db->where("mobile",$mobile);
            $query= $this->db->get();


            if($query->num_rows() == 0)
            {
                
                if(!empty($fcm_id))
                {
                    $fcm_id = $fcm_id;
                }
                else
                {
                    $fcm_id = "";
                }
               
                $post_data = array(
                    'mobile' => $mobile,
                    'otp' => $otp,
                    'verify_otp' => 0,
                    'fcm_id' => $fcm_id,
                    'created_date' => date('Y-m-d H:i:s'),
                    'updated_date' => date('Y-m-d H:i:s')
                 );

                $inserdata = $this->db->insert("user",$post_data);
                $insert_id = $this->db->insert_id();


                if($insert_id)
                {

                   $wheredata = array('field'=>'id,mobile,otp,verify_otp,created_date,updated_date',

                    'table'=>'user',

                    'where'=>array('mobile'=>$mobile),

                   );

                   $row = $this->Api_Model->getAllDataRow($wheredata);
                   $json['result']  = "true";
                   $json['msg']     = 'Signup successfully ,Please verify otp.';
                   $json['data']    = $row;

               }
               else
               {
                 $json['result'] = "false";
                 $json['msg']    = 'something went wronmg';
               }

            }
            else
            {

             if(!empty($fcm_id))
                {
                    $fcm_id = $fcm_id;
                }
                else
                {
                    $fcm_id = "";
                }
                
                
                 $post_data = array(
                    'otp' => $otp,
                    // 'verify_otp' => 0,
                    'fcm_id' => $fcm_id,
                    'updated_date' => date('Y-m-d H:i:s'),
                  );
                       $this->db->where("mobile",$mobile);
                $rr =  $this->db->update("user",$post_data);

              $this->db->where("mobile",$mobile);
              $this->db->select("id");
              $this->db->from("user");
              $vv = $this->db->get()->row();

                $wheredata = array('field'=>'id,mobile,otp,verify_otp,created_date,updated_date',

                    'table'=>'user',

                    'where'=>array('mobile'=>$mobile),

                   );

                   $row = $this->Api_Model->getAllDataRow($wheredata);


              $json['result']  = "true";
              $json['msg']     = 'Login successfully, Please verify otp.';
              $json['data']    = $row;

            }
        }
        else
        {
            $json['result'] = 'false';
            $json['msg']    = 'parameter required mobile,fcm_id';
        }

        echo json_encode($json);
    }


//...................verify otp......................................//
   
    public function verify_otp()
  {
    $user_id    = $this->input->post('user_id');
    $otp        = $this->input->post('otp');

    if(isset($user_id) && isset($otp))
    {
      $wheredata = array('field'=>'id',

       'table'=>'user',

       'where'=>array('id'=>$user_id,'otp'=>$otp),

      );



       $result = $this->Api_Model->getAllDataRow($wheredata);


      if($result)
       {

        $verify_otp = array(
         'verify_otp' => 1,
        //  'form_status' => 1
         );


       $this->db->where("id",$user_id);
       $this->db->update("user",$verify_otp);
       $where = array('field'=>'*',

       'table'=>'user',

       'where'=>array('id'=>$user_id),

      );


       $json['result'] = "true";
       $json['msg']    = "Otp verify Successfully.";
       $json['path'] = base_url()."assets/images/users/";
       $json['data']   = $this->Api_Model->getAllDataRow($where);


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

//...................resend otp.................................//

   public function resend_otp()
    {
        $user_id = $this->input->post('user_id');

        if(isset($user_id))
        {
            $wheredata1 = array('field'=>['id','mobile'],

                     'table'=>'user',

                     'where'=>array('id'=>$user_id)

            );

          $is_exist = $this->Api_Model->getAllDataRow($wheredata1);

          if($is_exist)
          {
                 $otp = rand(100000,999999);
            $post_data = array(
                'otp'=>$otp,
                );

            $this->db->where('id',$user_id);
            $result = $this->db->update('user',$post_data);

            if($result)
            {
                  $wheredata = array('field'=>'id,otp,mobile,verify_otp',

                    'table'=>'user',

                    'where'=>array('id'=>$user_id),

                   );

                   $row = $this->Api_Model->getAllDataRow($wheredata);

                 $json['result'] = "true";
                 $json['msg']    = "Resend successfully";
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

//...................... get profile......................//

   public function get_profile()
   {

     $user_id = $this->input->post('user_id');
        
        if(isset($user_id))
        {
            $wheredata = array('field'=>'*',
             'table'=>'user',
             'where'=>array('id' => $user_id),             
            //  'order_by'=>'id desc'
           );
        $result=$this->Api_Model->getAllDataRow($wheredata);

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/images/users/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No user found";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required user_id";
        }        
        
      echo json_encode($json);
    } 
 //.................update profile...................//

     public function update_profile() 
    {  

    $user_id                 =$this->input->post('user_id');
    $name                    =$this->input->post('name');
    $email                  =$this->input->post('email');
    $mobile                  =$this->input->post('mobile');
    $city                  =$this->input->post('city');
    $gender                  =$this->input->post('gender');
  


  if(isset($user_id) ){


    $post_data = array();

    if(!empty($name))
    {
      $post_data['name'] = $name;
    }

    if(!empty($mobile))
    {
      $post_data['mobile'] = $mobile;
    }
    if(!empty($email))
    {
      $post_data['email'] = $email;
    }
    if(!empty($city))
    {
      $post_data['city'] = $city;
    }
     if(!empty($gender))
    {
      $post_data['gender'] = $gender;
    }
    
    $post_data['updated_date'] = date('Y-m-d H:i:s');

      $result = $this->db->where('id',$user_id)
                         ->update('user',$post_data);
   

    $result_profile=$this->db->where('id',$user_id)->get('user')->row();            

    if($result_profile){
     $json['result'] = 'true';
     $json['msg'] = 'user profile update Successfully';
     $json['path'] = base_url()."assets/images/users/";
     $json['data'] = $result_profile;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'No user data found';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required user_id,optional(name,email,mobile,city,gender)';
}

echo json_encode($json);

}

//....................update image.........................//

//   public function update_image()
//     {
//       $user_id=$this->input->post('user_id');
//       if($user_id){

//         $config['upload_path'] = './assets/images/users/';
//         $config['allowed_types'] = 'gif|jpg|png|jpeg';
//         $this->load->library('upload', $config);


//         if(!$this->upload->do_upload('image'))
//         {
//           $postdata['image']='';
      
//         }else{
//           $this->load->library('image_lib');


//           $image_data =   $this->upload->data();

//           if($image_data['file_size'] <= 2400 ){
//             $configer =  array(
//               'image_library'   => 'gd2',
//               'source_image'    =>  $image_data['full_path'],
//               'maintain_ratio'  =>  TRUE,
//                     // 'width'           =>  2000,
//                     // 'height'          =>  2500,
//               'master_dim'          =>  'width',
//               'quality'          =>  '50%',

//             );

//           }else{
//             $configer =  array(
//               'image_library'   => 'gd2',
//               'source_image'    =>  $image_data['full_path'],
//               'maintain_ratio'  =>  TRUE,
//               'width'           =>  2000,
//               'height'          =>  2500,
//               'master_dim'      =>  'width',
//               'quality'         =>  '50%',

//             );

//           }
//           $this->image_lib->clear();
//           $this->image_lib->initialize($configer);
//           $this->image_lib->resize();
//           if($image_data['file_name']){
//             $image = $image_data['file_name'];
//             $postdata['image'] = $image;
//           }else{
//             $image = '';
//             $postdata['image'] = $image;
//           }

//         }


//         $result = $this->Api_Model->updateData('user',$postdata,$user_id);
//           $new_result=$this->db->select('id,image')
//                                 ->where('id',$user_id)
//                                 ->get('user')
//                                 ->row();
//             if($new_result)
//             {
//               $json['result'] = "true";
//               $json['msg'] = "Successfully updated image ";
//               $json['path'] = base_url()."assets/images/users/";
//               $json['data']=$new_result;
              
              
//             }else{
//               $json['result'] = "false" ;
//               $json['msg'] = "user not found. Please try later.";
//             }

//       }

//       else{
//           $json['result']  = "false";
//           $json['msg'] = "parameter required user_id,image";
//         }        
        
//       echo json_encode($json);
//     } 

//..................update image..........................//

    public function update_image()
{
$user_id        = $this->input->post('user_id');

if(isset($user_id))
{
$post_data = array();

if(!empty($_FILES['image']['name']))
          {
            $_FILES['file']['name']     = $_FILES['image']['name'];
            $_FILES['file']['type']     = $_FILES['image']['type'];
            $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
            $_FILES['file']['error']     = $_FILES['image']['error'];
            $_FILES['file']['size']     =  $_FILES['image']['size'];
            // File upload configuration
            $uploadPath = 'assets/images/users/';
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            // Load and initialize upload library
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            // Upload file to server
            if($this->upload->do_upload('file')){
              // Uploaded file data
              $fileData = $this->upload->data();
              $post_data['image'] = $fileData['file_name'];
            }
          }


$update = $this->db->where('id',$user_id)->update('user',$post_data);
if($update)
{
$json['result'] = "true";
$json['msg']    = "profile updated successfully";
$json['path'] = base_url()."assets/images/users/";
$json['data']=$this->db->select('id,image')->where('id',$user_id)->get('user')->row();
}else{
$json['result']  = "false";
$json['msg']     = "Something went wrong.";
}

}
else
{
$json['result'] = 'false';
$json['msg']    = 'parameter required user_id,optional(name,last_name,email,mobile,image)';
}
echo json_encode($json);
}



//...................about us...............................//

  public function get_about_us()
  {
     
    $this->db->select("*");
    $this->db->from("about_us");
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
  
//..................get feq...........................//

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
  
//...................add_feedback.........................//
 
  public function add_feedback() 
    {  

    $user_id                 =$this->input->post('user_id');
    $phone_no                =$this->input->post('phone_no');
    $title                   =$this->input->post('title');
    $question                =$this->input->post('question');



  if(isset($user_id) ){


    $post_data = array();
     if(!empty($user_id))
    {
      $post_data['user_id'] = $user_id;
    }

    if(!empty($phone_no))
    {
      $post_data['phone_no'] = $phone_no;
    }

    if(!empty($title))
    {
      $post_data['title'] = $title;
    }
    if(!empty($question))
    {
      $post_data['question'] = $question;
    }
     
    
    $post_data['date'] = date('Y-m-d H:i:s');

       $this->db->insert('feedback',$post_data);
       $insert_id=$this->db->insert_id();
   

    $result=$this->db->where('id',$insert_id)->get('feedback')->row();            

    if($result){
     $json['result'] = 'true';
     $json['msg'] = 'user Successfully give feedback';
     $json['data'] = $result;
   }

   else{
     $json['result'] = 'false';
     $json['msg'] = 'no feedback added';

   }     

 }

 else{

  $json['result'] = 'false';
  $json['msg'] = 'parameter required user_id,optional(phone_no,title,question)';
}

echo json_encode($json);

}


 //............................get privacy policy...............//
    public function get_privacy_policy()
  {
    $wheredata = array('field'=>'*',

       'table'=>'privacy',

       'where'=>array(''),
       
       'order_by'=>'id desc'

       );

$result = $this->db->get('privacy')->row();


    // $result = $this->Api_Model->getAllData($wheredata);
    
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

  //............................get terms conditon...............//
    public function get_terms_condtion()
  {
    $wheredata = array('field'=>'*',

       'table'=>'terms_conditions',

       'where'=>array(''),
       
       'order_by'=>'id desc'

       );

$result = $this->db->get('terms_conditions')->row();

    // $result = $this->Api_Model->getAllData($wheredata);
    
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
  
  //....................get Qr scan ..09 march..................//
  
  
  public function get_qr_unique_old()
 {

    $code_id = $this->input->post('unique_id');
    // $user_id = $this->input->post('user_id');

 $value=strrev(($code_id));

 $str2 = substr($value, 4, -6);
 $unique_id=$str2 ;
        
        if(isset($unique_id)  )
        {
            $wheredata = array('field'=>'*',
             'table'=>'qr_code_genrater',
             'where'=>array('unique_id' => $unique_id),             
             
           );


        $result=$this->Api_Model->getAllDataRow($wheredata);
        
        if($result)
        {

         $scan_count=$result->scan_count;
         $data_update['scan_count']=($scan_count+1);
        $update=$this->db->where('unique_id',$unique_id)
                         ->update('qr_code_genrater',$data_update);
                      
                         
        }                         

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required unique_id";
        }        
        
      echo json_encode($json);
    } 
    
    
//.......................add user unit.....................//
    
    public function add_user_unit_old()
    {
      $user_id=$this->input->post('user_id');
      $unit=$this->input->post('unit');
      $unique_id=$this->input->post('unique_id');
      if(isset($user_id) && isset($unit))
      {
        $wheredata = array('field'=>'id,total_unit',
             'table'=>'user',
             'where'=>array('id' => $user_id),             
             
           );
        if(!empty($unit)){

        }else{
          $unit=0;
        }


        $result=$this->Api_Model->getAllDataRow($wheredata);
        if($result){

         $total_unit=$result->total_unit;
         $data_update['total_unit']=($total_unit+$unit);
        $update=$this->db->where('id',$user_id)
                         ->update('user',$data_update);
        }
        $update_result=$this->Api_Model->getAllDataRow($wheredata);
        if($update_result){
          $json['result']='true';
          $json['msg']='successfully update unit';
          $json['data']=$update_result;

        }else{
          $json['result']='false';
        $json['msg']='something went wrong';

        }

      }else{
        $json['result']='false';
        $json['msg']='parameter required user_id,unit,unique_id';
      }
      echo json_encode($json);
    }
    
    public function get_scan_history()
    {

      $user_id = $this->input->post('user_id');

        if(isset($user_id))
         {
            
        // $result=$this->Api_Model->get_scan_history($user_id);
        $result=$this->Api_Model->get_scan_history_all($user_id);
       
     
        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['image_path']    = base_url()."assets/qr_image/";
          $json['qr_code']    = base_url();
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No Scan any QR Code";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required user_id";
        }        
        
      echo json_encode($json);
    }
    
    
    
    
     public function get_new_unique()
 {

    $unique_id = $this->input->post('unique_id');
    $user_id = $this->input->post('user_id');
    $address=$this->input->post('address');

 
        
        if(isset($unique_id) && isset($user_id) )
        {
            $wheredata = array('field'=>'*',
             'table'=>'qr_code_genrater',
             'where'=>array('code' => $unique_id),             
             
           );


        $result=$this->Api_Model->getAllDataRow($wheredata);
        
        if($result)
        {

         $scan_count=$result->scan_count;
         $data_update['scan_count']=($scan_count+1);
         $update=$this->db->where('code',$unique_id)
                         ->update('qr_code_genrater',$data_update);
                         
        
        if(!empty($address))
            {
              $address = $address;
            }else{
              $address='';
            }                         
                                 
                
         $data=array('user_id'=>$user_id,
                     'unique_id'=>$unique_id,
                     'address'=>$address,
                     'date'=>date('Y-m-d H:i:s')
                   ); 

          $this->db->insert('scan_history',$data);                 
                         
        }                         

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required unique_id,user_id,optional('address')";
        }        
        
      echo json_encode($json);
    } 
    
  //..............................11 march......................//
  public function get_qr_unique()
 {

    $unique_id = $this->input->post('unique_id');
    
        
        if(isset($unique_id)  )
        {
            $wheredata = array('field'=>'*',
             'table'=>'qr_code_genrater',
             'where'=>array('code' => $unique_id),             
             
           );


        $result=$this->Api_Model->getAllDataRow($wheredata);
        
        if($result)
        {

         $scan_count=$result->scan_count;
         $data_update['scan_count']=($scan_count+1);
        $update=$this->db->where('unique_id',$unique_id)
                         ->update('qr_code_genrater',$data_update);
                      
                         
        }                         

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "parameter required unique_id";
        }        
        
      echo json_encode($json);
    } 
    
     
 //.........................22 marach 2023...........................//

    
  
  //.......................24 May 2023................................//
  
  
    public function get_new_unique_scan_old()
    {

      $unique_id = $this->input->post('unique_id');
      $user_id = $this->input->post('user_id');
      $address=$this->input->post('address');

      
      
      if(isset($unique_id) && isset($user_id) )
      {
        $wheredata = array('field'=>'*',
         'table'=>'qr_code_genrater',
         'where'=>array('code' => $unique_id),             
         
       );


        $result=$this->Api_Model->getAllDataRow($wheredata);
    
        $scan_status=$result->scan_status;

        if($scan_status==0)
        {
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('Y-m-d H:i:s'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('Y-m-d H:i:s'),
           'type'=>'Not Valid',
         ); 

          $this->db->insert('scan_history',$data);
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
      }
      
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required unique_id,user_id,optional('address')";
    }        
    
    echo json_encode($json);
  }  
  
//..............................01 july 2023...........................//
 public function get_new_unique_scan_4july()
    {

      $unique_id = $this->input->post('unique_id');
      $user_id = $this->input->post('user_id');
      $address=$this->input->post('address');

      
      if(isset($unique_id) && isset($user_id) )
      {
        $unique_id=$unique_id;
        $type=$unique_id[0];
        if($type=='O')
        {
           $wheredata = array('field'=>'*',
         'table'=>'one_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        
        $scan_status=$result->scan_status;

        if($scan_status==0)
        {
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('one_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
         
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 

          $this->db->insert('scan_history',$data);
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
        
        echo json_encode($json);
        exit;
      }
        
          
        }
        if($type=='T')
        {
            $wheredata = array('field'=>'*',
         'table'=>'two_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        
        $scan_status=$result->scan_status;
         $scan_count=$result->scan_count;

        if($scan_count<=1)
        {
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('two_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 

          $this->db->insert('scan_history',$data);
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
      }
        
        
        
        
        }
        if($type=='R')
        {
            $wheredata = array('field'=>'*',
         'table'=>'reset_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        
          $scan_status=$result->scan_status;

        if($scan_status==0)
        {
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('reset_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 

          $this->db->insert('scan_history',$data);
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
      }
        
        
        
        }
        
      
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required unique_id,user_id,optional('address')";
    }        
    
    echo json_encode($json);
  } 
//...................06 july 2023.....................................// 
public function get_new_unique_scan_left_left()
    {

      $unique_id = $this->input->post('unique_id');
      $user_id = $this->input->post('user_id');
      $address=$this->input->post('address');

      
      if(isset($unique_id) && isset($user_id) )
      {
        $unique_id=$unique_id;
        $type=$unique_id[0];
        
        $fetch_scan=$this->db->where('unique_id',$unique_id)
                              ->where('type','Valid')
                             ->get('scan_history')
                             ->row();
         if($fetch_scan){
        if($type=='O')
        {
           $wheredata = array('field'=>'*',
         'table'=>'one_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);

      
        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

          
        }
        elseif($type=='T')
        {
            $wheredata = array('field'=>'*',
         'table'=>'two_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        
                               

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      
        
        }
        elseif($type=='R')
        {
            $wheredata = array('field'=>'*',
         'table'=>'reset_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        
                              

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

       
        }
        
            elseif($type=='I')
        {
            $wheredata = array('field'=>'*',
         'table'=>'infinite_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result_infinte=$this->Api_Model->getAllDataRow($wheredata);
        if($result_infinte){
            
        

        $result=$this->Api_Model->getAllDataRow($wheredata);
        

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

        }else{
            $json['result']  = "false";
          $json['msg']     = "QR code Time boundation";
        }
       
       
        }
        else{
            $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
        }else{
            $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
      
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required unique_id,user_id,optional('address')";
    }        
    
    echo json_encode($json);
  } 
  
//............................14 july 2023..............................//
public function get_new_unique_scan_28()
    {

      $unique_id = $this->input->post('unique_id');
      $user_id = $this->input->post('user_id');
      $address=$this->input->post('address');

      
      if(isset($unique_id) && isset($user_id) )
      {
        $unique_id=$unique_id;
        $type=$unique_id[0];
//............................one time scan............................//        
        if($type=='O')
        {
           $wheredata = array('field'=>'*',
         'table'=>'one_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        if($result){
        
        $scan_status=$result->scan_status;
        
        
        $whereData = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Valid') );
         
          $result_scan=$this->Api_Model->getAllDataRow($whereData);
          if($result_scan){
             $result_old=$this->Api_Model->getAllDataRow($wheredata);
              
                if($result_old)
                    {
                  $json['result']  = "true";
                  $json['msg']     = "All user Data show ";
                  $json['path']    = base_url()."assets/qr_image/";
                  $json['data']    = $result_old;
                }
                else
                {
                  $json['result']  = "false";
                  $json['msg']     = "No QR found";
                }
          }

        elseif($scan_status==0)
        {
           
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('one_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
         
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 
         
          $whereDatascan = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Not Valid') );
         $fetch_scan_history=$this->Api_Model->getAllDataRow($whereDatascan);
         if($fetch_scan_history){
             $this->db->where('unique_id',$unique_id)
                      ->where('user_id',$user_id)
                      ->update('scan_history',$data);
         }else{
             $this->db->insert('scan_history',$data); 
         }

         
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
        
        echo json_encode($json);
        exit;
      }
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
        
          
        }
//............................Two time scan............................//         
        elseif($type=='T')
        {
            $wheredata = array('field'=>'*',
         'table'=>'two_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        if($result){
        
        $scan_status=$result->scan_status;
         $scan_count=$result->scan_count;
         
         
         $whereData = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Valid') );
         
          $result_scan=$this->Api_Model->getAllDataRow($whereData);
          if($result_scan){
             $result_old=$this->Api_Model->getAllDataRow($wheredata);
              
                if($result_old)
                    {
                  $json['result']  = "true";
                  $json['msg']     = "All user Data show ";
                  $json['path']    = base_url()."assets/qr_image/";
                  $json['data']    = $result_old;
                }
                else
                {
                  $json['result']  = "false";
                  $json['msg']     = "No QR found";
                }
          }
        elseif($scan_count<=1)
        {
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('two_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 
         
             $whereDatascan = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Not Valid') );
         $fetch_scan_history=$this->Api_Model->getAllDataRow($whereDatascan);
         if($fetch_scan_history){
             $this->db->where('unique_id',$unique_id)
                      ->where('user_id',$user_id)
                      ->update('scan_history',$data);
         }else{
             $this->db->insert('scan_history',$data); 
         }

         
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
      }
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
        
        }
//............................Reset time scan............................//         
        elseif($type=='R')
        {
            $wheredata = array('field'=>'*',
         'table'=>'reset_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        
        if($result){
           
          $scan_status=$result->scan_status;
        
          
          $whereData = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Valid') );
         
          $result_scan=$this->Api_Model->getAllDataRow($whereData);
          if($result_scan){
             $result_old=$this->Api_Model->getAllDataRow($wheredata);
              
                if($result_old)
                    {
                  $json['result']  = "true";
                  $json['msg']     = "All user Data show ";
                  $json['path']    = base_url()."assets/qr_image/";
                  $json['data']    = $result_old;
                }
                else
                {
                  $json['result']  = "false";
                  $json['msg']     = "No QR found";
                }
          }
        elseif($scan_status==0)
        {
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('reset_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 

          $this->db->insert('scan_history',$data);
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
      }
        
        
        
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
      }
//............................Infinite time scan............................// 



elseif($type=='I')
        {
            $wheredata = array('field'=>'*',
         'table'=>'infinite_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result_infinte=$this->Api_Model->getAllDataRow($wheredata);
        if($result_infinte){
        
         if($result_infinte){
            
        $code_start=$result_infinte->code_start;
        $code_end=$result_infinte->code_end;
        
        $today_date=date('d-m-Y H:i');
        
        $whereDatavalid = array('field'=>'*',
         'table'=>'infinite_qr_code_genrater',
         'where'=>array('code' => $unique_id,
                        'code_start <=' => $today_date,
                        'code_end >=' => $today_date
                    
                    ) );

        $result_valid=$this->Api_Model->getAllDataRow($whereDatavalid);
         }
        
        
        
         $whereData = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Valid') );
         
          $result_scan=$this->Api_Model->getAllDataRow($whereData);
          if($result_scan){
             $result_old=$this->Api_Model->getAllDataRow($wheredata);
              
                if($result_old)
                    {
                  $json['result']  = "true";
                  $json['msg']     = "All user Data show ";
                  $json['path']    = base_url()."assets/qr_image/";
                  $json['data']    = $result_old;
                }
                else
                {
                  $json['result']  = "false";
                  $json['msg']     = "No QR found";
                }
          }
        elseif($result_valid)
        {
          
          if($result_valid)
          {

           $scan_count=$result_valid->scan_count;
           $data_update['scan_count']=($scan_count+1);
           $update=$this->db->where('code',$unique_id)
           ->update('infinite_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 
         
             $whereDatascan = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Not Valid') );
         $fetch_scan_history=$this->Api_Model->getAllDataRow($whereDatascan);
         if($fetch_scan_history){
             $this->db->where('unique_id',$unique_id)
                      ->where('user_id',$user_id)
                      ->update('scan_history',$data);
         }else{
             $this->db->insert('scan_history',$data); 
         }

        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
      }
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
        
        }
//...................................................//
else{
     $json['result']  = "false";
          $json['msg']     = "No QR found";
}
//......................................................//
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required unique_id,user_id,optional('address')";
    }        
    
    echo json_encode($json);
  } 
//.................................14 july 2023.....................................//
public function add_user_unit()
    {
      $user_id=$this->input->post('user_id');
      $unit=$this->input->post('unit');
      $unique_id=$this->input->post('unique_id');
      if(isset($user_id) && isset($unit)  && isset($unique_id) )
      {
        $wheredata = array('field'=>'id,total_unit,scan_total',
             'table'=>'user',
             'where'=>array('id' => $user_id),             
             
           );
        if(!empty($unit)){

        }else{
          $unit=0;
        }
        
       

        $result=$this->Api_Model->getAllDataRow($wheredata);
        if($result){

         $total_unit=$result->total_unit;
         $scan_total=$result->scan_total;
         $data_update['total_unit']=($total_unit+$unit);
         $data_update['scan_total']=($scan_total+1);
         
         
    $fetch_user_unit=$this->db->where('user_id',$user_id)
                                  ->where('unique_id',$unique_id)
                                  ->get('user_unit')
                                  ->row();
    if($fetch_user_unit){
            
        }else{
             $insert=array('user_id'=>$user_id,
                       'unit'=>$unit,
                       'unique_id'=>$unique_id,
                       'date'=>date('d-m-Y H:i')
                       );
        $this->db->insert('user_unit',$insert);
         $update=$this->db->where('id',$user_id)
                         ->update('user',$data_update);
        } 
         
       
        }
        $update_result=$this->Api_Model->getAllDataRow($wheredata);
        if($update_result){
          $json['result']='true';
          $json['msg']='successfully update unit';
          $json['data']=$update_result;

        }else{
          $json['result']='false';
        $json['msg']='something went wrong';

        }

      }else{
        $json['result']='false';
        $json['msg']='parameter required user_id,unit,unique_id';
      }
      echo json_encode($json);
    }
//.....................pdf testing..................................//
 public function downloadImages()
    {
        // Get the image URLs or file paths from your data source
        $imageUrls = array(
            'https://logicaltest.website/Anju/QRSP/assets/gallary/649e675ad6290.jpg',
            'https://logicaltest.website/Anju/QRSP/assets/gallary/649e675ad48fc.jpg',
            // Add more image URLs or file paths as needed
        );

        // Create a ZIP archive
        $zip = new ZipArchive();
        $zipName = 'images.zip';
        if ($zip->open($zipName, ZipArchive::CREATE) === TRUE) {

            // Download and add each image to the ZIP archive
            foreach ($imageUrls as $imageUrl) {
                $imageContent = file_get_contents($imageUrl);
                $imageName = basename($imageUrl);
                $zip->addFromString($imageName, $imageContent);
            }

            $zip->close();

            // Set appropriate headers for the ZIP file download
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipName . '"');
            header('Content-Length: ' . filesize($zipName));

            // Send the ZIP file to the browser
            readfile($zipName);

            // Delete the temporary ZIP file
            unlink($zipName);
        } else {
            // ZIP creation failed
            echo 'Failed to create ZIP archive.';
        }
    }
//.............................sms gateway..........................//
public function get_new_unique_scan_left()
    {

      $unique_id = $this->input->post('unique_id');
      $user_id = $this->input->post('user_id');
      $address=$this->input->post('address');

      
      if(isset($unique_id) && isset($user_id) )
      {
        $unique_id=$unique_id;
        $type=$unique_id[0];
        
       
            
        if($type=='O')
        {
           $wheredata = array('field'=>'*',
         'table'=>'one_qr_code_genrater',
         'where'=>array('code' => $unique_id ,'scan_status'=>'0') );

        $result=$this->Api_Model->getAllDataRow($wheredata);

      
        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

          
        }
        elseif($type=='T')
        {
            $wheredata = array('field'=>'*',
         'table'=>'two_qr_code_genrater',
         'where'=>array('code' => $unique_id , 'scan_count <=' =>'1') );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        
                               

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      
        
        }
        elseif($type=='R')
        {
            $wheredata = array('field'=>'*',
         'table'=>'reset_qr_code_genrater',
         'where'=>array('code' => $unique_id , 'scan_status'=>'0') );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        
                              

        if($result)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

       
        }
        
          elseif($type=='I')
        {
            $wheredata = array('field'=>'*',
         'table'=>'infinite_qr_code_genrater',
         'where'=>array('code' => $unique_id ) );

           $result_infinte=$this->Api_Model->getAllDataRow($wheredata);
        
         if($result_infinte){
            
        $code_start=$result_infinte->code_start;
        $code_end=$result_infinte->code_end;
        
        $today_date=date('d-m-Y H:i');
        
        $whereDatavalid = array('field'=>'*',
         'table'=>'infinite_qr_code_genrater',
         'where'=>array('code' => $unique_id,
                        'code_start <=' => $today_date,
                        'code_end >=' => $today_date
                    
                    ) );

        $result_valid=$this->Api_Model->getAllDataRow($whereDatavalid);
        

        if($result_valid)
        {
          $json['result']  = "true";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_valid;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

        }else{
            $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
       
       
        }
        else{
            $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
       
      
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required unique_id,user_id,optional('address')";
    }        
    
    echo json_encode($json);
  }
//..........................28 july ..................//
public function get_new_unique_scan()
    {

      $unique_id = $this->input->post('unique_id');
      $user_id = $this->input->post('user_id');
      $address=$this->input->post('address');

      
      if(isset($unique_id) && isset($user_id) )
      {
        $unique_id=$unique_id;
        $type=$unique_id[0];
//............................one time scan............................//        
        if($type=='O')
        {

           $wheredata = array('field'=>'*',
         'table'=>'one_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        if($result){

        
        $scan_status=$result->scan_status;
        
        
        $whereData = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Valid') );
         
          $result_scan=$this->Api_Model->getAllDataRow($whereData);
          if($result_scan){

             $result_old=$this->Api_Model->getAllDataRow($wheredata);
              
                if($result_old)
                    {
                  $json['result']  = "true";
                  $json['scan']  = "2";
                  $json['msg']     = "You have already verified this ! ";
                  $json['path']    = base_url()."assets/qr_image/";
                  $json['data']    = $result_old;
                }
                else
                {
                  $json['result']  = "false";
                  $json['msg']     = "No QR found";
                }
          }

        elseif($scan_status==0)
        {
           
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('one_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
           $json['scan']  = "1";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
         
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 
         
          $whereDatascan = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Not Valid') );
         $fetch_scan_history=$this->Api_Model->getAllDataRow($whereDatascan);
         if($fetch_scan_history){
             $this->db->where('unique_id',$unique_id)
                      ->where('user_id',$user_id)
                      ->update('scan_history',$data);
         }else{
             $this->db->insert('scan_history',$data); 
         }

         
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
        
        echo json_encode($json);
        exit;
      }
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
        
          
        }
//............................Two time scan............................//         
        elseif($type=='T')
        {
            $wheredata = array('field'=>'*',
         'table'=>'two_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        if($result){
        
        $scan_status=$result->scan_status;
         $scan_count=$result->scan_count;
         
         
         $whereData = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Valid') );
         
          $result_scan=$this->Api_Model->getAllDataRow($whereData);
          if($result_scan){
             $result_old=$this->Api_Model->getAllDataRow($wheredata);
              
                if($result_old)
                    {
                 $json['result']  = "true";
                  $json['scan']  = "2";
                  $json['msg']     = "You have already verified this !";
                  $json['path']    = base_url()."assets/qr_image/";
                  $json['data']    = $result_old;
                }
                else
                {
                  $json['result']  = "false";
                  $json['msg']     = "No QR found";
                }
          }
        elseif($scan_count<=1)
        {
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('two_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['scan']  = "1";        
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 
         
             $whereDatascan = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Not Valid') );
         $fetch_scan_history=$this->Api_Model->getAllDataRow($whereDatascan);
         if($fetch_scan_history){
             $this->db->where('unique_id',$unique_id)
                      ->where('user_id',$user_id)
                      ->update('scan_history',$data);
         }else{
             $this->db->insert('scan_history',$data); 
         }

         
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
      }
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
        
        }
//............................Reset time scan............................//         
        elseif($type=='R')
        {
            $wheredata = array('field'=>'*',
         'table'=>'reset_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result=$this->Api_Model->getAllDataRow($wheredata);
        
        if($result){
           
          $scan_status=$result->scan_status;
        
          
          $whereData = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Valid') );
         
          $result_scan=$this->Api_Model->getAllDataRow($whereData);
          if($result_scan){
             $result_old=$this->Api_Model->getAllDataRow($wheredata);
              
                if($result_old)
                    {
                  $json['result']  = "true";
                   $json['scan']  = "2";
                  $json['msg']     = "All user Data show ";
                  $json['path']    = base_url()."assets/qr_image/";
                  $json['data']    = $result_old;
                }
                else
                {
                  $json['result']  = "false";
                  $json['msg']     = "No QR found";
                }
          }
        elseif($scan_status==0)
        {
          
          if($result)
          {

           $scan_count=$result->scan_count;
           $data_update['scan_count']=($scan_count+1);
          $data_update['scan_status']='1';
           $update=$this->db->where('code',$unique_id)
           ->update('reset_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
           $json['scan']  = "1";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 

          $this->db->insert('scan_history',$data);
        
        $json['result']  = "false";
        $json['msg']     = "QR Code Already Scanned";
      }
        
        
        
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
      }
//............................Infinite time scan............................// 



elseif($type=='I')
        {
            $wheredata = array('field'=>'*',
         'table'=>'infinite_qr_code_genrater',
         'where'=>array('code' => $unique_id) );

        $result_infinte=$this->Api_Model->getAllDataRow($wheredata);
        if($result_infinte){
        
         if($result_infinte){
            
        $code_start=$result_infinte->code_start;
        $code_end=$result_infinte->code_end;
        
        $today_date=date('d-m-Y H:i');
        
        $whereDatavalid = array('field'=>'*',
         'table'=>'infinite_qr_code_genrater',
         'where'=>array('code' => $unique_id,
                        'code_start <=' => $today_date,
                        'code_end >=' => $today_date
                    
                    ) );

        $result_valid=$this->Api_Model->getAllDataRow($whereDatavalid);
         }
        
        
        
         $whereData = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Valid') );
         
          $result_scan=$this->Api_Model->getAllDataRow($whereData);
          if($result_scan){
             // $result_old=$this->Api_Model->getAllDataRow($wheredata);
             $result_old=$this->Api_Model->getAllDataRow($whereDatavalid);
              
                if($result_old)
                    {
                  $json['result']  = "true";
                  $json['scan']  = "2";
                 $json['msg']     = "You have already verified this !";

                  $json['path']    = base_url()."assets/qr_image/";
                  $json['data']    = $result_old;
                }
                else
                {
                  $json['result']  = "false";
                  $json['msg']     = "No QR found";
                }
          }
        elseif($result_valid)
        {
          
          if($result_valid)
          {

           $scan_count=$result_valid->scan_count;
           $data_update['scan_count']=($scan_count+1);
           $update=$this->db->where('code',$unique_id)
           ->update('infinite_qr_code_genrater',$data_update);
           
           $result_new=$this->Api_Model->getAllDataRow($wheredata);
           if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }                         
          
          
          $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Valid',
         ); 

          $this->db->insert('scan_history',$data);                 
          
        }                         

        if($result_new)
        {
          $json['result']  = "true";
          $json['scan']  = "1";
          $json['msg']     = "All user Data show ";
          $json['path']    = base_url()."assets/qr_image/";
          $json['data']    = $result_new;
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }

      }else{
          if(!empty($address))
           {
            $address = $address;
          }else{
            $address='';
          }
        
        
        $data=array('user_id'=>$user_id,
           'unique_id'=>$unique_id,
           'address'=>$address,
           'date'=>date('d-m-Y'),
           'time'=>date('H:i'),
           'type'=>'Not Valid',
         ); 
         
             $whereDatascan = array('field'=>'*',
         'table'=>'scan_history',
         'where'=>array( 'unique_id' => $unique_id ,
                          'user_id' => $user_id,
                          'type' => 'Not Valid') );
         $fetch_scan_history=$this->Api_Model->getAllDataRow($whereDatascan);
         if($fetch_scan_history){
             $this->db->where('unique_id',$unique_id)
                      ->where('user_id',$user_id)
                      ->update('scan_history',$data);
         }else{
             $this->db->insert('scan_history',$data); 
         }

        
        $json['result']  = "false";
        $json['msg']     = "No QR found";
      }
        }
        else
        {
          $json['result']  = "false";
          $json['msg']     = "No QR found";
        }
        
        }
//...................................................//
else{
     $json['result']  = "false";
          $json['msg']     = "No QR found";
}
//......................................................//
    }
    else
    {
      $json['result']  = "false";
      $json['msg']     = "parameter required unique_id,user_id,optional('address')";
    }        
    
    echo json_encode($json);
  }      
//...........................sachin end....march.......................//

}


?>
