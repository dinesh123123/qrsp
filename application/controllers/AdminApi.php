<?php
if(!defined('BASEPATH')) exit ('No direct script access allowed');
class AdminApi extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('RAdmin_Model');
    $this->load->model('UserModel');
  }

  public function AdminLogin(){
    extract($_POST);
    if(isset($user_name) && isset($password))
    {
      $email     = $this->input->post('user_name');
      $password  = $this->input->post('password');
      
      $wheredata=array(
          'user_name'  => $email,
          'password'   => $password
      ); 
      $result=$this->RAdmin_Model->singleRowdata($wheredata,'restaurants');
      if ($result){
          $data_result['result'] ='true';
          $data_result['msg']    ='admin login succesfully!';
          $data_result['data']   =$result;
          
      }else{
          $data_result['result'] ='false';
          $data_result['msg']    ='username or Password is incorrect!';
           $data_result['data']  = 'false';
          

      }
    }else{
       $data['result'] = 'false';
       $data['msg']    = 'Please provide parameters(mobile,password)';            
    }        

    echo json_encode($data_result);
  } 

 //get category
  public function getCategories(){
  	extract($_GET);
  	if(isset($type))
  	{
  		$result = $this->RAdmin_Model->selectAllCategory($type); 
  		foreach ($result as $key => $value) 
  		{
  			$data[] = array(
		  			'id' =>  $value['id'], 
		  			'type' =>   $value['type'], 
		  			'name' =>   $value['name']."(".$value['al_non_al_type'].")" 
		  		);
  		}

	    if (count($data)) {
	        $data_result['result'] = 'true';
	        $data_result['msg']    = 'Data found';
	        $data_result['data']   = $data;
	     } else {
	          $data_result['result'] = 'false';
	          $data_result['msg']    = 'No data found';
	          $data_result['data']   = array();
	      }
  	}else{
  		$data_result['result'] = 'false';
        $data_result['msg']    = 'Please provide parameter(type)';  
  	}
  
      echo json_encode($data_result);
  }

  //Add Menus
  public function addMenu()
  {
    
    extract($_POST);
    if( isset($type) &&
        isset($name) &&
        isset($price) &&
        // isset($offer_price) &&
        isset($restaurant_id) &&
        isset($category_id)
      ) 
    {
      if(!empty($_FILES['image']['name']))
      {

      
        $_FILES['file']['name']     = $_FILES['image']['name'];
        $_FILES['file']['type']     = $_FILES['image']['type'];
        $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
        $_FILES['file']['error']     = $_FILES['image']['error'];
        $_FILES['file']['size']     =  $_FILES['image']['size'];
        // File upload configuration
        $uploadPath = 'assets/uploaded/menues/';
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
      $_POST['offer_price']=$price;
        $result = $this->RAdmin_Model->insertAllData('menues', $_POST);
        
        if($result)  
        {
          $data = $this->RAdmin_Model->select_single_row('menues','id',$result);
          $data_result['result'] = 'true';
          $data_result['msg']    = 'Successfully added menu.';
          $data_result['data'] = $data; 
        }else{
          $data_result['result'] = 'false';
          $data_result['msg']    = 'Something went wrong.';
        } 
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give parameters (type,name,price,offer_price,category_id,restaurant_id,image(optional))';
    } 
    echo json_encode($data_result);
  }

  //get category
  public function getMenues(){
    $restaurant_id=$this->input->post('restaurant_id');
    extract($_GET);
    if(isset($restaurant_id))
    {
      $result = $this->RAdmin_Model->select_menus($restaurant_id); 
      if (count($result)) {
        $data_result['result'] = 'true';
        $data_result['msg']    = 'Data found';
        $data_result['data']   = $result;
      } else {
          $data_result['result'] = 'false';
          $data_result['msg']    = 'No data found';
          $data_result['data']   = array();
      }
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give perameter(restaurant_id)';
    }
      echo json_encode($data_result);
  }

  //get category
  public function editMenue(){
    extract($_GET);
    if(isset($id))
    {
      $result = $this->RAdmin_Model->select_single_row('menues', 'id', $id); 
      if ($result) {
        $data_result['result'] = 'true';
        $data_result['msg']    = 'Data found';
        $data_result['data']   = $result;
      } else {
          $data_result['result'] = 'false';
          $data_result['msg']    = 'No data found';
          $data_result['data']   = array();
      }
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give perameter(id), method GET';
    }
      echo json_encode($data_result);
  }

  //Update Menus
  public function updateMenu()
  {  
    extract($_POST);
    if( isset($id) &&
    	isset($type) &&
        isset($name) &&
        isset($price) &&
        // isset($offer_price) &&
        isset($restaurant_id) &&
        isset($category_id)
      ) 
    {
      if(!empty($_FILES['image']['name']))
      {
        $_FILES['file']['name']     = $_FILES['image']['name'];
        $_FILES['file']['type']     = $_FILES['image']['type'];
        $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
        $_FILES['file']['error']    = $_FILES['image']['error'];
        $_FILES['file']['size']     =  $_FILES['image']['size'];
        // File upload configuration
        $uploadPath = 'assets/uploaded/menues/';
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
        $result = $this->RAdmin_Model->updateData('menues',$_POST,$id);
        if($result)  
        {
          $data = $this->RAdmin_Model->select_single_row('menues','id',$id);
          $data_result['result'] = 'true';
          $data_result['msg']    = 'Successfully updated menu';
          $data_result['data'] = $data; 
        }else{
          $data_result['result'] = 'false';
          $data_result['msg']    = 'Something went wrong.';
        } 
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give parameters (id,type,name,price,offer_price,category_id,restaurant_id,image(optional))method POST';
    } 
    echo json_encode($data_result);
  }
  
  //5
  //get category
  public function deleteMenue(){
    extract($_GET);
    if(isset($id))
    {
      $result = $this->RAdmin_Model->delete_menu($id); 
     if($result) {
        $data_result['result'] = 'true';
        $data_result['msg']    = 'Deleted Successfully';
      } else {
          $data_result['result'] = 'false';
          $data_result['msg']    = 'No data found';
      }
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give perameter(id), method GET';
    }
      echo json_encode($data_result);
  }

  //check avablity in stock
  public function Check_avabilites_Instock(){
    $restaurant_id=$this->input->post('restaurant_id');
    extract($_GET);
    if(isset($restaurant_id))
    {
   
      $result = $this->RAdmin_Model->select_menus($restaurant_id); 
      if (count($result)) {
        $data_result['result'] = 'true';
        $data_result['msg']    = 'Data found';
        $data_result['data']   = $result;
      } else {
          $data_result['result'] = 'false';
          $data_result['msg']    = 'No data found';
          $data_result['data']   = array();
      }
    }
    else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give perameter(restaurant_id)';
    }
      echo json_encode($data_result);
  }

  //update for available or not
  public function update_for_InOutStock(){
   $id            = $this->input->post('id');
   $restaurant_id = $this->input->post('restaurant_id');
    $stock_status  = 1;
   
    if(isset($id) && isset($restaurant_id))
    {
      $data = array(
        'id'    => $id,
        'restaurant_id'    => $restaurant_id,
        'stock_status'     => 1
      );
      $data1 = array(
        'id'          => $id,
        'restaurant_id'          => $restaurant_id,
        'stock_status'     => 0
              
      );
      $query =  $this->db->query("SELECT * FROM menues WHERE id ='{$id}'AND stock_status = 0 "); 
      $datas = $query->row();
      if(!empty($datas))
      {
        $wdata=array(
          'id' => $datas->id
        ); 
        $result = $this->RAdmin_Model->updateMenus('menues',$data, $wdata);
        $message ="Out Off Stock.";
        $stock_status =1;

      }else{
        $query1 =  $this->db->query("SELECT * FROM menues WHERE id ='{$id}' AND stock_status = 1 "); 
        $datas1 = $query1->row();
        if(!empty($datas1))
        {
            $wdata=array(
                'id' => $datas1->id
            ); 
          $result = $this->RAdmin_Model->updateMenus('menues',$data1, $wdata);
          $message ="In Stock menue.";
          $stock_status =0;
        }else{
          $result = $this->RAdmin_Model->add('menues',$data1); 
          $message ="In Stock menue."; 
          $stock_status =0; 
        }
      } 

      if($result){
        
         $data_result['result']       = 'true';
         $data_result['msg']          =  $message;  
         $data_result['stock_status'] =  $stock_status;  
      }
      else{
          $data_result['result']         = 'false';
          $data_result['msg']            =  $message;  
          $data_result['stock_status']   =  $stock_status;  
      }
    }else{
         $data_result['result']     =  'false'; 
         $data_result['msg']        = 'Please give perameter menu id (id,restaurant_id)'; 
    }
    echo json_encode($data_result);
  }
  
  //whenever resta use promocode st1
  public function getPromoCode(){
    $restaurant_id = $this->input->get_post('restaurant_id');

    $wheredata = array(
      'restaurant_id'=> $restaurant_id
    );
    $Restapromo = $this->RAdmin_Model->singleRowdata($wheredata,'restaurants_promocode');    
    if (!empty( $Restapromo) && $Restapromo->promocode_id>0) {
          $restaurant = explode(",",$Restapromo->promocode_id);   
         $getList = $this->RAdmin_Model->getAll('tbl_promocode');
      
          foreach ($getList as $key => $value) {
           $status = 0; 
          if(in_array($value['id'], $restaurant)){               
              $status = 1;
              }
              $data[] = array(
                'id'              =>$value['id'],
                'promoCode'       =>$value['promoCode'],
                'promoCode_offer' =>$value['promoCode_offer'],
                'status'          =>$status
              );
          }
        }else{
        $getList = $this->RAdmin_Model->getAll('tbl_promocode');
        // print_r($getList);die;
        foreach ($getList as $key => $value) {
          $data[] = array(
            'id'              =>$value['id'],
            'promoCode'       =>$value['promoCode'],
            'promoCode_offer' =>$value['promoCode_offer'],
            'status'          =>0
          );
        }  
      }
      if(!empty($data)){

        $data_result['result'] = 'true';
        $data_result['msg']    = 'All promoCode List';
        $data_result['data']   = $data;

        }else {
          $data = array();
          $data_result['msg']    = 'Sorry Not in List';
          $data_result['result'] = 'false';
          $data_result['data']   = $data;
        }
        echo json_encode($data_result);
  }

  //resta apply promocode
  public function restaurant_promocode(){
    $restaurant_id = $this->input->post('restaurant_id');
    $promocode_id  = $this->input->post('promocode_id');
    $status  = 1;   
    if(isset($promocode_id) && isset($restaurant_id))
    {
      $data = array(
        'promocode_id'  => $promocode_id,
        'restaurant_id' => $restaurant_id,
        'status'        => 1
      );
      $data1 = array(
        'promocode_id'  => $promocode_id,
        'restaurant_id' => $restaurant_id,
        'status'        => 0
              
      );
      $query =  $this->db->query("SELECT * FROM restaurants_promocode WHERE restaurant_id ='{$restaurant_id}' AND promocode_id ='{$promocode_id}' AND status = 0 "); 
      $datas = $query->row();
      if(!empty($datas))
      {
        $wdata=array(
          'restaurant_id' => $datas->restaurant_id
        ); 
        $result = $this->RAdmin_Model->updateMenus('restaurants_promocode',$data, $wdata);
        $message ="Promocode Not apply";
        $status =1;

      }else{
        $query1 =  $this->db->query("SELECT * FROM restaurants_promocode WHERE restaurant_id ='{$restaurant_id}' AND promocode_id ='{$promocode_id}' AND  status = 1 "); 
        $datas1 = $query1->row();
        if(!empty($datas1))
        {
          $wdata=array(
            'id' => $datas1->id
          ); 
          $result = $this->RAdmin_Model->updateMenus('restaurants_promocode',$data1, $wdata);
          $message ="Promocode Apply successfully!";
          $status =0;
        }else{
          $result = $this->RAdmin_Model->add('restaurants_promocode',$data1); 
          $message ="Promocode Apply successfully"; 
          $status =0; 
        }
      } 

      if($result){
        
         $data_result['result'] = 'true';
         $data_result['msg']    =  $message;  
         $data_result['status'] =  $status;  
      }
      else{
          $data_result['result']  = 'false';
          $data_result['msg']     = $message;  
          $data_result['status']  = $status;  
      }
    }else{
         $data_result['result']     =  'false'; 
         $data_result['msg']        = 'Please give perameter menu id (id,restaurant_id)'; 
    }
    echo json_encode($data_result);
  }

  public function single_item_offer_price(){
    $restaurant_id = $this->input->post('restaurant_id');
    $menues_id     = $this->input->post('menues_id');
    $offer_price      = $this->input->post('offer_price');

    $data =array(
      'offer_price'    =>$offer_price
    );

    $wheredata =array(
      'restaurant_id' =>$restaurant_id,
      'id'            =>$menues_id   
    );
    $result = $this->RAdmin_Model->update($wheredata,'menues',$data);
    if($result){
      $data_result['result'] ='true';
      $data_result['data']  =$data;
      $data_result['msg']   ='offer price available.';
    }else{
      $data_result['result'] ='false';     
      $data_result['msg']    ='Not update offer Price';
    }
    echo json_encode($data_result);
  }


  public function single_item_offer_apply(){
    $restaurant_id=$this->input->post('restaurant_id');
    $menues_id    =$this->input->post('menues_id');
    // $status='On';
    if(isset($menues_id) && isset($restaurant_id))
    {
      $data = array(
        'status'        => 'Off',
      );
      $data1 = array(
        'status'        => 'On'              
      );
      $query =  $this->db->query("SELECT * FROM menues WHERE restaurant_id ='{$restaurant_id}' AND id ='{$menues_id}' AND status='On'"); 
      $datas = $query->row();
      if(!empty($datas))
      {
        $wdata=array(
          'id' => $menues_id
        ); 
        $result = $this->RAdmin_Model->updateMenus('menues',$data,$wdata);
        $message ="offer price Not apply";
        $status ='Off';

      }else{
        $query1 =  $this->db->query("SELECT * FROM menues WHERE restaurant_id ='{$restaurant_id}' AND id ='{$menues_id}' AND status='Off'"); 
        $datas1 = $query1->row();
        if(!empty($datas1))
        {
          $wdata=array(
            'id' => $menues_id
          ); 
          $result  = $this->RAdmin_Model->updateMenus('menues',$data1, $wdata);
          $message ="offer price Apply successfully!";
          $status  ='On';
        }
        else{
          $wdata=array(
            'id' => $menues_id
          ); 
          $result  = $this->RAdmin_Model->updateMenus('menues',$data1,$wdata); 
          $message ="offer price Apply successfully"; 
          $status  ='On'; 
        }
      } 

      if($result){
        
         $data_result['result'] = 'true';
         $data_result['msg']    =  $message;  
         $data_result['status'] =  $status;  
      }
      else{
          $data_result['result']  = 'false';
          $data_result['msg']     = $message;  
          $data_result['status']  = $status;  
      }
    }else{
         $data_result['result']     =  'false'; 
         $data_result['msg']        = 'Please give perameter menu id (menues_id,restaurant_id)'; 
    }
    echo json_encode($data_result);
  }

  //get restaurants profile
  public function get_restaurantsDetails(){
    $restaurant_id=$this->input->post('restaurant_id');
    if(isset($restaurant_id)){
      $wheredata =array(
        'id'=>$restaurant_id   
      );
     $result=$this->RAdmin_Model->selectAllById('restaurants',$wheredata);
     if($result){
      foreach ($result as $key => $value) {
        $resta[]  = array(
          "restaurant_id"=>$value['id'],
          "restaurant_name"=>$value['restaurant_name'],
          "restaurant_email"=>$value['restaurant_email'],
          "descriptin"=>$value['descriptin'],
          "location"=>$value['location'],
          "lat"=>$value['lat'],
          "lng"=>$value['lng'],
          "mobile"=>$value['mobile'],
          "user_name"=>$value['user_name'],
          "status"=>$value['status'],
          "image"=>$value['image'],
          "openTime"=>$value['openTime'],
          "closeTime"=>$value['closeTime'],
          "created_at"=>$value['created_at']
        );
      }
       $data_result['result']='true';
       $data_result['data']=$resta;
       $data_result['msg']='Restaurants Details.';
     }else{
      $data_result['result']='false';
      $data_result['msg']='Restaurants Details not available!!';
     }
   }else{
    $data_result['result']='false';
      $data_result['msg']='parameters required restaurant_id';
   }
   
     echo json_encode($data_result);
  } 

  //update restaurant details
  public function updateRestaurantDetails(){
    extract($_POST);
    if( isset($id) &&
      isset($restaurant_name) &&
      isset($restaurant_email) &&
        // isset($descriptin) &&
        // isset($location) &&
        // isset($lat) &&
        // isset($lng) &&
        isset($mobile)
        // isset($status) &&
        // isset($openTime) &&
        // isset($closeTime)
      ) 
    {
      if(!empty($_FILES['image']['name']))
      {
        $_FILES['file']['name']     = $_FILES['image']['name'];
        $_FILES['file']['type']     = $_FILES['image']['type'];
        $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
        $_FILES['file']['error']     = $_FILES['image']['error'];
        $_FILES['file']['size']     =  $_FILES['image']['size'];
        // File upload configuration
        $uploadPath = 'assets/uploaded/restaurants/';
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
        $result = $this->RAdmin_Model->updateData('restaurants',$_POST,$id);
        if($result)  
        {
          $data = $this->RAdmin_Model->select_single_row('restaurants','id',$id);
          $data_result['result'] = 'true';
          $data_result['msg']    = 'Successfully updated restaurants details';
          $data_result['data'] = $data; 
        }else{
          $data_result['result'] = 'false';
          $data_result['msg']    = 'Something went wrong.';
        } 
    }else{
      $data_result['result'] = 'false';
      $data_result['msg']    = 'Please give parameters (id,restaurant_name,restaurant_email,mobile,image(optional))method POST';
    } 
    echo json_encode($data_result);
  }

  //table chair(space area)
  public function NoOfTable_Area(){
    $restaurant_id=$this->input->post('restaurant_id');
    $NoOf_Table=$this->input->post('NoOf_Table');
     if(isset($restaurant_id) && isset($NoOf_Table)){
        $data= array(
         'number_of_table'=>$NoOf_Table
        );
        
        $result = $this->RAdmin_Model->updateData('restaurants',$data,$restaurant_id);
      if($result){
        $data_result['result']='true';
        $data_result['data']=$result;
        $data_result['msg'] ='No of table available in this restaurant.';
      }else{
        $data_result['result']='false';
        $data_result['msg']='Sorry having not table';
      }
    }else{
      $data_result['result']='false';
      $data_result['msg']='Please required all parameters(restaurant_id,NoOf_Table)';
    }
    
    echo json_encode($data_result);
  }

  public function get_order_detail1($order_id)
  {
    //extract($_POST);
    $total = 0.0;
    $gst = 0.0;
    $vat = 0.0;
    $convenience_fees = 0.0;
    $con_tax = 0.0;
    $convenience_fees =  $this->UserModel->get_convenience_fees();
    $con_tax = round($convenience_fees * 18 / 100);  
    if(isset($order_id))
    {
      $where = array(
        'order_id'=>$order_id
      );

      $order_get_data = $this->RAdmin_Model->select_orders('orders',$where);
      if(count($order_get_data)){
        foreach ($order_get_data as $key => $value) {
          $total = $total + $value['total_price'];
          $gst = $gst + $value['gst'];
          $vat = $vat + $value['vat'];
        }
        
          $tatols =  $total + $gst + $con_tax + $convenience_fees + $vat - $order_get_data[$key]['promo_discount'] ; 

           return number_format( $tatols , 2,'.','');
      }
    }else
    {
      $data_result['msg']    = 'Please give parameters(order_id) method _POST';
    }
    echo json_encode($data_result);
  }

	public function get_orders()
  	{
      extract($_POST);
      if(isset($restaurant_id))
      {
        $this->result = $this->RAdmin_Model->get_orders($restaurant_id);
        if(!empty($this->result))
        {
          foreach ($this->result as $or_data) 
          {
            $or_data->order_item = $this->RAdmin_Model->get_order_items($or_data->order_id);
            $or_data->total_payble = $this->get_order_detail1($or_data->order_id);
          }
          $data_result['data'] = $this->result;
          $data_result['result'] ='true';
          $data_result['msg'] ='Data found';
        }else{
          $data_result['result'] ='false';
          $data_result['msg'] ='No data';
        }
      }else{
        $data_result['result'] ='false';
        $data_result['msg'] ='Please provide parameter(restaurant_id)';
      }
      echo json_encode($data_result);
  }
  //Prepaired order details
  public function get_prepaired_orders()
	{
	    extract($_POST);
	    if(isset($restaurant_id))
	    {
	      $this->result = $this->RAdmin_Model->get_prepaired_orders($restaurant_id);
	      if(!empty($this->result))
	      {
	        foreach ($this->result as $or_data) 
	        {
	          $or_data->order_item = $this->RAdmin_Model->get_order_item($or_data->order_id);
            $or_data->total_payble = $this->get_order_detail1($or_data->order_id);
	        }
	        $data_result['data'] = $this->result;
	        $data_result['result'] ='true';
	        $data_result['msg'] ='Data found';
	      }else{
	        $data_result['result'] ='false';
	        $data_result['msg'] ='No data';
	      }
	    }else{
	      $data_result['result'] ='false';
	      $data_result['msg'] ='Please provide parameter(restaurant_id)';
	    }
	    echo json_encode($data_result);
	}

	public function get_order_history()
	{
	    extract($_POST);
	    if(isset($restaurant_id))
	    {
	      $this->result = $this->RAdmin_Model->get_complete_orders($restaurant_id);
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
	      $data_result['msg'] ='Please provide parameter(restaurant_id)';
	    }
	    echo json_encode($data_result);
	}

  public function get_order_detail()
  {
    extract($_POST);
    $total = 0.0;
    $gst = 0.0;
    $vat = 0.0;
    $convenience_fees = 0.0;
    $con_tax = 0.0;
    $convenience_fees =  $this->UserModel->get_convenience_fees();
    $con_tax = round($convenience_fees * 18 / 100);  
    if(isset($order_id))
    {
      $where = array(
        'order_id'=>$order_id
      );

      $order_get_data = $this->RAdmin_Model->select_orders('orders',$where);
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
         $data_result['user_name']   = $order_get_data[$key]['user_name'];
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
      $data_result['msg']    = 'Please give parameters(order_id) method _POST';
    }
    echo json_encode($data_result);
  }

  public function ordered_item_status_change()
  {
	    extract($_POST);
	    if(isset($id))
	    {
	      $wheredata =array(
	        'id'=>$id   
	      );
	      $data = array(
	      	'item_status'=>'Complete' 
	      );

	      $result = $this->RAdmin_Model->update($wheredata,'orders',$data);
	      if($result)
	      {
	      	$data_result['result'] ='true';
	      	$data_result['msg'] ='Successfully status updated';
	      }
	      
	    }else{
	      $data_result['result'] ='false';
	      $data_result['msg'] ='Please provide parameter(id)';
	    }
	    echo json_encode($data_result);
	}

	public function complete_order()
	{
	    extract($_POST);

	    if(isset($order_id))
	    {
	      $wheredata =array(
          'order_id'=>$order_id,    
	        'payment_status'=>'Unpaid'
          	      );
        if(!$this->RAdmin_Model->check_is_complete($wheredata))
        {
          $data = array(
              'order_status'=>'Finish' 
            );
          $wheredata =array(
          'order_id'=>$order_id,    
          'payment_status'=>'Paid',
          'order_status !='=>'Cancelled'
          );
            $result = $this->RAdmin_Model->update($wheredata,'orders',$data);
            if($result)
            {
              $data_result['result'] ='true';
              $data_result['msg'] ='Successfully order completed.';
            }
        }else{
          $data_result['result'] ='false';
          $data_result['msg'] ='Payment not done by customer.';
        }
	    }else{
	      $data_result['result'] ='false';
	      $data_result['msg'] ='Please provide parameter(order_id)';
	    }
	    echo json_encode($data_result);
	}  

	public function accept_order()
    {
      extract($_POST);

      if(isset($order_id))
      {
        $wheredata =array(
          'order_id'=>$order_id,
          'order_status'=>'Pending'
        );
        $data = array(
          'order_status'=>'Preparing',
          'item_status'=>'Preparing'
        );

        $result = $this->RAdmin_Model->update($wheredata,'orders',$data);
        if($result)
        {
          $data_result['result'] ='true';
          $data_result['msg'] ='Successfully order accepted.';
        }
        
      }else{
        $data_result['result'] ='false';
        $data_result['msg'] ='Please provide parameter(order_id)';
      }
      echo json_encode($data_result);
  }

	public function cancel_order()
  	{
	    extract($_POST);
	    if(isset($order_id))
	    {
	    	if(!$this->RAdmin_Model->is_record_exist('orders', 'order_id',$order_id))
	    	{
	    		$data_result['result'] ='false';
	      	    $data_result['msg'] ='Invalid order_id';
	    	}else{
	    		$wheredata =array(
			        'order_id'=>$order_id,
              'order_status'=>'Pending'   
			      );
			      $data = array(
			      	'order_status'=>'Cancelled' 
			      );

			      $result = $this->RAdmin_Model->update($wheredata,'orders',$data);
			      if($result)
			      {
			      	$data_result['result'] ='true';
			      	$data_result['msg'] ='Successfully order cancel.';
			      }
	    	}
	    }else{
	      $data_result['result'] ='false';
	      $data_result['msg'] ='Please provide parameter(order_id)';
	    }
	    echo json_encode($data_result);
	}

  //order summry
  public function get_order_summary(){
    extract($_POST);
    $start_date = date("d/m/Y", strtotime($start_date));
    $end_date   = date("d/m/Y", strtotime($end_date));
    $total = 0.0;
    if(isset($restaurant_id) && isset($start_date) && isset($end_date))
    {

    $this->result = $this->RAdmin_Model->sum_order($restaurant_id,$start_date,$end_date);
      if(!empty($this->result))
      { 
        foreach ($this->result as $value) {

          // $value->$total = $total + $value->total_price;
          $value->total_payble = round($this->get_order_detail1($value->order_id));
          $total = $total + $value->total_payble;
        }

        $data_result['data'] = $this->result;
        $data_result['sum total price'] = $total;
        $data_result['result'] ='true';
        $data_result['msg'] ='Order found';
      }else{
        $data_result['result'] ='false';
        $data_result['msg'] ='No Order List';
      }
    }else{
      $data_result['result'] ='false';
      $data_result['msg'] ='Please provide parameter(restaurant_id,start_date,end_date)';
    }
    echo json_encode($data_result);
  }
  //order summry
  public function get_notifications(){

    extract($_POST);
    if(isset($restaurant_id))
    {
      $result = $this->RAdmin_Model->get_notifications($restaurant_id);
      if($result)
      {
        $data_result['result'] ='true';
        $data_result['data'] = $result;
        $data_result['msg'] ='notifications list.'; 
      }else{
        $data_result['result'] ='false';
        $data_result['msg'] ='No notifications found.'; 
      }
    }else{
      $data_result['result'] ='false';
      $data_result['msg'] ='Please provide parameter(restaurant_id)';
    }
    echo json_encode($data_result);
  } 
}