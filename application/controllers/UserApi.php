<?php

if(!defined('BASEPATH')) exit ('No direct script access allowed');
require_once ('vendor/autoload.php');
class UserApi extends CI_Controller

{
  public function __construct(){
    parent::__construct();
    $this->load->model('UserModel');
    date_default_timezone_set('Asia/Calcutta');
  }

  private function hash_password($password)
  {
    return password_hash($password, PASSWORD_DEFAULT);
  } 

  public function singUp()
  {

    $post_data = (array) $this->input->post();

    $post_data['name'] = $this->input->post('name');

    $post_data['lat'] = $this->input->post('lat');

    $post_data['lng'] = $this->input->post('lng');

    $post_data['address'] = $this->input->post('address');
    $post_data['image'] = 'icon.png';

    $post_data['password'] = $this->hash_password($this->input->post('password')); 
   
    if(! $this->UserModel->is_record_exist('users','mobile', $post_data['mobile']) )

    {

      $result = $this->UserModel->insertAllData('users',$post_data);

      if($result)

      {

        $data['result'] = 'true';

        $data['data'] = $this->UserModel->select_single_row('users','id',$result);

        $data['msg']    = 'Successfully register with us.';

      }else{

        $data['result'] = 'false';

        $data['msg']    = 'Sorry Something went wrong.';

      }

    }else{

      $data['result'] = 'false';

      $data['msg']    = 'Sorry Mobile Number already exists!';

    }

    echo json_encode($data);
  }



  public function singIn()
  {   

    extract($_POST);

    if(isset($mobile) && isset($password))
    {

      $result =  $this->UserModel->check_credentials($this->input->post('mobile'));

      if($result)
      {

        if (password_verify($this->input->post('password'), $result->password)) 
        {

          if($result->status!="Inactive")
          {
            $data['result'] = 'true';
            $data['data']   = $result;
            $data['msg']    = 'Success';

          }else{
            $data['result'] = 'false';
            $data['msg']    = 'Your account currently Inactive';            
          }

        }else{
          $data['result'] = 'false';
          $data['msg']    = 'Invalid Password';
        }

      }else{
        $data['result'] = 'false';
        $data['msg']    = 'Invalid Mobile Number';
      }  

    }else{
      $data['result'] = 'false';
      $data['msg']    = 'Please provide parameters(mobile,password)';            
    }          
    echo json_encode($data);
  }
  
  //get restaurant list
  public function restaurant_List() {
    extract($_GET);
    if(isset($lat) && isset($lng))
    {
      $result=$this->UserModel->get_near_by_restaurant($lat,$lng);
      if($result){
        foreach ($result as $key => $res){
          $arr[] = array(
              "id"    => $res['id'],
              "restaurant_name"  => $res['restaurant_name'],
              "mobile"  => $res['mobile'],
              "descriptin"       => $res['descriptin'],
              "location"         => $res['location'],
              "lat"              => $res['lat'],
              "lng"              => $res['lng'],
              "image"            => $res['image'],            
              "openTime"         => $res['openTime'],
              "closeTime"        => $res['closeTime']
          );        
        } 
          $data_result['result'] = 'true';
          $data_result['data']   = $arr;
          $data_result['msg']    = 'Show restaurant List!';
      }else{
        $data_result['result'] = 'false';
        $data_result['msg']    = 'Sorry Does not exit restaurant!'; 
      }
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give parameters(lat,lng)';
    }
    echo json_encode($data_result);
  }


  public function bannerImage_List() {
    $result=$this->UserModel->select('banner_list');
    if($result){
      foreach ($result as $key => $res) {
        $arr[] = array(
          "banner_id"        => $res['banner_id'],
          // "image"            => base_url().'images/banner/'.$res['image'],
          "image"            => $res['image']
        );
      } 
        $data_result['result'] = 'true';
        $data_result['data']   = $arr;
        $data_result['msg']    = 'Show banner image List!'; 

    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Sorry Does not exit banner image !'; 
    }

    echo json_encode($data_result);
  }


  public function getNearByPlace()
  {       
   // echo "string";die;    
    $lat = $this->input->get_post('lat') ;
    $lng = $this->input->get_post('lng') ;
    // $user_id = $this->input->get_post('user_id', TRUE) ;
    // $dist = 100;
   
    $sql=("SELECT *, 3956 * 2 * ASIN(SQRT(POWER(SIN(('$lat' -abs(dest.lat)) * pi()/180 / 2),2) + COS('$lat' * pi()/180 ) * COS(abs(dest.lat) *  pi()/180) * POWER(SIN(('$lng' - abs(dest.lng)) *  pi()/180 / 2), 2))
      )*1.60934 as distance 
      FROM restaurants as dest
      having distance < '100'
      ORDER BY distance");

    
      $get= $this->db->query($sql)->result();
       // print_r($get);exit();
      
              if($get    = $this->UserModel->selectAllByLat_Lon('restaurants','id desc')){
                foreach ($get as $key => $value) {

                  $data[] =  array(
                    'id'                =>$value['id'], 
                    'restaurant_name'   =>$value['restaurant_name'],
                    'descriptin'        =>$value['descriptin'],  
                    'location'          =>$value['location'], 
                    'lat'               =>$value['lat'], 
                    'lng'               =>$value['lng'],
                     // "image"            => base_url().'images/banner/'.$value['image'],
                     // "image"            => base_url().'assets/uploaded/banner/'.$value['image'],
                     "image"            => $value['image'],
                    "openTime"          => $value['openTime'],
                    "closeTime"         => $value['closeTime']
                                  
                  );
                }

                // print_r($data);exit();
                
      $data_result['result'] = 'true';
      $data_result['data']   = $data;
      $data_result['msg']    = 'Search All restaurants!'; 
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Sorry Not Search Any restaurants!'; 
    }

    echo json_encode($data_result);   
  }



  public function Search_restaurant(){
    $search = $this->input->get_post('search');
    $result = $this->db->query("SELECT * FROM restaurants  
    WHERE location LIKE '%$search%' OR restaurant_name LIKE '%$search%'")->result_array();
    if ($result) {
      $result1= $this->UserModel->select('restaurants ','id desc');
      if($result1){
        foreach ($result as $user){
          $arr[] = array(
            "id"                =>$user['id'],
            "restaurant_name"   =>$user['restaurant_name'],
            'descriptin'        =>$user['descriptin'], 
            "location"          =>$user['location'],
            "lat"               =>$user['lat'],
            "lng"               =>$user['lng'],
            "mobile"            =>$user['mobile'],
            "image"             =>$user['image'],
          );
        }
        $data_result['result'] = 'true';
        $data_result['data']   = $arr;
        $data_result['msg']    = 'Search All restaurant!'; 
      }
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Sorry Not Search Any restaurant!'; 
    }

    echo json_encode($data_result);
  }




  // show Doctor profile by cat_id
  public function UserprofileDetails() {
    $wheredata=array(
      'id'=>$this->input->post('id')
    ); 
    $result=$this->UserModel->selectAllById('users',$wheredata);

    if($result){
      foreach ($result as $key => $r) {
        $arr[] = array(

          "id"           =>$r['id'],
          "name"        =>$r['name'],
          "email"        =>$r['email'],
          "mobile"      =>$r['mobile'],
          "image"            => base_url().'images/user/'.$r['image']
        );           
      }
      $data_result['result'] = 'true';
      $data_result['data']   = $arr;
      $data_result['msg']    = 'Show user profile!'; 
    }
    else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Sorry no show user profile!'; 
    }
     echo json_encode($data_result);
  }

  //manu category
  // public function menuCategory() {
    //   $restaurant_id = $this->input->get_post('restaurant_id');
    //   $type   = $this->input->get_post('type');
    //   $result = $this->db->query("SELECT 
    //                               categories.id,
    //                               categories.name
    //                          FROM menues
    //                          INNER JOIN categories ON menues.category_id = categories.id 
    //                          WHERE menues.restaurant_id = $restaurant_id AND menues.type ='$type' GROUP BY  categories.name")->result_array();

    //   if($result){
    //     $data_result['result'] = 'true';
    //     $data_result['data']   = $result;
    //     $data_result['msg']    = 'Show all manu List!'; 
    //   }else{
    //     $data_result['result'] = 'false';
    //     $data_result['msg']    = 'Sorry Does not exit all manu list !'; 
    //   }
    //     echo json_encode($data_result);
  // }

  // get menues at restro
   public function menuCategory(){
    $restaurant_id = $this->input->get_post('restaurant_id');
    $type   = $this->input->get_post('type');
      // extract($_POST);
      $result = $this->UserModel->get_category($restaurant_id,$type); 
      if(count($result))
      {
        $data_result['result']='true';
        $data_result[',msg']='Show all manu List!';
        $data_result['data']= $result;
      }else
      {
        $data_result['result'] ='false';
        $data_result['msg']    = 'Sorry Does not exit all manu list !'; 
        $data_result['data']   =  '';
      }
      echo json_encode($data_result);       
    }


 


  public function Item_List() {

    $restaurant_id = $this->input->get_post('restaurant_id');
    $category_id   = $this->input->get_post('category_id');
    $category_id  = $this->UserModel->getCateId($category_id); 

    $type          = $this->input->get_post('type');
    $user_id          = $this->input->get_post('user_id');
    
    $wheredata = array(
      'restaurant_id' => $restaurant_id,
      'category_id'   => $category_id,
      'type'          => $type
    );
    $wheredata1 = array(
      'restaurant_id' => $restaurant_id,
      'category_id'   => $category_id     
    );
    $wheredata2 = array(
      'restaurant_id' => $restaurant_id,
      'type'          => $type    
    );
    $wheredata4 = array(
      'restaurant_id' => $restaurant_id       
    );
    $result=$this->UserModel->selectAllById('menues',$wheredata);
 
    if(empty($type)){
      $result=$this->UserModel->selectAllById('menues',$wheredata1);
    }
    if(empty($category_id)){
     $result=$this->UserModel->selectAllById('menues',$wheredata2);
    }
    if(empty($type) && empty($category_id)){
      $result=$this->UserModel->selectAllById('menues',$wheredata4);
    }
    if($result){
      foreach ($result as $key => $res) {
        $arr[] = array(
          "id"             => $res['id'],              
          "restaurant_id"  => $res['restaurant_id'],
          "category_id"    => $res['category_id'],
          "name"           => $res['name'],
          "price"          => $res['price'],
          "offer_price"    => $res['offer_price'],
          "type"           => $res['type'],
          "image"          => $res['image'],
          "description"    => $res['description'],
          "stock_status"    => $res['stock_status'],
          "quantity"    => $this->UserModel->is_exist_item_in_cart($user_id,$res['id'])
        );
      } 
      $data_result['result'] = 'true';
      $data_result['data']   = $arr;
      $data_result['msg']    = 'Show all item List!'; 
    }
    else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Sorry Does not exit all item !'; 
    }
    echo json_encode($data_result);
  }


  public function Add_to_Cart(){
    $user_id         = $this->input->post('user_id');
    $category_id     = $this->input->post('category_id');
    $menues_id     = $this->input->post('menues_id');
    if(isset($user_id) && isset($category_id) && isset($menues_id))
    {
      $data= array(
          'user_id'       =>$user_id,
          'category_id'   =>$category_id,
          'menues_id'     =>$menues_id,
          'quantity'     =>1,
      );
      $res_check=$this->db->query("SELECT * FROM `tbl_cart` WHERE `user_id`='$user_id' AND `menues_id`='$menues_id' ");

      if($res_check->num_rows()>0){
          $data_result['result'] ='false';
          $data_result['msg']    ='Already Added this Menue';
      }else
      {
         $result = $this->UserModel->createData($data,'tbl_cart');
        if($result){
          $data_result['result'] = 'true';
          $data_result['data']   =$data;
          $data_result['msg']    = 'Add to cart successfully!';               
          }else{
            $data_result['result'] = 'false';
            $data_result['msg']    = 'Sorry Not Add.!';
          }
        }
    }else{
        $data_result['result'] = 'false';
        $data_result['msg']    = 'Please Parameter Required(user_id,category_id,menues_id)!!';
    }
         
      echo json_encode($data_result);
  }

  public function add_quantity_in_cart(){
    $user_id         = $this->input->post('user_id');
    $quantity     = $this->input->post('quantity');
    $menues_id     = $this->input->post('menues_id');
    if(isset($user_id) && isset($quantity) && isset($menues_id))
    {
      $update_cart = $this->UserModel->cart_update($user_id,$quantity,$menues_id);
      if($update_cart)
      {
        $data_result['result'] = 'true';
        $data_result['msg']    = 'success';  
      }else{
          $data_result['result'] = 'false';
          $data_result['msg']    = 'unsuccess'; 
      }
    }else{
        $data_result['result'] = 'false';
        $data_result['msg']    = 'Please Parameter Required(user_id,menues_id,quantity )!!';
    }  
      echo json_encode($data_result);
  }
  public function remove_quantity_in_cart(){
    $user_id         = $this->input->post('user_id');
    $quantity     = $this->input->post('quantity');
    $menues_id     = $this->input->post('menues_id');
    if(isset($user_id) && isset($quantity) && isset($menues_id))
    {
      $update_cart = $this->UserModel->cart_update1($user_id,$quantity,$menues_id);
      if($update_cart)
      {
        $data_result['result'] = 'true';
        $data_result['msg']    = 'success';  
      }else{
          $data_result['result'] = 'false';
          $data_result['msg']    = 'unsuccess'; 
      }
    }else{
        $data_result['result'] = 'false';
        $data_result['msg']    = 'Please Parameter Required(user_id,menues_id,quantity )!!';
    }  
      echo json_encode($data_result);
  }


  public function Cart_List() {
    $user_id = $this->input->get_post('user_id');
     if(isset($user_id))
    {

    $result = $this->db->query("SELECT 
                                tbl_cart.id,
                                tbl_cart.menues_id,
                                tbl_cart.user_id,
                                tbl_cart.quantity,
                                menues.category_id,
                                menues.name,
                                menues.price,
                                menues.offer_price,
                                menues.type,
                                menues.image,
                                menues.create_date,
                                menues.stock_status,
                               (menues.price * tbl_cart.quantity) as total 

                             FROM tbl_cart INNER JOIN  menues ON menues.id = tbl_cart.menues_id 
                             WHERE tbl_cart.user_id = '$user_id'")->result_array();

    
    if($result){
      $data_result['result'] = 'true';
      $data_result['data']   = $result;
      $data_result['msg']    = 'Show all item List!'; 
    }else{
        $data_result['result'] = 'false';
        $data_result['msg']    = 'Sorry Does not exit all item!'; 
      }
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please Parameter user_id Required!'; 
    }

     echo json_encode($data_result);

  }

  public function Delete_Cart_Menues(){
    $user_id=$this->input->post('user_id');
    $cart_menu_id=$this->input->post('cart_menu_id');
    
    if(isset($user_id) &&isset($cart_menu_id))
    {
      $wheredata =array(
       'id'     =>$cart_menu_id,
       'user_id'=>$user_id
      );
      $result=$this->UserModel->deleterec($wheredata,'tbl_cart');
      if($result){
        $data_result['result'] ='true';
        $data_result['data']   = $result;
        $data_result['msg']    ='Item Remove From Cart';

      }else{
        $data_result['result'] ='false';
        $data_result['msg']    ='Sorry Item Not Remove From Cart';
      }
    }else{
      $data_result[]='false';
      $data_result[]='Please Parameter Required(user_id,cart_menu_id)!!';
    }
    echo json_encode($data_result);
  }


  public function Google_Login()
  {      
    $email        = $this->input->post('email');
    $name         = $this->input->post('name');
    $mobile       = $this->input->post('mobile');
    $lat          = $this->input->post('lat');
    $lng          = $this->input->post('lng');
    $fcm_id          = $this->input->post('fcm_id');
    $social_id          = $this->input->post('social_id');
    $wheredata = array(    
      'email'  =>$email                         
    );
    $res = $this->UserModel->selectAllById('users', $wheredata);
    if ($res){
      $data_result['result'] = 'true';
      $data_result['msg']    = 'Google Login successfully!';
      $data_result['data']   = $res;
    }else{
      $data = array( 
      
        'email'  =>$email,
        'name'   =>$name,
        'mobile' =>$mobile,           
        'social_id'=>$social_id,           
        'fcm_id' =>$fcm_id,           
        'lat'    =>$lat,           
        'lng'    =>$lng 
      );

    $result    = $this->UserModel->insert('users',$data);
    $wheredata = array(
      'id' => $this->db->insert_id()
    );
    $res1      = $this->UserModel->selectAllById('users', $wheredata);
            
    if($result){
        $data_result['result'] = 'true';
        $data_result['data']   = $res1;
        $data_result['msg']    = 'Google id insert successfully!';
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Your record not insert!';
    }}
    echo json_encode($data_result);
  }


  public function Apply_Coupon(){
    $currentdate = date('Y-m-d H:i:s');
    $date = date("Y-m-d H:i:s", strtotime($currentdate));
  	extract($_POST);
  	if(isset($user_id) && isset($order_id) && isset($sub_total) && isset($promoCode))
  	{
  		$check_promo = $this->UserModel->is_record_exist('tbl_promocode','promoCode', $promoCode);
  		if($check_promo)
  		{
  			$get_promo = $this->UserModel->select_single_row('tbl_promocode','promoCode', $promoCode);
  			if($get_promo)
  			{	
          $check_restaurant = $this->UserModel->is_check_restaurant($promoCode,$order_id);
          if($check_restaurant)
          { 
            $expiry = $get_promo->expiryDate;
             if($expiry!='')
             {
                if ($date < $expiry) {
                    $user_already_used = $this->UserModel->user_already_used_promo($user_id,$promoCode);
                    if(!$user_already_used)
                    {
                      $discount = $get_promo->promoCode_offer;
                      $discount_amt = $sub_total * $discount  / 100 ;
                      $update_data = array(
                        'promoCode'=>$promoCode,
                        'promo_discount'=>$discount_amt
                        );
                      $where = array(
                        'order_id'=> $order_id
                      );
                      $result = $this->UserModel->update($where,'orders',$update_data); 
                      if($result)
                      {
                        $data_result['result'] ='true';
                        $data_result['msg'] ='Successfully applied.';  
                      }
                    }else{
                      $data_result['result'] ='false';
                      $data_result['msg'] ='You have already used this code';
                    }
                }else{
                    $data_result['result'] ='false';
                    $data_result['msg'] ='Promo Code date expired';
                }
             }else
             {
              $user_already_used = $this->UserModel->user_already_used_promo($user_id,$promoCode);
              if(!$user_already_used)
              {
                $discount = $get_promo->promoCode_offer;
                $discount_amt = $sub_total * $discount  / 100 ;
                $update_data = array(
                  'promoCode'=>$promoCode,
                  'promo_discount'=>$discount_amt
                  );
                $where = array(
                  'order_id'=> $order_id
                );
                $result = $this->UserModel->update($where,'orders',$update_data); 
                if($result)
                {
                  $data_result['result'] ='true';
                  $data_result['msg'] ='Successfully applied.';  
                }
              }else{
                $data_result['result'] ='false';
                $data_result['msg'] ='You have already used this code';
              }
             }   
          }else{
            $data_result['result'] ='false';
            $data_result['msg'] ='This Promo Code not allowed on this restaurant';
          } 
  			}else{
  				$data_result['result'] ='false';
       		$data_result['msg'] ='Invalid Promo Code';
  			}
  		}else{
  			$data_result['result'] ='false';
       	$data_result['msg'] ='Invalid Promo Code';
  		}
  	}else{
  		$data_result['result'] ='false';
       	$data_result['msg'] ='Please give parameters(user_id,order_id,sub_total,promoCode)';
  	}
    echo json_encode($data_result);
  }


  function send_notification($data=array()){
    if(!isset($data['message']))
    {
        $data['message']='you have a new order';
    }
        
    if(!isset($data['fcm_id']))
    {
        $data['fcm_id']='xxsx';
    }
    if(!isset($data['type']))
    {
        $data['type']='xxsx';
    }
    $fields=array(
    "to"  => $data['fcm_id'],
    "notification"  => $data,
    "data"=> $data
    );
    $url = 'https://fcm.googleapis.com/fcm/send';
    $headers = array(
    'Authorization: key='.FIREBASEKEY,

    'Content-Type: application/json'
    );
    // Open connection
    $ch = curl_init();
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    // Execute post
    $result = curl_exec($ch);
    //print_r($result);die;
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    // Close connection
    curl_close($ch);
    return $result;
  }

  
  public function place_order(){
    $result = false;
    extract($_POST);
    $this->UserModel->delete_from_cart($user_id);
    $array = json_decode($passArray,true);
    $orderId_data = $this->UserModel->get_last_order_id();
    if($orderId_data)
    {
      $order_id = $orderId_data->order_id;
      $order_id++;                 
    }else{
      $order_id = "ORD-000001";
    }

    $select_data = $this->UserModel->get_order_id($table_id,$user_id);
    if($select_data)
    {
      $order_id = $select_data->order_id;
    }

    foreach ($array as $key => $val) 
    {
      $order_data['order_id']     = $order_id;       
      $order_data['table_number'] = $table_id;       
      $order_data['user_id']      = $user_id;            
      $order_data['date']      = date('Y-m-d');            
      $order_data['menues_id']    = $val['item_id'];       
      $order_data['quantity']     = $val['qty'];       
      $order_data['price']        = $val['price'];       
      $order_data['total_price']  = $val['item_total_price'];
      $done =  $this->UserModel->insertAllData('orders', $order_data); 
      if($done)
      {
        $result = true;
      }      
    } 

    $where = array(
      'order_id'=>$order_id
    );
    if($done){
      
               
      // if(!empty($done[0])){
      //   $notificationArray['fcm_id']   = $done[0]->fcm_id;
      //   $notificationArray['message']  ="".$order_get_data->table_id." ordered this";
      //   $insertNotification = array(
      //     'user_id'       =>$table_id,
      //     'menues_id'     =>$notificationArray['menues_id'],
      //     'message'       =>$notificationArray['message']
      //   ); 

      //   print_r($insertNotification);exit();
      //   if($order_get_data->user_id != $done[0]->table_id ){
      //     $this->UserModel->insert('notification', $insertNotification);           
      //     $this->send_notification($notificationArray);

      //   }
            
      // }

       $order_get_data = $this->UserModel->selectAllById('orders',$where);
      if($result){
         $data_result['result']   = 'true';
         $data_result['data']     = $order_get_data;
         $data_result['order_id'] = $order_id;
         $data_result['msg']      = 'data found'; 
      }else
      {
          $data_result['result']     = 'false';
          $data_result['data']       = $order_get_data;
          $data_result['order_id']   = $order_id;
          $data_result['msg']        = 'no data found'; 
      }
   }
    echo json_encode($data_result);
  }

  public function get_order_history()
  {
      extract($_POST);
      if(isset($user_id))
      {
        $this->result = $this->UserModel->get_orders($user_id);
        if(!empty($this->result))
        {
          // foreach ($this->result as $or_data) 
          // {
          //   $or_data->order_item = $this->RAdmin_Model->get_order_items($or_data->order_id);
          // }
          $data_result['data'] = $this->result;
          $data_result['result'] ='true';
          $data_result['msg'] ='Data found';
        }else{
          $data_result['result'] ='false';
          $data_result['msg'] ='No data';
        }
      }else{
        $data_result['result'] ='false';
        $data_result['msg'] ='Please provide parameter(user_id)';
      }
      echo json_encode($data_result);
  }

  public function get_bill()
  {
    
    extract($_POST);
    $total = 0.0;
    $gst = 0.0;
    $vat = 0.0;
    $convenience_fees = 0.0;
    $con_tax = 0.0;
    $convenience_fees =  $this->UserModel->get_convenience_fees();
    $con_tax = $convenience_fees * 18 / 100; 
    $con_tax = round($con_tax, 2); 
    if(isset($order_id) && isset($user_id))
    {
      $where = array(
        'order_id'=>$order_id,
        'user_id' => $user_id
      );

      $order_get_data = $this->UserModel->select_orders('orders',$where);
      if(count($order_get_data)){
        foreach ($order_get_data as $key => $value) {
          $total = $total + $value['total_price'];
          $gst = $gst + $value['gst'];
          $vat = $vat + $value['vat'];
        }

         $data_result['result']     = 'true';
         $data_result['data']       = $order_get_data;
         $data_result['order_id']   = $order_id;
         $data_result['item_total'] = $total;
         $data_result['convenience_fee'] = $convenience_fees;
         $data_result['gst'] = number_format($gst + $con_tax, 2,'.','');
         $data_result['vat'] = $vat;
         $data_result['promo_discount']   = $order_get_data[$key]['promo_discount'];
         $data_result['total_payble'] = number_format( $total + $gst + $con_tax + $convenience_fees + $vat - $order_get_data[$key]['promo_discount'], 2,'.','');
         $data_result['table_id']   = $order_get_data[$key]['table_number'];
         $data_result['order_status']   = $order_get_data[$key]['order_status'];
         $data_result['restaurant_id']   = $order_get_data[$key]['restaurant_id'];
         $data_result['msg']        = 'data found'; 
      }else
      {
          $data_result['result'] = 'false';
          $data_result['data']   = "Na";
          $data_result['msg']    = 'no data found'; 
      }
    }else
    {
      $data_result['result'] = 'false';
      $data_result['data']   = "Na";
      $data_result['msg']    = 'Please give parameters(order_id,user_id) method _POST';
    }
    echo json_encode($data_result);
  }

  public function add_payment()
  {
    extract($_POST);

    if(isset($order_id))
    {
      $wheredata =array(
        'order_id'=>$order_id   
      );
      $data = array(
        'payment_status'=>'Paid',
        'payment_method'=>'Case'
      );

      $result = $this->UserModel->update($wheredata,'orders',$data);
      if($result)
      {
        $data_result['result'] ='true';
        $data_result['msg'] ='Successfully payment.';
      }
      
    }else{
      $data_result['result'] ='false';
      $data_result['msg'] ='Please provide parameter(order_id)';
    }
    echo json_encode($data_result);
  }  

  //update user profile
  public function UpdateProfile(){
    $user_id=$this->input->post('user_id');
    extract($_POST);
    if(
      isset($id) && 
      isset($name) && 
      isset($mobile) && 
      isset($email) && 
      isset($lat) && 
      isset($lng) && 
      isset($address)
      
    ){

      if(!empty($_FILES['image']['name']))
      {
        $_FILES['file']['name']     = $_FILES['image']['name'];
        $_FILES['file']['type']     = $_FILES['image']['type'];
        $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
        $_FILES['file']['error']    = $_FILES['image']['error'];
        $_FILES['file']['size']     =  $_FILES['image']['size'];
        // File upload configuration
        $uploadPath = 'images/user/';
        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        // Upload file to server
        if($this->upload->do_upload('file')){
            $fileData = $this->upload->data();
            $_POST['image'] = $fileData['file_name'];
        }
      }

      $result = $this->UserModel->updateData('users',$_POST,$id);
        if($result)  
        {
          $data = $this->UserModel->select_single_row('users','id',$id);
          $data_result['result'] = 'true';
          $data_result['msg']    = 'Successfully updated user details';
          $data_result['data']   = $data; 
        }else{
          $data_result['result'] = 'false';
          $data_result['msg']    = 'Something went wrong.';
        } 
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give parameters (id,name,mobile,email,location,lat,lng,address,image(optional))method POST';
    } 
    echo json_encode($data_result);
  }

  public function forgotpassword()
  {        
    $wheredata = array(
      'email' => $this->input->post('email')
    );
    $result    = $this->UserModel->selectAllById('users', $wheredata);
    if ($result) {
        $wherenewpass = array(
            'email' => $this->input->post('email')
        );
        
        $random_no = rand(100000, 999999);
        
        $otp = array(
            'password' => $this->hash_password( $random_no )
        );
        
        $res  = $this->UserModel->update($wherenewpass,'users',$otp);
        $res1 = $this->UserModel->selectAllById('users',$wherenewpass);
        if ($res1){
          foreach ($res1 as $key => $value) {
              $myotp    = $value['password'];
              $name     = $value['name'];                
            }
        }
        
        $ra_email = 'Rapidine.com';
        $to       = $this->input->post('email');
        $subject  = 'User Password Reset';
        
        $headers = "From: <" . $ra_email . ">" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message = '';
        $message .= '<!DOCTYPE html>
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        </head>
   
        <body style="font-family:roboto !important;">
            <div style="width:100%; text-align:center; margin:0px auto;">
            <div style="width: 550px; height: auto;  margin: 10px auto;;">
            <div style="padding:2px 2px 8px 2px; background:#3498db; text-align:center;">

              <div style="font-size: 20px; line-height: 40px; padding: 0;  margin-top: 5px;
                text-align: center; color: #fff;">Rapidine 
              </div>

            </div>
            <div style="background:whitesmoke;padding: 15px 0;font-family: sans-serif;">
            <h3 style="text-align: left;padding-left: 100px; font-size: 18px;  margin-top: 0px;
            margin-bottom: 10px; color: #2dbba4;">Forgot Password</h3>
            <center>
            <p>Dear' . $name . '.</p>
            <p>Your New password is: ' . $random_no . '.</p>
            </center>
            </div>
            <footer>
                <div style="background: #3498db; padding: 20px 5px 25px 5px;">
                <div style="width:100%; text-align:center;">
                <div style="font-size: 13px; line-height: 7px; padding: 0;  margin-top: 0px;
                text-align: center; color: #fff;"> © Rapidine 2019. All Rights Reserved.
                </div> 
                </div>
                </div>
            </footer>
            </div>
            </div>
        </body>
        </html>';
        if (mail($to, $subject, $message, $headers)) {
            $data_result['result']    = 'true';
            $data_result['msg']       = 'mail success';
            $data_result['email']     = $to;
            $data_result['password']  = $myotp;
            
        } else {
            $data_result['result'] = 'false';
            $data_result['msg']    = "Emailexist.";
        }
    } else {
        $data_result['result'] = 'false';
        $data_result['msg']    = "Email not exist.";
    }    
    echo json_encode($data_result);
  }

  //Send otp
  public function Send_Otp() {
    $mobile=$this->input->post('mobile');
    // $fcm_id=$this->input->post('fcm_id');
    date_default_timezone_set('Asia/Calcutta');
    $date=date('Y-m-d H:i:s');
    $wheredata=array(
      'mobile'=>$mobile
    );

    $result=$this->UserModel->selectAllById('users',$wheredata);
     $data=array(
        
        'mobile_status'=>'Verified',
    );
     $res2=$this->UserModel->update($wheredata,'users',$data);

    if($result){
        $data1 = array(
            
            'created_at' => $date
        );
        $res1 = $this->UserModel->update($wheredata, 'users', $data1);
        $otp  = rand(11111, 99999);
        $data = array(
            
            'otp' => $otp
        );
        
        $res = $this->UserModel->update($wheredata, 'users', $data);

        if($res){
           
            $msgmobile = "Your Mobile verification otp is:-" . $otp;
            $authKey   = "abgbsaxhbsadbasbhdjhsajhd";
            //Sender ID,While using route4 sender id should be 6 characters long.
            $senderId  = "RAPDIN";
            
            //Define route 
            $route = "4";
            
            //Prepare you post parameters
            $postData = array(
                'authkey' => $authKey,
                'mobiles' => $mobile,
                'message' => $msgmobile,
                'sender' => $senderId,
                'route' => $route,
                'response' => 'json',
                'ignoreNdnc' => 1
            );
            
            //API URL
            $url = "https://control.msg91.com/sendhttp.php";
            
            
            // init the resource
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData
            ));
            
            //Ignore SSL certificate verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            
            //get response
            $output = curl_exec($ch);
            
            curl_close($ch);
            $result1=$this->UserModel->selectAllById('users',$wheredata);
            foreach ($result1 as $key => $value) {
              $myotp=$value['otp'];
              
            }
        $data_result['result'] = 'true';
        $data_result['otp']    = $myotp;
        $data_result['msg']    = 'Send otp on your mobile!';
        }
      }else{

          $data_result['result'] = 'false';
          $data_result['msg']    = 'message Not send!';

        }
     echo json_encode($data_result);
         
  }

  public function Check_otp() {
    $wheredata=array(
      'mobile'=>$this->input->post('mobile'),
      'otp'=>$this->input->post('otp')
    );
    $result=$this->UserModel->selectAllById('users',$wheredata);
    if($result){
      foreach ($result as $key => $value) {
        $user_id=$value['id'];
        $status=$value['mobile_status'];
      }
      $data_result['result']='true';
      $data_result['id']= $user_id;
      $data_result['mobile_status']= $status;
      $data_result['msg']='Your otp matched!';
    }else{
      $data_result['result']='false';
      $data_result['msg']='Sorry Your otp not matched!';
    }
    echo json_encode($data_result);
  }

  public function post_rating_reviews()
  { 
    extract($_POST);
    if( isset($user_id) &&
        isset($restaurant_id) && 
        isset($rating) &&  
        isset($review)
      )
    {
       $post_data = (array) $this->input->post();
       $post_data['date_time'] = date("Y-m-d H:i:s");
       $result = $this->UserModel->insert('restaurant_rating_reviews',$post_data);
      if($result)
      {
        $data_result['result'] = 'true';
        $data_result['data']   = $this->UserModel->selectAllById('restaurant_rating_reviews',array('id'=>$result));
        $data_result['msg']    = 'Successfully post rating reviews !';
      }else{
        $data_result['result'] = 'false';
        $data_result['msg']    = 'Opps! Something went wrong. Please try again.';
      }
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give parameters(user_id,restaurant_id,rating,review)';
    }
    echo json_encode($data_result);
  }

  public function call_waiter_notification()
  { 
    extract($_POST);
    if( isset($user_id) &&
        isset($restaurant_id) && 
        isset($table_number) 
      )
    {

      $restaurant_data = $this->UserModel->select_single_row('restaurants','id',$restaurant_id);
      if($restaurant_data)
      {
        $notificationArray['fcm_id']  = $restaurant_data->fcm_id;
        $notificationArray['message'] = "Waiter called from table $table_number";
        $this->send_notification($notificationArray);

        $post_data = (array) $this->input->post();
        $post_data['date_time'] = date("Y-m-d H:i:s");
        $post_data['message'] = $notificationArray['message']; 
        $result = $this->UserModel->insert('notification',$post_data);
        if($result)
        {
          $data_result['result'] = 'true';
          $data_result['data']   = $this->UserModel->selectAllById('notification',array('id'=>$result));
          $data_result['msg']    = 'Successfully send notification!';
        }else{
          $data_result['result'] = 'false';
          $data_result['msg']    = 'Opps! Something went wrong. Please try again.';
        }
      }else{
       $data_result['result'] = 'false';
       $data_result['msg']    = 'Invalid restaurant_id id.'; 
      }
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give parameters(user_id,restaurant_id,table_number)';
    }
    echo json_encode($data_result);
  }
}
