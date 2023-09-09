<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RAdmin_Model extends CI_Model {
  
  public function selectAllData($table,$order_by){
   $this->db->select('*');
   $this->db->order_by($order_by);
   $this->db->from($table);
   $query = $this ->db-> get();    
   return $query->result_array();
  }

  /*
  !get notification 
  */ 
public function get_notifications($restaurant_id)
{
  $this->db->select('users.name,
                     notification.table_number,
                     notification.message,
                     notification.date_time');
  $this->db->from('notification');
  $this->db->join('users', 'users.id = notification.user_id');
  $this->db->where('notification.restaurant_id',$restaurant_id);
  $query = $this->db->get();
  return $query->result_array();
}
  

  public function selectAllCategory($type)
  {      
   $query = "SELECT id, type,name, al_non_al_type FROM categories WHERE type ='$type' OR type='3'"; 
    $query1 = $this->db->query($query);    
    return $query1->result_array();
  }



  // public function select_menus($id){
  //  $query_str = "SELECT menues.*,
  //   CONCAT('".base_url("assets/uploaded/menues/")."',menues.image) as image,
  //   categories.name as category_name
  //   FROM  menues
  //   INNER JOIN categories ON menues.category_id = categories.id
  //   WHERE menues.restaurant_id = '{$id}'";

  //  $query = $this->db->query($query_str);    
  //  return $query->result_array();
  // }


  public function select_menus($id){
     $query_str = "SELECT menues.*,
     menues.image as image,
      categories.name as category_name
      FROM  menues
      INNER JOIN categories ON menues.category_id = categories.id
      WHERE menues.restaurant_id = '{$id}'";

     $query = $this->db->query($query_str);    
     return $query->result_array();
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

   public function check_is_complete($data)
  {
    $query = $this->db->get_where('orders', array('order_id' => $data['order_id'],'payment_status'=> $data['payment_status']));
    if(count($query->result()))
    {
      return true;

    }else{
      return false;
    }
  }


  function insertAllData($table,$userdata)
  { 
    $this->db->insert($table, $userdata);
    $insert_id = $this->db->insert_id();
    // echo $this->db->last_query();
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
    // echo $this->db->last_query();exit();
    return $query->row();
  }


  public function singleRowdata($where_data,$table){
    $this->db->where($where_data);
    $query=$this->db->get($table);
    return $query->row();
  } 



  public function delete_menu($id){
     $result = $this->db->delete('menues', array('id' => $id)); 
     if($result)
     {
      return true;
     }else{
      return false;
     }
  }

  public function createData($data,$table){
    $this->db->set($data);
    $insertData = $this->db->insert($table);
    if($insertData){
      return TRUE;
    }else{
      return FALSE;
    }
  }

  public function select_orders($table,$wheredata){
    $query = "SELECT  menues.name,
                      users.name as user_name,
                      orders.quantity,
                      orders.price,
                      orders.total_price,
                      orders.table_number,
                      orders.promo_discount,
                      orders.order_status,
                      CASE
                        WHEN categories.al_non_al_type = 'Alcoholic' THEN ROUND( orders.total_price * 5.0 / 100, 2)   
                      END AS vat,
                      CASE
                        WHEN categories.al_non_al_type = 'Non Alcoholic' THEN ROUND( orders.total_price * 5.0 / 100, 2)  
                      END AS gst
              FROM  orders 
              INNER JOIN users ON  orders.user_id = users.id
              INNER JOIN menues ON  orders.menues_id = menues.id
              INNER JOIN categories ON  menues.category_id = categories.id
              WHERE orders.order_id='{$wheredata['order_id']}' AND orders.order_status !='Cancelled'";
    $result = $this->db->query($query);
    return $result->result_array();
    }

    public function select($table,$order_by=""){

    $this->db->select('*');

    $this->db->order_by($order_by);

    $this->db->from($table);

    $query = $this->db->get();

    return $query->result_array();

  }

  public function selectAllById($table,$wheredata){

    $this->db->select('*');

    $this->db->from($table);

    $this->db->where($wheredata);

      $query = $this->db->get();

     return $query->result_array();

  }
  
  //order list
  public function get_orders($restaurant_id)
  {
    $this->db->select('orders.order_id,
                       orders.table_number,
                       users.name as user_name,
                       DATE_FORMAT(orders.date_time,"%r") as time 
                       ');
    $this->db->select_sum('orders.total_price');
    $this->db->from('orders');
    $this->db->join('menues', 'menues.id = orders.menues_id');
    $this->db->join('users', 'users.id = orders.user_id');
    $this->db->where('menues.restaurant_id',$restaurant_id);
    $this->db->where('orders.order_status','Pending');
    $this->db->where('orders.item_status','New');
    $this->db->group_by('orders.order_id');
    $this->db->order_by('orders.id', 'DESC');
    $query = $this->db->get();
    return $query->result();
  }

  // prepared order(accept  by admin)
  public function get_prepaired_orders($restaurant_id)
  {
    $this->db->select('orders.order_id,
                       orders.table_number,
                       orders.order_status,
                       users.name as user_name,
                       DATE_FORMAT(orders.date_time,"%r") as time 
                       ');
    $this->db->select_sum('orders.total_price');
    $this->db->from('orders');
    $this->db->join('menues', 'menues.id = orders.menues_id');
    $this->db->join('users', 'users.id = orders.user_id');
    $this->db->where('menues.restaurant_id',$restaurant_id);
    $this->db->where('orders.order_status','Preparing');
    $this->db->where('orders.item_status','Preparing');
    $this->db->group_by('orders.order_id');
    $this->db->order_by('orders.id', 'DESC');
    $query = $this->db->get();
    return $query->result();
  }

  // order detail after preparing
  public function get_order_item($order_id)
  {
    $this->db->select('orders.id,
                        menues.name,
                        orders.order_status,
                        orders.item_status,
                        orders.quantity,
                        orders.total_price
                      ');
    $this->db->from('orders');
    $this->db->join('menues', 'menues.id = orders.menues_id');
    $this->db->where('orders.order_status','Preparing');
    $this->db->where('orders.order_id',$order_id);
    $query = $this->db->get();
    return $query->result_array();
  }

  //order complete by superadmin
  public function get_complete_orders($restaurant_id)
  {
    $this->db->select('orders.order_id,
                       orders.table_number,
                       orders.table_number,
                       users.name as user_name,
                       DATE_FORMAT(orders.date_time,"%m %d %Y") as date, 
                       COUNT(orders.menues_id) as total_item 
                       ');
    $this->db->select_sum('orders.total_price');
    $this->db->from('orders');
    $this->db->join('menues', 'menues.id = orders.menues_id');
    $this->db->join('users', 'users.id = orders.user_id');
    $this->db->where('menues.restaurant_id',$restaurant_id);
    $this->db->where('orders.order_status','Finish');
    $this->db->group_by('orders.order_id');
    $this->db->order_by('orders.id', 'DESC');
    $query = $this->db->get();
    return $query->result();
  } 

  // all summary of orders
  public function sum_order($restaurant_id,$start_date,$end_date){
    $this->db->select('orders.order_id,
                       orders.table_number,
                       orders.table_number,
                       users.name as user_name,
                       DATE_FORMAT(orders.date_time,"%m %d %Y") as date,  
                       ');
    $this->db->select_sum('orders.total_price');
    $this->db->from('orders');
    $this->db->join('menues', 'menues.id = orders.menues_id');
    $this->db->join('users', 'users.id = orders.user_id');
    $this->db->where('menues.restaurant_id',$restaurant_id);
    $this->db->where('date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
    $this->db->where('orders.order_status','Finish');
    $this->db->group_by('orders.order_id');
    $this->db->order_by('orders.id', 'DESC');
    $query = $this->db->get();
    return $query->result();
  }

  


  // order detail
  public function get_order_items($order_id)
  {
    $this->db->select('orders.id,
                        menues.name,
                       orders.item_status,
                       orders.quantity,
                       orders.total_price
                      ');
    $this->db->from('orders');
    $this->db->join('menues', 'menues.id = orders.menues_id');
    $this->db->where('orders.order_id',$order_id);
    $this->db->where('orders.item_status','New');
    $query = $this->db->get();
    return $query->result_array();
  }
  
  public function update($wheredata,$table,$data){

    $query = $this->db->where($wheredata);

    $query = $this->db->update($table,$data);

    return $query;
  }

  public function updateMenus($table,$data,$wheredata){

    $this->db->where($wheredata);

    $updateData=$this->db->update($table,$data);

    // echo $this->db->last_query();

    if($updateData){

      return $updateData;

    }else{

      return false;

    }

  }


  //insert 
  public function add($tableName, $data) {
      $this->db->insert($tableName, $data);
    
      return $this->db->insert_id();
  }

  public function getAll($table,$order_by=""){
    $this->db->select('*');
    $this->db->order_by($order_by);
    $this->db->from($table);
    $query = $this->db->get();
    return $query->result_array();
  }

  // public function getOrders($table_number=''){
  //   $this->db->select('*');
  //   $this->db->from('orders o'); 
  //   $this->db->join('users u', 'u.id=o.user_id','left');
  //   $this->db->join('menues m', 'm.id=o.menues_id','left');
  //   $this->db->where('o.table_number',$table_number);
  //  $this->db->last_query();
  //   $query = $this->db->get(); 
  //   if($query->num_rows() != 0)
  //   {
  //       return $query->result_array();
  //   }
  //   else
  //   {
  //       return false;
  //   }
  // }


  // public function get_ord($table_number=''){
  //   $this->db->select('orders.id, orders.order_id, orders.table_number,orders.quantity, orders.menues_id, orders.price,orders.total_price,users.id,users.name');
  //   $this->db->from('users');
  //   $this->db->join('menues','menues.id = orders.menues_id');
  //   $this->db->join('users',  'users.id = orders.user_id');       
  //   if($table_number !=''){        
  //     $this->db->where('orders.table_number', $table_number); 
  //   }    
  //   $this->db->where('orders.table_number', $table_number);
  //     return $this->db->get()->result();
  //   }

  //   public function orderbyTableNo($table_number){
  //     $this->db->select('*');    
  //     $this->db->from('orders');
  //     $this->db->join('menues', 'orders.menues_id = menues.id');
  //     $this->db->join('users', 'orders.user_id = users.id');
  //     $this->db->where('orders.table_number',$table_number); 
  //     $query = $this->db->get();
  //   }  
 }
?>