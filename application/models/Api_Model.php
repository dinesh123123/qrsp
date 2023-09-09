<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Api_Model extends CI_Model {
    
    
    
    
    

     public function is_record_exist($tbl,$key,$value)
  {

    $query = $this->db->get_where($tbl, array($key => $value));

    if(count($query->result()))

    {

      return true;

    }else{

      return false;

    }

  }

     public function email_password($email,$password)
  {

    
    $query = $this->db->where('email',$email)
                      ->where('plain_password',$password)
                      ->get('user')
                      ->row();


if($query)
    {

      return true;

    }else{

      return false;

    }

  }



      public function select_single_rows($table,$key,$value) 
    {
      $this->db->select('*');
      $this->db->from($table);
      $this->db->where($key,$value);
      $query = $this->db->get();
      return $query->row();
    }



  public function is_record_exists($tbl,$key,$value)
  {
    $query = $this->db->get_where($tbl, array($key => $value));
    if(count($query->result()))
    {
      return true;
    }else{
      return false;
    }
  }



     public function getAllDataRow($data)
    {
        $this->db->select($data['field']);
        $this->db->where($data['where']);
        $data = $this->db->get($data['table']);
        return $data->row();
    }




      public function select_single_row($table,$key,$value)
  {
    $this->db->select('id,phone,image as type');
    $this->db->from($table);
    $this->db->where($key,$value);
    $query = $this->db->get();
    return $query->row();
  }
  
  
  
  
      public function select_single_row_ss($table,$key,$value)
  {
    $this->db->select('*');
    $this->db->from($table);
    $this->db->where($key,$value);
    $query = $this->db->get();
    return $query->row();
  }


  // public function check_credentials($vendor_email)
  // {
  //   $query = $this->db->query("SELECT * FROM vendor WHERE `vendor_email`= '$vendor_email'");
  //   if(count($query->result()))
  //   {
  //     return $query->row();
  //   }else{
  //     return array();
  //   }
  // }


  public function check_email($email)
  {
    $query = $this->db->query("SELECT * FROM users WHERE `email`= '$email'");
    if(count($query->result()))
    {
      return $query->row();
    }else{
      return array();
    }
  }


  public function update($wheredata,$table,$data){
    $query = $this->db->where($wheredata);
    $query = $this->db->update($table,$data);
    return $query;
  }




     function getAllData($data){

    $this->db->select($data['field']);
    $this->db->where($data['where']);
    $this->db->order_by($data['order_by']);
    $data = $this->db->get($data['table']);
    return $data->result();
    }



    public function insertAllData($table,$post_data)
    {
      $query = $this->db->insert($table, $post_data);
    return  $this->db->insert_id();
        
    }






     public function getSingleResultWithJoin($table,$column,$joins,$where,$order_by)
  {
    if($column!='')
    {
      $fields = implode(",",$column);
    }else{
      $fields = "*";
    }
    $this->db->select($fields);
    $this->db->from($table);
    $this->db->order_by($order_by);
    if(!empty($joins))
    {
      foreach ($joins as $k => $v)
      {
        $this->db->join($v['table'], $v['on']);
      }
    }
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result();
  }





  
  
   public function getSingleResultWithJoin_limit($table,$column,$joins,$where,$order_by,$limit)
  {
    if($column!='')
    {
      $fields = implode(",",$column);
    }else{
      $fields = "*";
    }
    $this->db->select($fields);
    $this->db->from($table);
    $this->db->order_by($order_by);
    $this->db->limit($limit);
    if(!empty($joins))
    {
      foreach ($joins as $k => $v)
      {
        $this->db->join($v['table'], $v['on']);
      }
    }
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result();
  }






   public function getSingleResultWithJoin_gg($table,$column,$joins,$where,$order_by)
  {
    if($column!='')
    {
      $fields = implode(",",$column);
    }else{
      $fields = "*";
    }
    $this->db->select($fields);
    $this->db->from($table);
    $this->db->order_by($order_by);
    if(!empty($joins))
    {
      foreach ($joins as $k => $v)
      {
        $this->db->join($v['table'], $v['on'],$v['right_left']);
      }
    }
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result();
  }






   public function getSingleRowWithJoin($table,$column,$joins,$where)
  {
    if($column!='')
    {
      $fields = implode(",",$column);
    }else{
      $fields = "*";
    }
    $this->db->select($fields);
    $this->db->from($table);
    if(!empty($joins))
    {
      foreach ($joins as $k => $v)
      {
        $this->db->join($v['table'], $v['on'],$v['right_left']);
      }
    }
    $this->db->where($where);
    $query = $this->db->get();
    return $query->row();
  }






    public function updateData_tt($table,$post_data,$whereas)
    {
        $this->db->where($whereas);
      $this->db->update($table,$post_data);

      return true;


    }


function updateData($table,$data,$id)
  {
    $this->db->where('id', $id);
    $this->db->update($table, $data);
    return true;
  }



      public function select_single_row_specific($data)
  {

    $this->db->select($data['field']);
    $this->db->where($data['where']);
    $data = $this->db->get($data['table']);
    return $data->row();
  }




  public function get_community($table,$where,$order_by)
  {
      $this->db->where($where);
      $this->db->select("ifnull(users.id,'') as user_id,
                         ifnull(users.name,'') as name,
                         ifnull(users.image,'') as user_image,
                        ifnull(users.mobile,'') as user_mobile,
                         ifnull(category.name,'') as required,
                         community.id,
                         community.description,
                         community.image as community_image,
                         community.created_date,
                         ifnull(city.name,'') as city_name,
                         users.type as usertype,
                         community.phone,
                         community.user_id as user_iddd,
                         ifnull(category.name,'') as my_category_name

                         ");
      $this->db->from($table);
      $this->db->join("users","users.id = community.user_id","left");
      $this->db->join("category","category.id = community.category_id","left");
      $this->db->join("city","city.id = users.city_id","left");
      $this->db->order_by($order_by);
      $result = $this->db->get()->result();

      return $result;
  }





  public function get_my_community($table,$user_id)
  {
      $this->db->where("community.user_id",$user_id);
      $this->db->select("community.id,
                         community.description,
                         community.image,
                         community.type,
                         community.created_date,
                         users.city_name,
                         users.type as usertype

                         ifnull(category.name,'') as category_name");
      $this->db->from($table);
      $this->db->join("category","category.id = community.category_id","left");
      $this->db->join("users","users.id = $user_id","left");

      $this->db->order_by("community.id","DESC");
      $result = $this->db->get()->result();

      return $result;
  }
  
  
  
  

    public function get_my_reel() {

      $this->db->select("reels.*,
                         users.name,
                    
                         city.name as city_name,
                         users.image as user_image");
      $this->db->from('reels');
      $this->db->join('users','users.id = reels.user_id');
      $this->db->join('city','city.id = users.city_id');
      $this->db->order_by('reels.id','DESC');
      $query = $this->db->get();
      $result = $query->result();


           

      return $result;   

  } 

  
  
  public function get_catwise_users($table,$type)
  {
      $this->db->where("users.type",$type);
      $this->db->select("users.id,
                         users.image,
                         users.name,
                         ifnull(category.name,'') as category_name");
      $this->db->from($table);
      $this->db->join("category","category.id = users.category_id","left");
      $this->db->order_by("users.id","DESC");
      $rest = $this->db->get()->result();
      
      return $rest;
  }
  
  
  
  


  
  
  
  public function get_all_cats($table,$order_by)
  {
      $this->db->select("category.id,
                         category.name");
      $this->db->from($table);
      $this->db->order_by($order_by);
      $result = $this->db->get()->result();
      
      return $result;
  }
  
  
  
  
  
  
  public function get_reel_comments($table,$reel_id)
  {
      $this->db->where("comment_on_reels.reel_id",$reel_id);
      $this->db->select("comment_on_reels.id,
                         comment_on_reels.user_id,
                         comment_on_reels.reel_id,
                         comment_on_reels.comment,
                         comment_on_reels.created_date,
                         users.name as user_name,
                         users.image as user_image");
      $this->db->from($table);
      $this->db->join("users","users.id = comment_on_reels.user_id");
      $this->db->order_by("comment_on_reels.id","DESC");
      $result = $this->db->get()->result();
      
      return $result;
  }
  
  
  
  
  
//   public function get_my_reels($table,$user_id)
//   {

//       $this->db->where("reels.user_id",$user_id);
//       $this->db->select("reels.*");
//       $this->db->from("reels");
//       $this->db->order_by("reels.id","DESC");
//       $rest = $this->db->get()->result();
      
//       return $rest;
//   }
  
  
  
  public function singleRowdata($where_data,$table){
      $this->db->where($where_data);
      $query=$this->db->get($table);
      return $query->row();
    }



      
  public function insertAllDataa($table,$post_data)
    {
      $this->db->insert($table, $post_data);
      // $this->db->order_by($order_by);
      $insert_id = $this->db->insert_id();
      return  $insert_id;
    }
   
  



  public function deleteData($table,$key,$value)
    {
        $this->db->where($key, $value);
        $this->db->delete($table);
        
        if($this->db->affected_rows() > 0)
        {
            return true;
        }
        else
        {
           return false; 
        }
        
        
    }
    
    
    public function get_All_data_fetch($where)
    {
        return $this->db->select($where['field'])
                         ->where($where['where'])
                         ->order_by($where['order_by'])
                         ->get($where['table'])
                         ->result();
    } 
    public function All_data_insert($table,$data)
    {
      return $this->db->insert($table, $data);
    }

   public function All_data_delete($table,$where)
    {
        $this->db->where($where);
        $this->db->delete($table);
        
        if($this->db->affected_rows() > 0)
        {
            return true;
        }
        else
        {
           return false; 
        }
    }
     public function listfavourite_user($listing_id)
   {
    return $this->db->select('*')
                    ->where('id',$listing_id)
                    ->get('listing')
                    ->row();
    }
    

public function get_single_data_fetch($where)
    {
        return $this->db->select($where['field'])
                         ->where($where['where'])
                         ->order_by($where['order_by'])
                         ->get($where['table'])
                         ->row();
    } 

    


  public function service_listing($service_id)
   {
    return $this->db->select('*')
                    ->where('id',$service_id)
                    ->get('serviceoffer')
                    ->row();
    }
    
    public function update_All_data($where,$data)
    {
    
         $this->db->where($where['where'])
                  ->update($where['table'],$data);
          return true;               
    }

  //...................................................//
  
public function get_post_job($company_id)
{
  // $result_skills=null;
  $data_new=null;
   $result=$this->db->select('post_job.*')
                           ->select('company_user.company_name,company_user.address as company_address')
                           ->where('post_job.company_id',$company_id)
                           ->from('post_job')
                           ->join('company_user','company_user.id =post_job.company_id')
                           ->get()
                           ->result();


      foreach($result as $value)
      {
         $data = null;
        $skills=$value->skills;
        $id=$value->id;
         $sk_id=(explode(",", $skills));

          foreach($sk_id as $x=>$x_value){
                                $skills_id=$x_value;
                                $result_skills=$this->db->where('id',$skills_id)
                                ->get('skills')
                                ->row();           

                                $data[]=$result_skills;
                              }

         $result_new=$this->db->select('post_job.*')
                           ->select('company_user.company_name,company_user.address as company_address')
                           ->where('post_job.id',$id)
                           ->where('post_job.company_id',$company_id)
                           ->from('post_job')
                           ->join('company_user','company_user.id =post_job.company_id')
                           ->get()
                           ->row();

             
            if($result_new){
             $result_new->skills= $data; 
               $data_new[]=$result_new;
             }

     }
      return $data_new;

}

//.............................................................//

// public function get_post_internship($company_id)
// {
 
//   return $result=$this->db->select('post_internship.*')
//                            ->select('company_user.company_name,company_user.address as company_address')
//                            ->where('post_internship.company_id',$company_id)
//                            ->from('post_internship')
//                            ->join('company_user','company_user.id =post_internship.company_id')
//                            ->get()
//                            ->result();

// }
//............................................................//

public function get_post_internship($company_id)
{
 $data_new=null;
          $result=$this->db->select('post_internship.*')
                           ->select('company_user.company_name,company_user.address as company_address')
                           ->where('post_internship.company_id',$company_id)
                           ->from('post_internship')
                           ->join('company_user','company_user.id =post_internship.company_id')
                           ->get()
                           ->result();


      foreach($result as $value)
      {
         $data = null;
        $skills=$value->skills;
        $id=$value->id;
         $sk_id=(explode(",", $skills));

          foreach($sk_id as $x=>$x_value){
                                $skills_id=$x_value;
                                $result_skills=$this->db->where('id',$skills_id)
                                ->get('skills')
                                ->row();           

                                $data[]=$result_skills;
                              }

         $result_new=$this->db->select('post_internship.*')
                           ->select('company_user.company_name,company_user.address as company_address')
                           ->where('post_internship.company_id',$company_id)
                           ->where('post_internship.id',$id)
                           ->from('post_internship')
                           ->join('company_user','company_user.id =post_internship.company_id')
                           ->get()
                           ->row();

            if($result_new){
             $result_new->skills= $data; 
               $data_new[]=$result_new;
             }

            
     }
return $data_new;

}
  //....................................................//  

public function get_coupons($company_id)
{
  return   $result=$this->db->select('company_user.company_name')
                            ->select('create_coupons.*')
                            ->where('company_user.id',$company_id)
                            ->where('create_coupons.company_id',$company_id)
                            ->from('create_coupons')
                            ->join('company_user','company_user.id=create_coupons.company_id')
                            ->get()
                            ->result();
}
//.........................company products................................//
public function company_products($company_id)
{
  return   $result=$this->db->select('company_products.*')
                            ->select('category_kapsul.cate_name,category_kapsul.cate_image')
                            ->where('company_products.company_id',$company_id)
                            ->from('company_products')
                            ->join('category_kapsul','category_kapsul.id=company_products.category_kapsul_id')
                            ->get()
                            ->result();
}

//.......................appy job internship..............................//
 
 public function apply_job_internship($user_id,$type)
 {
     $result=$this->db->where('apply_job_internship.user_id',$user_id)
                       ->where('apply_job_internship.type',$type)
                       ->select('apply_job_internship.user_id,apply_job_internship.job_internship_id,apply_job_internship.type,apply_job_internship.apply_date')
                       ->select('job_internship.medical_id,job_internship.job_profile,job_internship.organization,job_internship.location')
                       ->from('apply_job_internship')
                       ->join('job_internship','job_internship.id=apply_job_internship.job_internship_id')
                       ->get()
                       ->result();
         return $result;              
 }




//................15 feb 2023...........................//

 public function appy_already_old($medical_id)
 {
  $result=$this->db->select('*')
  ->where('medical_id',$medical_id)
  ->where('type','job')
  ->get('apply_job_internship')
  ->result();


  $result_job_internship=$this->db->select('*')
                    // ->where('user_id',$user_id)
  
  ->get('post_job')
  ->result(); 
  if($result){                           

    foreach($result_job_internship as $value)
    {

      $id=$value->id;

      foreach($result as $val)
      {

        $applyid=$val->job_internship_id;

      }
      $newresult=$this->db->select('*')
      ->where('id',$id)
      
      ->get('post_job')
      ->row();



      if($applyid==$id)
      {

        $newresult->status='1';

      }else{

        $newresult->status='0';

      } 
      $new_data[]=$newresult;

    }
    return $new_data; 
  }else{
    return $result_job_internship; 
  }

}


//..........................................................//

 public function appy_already_internship_old($medical_id)
 {
  $result=$this->db->select('*')
  ->where('medical_id',$medical_id)
  ->where('type','internship')
  ->get('apply_job_internship')
  ->result();


  $result_job_internship=$this->db->select('*')
                    // ->where('user_id',$user_id)
 
  ->get('post_internship')
  ->result(); 
  if($result){                           

    foreach($result_job_internship as $value)
    {

      $id=$value->id;

      foreach($result as $val)
      {

        $applyid=$val->job_internship_id;
        
      }
      $newresult=$this->db->select('*')
      ->where('id',$id)
      
      ->get('post_internship')
      ->row();



      if($applyid==$id)
      {
        
        $newresult->status='1';

      }else{
        
        $newresult->status='0';

      } 
      $new_data[]=$newresult;

    }
    return $new_data; 
  }else{
    return $result_job_internship; 
  }
  
}

//....................20 feb 2023..............................//

// public function get_post_job_new($company_id)
public function appy_already($medical_id)
{
             // $result_skills=null;

  $data_new=null;
   $result=$this->db->select('post_job.*')
                           ->select('company_user.company_name,company_user.address as company_address')
                           // ->where('post_job.company_id',$company_id)
                           ->from('post_job')
                           ->join('company_user','company_user.id =post_job.company_id')
                           ->get()
                           ->result();


      foreach($result as $value)
      {
         $data = null;
        $skills=$value->skills;
        $id=$value->id;
         $sk_id=(explode(",", $skills));

          foreach($sk_id as $x=>$x_value){
            $result_skills=null;
                                $skills_id=$x_value;
                                $result_skills=$this->db->where('id',$skills_id)
                                ->get('skills')
                                ->row();           

                                $data[]=$result_skills;
                              }

         $result_new=$this->db->select('post_job.*')
                           ->select('company_user.company_name,company_user.address as company_address,company_user.image as company_image')
                           ->where('post_job.id',$id)
                           // ->where('post_job.company_id',$company_id)
                           ->from('post_job')
                           ->join('company_user','company_user.id =post_job.company_id')
                           ->get()
                           ->row();

          $result_apply=$this->db->select('*')
                          ->where('job_internship_id',$id)
                          ->where('type','job')
                          ->get('apply_job_internship')
                          ->row();                 


               if($result_apply){
                 $result_new->i_applied='1';

               }else{
                $result_new->i_applied='0';

               }

               $result_saved=$this->db->select('*')
                          ->where('job_internship_id',$id)
                          ->where('type','job')
                          ->get('save_job_internship')
                          ->row();                 


               if($result_saved){
                 $result_new->i_saved='1';

               }else{
                $result_new->i_saved='0';

               }             

             
            if($result_new){
             $result_new->skills= $data; 
               $data_new[]=$result_new;
             }

     }
      return $data_new;

}

//.....................................................//

 public function appy_already_internship($medical_id)
 {
 
 $data_new=null;
          $result=$this->db->select('post_internship.*')
                           ->select('company_user.company_name,company_user.address as company_address')
                           // ->where('post_internship.company_id',$company_id)
                           ->from('post_internship')
                           ->join('company_user','company_user.id =post_internship.company_id')
                           ->get()
                           ->result();


      foreach($result as $value)
      {
         $data = null;
        $skills=$value->skills_required;
        $id=$value->id;
         $sk_id=(explode(",", $skills));

          foreach($sk_id as $x=>$x_value){
                                $skills_id=$x_value;
                                $result_skills=$this->db->where('id',$skills_id)
                                ->get('skills')
                                ->row();           

                                $data[]=$result_skills;
                              }

         $result_new=$this->db->select('post_internship.*')
                           ->select('company_user.company_name,company_user.address as company_address,company_user.image as company_image')
                           // ->where('post_internship.company_id',$company_id)
                           ->where('post_internship.id',$id)
                           ->from('post_internship')
                           ->join('company_user','company_user.id=post_internship.company_id')
                           ->get()
                           ->row();

                   $result_apply=$this->db->select('*')
                          ->where('job_internship_id',$id)
                          ->where('type','internship')
                          ->get('apply_job_internship')
                          ->row();                 


               if($result_apply){
                 $result_new->i_applied='1';

               }else{
                $result_new->i_applied='0';

               } 


                  $result_saved=$this->db->select('*')
                          ->where('job_internship_id',$id)
                          ->where('type','internship')
                          ->get('save_job_internship')
                          ->row();                 


               if($result_saved){
                 $result_new->i_saved='1';

               }else{
                $result_new->i_saved='0';

               } 




            if($result_new){
             $result_new->skills_required= $data; 
               $data_new[]=$result_new;
             }

            
     }
return $data_new;

}


//.....................................................//
   
 // public function apply_job_internship_medical($medical_id)
 // {
 //     $result=$this->db->where('medical_id',$medical_id)
 //                       ->get('apply_job_internship')
 //                       ->result();
                       
 //         return $result;           
 // }

 //  public function apply_job_internship_company($company_id)
 // {
 //     $result=$this->db->where('company_id',$company_id)
 //                       ->get('apply_job_internship')
 //                       ->result();
                       
 //         return $result;              
 // }
 
//...........................17 feb 2023..........................//

 public function apply_job_internship_medical($medical_id)
 {
      $merged_arr=null; 
     $result_job=$this->db->select('apply_job_internship.*')
                      ->select('company_user.company_name,company_user.type as company_type')
                      ->select('post_job.name as post_job_name,post_job.questions_1,post_job.questions_2,post_job.questions_3,post_job.questions_4,post_job.questions_5,post_job.count,post_job.i_saved,post_job.i_applied')
                      ->select('post_job.count,post_job.i_saved,post_job.i_applied')
                      ->where('apply_job_internship.medical_id',$medical_id)
                      ->where('apply_job_internship.type','job')
                      ->from('apply_job_internship')
                      ->join('post_job','post_job.id=apply_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=post_job.company_id')
                       ->get()
                       ->result();

            if($result_job){
              $job_data=$result_job;

            }else{
               $result_job='';
              $job_data=$result_job;

            }          
                    


          $result_internship=$this->db->select('apply_job_internship.*')
                      ->select('company_user.company_name,company_user.type as company_type')
                      ->select('post_internship.internship_title as post_internship_name,post_internship.questions_1,post_internship.questions_2,post_internship.questions_3,post_internship.questions_4,post_internship.questions_5')
                      ->select('post_internship.count,post_internship.i_saved,post_internship.i_applied')
                      ->where('apply_job_internship.medical_id',$medical_id)
                      ->where('apply_job_internship.type','internship')
                      ->from('apply_job_internship')
                      ->join('post_internship','post_internship.id=apply_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=post_internship.company_id')
                       ->get()
                       ->result();



            if($result_internship){
              $internship_data=$result_internship;

            }else{
               $result_internship='';
              $internship_data=$result_internship;

            }     
        if($job_data && $internship_data ){

            $merged_arr = array_merge($job_data,$internship_data);

           }elseif ($job_data) { 
            $merged_arr=$job_data; 
           }elseif ($internship_data) {
            $merged_arr=$internship_data;
          }else{ 
            $merged_arr=''; 
           }

         return $merged_arr;           
 }
//.....................company...............................//

public function apply_job_internship_company($company_id)
 {
      $merged_arr=null; 
     $result_job=$this->db->select('apply_job_internship.*')
                      ->select('company_user.name as medical_name,company_user.type as medical_type')
                      ->select('post_job.name as post_job_name,post_job.questions_1,post_job.questions_2,post_job.questions_3,post_job.questions_4,post_job.questions_5,post_job.count,post_job.i_saved,post_job.i_applied')
                      ->select('post_job.count,post_job.i_saved,post_job.i_applied')
                      ->where('apply_job_internship.company_id',$company_id)
                      ->where('apply_job_internship.type','job')
                      ->from('apply_job_internship')
                      ->join('post_job','post_job.id=apply_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=apply_job_internship.medical_id')
                       ->get()
                       ->result();

            if($result_job){
              $job_data=$result_job;

            }else{
               $result_job='';
              $job_data=$result_job;

            }          
                    


          $result_internship=$this->db->select('apply_job_internship.*')
                      ->select('company_user.name as medical_name,company_user.type as medical_type')
                      ->select('post_internship.internship_title as post_internship_name,post_internship.questions_1,post_internship.questions_2,post_internship.questions_3,post_internship.questions_4,post_internship.questions_5')
                      ->select('post_internship.count,post_internship.i_saved,post_internship.i_applied')
                      ->where('apply_job_internship.company_id',$company_id)
                      ->where('apply_job_internship.type','internship')
                      ->from('apply_job_internship')
                      ->join('post_internship','post_internship.id=apply_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=apply_job_internship.medical_id')
                       ->get()
                       ->result();



            if($result_internship){
              $internship_data=$result_internship;

            }else{
               $result_internship='';
              $internship_data=$result_internship;

            }     
        if($job_data && $internship_data ){

            $merged_arr = array_merge($job_data,$internship_data);

           }elseif ($job_data) { 
            $merged_arr=$job_data; 
           }elseif ($internship_data) {
            $merged_arr=$internship_data;
          }else{ 
            $merged_arr=''; 
           }

         return $merged_arr;             
 }

 //...................saved list job internship............//

  public function saved_job_internship($medical_id)
{
        $merged_arr=null; 
     $result_job=$this->db->select('save_job_internship.*')
                      ->select('company_user.company_name,company_user.type as company_type,company_user.image as company_image')
                      ->select('post_job.name as post_job_name,post_job.questions_1,post_job.questions_2,post_job.questions_3,post_job.questions_4,post_job.questions_5')
                      ->select('post_job.count,save_job_internship.save as i_saved,post_job.i_applied')
                      ->select('post_job.price_from,post_job.fixed_pay')
                      ->where('save_job_internship.medical_id',$medical_id)
                      ->where('save_job_internship.type','job')
                      ->from('save_job_internship')
                      ->join('post_job','post_job.id=save_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=post_job.company_id')
                      ->get()
                      ->result();

            if($result_job){
              $job_data=$result_job;

            }else{
               $result_job='';
              $job_data=$result_job;

            }          
                    


          $result_internship=$this->db->select('save_job_internship.*')
                      ->select('company_user.company_name,company_user.type as company_type ,company_user.image as company_image')
                      ->select('post_internship.internship_title as post_internship_name,post_internship.questions_1,post_internship.questions_2,post_internship.questions_3,post_internship.questions_4,post_internship.questions_5')
                      ->select('post_internship.count,save_job_internship.save as i_saved,post_internship.i_applied')
                      ->select('post_internship.internship_duration,post_internship.price_from,post_internship.duration')
                      ->where('save_job_internship.medical_id',$medical_id)
                      ->where('save_job_internship.type','internship')
                      ->from('save_job_internship')
                      ->join('post_internship','post_internship.id=save_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=post_internship.company_id')
                       ->get()
                       ->result();



            if($result_internship){
              $internship_data=$result_internship;

            }else{
               $result_internship='';
              $internship_data=$result_internship;

            }     
        if($job_data && $internship_data ){

            $merged_arr = array_merge($job_data,$internship_data);

           }elseif ($job_data) { 
            $merged_arr=$job_data; 
           }elseif ($internship_data) {
            $merged_arr=$internship_data;
          }else{ 
            $merged_arr=''; 
           }

         return $merged_arr;

}
//.......................get applicants list.................//
  
  public function applicants_list($company_id,$job_internship_id)
 {
      $merged_arr=null; 
     $result_job=$this->db->select('apply_job_internship.*')
                      ->select('company_user.name as medical_name,company_user.type as medical_type')
                      ->select('post_job.name as post_job_name,post_job.questions_1,post_job.questions_2,post_job.questions_3,post_job.questions_4,post_job.questions_5,post_job.count,post_job.i_saved,post_job.i_applied')
                      ->select('post_job.count,post_job.i_saved,post_job.i_applied')
                      ->where('apply_job_internship.company_id',$company_id)
                      ->where('apply_job_internship.job_internship_id',$job_internship_id)
                      ->where('apply_job_internship.type','job')
                      ->from('apply_job_internship')
                      ->join('post_job','post_job.id=apply_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=apply_job_internship.medical_id')
                       ->get()
                       ->result();

            if($result_job){
              $job_data=$result_job;

            }else{
               $result_job='';
              $job_data=$result_job;

            }          
                    


          $result_internship=$this->db->select('apply_job_internship.*')
                      ->select('company_user.name as medical_name,company_user.type as medical_type')
                      ->select('post_internship.internship_title as post_internship_name,post_internship.questions_1,post_internship.questions_2,post_internship.questions_3,post_internship.questions_4,post_internship.questions_5')
                      ->select('post_internship.count,post_internship.i_saved,post_internship.i_applied')
                      ->where('apply_job_internship.company_id',$company_id)
                      ->where('apply_job_internship.job_internship_id',$job_internship_id)
                      ->where('apply_job_internship.type','internship')
                      ->from('apply_job_internship')
                      ->join('post_internship','post_internship.id=apply_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=apply_job_internship.medical_id')
                       ->get()
                       ->result();



            if($result_internship){
              $internship_data=$result_internship;

            }else{
               $result_internship='';
              $internship_data=$result_internship;

            }     
        if($job_data && $internship_data ){

            $merged_arr = array_merge($job_data,$internship_data);

           }elseif ($job_data) { 
            $merged_arr=$job_data; 
           }elseif ($internship_data) {
            $merged_arr=$internship_data;
          }else{ 
            $merged_arr=''; 
           }

         return $merged_arr;             
 }

//................applicants status.........................//
  

public function applicants_apply_status_company($company_id,$apply_status)
 {
      $merged_arr=null; 
     $result_job=$this->db->select('apply_job_internship.*')
                      ->select('company_user.name as medical_name,company_user.type as medical_type')
                      ->select('post_job.name as post_job_name,post_job.questions_1,post_job.questions_2,post_job.questions_3,post_job.questions_4,post_job.questions_5,post_job.count,post_job.i_saved,post_job.i_applied')
                      ->select('post_job.count,post_job.i_saved,post_job.i_applied')
                      ->where('apply_job_internship.company_id',$company_id)
                      ->where('apply_job_internship.apply_status',$apply_status)
                      ->where('apply_job_internship.type','job')
                      ->from('apply_job_internship')
                      ->join('post_job','post_job.id=apply_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=apply_job_internship.medical_id')
                       ->get()
                       ->result();

            if($result_job){
              $job_data=$result_job;

            }else{
               $result_job='';
              $job_data=$result_job;

            }          
                    


          $result_internship=$this->db->select('apply_job_internship.*')
                      ->select('company_user.name as medical_name,company_user.type as medical_type')
                      ->select('post_internship.internship_title as post_internship_name,post_internship.questions_1,post_internship.questions_2,post_internship.questions_3,post_internship.questions_4,post_internship.questions_5')
                      ->select('post_internship.count,post_internship.i_saved,post_internship.i_applied')
                      ->where('apply_job_internship.company_id',$company_id)
                      ->where('apply_job_internship.apply_status',$apply_status)
                      ->where('apply_job_internship.type','internship')
                      ->from('apply_job_internship')
                      ->join('post_internship','post_internship.id=apply_job_internship.job_internship_id')
                      ->join('company_user','company_user.id=apply_job_internship.medical_id')
                       ->get()
                       ->result();



            if($result_internship){
              $internship_data=$result_internship;

            }else{
               $result_internship='';
              $internship_data=$result_internship;

            }     
        if($job_data && $internship_data ){

            $merged_arr = array_merge($job_data,$internship_data);

           }elseif ($job_data) { 
            $merged_arr=$job_data; 
           }elseif ($internship_data) {
            $merged_arr=$internship_data;
          }else{ 
            $merged_arr=''; 
           }

         return $merged_arr;             
 }
public function get_scan_history($user_id)
{
  return $result=$this->db->select('user.name as user_name')
                          ->select('one_qr_code_genrater.*')
                          ->select('scan_history.id as scan_id,scan_history.date as scan_date,scan_history.address,scan_history.type as scan_type')
                          ->from('scan_history')
                          ->join('user','user.id=scan_history.user_id')
                          ->join('one_qr_code_genrater','one_qr_code_genrater.code=scan_history.unique_id')
                           ->where('scan_history.user_id',$user_id)
                           ->order_by('scan_history.id','desc')
                          ->get()
                          ->result();
}

//......................................................................//
public function get_scan_history_all($user_id)
{

$resultdata=null;
$result=$this->db->select('*')
                  ->where('user_id',$user_id)
                  ->order_by('id','desc')
                 ->get('scan_history')
                 ->result();
if($result)
{
    foreach($result as $value)
    {
        $unique_id=$value->unique_id;
        $scan_id=$value->id;
        $unique=$unique_id['0'];
        if($unique=='O')
        {
        $newresult=$this->db->select('user.name as user_name')
                          ->select('one_qr_code_genrater.*')
                          ->select('scan_history.id as scan_id,scan_history.date as scan_date,scan_history.address,scan_history.type as scan_type')
                          ->from('scan_history')
                          ->join('user','user.id=scan_history.user_id')
                          ->join('one_qr_code_genrater','one_qr_code_genrater.code=scan_history.unique_id')
                           ->where('scan_history.user_id',$user_id)
                           ->where('scan_history.id',$scan_id)
                           ->order_by('scan_history.id','desc')
                          ->get()
                          ->row();
                          
        
        }
        elseif($unique=='T')
        { 
            $newresult=$this->db->select('user.name as user_name')
                          ->select('two_qr_code_genrater.*')
                          ->select('scan_history.id as scan_id,scan_history.date as scan_date,scan_history.address,scan_history.type as scan_type')
                          ->from('scan_history')
                          ->join('user','user.id=scan_history.user_id')
                          ->join('two_qr_code_genrater','two_qr_code_genrater.code=scan_history.unique_id')
                           ->where('scan_history.user_id',$user_id)
                           ->where('scan_history.id',$scan_id)
                           ->order_by('scan_history.id','desc')
                          ->get()
                          ->row();
          
        }
        elseif($unique=='R')
        { 
             $newresult=$this->db->select('user.name as user_name')
                          ->select('reset_qr_code_genrater.*')
                          ->select('scan_history.id as scan_id,scan_history.date as scan_date,scan_history.address,scan_history.type as scan_type')
                          ->from('scan_history')
                          ->join('user','user.id=scan_history.user_id')
                          ->join('reset_qr_code_genrater','reset_qr_code_genrater.code=scan_history.unique_id')
                           ->where('scan_history.user_id',$user_id)
                           ->where('scan_history.id',$scan_id)
                           ->order_by('scan_history.id','desc')
                          ->get()
                          ->row();
           
        }
           elseif($unique=='I')
        { 
             $newresult=$this->db->select('user.name as user_name')
                          ->select('infinite_qr_code_genrater.*')
                          ->select('scan_history.id as scan_id,scan_history.date as scan_date,scan_history.address,scan_history.type as scan_type')
                          ->from('scan_history')
                          ->join('user','user.id=scan_history.user_id')
                          ->join('infinite_qr_code_genrater','infinite_qr_code_genrater.code=scan_history.unique_id')
                           ->where('scan_history.user_id',$user_id)
                           ->where('scan_history.id',$scan_id)
                           ->order_by('scan_history.id','desc')
                          ->get()
                          ->row();
           
        }else{
            $newresult=null;
        }
        if($newresult){
      
        $resultdata[]=$newresult;
        }
    }
    return $resultdata;
}


}

//.............................01 jun 2023...........................//

//...........................................................//



}



 
?>
