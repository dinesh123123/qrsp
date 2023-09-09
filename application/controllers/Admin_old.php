<?php
if(!defined('BASEPATH')) exit ('No direct script access allowed');
require_once ('vendor/autoload.php');
// require_once ('vendor1/autoload.php');
// use Dompdf\Dompdf;
class Admin extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Admin_model');
    $this->load->model('UserModel');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data['title']='Super Admin | Rapidine';
    $this->load->view('admin/header',$data);
    $this->load->view('admin/index',$data);
  }


  public function login()
  {
    $data['title']= 'Super Admin | Rapidine';
    $this->load->view('admin/header',$data);

    $this->load->library('form_validation');
    $this->form_validation->set_rules('email', 'Email', 'trim|required');
    $this->form_validation->set_rules('password', 'Password', 'trim|required');
    if ($this->form_validation->run() == FALSE) 
    {
      if($this->form_validation->error_string()!="")
      {
        $data["error"] = '<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <strong>Warning!</strong> '.$this->form_validation->error_string().'
          </div>';
      }
      }else
      {
        $q = $this->db->query("Select * from `admin` where (`admin_email`='".$this->input->post("email")."') and admin_password='".md5($this->input->post("password"))."'  Limit 1");
            // print_r($q) ; 
        if ($q->num_rows() > 0)
        {
          $row = $q->row(); 
          if($row->admin_status == "0")
          {
            $data["error"] = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <strong>Warning!</strong> Your account currently inactive.</div>';
          }
          else
          {
            $newdata = array(
             'admin_name'  => $row->admin_fullname,
             'admin_email'     => $row->admin_email,
             'logged_in' => TRUE,
             'admin_id'=>$row->admin_id,
             'admin_type_id'=>$row->admin_type_id,
             'admin_image'=>$row->admin_image
            );
            $this->session->set_userdata($newdata);
            redirect('admin/dashboard');       
          }
        }
        else
        {
          $data["error"] = '<div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <strong>Warning!</strong> Invalid User and password. </div>';
        }
                   
                    
      }

    $this->load->view("admin/index",$data);
  }

  function signout(){
    $this->session->sess_destroy();
    redirect("Admin");
  }

  public function dashboard(){
    if(! $this->session->userdata('admin_id')){
      redirect('Admin');
    }
    $data['title']='Super Admin| Dashboard ';
    $data['restro'] = $this->Admin_model->count('restaurants','id desc');
    $data['user']  = $this->Admin_model->count('users','id desc');
    $data['cat']  = $this->Admin_model->count('categories','id desc');
    $data['banner']  = $this->Admin_model->count('banner_list','banner_id desc');
    // $data['banner']  = $this->Admin_model->count('banner_list','banner_id desc');
    $this->load->view('admin/header',$data);   
    $this->load->view("admin/dashboard",$data);
        
  }

  /* 
      @restaurants function 
  */

  public function  restaurants()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Super Admin | Restaurants';
    $this->load->view('admin/header',$data);
    $data['restaurant']= $this->Admin_model->selectAllData('restaurants','id DESC');
    $this->load->view('admin/restaurants',$data);

  }

  public function billings()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Super Admin | Billings';
    $this->load->view('admin/header',$data);
    $data['restaurant']= $this->Admin_model->selectAllData('restaurants','id DESC');
    $this->load->view('admin/billings',$data);
  } 

  public function restaurant_orders($id)
  {
    if (!$this->session->userdata('admin_id'))
    {
      redirect('admin');
    }
    $date = date("Y-m-d");
    extract($_GET);
    
    $date = date("Y-m-d", strtotime($date));
    $data['title']='Super Admin | Restaurant Orders';
    $this->load->view('admin/header',$data);
    $data['restaurant_name'] = $this->Admin_model->select_single_row('restaurants', 'id',$id)->restaurant_name;
    $data['orders']= $this->Admin_model->select_daywise_billing($id,$date);
    $data['current_date'] = $date;  
    $this->load->view('admin/restaurant-orders',$data);
  } 
  public function order_details($id)
  {
    if (!$this->session->userdata('admin_id'))
    {
      redirect('admin');
    }

    $data['title']='Super Admin | Orders Details';
    $this->load->view('admin/header',$data);
     $convenience_fees =  $this->UserModel->get_convenience_fees();
    $data['convenience_fees'] =  $this->UserModel->get_convenience_fees();
    $data['con_tax'] =  round($convenience_fees * 18 / 100);
    $data['main_details'] = $this->Admin_model->order_main_details($id);
    $data['orders_item']= $this->Admin_model->select_order_items($id);
     
    $this->load->view('admin/order-details',$data);
  }


  public function get_single_order_item($id)
  {
     return $this->Admin_model->select_order_items($id);
  }

  public function delete_restaurant($id){
    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    $this->db->query("Delete from `restaurants` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>Restaurant deleted successfully...</div>');

    redirect("admin/restaurants");
  }

  public function  promoCode($id = -1)
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Super Admin | PromoCode';
    $this->load->view('admin/header',$data);
    $data['promo']= $this->Admin_model->selectAllData('tbl_promocode','id DESC');
    if($id != -1)
    {
      $data['data']= $this->Admin_model->select_single_row('tbl_promocode','id',$id); 
    }
    $this->load->view('admin/promocode',$data);

  }


   public function Add_PromoCode()
  {

    date_default_timezone_set('Asia/Calcutta');
    if (!$this->session->userdata('admin_id'))
    {
      redirect('admin');
    }

    $this->form_validation->set_rules('promoCode', 'PromoCode', 'required',
            array('required' => 'You must provide a %s.')
          );
    $this->form_validation->set_rules('promoCode_offer', 'promoCode_offer', 'required',
            array('required' => 'You must provide a %s.')
          );
    $this->form_validation->set_rules('startDate', 'startDate', 'required',
            array('required' => 'You must provide a %s.')
          );
    $this->form_validation->set_rules('expiryDate', 'expiryDate', 'required',
            array('required' => 'You must provide a %s.')
          );
    
    if ($this->form_validation->run() == FALSE)
    { 
      $data['title']='Super Admin | promoCode';
      $this->load->view('admin/header',$data);
      $data['promo']=$this->Admin_model->selectAllData('tbl_promocode','id DESC');
      $this->load->view('admin/promocode',$data);
    }
    else
    {
      $post_data = (array) $this->input->post();
      $post_data['startDate'] = date("Y-m-d H:i:s",strtotime($this->input->post('startDate')));
      $post_data['expiryDate'] = date("Y-m-d H:i:s",strtotime($this->input->post('expiryDate')));
      $result = $this->Admin_model->insertAllData('tbl_promocode', $post_data);
     if ($result) {
        $this->session->set_flashdata('success_req', '<div class="alert alert-success ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>PromoCode Sucessfully Added. !!</strong>.
        </div>');
        redirect('Admin/promoCode');
      }else{
        $this->session->set_flashdata('not', '<div class="alert alert-danger ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Something went Wrong.! Please try again.</strong>.
        </div>');
        redirect('Admin/promoCode');
      }
    }
  }

  public function update_promocode($id)
  {
    $this->form_validation->set_rules('promoCode', 'promoCode', 'required',
      array('required' => 'You must provide a %s.')
    );
    $this->form_validation->set_rules('promoCode_offer', 'promoCode_offer', 'required',
      array('required' => 'You must provide a %s.')
    );
    $this->form_validation->set_rules('startDate', 'startDate', 'required',
      array('required' => 'You must provide a %s.')
    );
    $this->form_validation->set_rules('expiryDate', 'expiryDate', 'required',
      array('required' => 'You must provide a %s.')
    );

      if ($this->form_validation->run() == FALSE)
      { 
        $data['title']='Super Admin | promoCode';
        $this->load->view('admin/header',$data);
        if($id != -1)
        {
          $data['data'] = $this->Admin_model->getSingleRow('tbl_promocode',$id);
        }
        $data['promo']=$this->Admin_model->selectAllData('tbl_promocode','id DESC');
        $this->load->view('admin/promoCode',$data);
      }
      else
      {
        $post_data = (array) $this->input->post();
        $post_data['startDate'] = date("Y-m-d H:i:s",strtotime($this->input->post('startDate')));
      $post_data['expiryDate'] = date("Y-m-d H:i:s",strtotime($this->input->post('expiryDate')));
        $this->Admin_model->updateData('tbl_promocode',$post_data, $id);
        $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
        <i class="fa fa-check"></i>
        <button type="button" class="close" data-dismiss="alert">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Close</span>
        </button>
        <strong>Success!</strong>promoCode updated successfully...</div>');

      redirect("Admin/promoCode");
    }

  }

   public function delete_PromoCode($id)
  {  
    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    $this->db->query("Delete from `tbl_promocode` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>promocode deleted successfully...</div>');

    redirect("Admin/promoCode");
  }




  public function  banners($id = -1)
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Super Admin | Banners';
    $this->load->view('admin/header',$data);
    $data['banners']= $this->Admin_model->selectAllData('banner_list','banner_id DESC');
    if($id != -1)
    {
      $data['data']= $this->Admin_model->select_single_row('banner_list','banner_id',$id); 
    }
    $this->load->view('admin/banners',$data);

  } 

  public function  convenience_fee($id = -1)
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Super Admin | Banners';
    $this->load->view('admin/header',$data);
    $data['convenience']= $this->Admin_model->selectAllData('convenience_fees','id DESC');
    if($id != -1)
    {
      $data['data']= $this->Admin_model->select_single_row('convenience_fees','id',$id); 
    }
    $this->load->view('admin/convinance_fee',$data);

  }

  public function save_convenience_fee()
  {
    $insertData = array(
      'amount' => $this->input->post('amount')
      );
    $result = $this->Admin_model->insert('convenience_fees', $insertData);
     if ($result) {
        $this->session->set_flashdata('sucesscate', '<div class="alert alert-success ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong> Amount Added Sucessfully!!</strong>.
        </div>');
        redirect('admin/convenience_fee');
      }else{
        $this->session->set_flashdata('not', '<div class="alert alert-danger ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>  Banner Not Added!!</strong>.
        </div>');
        redirect('admin/convenience_fee');
      }
  }

  public function save_banner()
  {
     $image='';
    if(!empty($_FILES['image']['name']))
    {
      
      $image =$_FILES["image"]['name'];
      $config['upload_path'] = 'assets/uploaded/banner/';
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['file_name'] = $image;
      $config['overwrite'] = TRUE;
      $config['remove_spaces'] = TRUE;
      
      $this->upload->initialize($config);
      if (!$this->upload->do_upload('image')) {

        $error = array('error' => $this->upload->display_errors());
         echo $error['error'];
        }else{
        
        $imageDetailArray = $this->upload->data();
        $image =  $imageDetailArray['file_name'];

      }
    }
   if($image){
    $insertData['image'] = $image;

   }else{
    $insertData['image'] = '';
   }

    $result = $this->Admin_model->insert('banner_list', $insertData);
     if ($result) {
        $this->session->set_flashdata('sucesscate', '<div class="alert alert-success ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong> Banner Added Sucessfully!!</strong>.
        </div>');
        redirect('admin/banners');
      }else{
        $this->session->set_flashdata('not', '<div class="alert alert-danger ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>  Banner Not Added!!</strong>.
        </div>');
        redirect('admin/banners');
      }
  }

  public function delete_banner($id)
  {  
    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    $this->db->query("Delete from `banner_list` where banner_id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>Banner deleted successfully...</div>');

    redirect("Admin/banners");
  }

  public function delete_convenience($id)
  {  
    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    $this->db->query("DELETE FROM `convenience_fees` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>Deleted successfully...</div>');

    redirect("Admin/convenience_fee");
  }

  public function  categories($id = -1)
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Super Admin | Categories';
    $this->load->view('admin/header',$data);
    $data['categories']= $this->Admin_model->selectAllData('categories','id DESC');
    if($id != -1)
    {
      $data['data']= $this->Admin_model->select_single_row('categories','id',$id); 
    }
    $this->load->view('admin/categories',$data);

  }

  public function save_categories()
  {
    if (!$this->session->userdata('admin_id'))
    {
      redirect('admin');
    }

    $this->form_validation->set_rules('name', 'Name', 'required',
            array('required' => 'You must provide a %s.')
          );
	$this->form_validation->set_rules('al_non_al_type', 'Type', 'required',
            array('required' => 'You must provide a %s.')
          );
	$this->form_validation->set_rules('type', 'Food Type', 'required',
            array('required' => 'You must provide a %s.')
          );
    if ($this->form_validation->run() == FALSE)
    { 
           $data['title']='Super Admin | Categories';
          $this->load->view('admin/header',$data);
          $data['categories']=$this->Admin_model->selectAllData('categories','id DESC');
          $this->load->view('admin/categories',$data);
    }
    else
    {
      $post_data = (array) $this->input->post();
      $result = $this->Admin_model->insertAllData('categories', $post_data);
     if ($result) {
        $this->session->set_flashdata('success_req', '<div class="alert alert-success ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Category Sucessfully Added. !!</strong>.
        </div>');
        redirect('Admin/categories');
      }else{
        $this->session->set_flashdata('not', '<div class="alert alert-danger ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Something went Wrong.! Please try again.</strong>.
        </div>');
        redirect('Admin/categories');
      }
    }
  }

  public function update_categories($id)
  {
          $this->form_validation->set_rules('name', 'Name', 'required',
            array('required' => 'You must provide a %s.')
          );
		$this->form_validation->set_rules('al_non_al_type', 'Type', 'required',
	            array('required' => 'You must provide a %s.')
	          );
		$this->form_validation->set_rules('type', 'Food Type', 'required',
	            array('required' => 'You must provide a %s.')
	          );

      if ($this->form_validation->run() == FALSE)
      { 
        $data['title']='Super Admin | Categories';
        $this->load->view('admin/header',$data);
        if($id != -1)
        {
          $data['data'] = $this->Admin_model->getSingleRow('categories',$id);
        }
        $data['categories']=$this->Admin_model->selectAllData('categories','id DESC');
        $this->load->view('admin/categories',$data);
      }
      else
      {
        $post_data = (array) $this->input->post();
        $this->Admin_model->updateData('categories',$post_data, $id);
        $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
        <i class="fa fa-check"></i>
        <button type="button" class="close" data-dismiss="alert">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Close</span>
        </button>
        <strong>Success!</strong>Category updated successfully...</div>');

      redirect("Admin/categories");
    }

  }


  // public function  restaurants_data()
  // {
  //   if (!$this->session->userdata('admin_id')){
  //     redirect('admin');
  //   }
  //   extract($_GET);
  //   $data['data']=$this->Admin_model->selectAllData('restaurant_admin',$start,$length);
  //   print_r($data['data']);
  //   die;
  //   echo json_encode($data);
  // }

  public function add_restaurant()
  {
    $data['title']='Admin | Add Restaurant';
    $this->load->view('admin/header',$data);
    $this->load->view('admin/add_restaurant',$data);
  } 

  public function save_restaurant()
  {
    $this->form_validation->set_rules('restaurant_name', 'Restaurant Name', 'required',
            array('required' => 'You must provide a %s.')
          );
    $this->form_validation->set_rules('location', 'Location', 'required',
            array('required' => 'You must provide a %s.')
          );
    $this->form_validation->set_rules('user_name', 'Username', 'required',
            array('required' => 'You must provide a %s.')
          );
    $this->form_validation->set_rules('restaurant_email', 'Restaurant Email', 'required',
            array('required' => 'You must provide a %s.')
    );
    $this->form_validation->set_rules('mobile', 'Mobile Number', 'required',
            array('required' => 'You must provide a %s.')
    );
    
     $this->form_validation->set_rules('openTime', 'Open Time', 'required',
            array('required' => 'You must provide a %s.')
    );   

    $this->form_validation->set_rules('closeTime', 'Close Time', 'required',
            array('required' => 'You must provide a %s.')
    );

    $this->form_validation->set_rules('password', 'Password', 'required',
            array('required' => 'You must provide a %s.')
    );

    if ($this->form_validation->run() == FALSE)
    { 

    }
    else
    {

      $post_data = (array) $this->input->post();
      unset($post_data['submit']);
        if(! $this->Admin_model->is_record_exist('restaurants','user_name', $post_data['user_name']) )
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

          $result = $this->Admin_model->insertAllData('restaurants',$post_data);
          if($result)
          {
            $this->session->set_flashdata("message",'Successfully added restaurant.');
            redirect("admin/restaurants");
          }
        }else{
           $data['error']="<h3 style='color:#fdb813'>Username already exists!</h3>";
        }
    }

    $data['title']='Admin | Add Restaurant';
    $this->load->view('admin/header',$data);
    $this->load->view('admin/add_restaurant',$data);
   }


  public function edit_restaurant($id)
  {
    $data['title']='Admin | Update Restaurant';
    $this->load->view('admin/header',$data);
    $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
    $this->load->view('admin/edit-restaurant',$data);
  }

  public function update_restaurant($id)
  {

    $this->form_validation->set_rules('restaurant_name', 'Restaurant Name', 'required',
            array('required' => 'You must provide a %s.')
          );
    $this->form_validation->set_rules('location', 'Location', 'required',
            array('required' => 'You must provide a %s.')
          );
    $this->form_validation->set_rules('user_name', 'Username', 'required',
            array('required' => 'You must provide a %s.')
          );
     $this->form_validation->set_rules('mobile', 'Mobile Number', 'required',
            array('required' => 'You must provide a %s.')
    );
    
     $this->form_validation->set_rules('restaurant_email', 'Restaurant Email', 'required',
            array('required' => 'You must provide a %s.')
    );

     $this->form_validation->set_rules('openTime', 'Open Time', 'required',
            array('required' => 'You must provide a %s.')
    );   

    $this->form_validation->set_rules('closeTime', 'Close Time', 'required',
            array('required' => 'You must provide a %s.')
    );

    $this->form_validation->set_rules('password', 'Password', 'required',
            array('required' => 'You must provide a %s.')
    );

    if ($this->form_validation->run() == FALSE)
    { 
      $data['title']='Admin | Update Restaurant';
      $this->load->view('admin/header',$data);
      $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
      $this->load->view('admin/edit-restaurant',$data);
    }
    else
    {
      $post_data = (array) $this->input->post();
      unset($post_data['submit']);
      if(! $this->Admin_model->is_record_exist_update('restaurants','user_name', $post_data['user_name'], $id))
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
          
        $result = $this->Admin_model->updateData('restaurants',$post_data, $id);
        if($result)
        {
          $this->session->set_flashdata("message",'Successfully updated restaurant.');
          redirect("admin/restaurants");
        }else{
          $this->session->set_flashdata("message",'Something went wrong.');
          redirect("admin/restaurants");
        } 
      }else{
        $data['error']="<h3 style='color:#fdb813'>Username already exists!</h3>";
        $data['title']='Admin | Update Restaurant';
        $this->load->view('admin/header',$data);
        $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
        $this->load->view('admin/edit-restaurant',$data);
      } 
    }
  }

  public function delete_category($id)
  {  
    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    $this->db->query("Delete from `categories` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>Category deleted successfully...</div>');

    redirect("Admin/categories");
  }
  /*
    Amenities Master
  */
  public function amenities()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
     }

     $data['title']='Admin | Amenities';
      $this->load->view('admin/header',$data);
      $data['amenities']=$this->Admin_model->selectAllData('amenities','id DESC');
      $this->load->view('admin/amenities',$data);

  }


  public function add_amenities()
  {
    $data['title']='Admin | Amenities';
    $this->load->view('admin/header',$data);
    $this->load->view('admin/add-amenities',$data);
  }

  public function store_amenities()
  {
    $this->form_validation->set_rules('name', 'Name', 'required',
            array('required' => 'You must provide a %s.')
          );

    if ($this->form_validation->run() == FALSE)
    { 

    }
    else
    {
      $post_data = (array) $this->input->post();
      unset($post_data['submit']);
       if(!empty($_FILES['icon']['name']))
          {
            $_FILES['file']['name']     = $_FILES['icon']['name'];
            $_FILES['file']['type']     = $_FILES['icon']['type'];
            $_FILES['file']['tmp_name'] = $_FILES['icon']['tmp_name'];
            $_FILES['file']['error']    = $_FILES['icon']['error'];
            $_FILES['file']['size']     = $_FILES['icon']['size'];
            
            $uploadPath = 'assets/uploaded/amenities_images/';
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            // Load and initialize upload library
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            // Upload file to server
            if($this->upload->do_upload('file')){
                $fileData = $this->upload->data();
                $post_data['icon'] = $fileData['file_name'];
            }
          }
      
      if(! $this->Admin_model->is_record_exist('amenities','name', $post_data['name']))
      {
        $result = $this->Admin_model->insertAllData('amenities',$post_data);
        if($result)
        {
          $data['error']="<h3 style='color:#fdb813'>Amenity submitted successfully</h3>";
        }
      }else{
          $data['error']="<h3 style='color:#fdb813'>Name already exists!</h3>";
      }
    }

    $data['title']='Admin | Amenities';
    $this->load->view('admin/header',$data);
    $this->load->view('admin/add-amenities',$data);
   }

  public function edit_amenities($id)
  {

    $data['title']='Admin | Update Amenities';
    $query = $this->db->get_where('amenities', array('id' => $id));
    $data['datas'] = $query->row();
    $this->load->view('admin/header',$data);
    $this->load->view('admin/add-amenities',$data);
  }

  public function update_amenities($id)
  {

    $this->form_validation->set_rules('name', 'Name', 'required',
            array('required' => 'You must provide a %s.')
          );

    if ($this->form_validation->run() == FALSE)
    { 
      $data['title']='Admin | Update Amenities';
      $query = $this->db->get_where('amenities', array('id' => $id));
      $data['datas'] = $query->row();
      $this->load->view('admin/header',$data);
      $this->load->view('admin/add-amenities',$data);
    }
    else
    {
      $post_data = (array) $this->input->post();
      unset($post_data['submit']);
       if(!empty($_FILES['icon']['name']))
          {
            $_FILES['file']['name']     = $_FILES['icon']['name'];
            $_FILES['file']['type']     = $_FILES['icon']['type'];
            $_FILES['file']['tmp_name'] = $_FILES['icon']['tmp_name'];
            $_FILES['file']['error']    = $_FILES['icon']['error'];
            $_FILES['file']['size']     = $_FILES['icon']['size'];
            
            $uploadPath = 'assets/uploaded/amenities_images/';
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            // Load and initialize upload library
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            // Upload file to server
            if($this->upload->do_upload('file')){
                $fileData = $this->upload->data();
                $post_data['icon'] = $fileData['file_name'];
            }
          }

        if(! $this->Admin_model->is_record_exist_update('amenities','name', $post_data['name'], $id) )
        {
          $result = $this->Admin_model->updateData('amenities',$post_data, $id);
          if($result)
          {
            $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
                <i class="fa fa-check"></i>
                <button type="button" class="close" data-dismiss="alert">
                  <span aria-hidden="true">&times;</span>
                  <span class="sr-only">Close</span>
                </button>
                <strong>Successfully updated.!</strong></div>');

              redirect("admin/amenities");
          }
        }else{
           $data['error']="<h3 style='color:#fdb813'>Name already exists!</h3>";
           $data['title']='Admin | Update Amenities';
           $query = $this->db->get_where('amenities', array('id' => $id));
           $data['datas'] = $query->row();
            $this->load->view('admin/header',$data);
            $this->load->view('admin/add-amenities',$data);
        }
      
    }
  }

  public function delete_amenities($id)
  {  
    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    $this->db->query("Delete from `amenities` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>Deleted successfully...</div>');

    redirect("admin/amenities");
  }


  public function  users()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Super Admin | Users';
    $this->load->view('admin/header',$data);
    $data['user']= $this->Admin_model->selectAllData('users','id DESC');
    $this->load->view('admin/user',$data);

  }


   public function UserDelete($id){

    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    $this->db->query("Delete from `users` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>User Deleted successfully...</div>');

    redirect("admin/users");
   }



  public function generate_qr_code($res_id)
  { 

    extract($_POST);
    $is_count = $this->Admin_model->selectAllById('qr_codes','id ASC','restaurant_id',$res_id);
    if(count($is_count))
    {
    	$start = count($is_count); 

    }else
    {
		$start = 1; 
    }


    //$this->Admin_model->delete_qr($tables,$res_id);
    $this->Admin_model->update_number_of_table($tables,$res_id);
    $barcode = new \Com\Tecnick\Barcode\Barcode();

    $targetPath = "/home/logicals/public_html/rapidine/assets/qr_codes/";
    
   
    if (! is_dir($targetPath)) 
    {
        mkdir($targetPath, 0777, true);
    }

   for ($i=$start; $i <= $tables ; $i++) 
   {
    // $restaurant['restaurant_code'] = $res_id;
    // $restaurant['table_no'] = "$i";
    $restaurant = array(
      'restaurant_code'=> $res_id, 
      'table_no' => "$i"
    );
    $data = json_encode($restaurant); 

    $bobj = $barcode->getBarcodeObj('QRCODE,H', $data, - 16, - 16, 'black', array(
        - 2,
        - 2,
        - 2,
        - 2
    ))->setBackgroundColor('#f0f0f0');
    
    $imageData = $bobj->getPngData();
    $timestamp = $res_id."-".$i."-".time();
    $save_data['restaurant_id'] = $res_id; 
    $save_data['table_no'] = $i; 
    $save_data['qr_code'] = $timestamp; 
    $this->Admin_model->insertAllData('qr_codes',$save_data);
    
    file_put_contents($targetPath . $timestamp . '.png', $imageData);   
    }

    $this->session->set_flashdata("message",'<div class="alert alert-success alert-dismissible" role="alert">
    <i class="fa fa-check"></i>
    <button type="button" class="close" data-dismiss="alert">
      <span aria-hidden="true">&times;</span>
      <span class="sr-only">Close</span>
    </button>
    Successfully generated QR Codes</div>');
    redirect("admin/qr_codes/".$res_id);
  }

  public function qr_codes($id){


    $data['title']='Restaurant | QR Code';
    $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
    $data['qr_codes'] = $this->Admin_model->selectAllById('qr_codes','id ASC','restaurant_id',$id);
    $this->load->view('admin/header',$data);
    $this->load->view('admin/genrt_qr_code',$data);
  }  

  public function payments(){
    $from_date = $to_date = "";
    extract($_GET);
    $data['title']='Restaurant | Payment';
    $convenience_fees =  $this->UserModel->get_convenience_fees();
    $data['convenience_fees'] =  $this->UserModel->get_convenience_fees();
    $data['con_tax'] =  round($convenience_fees * 18 / 100);
    $data['data'] = $this->Admin_model->all_restaurant($from_date,$to_date); 
    $data['controller']=$this; 
    $this->load->view('admin/header',$data);
    $this->load->view('admin/payments',$data);
  } 
  public function generate_pdf($id){
    $data['title']='Restaurant | QR Codes';
    $codes = $this->Admin_model->selectAllById('qr_codes','id ASC','restaurant_id',$id);
    if(count($codes))
    {
       $html = "<!DOCTYPE html>
              <html>
              <body>";
      foreach ($codes as $key => $value) 
      {
        $img = file_get_contents( base_url('assets/qr_codes/').$value['qr_code'].".png"); 
        $image_base = base64_encode($img); 
        $html .="<div class='testing' style='width:25%; float:left; margin-bottom: 15px;'>";
        $html .="<img height='200' width='210' src='data:image/png;base64, $image_base' alt='Red dot'/>";
        $html .="<b><p style='padding-left: 67px;'>Table No. {$value['table_no']} </p></b>";
        $html .="</div>";
      }
       $html .= "</body>
              </html>";
     print_r($html);        
    }
  }
  
}