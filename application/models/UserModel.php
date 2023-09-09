<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class UserModel extends CI_Model {

  

  public function selectAllData($table,$order_by){

   $this->db->select('*');

   $this->db->order_by($order_by);

   $this->db->from($table);

   $query = $this ->db-> get();    

   return $query->result_array();

  }
  public function get_last_order_id() 
  {
    $this->db->select('order_id');
    $query = $this->db->get('orders');
    return  $query->last_row();
  }
  public function delete_from_cart($user_id) 
  {
    $this->db->where('user_id', $user_id);
    $this->db->delete('tbl_cart');
  } 

  public function get_order_id($table_id, $user_id) 
  {
    $query = "SELECT order_id FROM orders WHERE table_number = '{$table_id}' AND user_id='{$user_id}' AND (order_status ='Pending' OR order_status='Preparing')"; 
    $result = $this->db->query($query);
    return $result->last_row();
  }


    public function selectAllById($table,$wheredata){
    $this->db->select('*');
    $this->db->from($table);
    $this->db->where($wheredata);
    $query = $this->db->get();
    // echo $this->db->last_query(); exit();
    return $query->result_array();
  }

    public function select_orders($table,$wheredata){
    $query = "SELECT  menues.name,
                      menues.restaurant_id,
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
                        WHEN categories.al_non_al_type = 'Non Alcoholic' THEN ROUND(orders.total_price * 5.0 / 100, 2)   
                      END AS gst
              FROM  orders 
              INNER JOIN menues ON  orders.menues_id = menues.id
              INNER JOIN categories ON  menues.category_id = categories.id
              INNER JOIN users ON  orders.user_id = users.id
              WHERE orders.user_id = '{$wheredata['user_id']}' AND orders.order_id='{$wheredata['order_id']}' AND orders.order_status !='Cancelled'";
    $result = $this->db->query($query);
    return $result->result_array();
    }

    public function get_near_by_restaurant($lat,$lng)
    {
      $sql = ("SELECT *, 3956 * 2 * ASIN(SQRT(POWER(SIN(('$lat' -abs(dest.lat)) * pi()/180 / 2),2) + COS('$lat' * pi()/180 ) * COS(abs(dest.lat) *  pi()/180) * POWER(SIN(('$lng' - abs(dest.lng)) *  pi()/180 / 2), 2))
      )*1.60934 as distance 
      FROM restaurants as dest
      having distance < 25
      ORDER BY distance");
      $get = $this->db->query($sql);
      return $get->result_array();
    }

    public function getWhere($table,$where){
      $this->db->select('*'); 
        $this->db->from($table);
      $this->db->where($where);
      $query = $this->db->get();
       return $query->result();
        // return $query->result_array();
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
  public function is_exist_item_in_cart($user_id,$item_id)
  {
    $query = $this->db->get_where('tbl_cart', array('user_id' => $user_id,'menues_id'=>$item_id));
    if($query->row())
    {
      return $query->row()->quantity;
    }else{
      return "";
    }
  }  
  public function getCateId($name)
  {
    $query = $this->db->get_where('categories', array('name' => $name));
    if($query->row())
    {
      return $query->row()->id;
    }else{
      return "";
    }
  }

  public function chech_restaurant($user_id,$menues_id)
  {
     $query = "SELECT  
                menues.restaurant_id,
              FROM  tbl_cart 
              INNER JOIN menues ON  tbl_cart.menues_id = menues.id
              WHERE tbl_cart.user_id = '{$user_id}' AND tbl_cart.menues_id='{$menues_id}'";
    $result = $this->db->query($query);
    $new_res =  $result->row()->restaurant_id;

    $query = $this->db->get_where($tbl, array($key => $value));
    if(count($query->result()))
    {
      return true;
    }else{
      return false;
    }
  }

  public function is_check_restaurant($promoCode,$order_id)
  {
    // get restaurant id 
    $this->db->select('menues.restaurant_id as restaurant_id');
    $this->db->from('orders');
    $this->db->join('menues', 'menues.id = orders.menues_id');
    $this->db->where('orders.order_id',$order_id);
    $query = $this->db->get();
    $restaurant_id = $query->row()->restaurant_id;
    $this->db->select('tbl_promocode.id as promocode_id');
    $this->db->from('tbl_promocode');
    $this->db->where('tbl_promocode.promoCode',$promoCode);
    $query1 = $this->db->get();
    $promocode_id = $query1->row()->promocode_id;

    $query12 = $this->db->get_where('restaurants_promocode', array('restaurant_id' => $restaurant_id,'promocode_id'=> $promocode_id
  ));
    if(count($query12->result()))
    {
      return true;
    }else{
      return false;
    }
  }
  public function user_already_used_promo($id,$promo)
  {

    $query = $this->db->get_where('orders', array('user_id' => $id,'promoCode' => $promo));

    if(count($query->result()))
    {
      return true;
    }else{
      return false;
    }
  }



  public function check_credentials($mobile)

  {

    $query = $this->db->get_where("users", array("mobile" => $mobile));

    if(count($query->result()))

    {

      return $query->row();



    }else{

      return array();

    }

  }

   public function singleRowdata($where_data,$table){
    $this->db->where($where_data);

    $query=$this->db->get($table);

    return $query->row();

  }  
   public function get_convenience_fees(){
    $query = $this->db->get('convenience_fees');  
    return $query->row()->amount;
  }

  public function check_credentials1($user_name)

  {

    $query = $this->db->get_where("restaurants", array("user_name" => $user_name));

    if(count($query->result()))

    {

      return $query->row();



    }else{

      return array();

    }

  }

  public function get_orders($user_id)
  {
    $this->db->select('orders.order_id,
                       orders.table_number,
                       users.id as user_id ,
                       DATE_FORMAT(orders.date_time,"%r") as time,
                       DATE_FORMAT(orders.date,"%r") as date 
                       ');
    $this->db->select_sum('orders.total_price');
    $this->db->from('orders');
    $this->db->join('menues', 'menues.id = orders.menues_id');
    $this->db->join('users', 'users.id = orders.user_id');
    $this->db->where('orders.user_id',$user_id);
    $this->db->where('orders.order_status !=','Cancelled');
    $this->db->group_by('orders.order_id');
    $this->db->order_by('orders.id', 'DESC');
    $query = $this->db->get();
    return $query->result();
  }
  // public function get_complete_orders($restaurant_id)
  // {
  //   $this->db->select('orders.order_id,
  //                      orders.table_number,
  //                      orders.table_number,
  //                      users.name as user_name,
  //                      DATE_FORMAT(orders.date_time,"%m %d %Y") as date, 
  //                      COUNT(orders.menues_id) as total_item 
  //                      ');
  //   $this->db->select_sum('orders.total_price');
  //   $this->db->from('orders');
  //   $this->db->join('menues', 'menues.id = orders.menues_id');
  //   $this->db->join('users', 'users.id = orders.user_id');
  //   $this->db->where('menues.restaurant_id',$restaurant_id);
  //   $this->db->where('orders.order_status','Complete');
  //   $this->db->group_by('orders.order_id');
  //   $this->db->order_by('orders.id', 'DESC');
  //   $query = $this->db->get();
  //   return $query->result();
  // } 
  // public function get_order_items($order_id)
  // {
  //   $this->db->select('orders.id,
  //                       menues.name,
  //                      orders.item_status,
  //                      orders.quantity,
  //                      orders.total_price
  //                     ');
  //   $this->db->from('orders');
  //   $this->db->join('menues', 'menues.id = orders.menues_id');
  //   $this->db->where('orders.order_id',$order_id);
  //   $query = $this->db->get();
  //   return $query->result_array();
  // }


  function insertAllData($table,$userdata)
  { 
    $this->db->insert($table, $userdata);
    $insert_id = $this->db->insert_id();
    return  $insert_id;
  }



  function updateData($table,$data,$id)

  { 

    $this->db->where('id', $id);

    $this->db->update($table, $data);

    return true;

  }

  function cart_update($user_id,$quantity,$menues_id)
  { 
    if($quantity <= 0)
    {
      $query = $this->db->delete('tbl_cart', array('user_id' => $user_id,'menues_id'=>$menues_id));
      if($query)
      {
        return true;
      }else{
        return false;
      }
    }else
    {
      $query = $this->db->query("UPDATE tbl_cart SET quantity = quantity+1 WHERE user_id='$user_id' AND menues_id='$menues_id'"); 
      if($query)
      {
        return true;
      }else{
        return false;
      }
    }
  } 

  function cart_update1($user_id,$quantity,$menues_id)
  { 
    if($quantity  <= 0)
    {
      $query = $this->db->delete('tbl_cart', array('user_id' => $user_id,'menues_id'=>$menues_id));
      if($query)
      {
        return true;
      }else{
        return false;
      }
    }else
    {
      $query = $this->db->query("UPDATE tbl_cart SET quantity = quantity - 1 WHERE user_id='$user_id' AND menues_id='$menues_id'"); 
      if($query)
      {
        return true;
      }else{
        return false;
      }
    }
  }

    public function update($wheredata,$table,$data){
    $query = $this->db->where($wheredata);
    $query = $this->db->update($table,$data); 
    return $query;
  
  }



  public function select_single_row($table,$key,$value) 

  {

    $this->db->select('*');

    $this->db->from($table);

    $this->db->where($key,$value);

    $query = $this->db->get();

    return $query->row();

  }


  public function selectAllByLat_Lon($table,$order_by=""){
      $this->db->select('*');
    $this->db->order_by($order_by);
    $this->db->from($table);
    $query = $this->db->get();
    return $query->result_array();
  }



    public function select($table,$order_by=""){

    $this->db->select('*');

    $this->db->order_by($order_by);

    $this->db->from($table);

    $query = $this->db->get();

    return $query->result_array();

  }

    public function select_menus($id){
   $query_str = "SELECT menues.*,
    CONCAT('".base_url("assets/uploaded/menues/")."',menues.image) as image,
    categories.name as category_name
    FROM  menues
    INNER JOIN categories ON menues.category_id = categories.id
    WHERE menues.restaurant_id = '{$id}'";

   $query = $this->db->query($query_str);    
   return $query->result_array();
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

  public function get_row($tableName, $colName, $id){
      $this->db->where($colName, $id);
      $result = $this->db->get($tableName);
      if ($result->num_rows() > 0) {
          return $result->row();
      }
      else
       return FALSE;
  }

  public function deleterec($wheredata,$tbl){
    $query = $this->db->where($wheredata);
    $query = $this->db->delete($tbl);
    return $query;
  }


  public function insert($table,$data)
  {   $this->db->insert($table, $data);
       $insert_id = $this->db->insert_id();
    return  $insert_id;
      
  }  

   public function get_category($restaurant_id,$type)
  {
    if($type!="")
    {
      $query12="SELECT 
                  categories.id,
                  categories.name
                FROM menues
                INNER JOIN categories ON menues.category_id = categories.id 
                WHERE menues.restaurant_id = $restaurant_id AND menues.type ='$type' OR type = '3' GROUP BY  categories.name order BY id DESC";
      $query2 = $this->db->query($query12);
      return $query2->result_array();
    }else
    {   
      $query = "SELECT 
                  categories.id,
                  categories.name
                FROM menues
                INNER JOIN categories ON menues.category_id = categories.id 
                WHERE menues.restaurant_id = $restaurant_id GROUP BY  categories.name order BY id DESC";
      $query1 = $this->db->query($query);
      return $query1->result_array();
    }
  }

}

?>