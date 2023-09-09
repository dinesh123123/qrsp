<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {
  
  public function selectAllData($table,$orderby="")
  {      
   $this->db->select('*');
   $this->db->order_by($orderby);
   $this->db->from($table);
   $query = $this->db-> get();
   return $query->result_array();
  }   
 

  public function selectAllById($table,$orderby="", $key, $value)
  {      
   $this->db->select('*');
   $this->db->from($table);
   $this->db->where($key,$value);
   $this->db->order_by($orderby);
   $query = $this->db-> get();
   return $query->result_array();
  }
  
  public function select_single_row_specific($data)
  {
    $this->db->select($data['field']);
    $this->db->where($data['where']);
    $data = $this->db->get($data['table']);
    return $data->row();
  }

  public function select_daywise_billing($restaurant_id,$date) 
  {
     $custom_query = "SELECT orders.order_id,orders.date, SUM(orders.total_price) as total_price
                      FROM orders 
                      INNER JOIN menues ON orders.menues_id =  menues.id 
                      WHERE menues.restaurant_id = '$restaurant_id' AND orders.date =  '$date' AND orders.payment_status =  'Paid'
                      GROUP BY orders.order_id ORDER BY orders.order_id DESC";
    $query = $this->db->query($custom_query);    
    return $query->result_array();                      
  }

  public function all_restaurant($from_date="", $to_date="") 
  {

     $custom_query = "SELECT 
                        orders.date,
                        orders.order_id ,
                        restaurants.restaurant_name,
                        SUM(orders.total_price) as total_price
                      FROM orders 
                      INNER JOIN menues ON orders.menues_id =  menues.id 
                      INNER JOIN restaurants ON menues.id =  restaurants.id ";
              if(!empty($from_date) && !empty($to_date))
              {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date = date('Y-m-d', strtotime($to_date));
                
                  $custom_query .= " WHERE orders.date BETWEEN '$from_date' AND '$to_date' "; 
              }

              $custom_query .= "GROUP BY orders.order_id ORDER BY orders.order_id DESC"; 
    $query = $this->db->query($custom_query);    
    return $query->result_array();                      
  }

  public function order_main_details($id) 
  {
     $custom_query = "SELECT orders.order_id,
                             orders.date,
                             users.name as user_name,
                             users.mobile as user_mobile,
                             users.address as user_address,
                             users.email as user_email,
                             restaurants.restaurant_name, 
                             restaurants.restaurant_email, 
                             restaurants.location as restaurant_location, 
                             restaurants.mobile as restaurant_mobile
                      FROM orders 
                      INNER JOIN users ON orders.user_id =  users.id 
                      INNER JOIN menues ON orders.menues_id =  menues.id 
                      INNER JOIN restaurants ON menues.restaurant_id =  restaurants.id 
                      WHERE orders.order_id = '$id'";
    $query = $this->db->query($custom_query);    
    return $query->row();                      
  } 
  public function select_order_items($id) 
  {
     $custom_query = "SELECT orders.quantity,
                             orders.price,
                             orders.total_price,
                             orders.promo_discount,
                             orders.table_number,
                             menues.name as menue_name,
                              CASE
                                WHEN categories.al_non_al_type = 'Alcoholic'  THEN orders.total_price * 5.0 / 100  
                              END AS vat,
                              CASE
                                WHEN categories.al_non_al_type = 'Non Alcoholic' THEN orders.total_price * 5.0 / 100  
                              END AS gst
                      FROM orders 
                      INNER JOIN menues ON menues.id = orders.menues_id 
                      INNER JOIN categories ON  menues.category_id = categories.id
                      WHERE orders.order_id = '$id'";
    $query = $this->db->query($custom_query);    
    return $query->result_array();
  }

  // public function selectAllData($table,$start,$length)
  // {      
  //  $this->db->select('*');
  //  $this->db->order_by('id DESC');
  //  $this->db->limit($start,$length);
  //  $this->db->from($table);
  //  $query = $this ->db-> get();
  //  return $query->result_array();
  // }

  public function count($tbl,$id)
  {
    $this->db->select($id);
    // $this->db->distinct();
    $this->db->from($tbl);
    $query = $this->db->get();
    return $query->num_rows();
  } 
  
  public function countreset()
  {
    $this->db->select('*');
    // $this->db->distinct();
    $this->db->where('scan_status','1');
    $this->db->from('reset_qr_code_genrater');
    $query = $this->db->get();
    return $query->num_rows();
  } 

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

  public function is_record_exist_update($tbl,$key,$value,$id)
  { 
    $this->db->select('*');
    $this->db->from($tbl);
    $this->db->where($key.'=', $value);
    $this->db->where('id !=', $id); // Produces: WHERE name != 'Joe' AND id < 45.
    $query = $this->db->get();
  
    if(count($query->result()))
    {
      return true;

    }else{
      return false;
    }
  }


  function insertAllData($table,$data)
  { 
    $result = $this->db->insert($table, $data);
    // /$this->db->insert('mytable', $data);
    $insert_id = $this->db->insert_id();
    // echo $this->db->last_query();exit();
    return  $insert_id;
      
  }  

  function updateData($table,$data,$id)
  { 
    $this->db->where('id', $id);
    $this->db->update($table, $data);
    return true;
  }

public function select_single_row($table,$key,$value) 
  {
    $this->db->select('*');
    $this->db->from($table);
    $this->db->where($key,$value);
    $query = $this->db->get();
    return $query->row();
  }

  public function select_row(){

  }

  public function insert($table,$data)
  {   $this->db->insert($table, $data);
       $insert_id = $this->db->insert_id();
       /*  echo $this->db->last_query();exit();*/
    return  $insert_id;
      
  } 

  public function get_RestraById($id)
    {
     $this->db->where('id',$id);
      $query = $this->db->get('restaurants');
      $query_result = $query->result();
      return $query_result;
  }

  public function select($table){
    $this->db->select('*');
    // $this->db->order_by($order_by);
    $this->db->from($table);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function delete_qr($tables,$res_id){
    $this->db->delete('qr_codes', array('restaurant_id' => $res_id));
    $data = array(
        'number_of_table' => $tables
    );
    $this->db->where('id', $res_id);
    $this->db->update('restaurants', $data);
    return true;
  }  

  public function update_number_of_table($tables,$res_id){
    $data = array(
        'number_of_table' => $tables
    );
    $this->db->where('id', $res_id);
    $this->db->update('restaurants', $data);
    return true;
  }


//..................04 march 2023....setting...............................//
public function get_condition_term()
  {
     $res = $this->db->get('terms_conditions');
     return $res->row_array();
  }  

public function terms_condition_update($id,$formarray)
  {
     $this->db->where('id',$id);
     $this->db->update('terms_conditions',$formarray);
     return true;
  }


//...................04 march 2023..contact us.................//

public function get_contact_us()
  {
     $que = $this->db->get('contact_us');
      return $que->row_array();
  }

  public function update_contact($id,$formarray)
  {
     $this->db->where('id',$id);
     $this->db->update('contact_us',$formarray);
     return true;
  }

  //..................04 march 2023....setting.....................//
public function get_privcay_policy()
  {
     $res = $this->db->get('privacy');
     return $res->row_array();
  }  

public function privacy_policy_update($id,$formarray)
  {
     $this->db->where('id',$id);
     $this->db->update('privacy',$formarray);
     return true;
  }

  //..................04 march 2023....setting.....................//
public function get_about_us()
  {
     $res = $this->db->get('about_us');
     return $res->row_array();
  }  

public function about_us_update($id,$formarray)
  {
     $this->db->where('id',$id);
     $this->db->update('about_us',$formarray);
     return true;
  }

  //.............................................//
public function feedback_user()
{
  return $this->db->select('user.name,user.mobile,user.image')
           ->select('feedback.*')
           ->from('feedback')
           ->join('user','user.id=feedback.user_id')
           ->order_by("feedback.id", "desc")
           ->get()
           ->result_array();
}
//.....................................//
public function get_qr_code_table()
{
  $this->db->order_by("id", "desc");
 $result= $this->db->get('qr_code_genrater');
 return $result->result_array();
}

public function get_qr_codeimage($id)
{
 $result= $this->db->where('id',$id)->get('qr_code_genrater');
 return $result->result_array();
}

public function get_qr_code_edit($id)
{
 $result= $this->db->where('id',$id)->get('qr_code_genrater');
 return $result->row_array();
}

public function scan_history()
{
  return $this->db->select('user.name,user.image')
           ->select('scan_history.*,')
           ->select('qr_code_genrater.qr_code,qr_code_genrater.unique_id')
           ->from('scan_history')
           ->join('user','user.id=scan_history.user_id')
           ->join('qr_code_genrater','qr_code_genrater.code=scan_history.unique_id')
           ->order_by("scan_history.id", "desc")
           ->get()
           ->result_array();
}

function getRows($params = array()){
        $this->db->select('*');
        $this->db->from('qr_code_genrater');
        
        if(array_key_exists("where", $params)){
            foreach($params['where'] as $key => $val){
                $this->db->where($key, $val);
            }
        }
        
        if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
            $result = $this->db->count_all_results();
        }else{
            if(array_key_exists("id", $params)){
                $this->db->where('id', $params['id']);
                $query = $this->db->get();
                $result = $query->row_array();
            }else{
                $this->db->order_by('id', 'desc');
                if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                    $this->db->limit($params['limit'],$params['start']);
                }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                    $this->db->limit($params['limit']);
                }
                
                $query = $this->db->get();
                $result = ($query->num_rows() > 0)?$query->result_array():FALSE;
            }
        }
        
        // Return fetched data
        return $result;
    }
    
    /*
     * Insert members data into the database
     * @param $data data to be insert based on the passed parameters
     */
    public function insert_qr($data = array()) {
        if(!empty($data)){
            // Add created and modified date if not included
            if(!array_key_exists("created_date", $data)){
                $data['created_date'] = date("Y-m-d H:i:s");
            }
            if(!array_key_exists("update_date", $data)){
                $data['update_date'] = date("Y-m-d H:i:s");
            }
            
            // Insert member data
            $insert = $this->db->insert('qr_code_genrater', $data);
            
            // Return the status
            return $insert?$this->db->insert_id():false;
        }
        return false;
    }
    
    /*
     * Update member data into the database
     * @param $data array to be update based on the passed parameters
     * @param $condition array filter data
     */
    public function update_qr($data, $condition = array()) {
        if(!empty($data)){
            // Add modified date if not included
            if(!array_key_exists("update_date", $data)){
                $data['update_date'] = date("Y-m-d H:i:s");
            }
            
            // Update member data
            $update = $this->db->update('qr_code_genrater', $data, $condition);
            
            // Return the status
            return $update?true:false;
        }
        return false;
    }
//...............07 April 2023....................//
 public function gallary_list()
{
  return $this->db->select('*')
             ->order_by('id','desc')
             ->get('images')

           ->result_array();
}

//.........................20 jun 2023........................//
public function qr_code_one_edit($id)
{
 $result= $this->db->where('id',$id)->get('one_qr_code_genrater');
 return $result->row_array();
}
public function get_qr_code_table_one()
{
  $this->db->order_by("id", "desc");
 $result= $this->db->get('one_qr_code_genrater');
 return $result->result_array();
}
//....................21 jun 2023..................................//
public function update_qr_one($data, $condition = array()) {
        if(!empty($data)){
            // Add modified date if not included
            if(!array_key_exists("update_date", $data)){
                $data['update_date'] = date("d-m-Y");
                $data['update_time'] = date("H:i");
            }
            
            // Update member data
            $update = $this->db->update('one_qr_code_genrater', $data, $condition);
            
            // Return the status
            return $update?true:false;
        }
        return false;
    }
    
//...................................................//
function getRows_one($params = array()){
        $this->db->select('*');
        $this->db->from('one_qr_code_genrater');
        
        if(array_key_exists("where", $params)){
            foreach($params['where'] as $key => $val){
                $this->db->where($key, $val);
            }
        }
        
        if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
            $result = $this->db->count_all_results();
        }else{
            if(array_key_exists("id", $params)){
                $this->db->where('id', $params['id']);
                $query = $this->db->get();
                $result = $query->row_array();
            }else{
                $this->db->order_by('id', 'desc');
                if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                    $this->db->limit($params['limit'],$params['start']);
                }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                    $this->db->limit($params['limit']);
                }
                
                $query = $this->db->get();
                $result = ($query->num_rows() > 0)?$query->result_array():FALSE;
            }
        }
        
        // Return fetched data
        return $result;
    }
    
//.........................30 jun 2023...........................//
public function qr_code_two_edit($id)
{
 $result= $this->db->where('id',$id)->get('two_qr_code_genrater');
 return $result->row_array();
}
public function get_qr_code_table_two()
{
  $this->db->order_by("id", "desc");
 $result= $this->db->get('two_qr_code_genrater');
 return $result->result_array();
}

//..............................................................//
function getRows_two($params = array()){
        $this->db->select('*');
        $this->db->from('two_qr_code_genrater');
        
        if(array_key_exists("where", $params)){
            foreach($params['where'] as $key => $val){
                $this->db->where($key, $val);
            }
        }
        
        if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
            $result = $this->db->count_all_results();
        }else{
            if(array_key_exists("id", $params)){
                $this->db->where('id', $params['id']);
                $query = $this->db->get();
                $result = $query->row_array();
            }else{
                $this->db->order_by('id', 'desc');
                if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                    $this->db->limit($params['limit'],$params['start']);
                }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                    $this->db->limit($params['limit']);
                }
                
                $query = $this->db->get();
                $result = ($query->num_rows() > 0)?$query->result_array():FALSE;
            }
        }
        
        // Return fetched data
        return $result;
    }
//......................................................//
public function update_qr_two($data, $condition = array()) {
        if(!empty($data)){
            // Add modified date if not included
            if(!array_key_exists("update_date", $data)){
                $data['update_date'] = date("d-m-Y");
                $data['update_time'] = date("H:i");
            }
            
            // Update member data
            $update = $this->db->update('two_qr_code_genrater', $data, $condition);
            
            // Return the status
            return $update?true:false;
        }
        return false;
    }
//..........................................................//
public function qr_code_reset_edit($id)
{
 $result= $this->db->where('id',$id)->get('reset_qr_code_genrater');
 return $result->row_array();
}
public function get_qr_code_table_reset()
{
  $this->db->order_by("id", "desc");
 $result= $this->db->get('reset_qr_code_genrater');
 return $result->result_array();
}
//..................................................................//
function getRows_reset($params = array()){
        $this->db->select('*');
        $this->db->from('reset_qr_code_genrater');
        
        if(array_key_exists("where", $params)){
            foreach($params['where'] as $key => $val){
                $this->db->where($key, $val);
            }
        }
        
        if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
            $result = $this->db->count_all_results();
        }else{
            if(array_key_exists("id", $params)){
                $this->db->where('id', $params['id']);
                $query = $this->db->get();
                $result = $query->row_array();
            }else{
                $this->db->order_by('id', 'desc');
                if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                    $this->db->limit($params['limit'],$params['start']);
                }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                    $this->db->limit($params['limit']);
                }
                
                $query = $this->db->get();
                $result = ($query->num_rows() > 0)?$query->result_array():FALSE;
            }
        }
        
        // Return fetched data
        return $result;
    }
//.........................................................//
public function update_qr_reset($data, $condition = array()) {
        if(!empty($data)){
            // Add modified date if not included
            if(!array_key_exists("update_date", $data)){
                $data['update_date'] = date("d-m-Y");
                $data['update_time'] = date("H:i");
            }
            
            // Update member data
            $update = $this->db->update('reset_qr_code_genrater', $data, $condition);
            
            // Return the status
            return $update?true:false;
        }
        return false;
    }
//...................03 july 2023...........................//
public function scan_history_all()
{

$resultdata=null;
$result=$this->db->select('*')
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
        $newresult= $this->db->select('user.name,user.image,user.mobile')
          ->select('scan_history.*,')
          ->select('one_qr_code_genrater.qr_code,one_qr_code_genrater.unique_id,one_qr_code_genrater.type as qr_type,one_qr_code_genrater.title')
          ->where('scan_history.id',$scan_id)
          ->from('scan_history')
          ->join('user','user.id=scan_history.user_id')
          ->join('one_qr_code_genrater','one_qr_code_genrater.code=scan_history.unique_id')
          ->order_by("scan_history.id", "desc")
          ->get()
          ->row_array();
        }
        elseif($unique=='T')
        {
            $newresult= $this->db->select('user.name,user.image,user.mobile')
          ->select('scan_history.*,')
          ->select('two_qr_code_genrater.qr_code,two_qr_code_genrater.unique_id,two_qr_code_genrater.type as qr_type,two_qr_code_genrater.title')
          ->where('scan_history.id',$scan_id)
          ->from('scan_history')
          ->join('user','user.id=scan_history.user_id')
          ->join('two_qr_code_genrater','two_qr_code_genrater.code=scan_history.unique_id')
          ->order_by("scan_history.id", "desc")
          ->get()
          ->row_array(); 
        }
        elseif($unique=='R')
        {
             $newresult= $this->db->select('user.name,user.image,user.mobile')
          ->select('scan_history.*,')
          ->select('reset_qr_code_genrater.qr_code,reset_qr_code_genrater.unique_id,reset_qr_code_genrater.type as qr_type,reset_qr_code_genrater.title')
          ->where('scan_history.id',$scan_id)
          ->from('scan_history')
          ->join('user','user.id=scan_history.user_id')
          ->join('reset_qr_code_genrater','reset_qr_code_genrater.code=scan_history.unique_id')
          ->order_by("scan_history.id", "desc")
          ->get()
          ->row_array();
        }
        elseif($unique=='I')
        {
             $newresult= $this->db->select('user.name,user.image,user.mobile')
          ->select('scan_history.*,')
          ->select('infinite_qr_code_genrater.qr_code,infinite_qr_code_genrater.unique_id,infinite_qr_code_genrater.type as qr_type,infinite_qr_code_genrater.title')
          ->where('scan_history.id',$scan_id)
          ->from('scan_history')
          ->join('user','user.id=scan_history.user_id')
          ->join('infinite_qr_code_genrater','infinite_qr_code_genrater.code=scan_history.unique_id')
          ->order_by("scan_history.id", "desc")
          ->get()
          ->row_array();
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
//.....................................................//
public function qr_code_infinite_edit($id)
{
 $result= $this->db->where('id',$id)->get('infinite_qr_code_genrater');
 return $result->row_array();
}
//...................one time.................//
 public function selectAllById_one()
  {      
   $this->db->select('*');
   $this->db->where('unique_id !=','');
//   $this->db->where('pdf ','0');
   $this->db->order_by('id','asc');
   $query = $this->db->get('one_qr_code_genrater');
   return $query->result_array();
  }
   public function selectAllById_two()
  {      
   $this->db->select('*');
   $this->db->where('unique_id !=','');
//   $this->db->where('pdf ','0');
   $this->db->order_by('id','asc');
   $query = $this->db->get('two_qr_code_genrater');
   return $query->result_array();
  }
   public function selectAllById_reset()
  {      
   $this->db->select('*');
   $this->db->where('unique_id !=','');
//   $this->db->where('pdf ','0');
   $this->db->order_by('id','asc');
   $query = $this->db->get('reset_qr_code_genrater');
   return $query->result_array();
  }
   public function selectAllById_infinte()
  {      
   $this->db->select('*');
   $this->db->where('unique_id !=','');
//   $this->db->where('pdf ','0');
   $this->db->order_by('id','asc');
   $query = $this->db->get('infinite_qr_code_genrater');
   return $query->result_array();
  }
//.........................................//

}


?>