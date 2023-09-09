<?php
if(!defined('BASEPATH')) exit ('No direct script access allowed');
require_once ('vendor/autoload.php');
// require_once ('vendor1/autoload.php');
// use Dompdf\Dompdf;
class SubAdmin extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Admin_model');
    $this->load->model('UserModel');
    $this->load->library('form_validation');
    $this->load->helper('text');
  }
  
  public function signup()
  {
      if($this->session->userdata('s_id')){
      redirect('SubAdmin/dashboard');
    }
    $data['title']='Super Sub Admin | QRSP';
    $this->load->view('sub-admin/header',$data);
    $this->load->view('sub-admin/signup',$data);
  }
  
  public function save()
  {
    $this->form_validation->set_rules('s_name', 'Name', 'required');
    $this->form_validation->set_rules('s_email', 'Email', 'required|valid_email|is_unique[sub_admin.s_email]');
    $this->form_validation->set_rules('s_password', 'Password', 'required');
    $this->form_validation->set_message('required', '%s is empty.');
    $this->form_validation->set_message('is_unique', '%s already exist.');
    if ($this->form_validation->run() == FALSE) {
        if($this->session->userdata('s_id')){
      redirect('SubAdmin/dashboard');
    }
      $data['title']='Super Sub Admin | QRSP';
    $this->load->view('sub-admin/header',$data);
    $this->load->view('sub-admin/signup',$data);
    } else {
      if (isset($_POST['submit'])) {
    $s_name = $this->input->post('s_name');
    $s_email = $this->input->post('s_email');
    $s_password = md5($this->input->post('s_password'));

    if(!empty($_FILES['s_image']['name'])){
                $config['upload_path'] = 'upload/sub-admin/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['file_name'] = $_FILES['s_image']['name'];
                $this->load->library('upload',$config);
                $this->upload->initialize($config);                
                if($this->upload->do_upload('s_image')){
                    $uploadData = $this->upload->data();
                    $s_image = $uploadData['file_name'];
                }else{
                    $s_image = '';
                }
            }else{
                $s_image = '';
            }

    $data = array('s_name' => $s_name,
    's_email' => $s_email,
    's_password' => $s_password,
    's_image' => $s_image,
    'created_date' => date('Y-m-d H:i:s', time()));

    $data = $this->Admin_model->insertAllData('sub_admin',$data);
    if ($data) {
      redirect('SubAdmin/dashboard');
    }
  }
}
}

  public function index()
  {
      if($this->session->userdata('s_id')){
      redirect('SubAdmin/dashboard');
    }
    $data['title']='Super Sub Admin | QRSP';
    $this->load->view('sub-admin/header',$data);
    $this->load->view('sub-admin/index',$data);
  }


  public function login()
  {
      if($this->session->userdata('s_id')){
      redirect('SubAdmin/dashboard');
    }
    $data['title']= 'Super Sub Admin | Rapidine';
    $this->load->view('sub-admin/header',$data);

    $this->load->library('form_validation');
    $this->form_validation->set_rules('s_email', 'Email', 'trim|required');
    $this->form_validation->set_rules('s_password', 'Password', 'trim|required');
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
        $q = $this->db->query("Select * from `sub_admin` where (`s_email`='".$this->input->post("s_email")."') and s_password='".md5($this->input->post("s_password"))."'  Limit 1");
            // print_r($q) ; 
        if ($q->num_rows() > 0)
        {
          $row = $q->row(); 
          
            $newdata = array(
             's_name'  => $row->s_name,
             's_email'     => $row->s_email,
             'logged_in' => TRUE,
             's_id'=>$row->s_id,
             's_image'=>$row->s_image
            );
            $this->session->set_userdata($newdata);
            redirect('SubAdmin/dashboard');       
          
        }
        else
        {
          $data["error"] = '<div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <strong>Warning!</strong> Invalid User and password. </div>';
        }
                   
                    
      }

    $this->load->view("sub-admin/index",$data);
  }

  function signout(){
    $this->session->sess_destroy();
    redirect("SubAdmin");
  }

  public function dashboard(){
    if(! $this->session->userdata('s_id')){
      redirect('SubAdmin');
    }
    $data['title']='Super Sub Admin| Dashboard ';
    // $data['qr_code'] = $this->Admin_model->count('reset_qr_code_genrater','id desc');
    $data['qr_code'] = $this->Admin_model->countreset();
    $data['user']  = $this->Admin_model->count('user','id desc');
    $data['feed']  = $this->Admin_model->count('feedback','id desc');
    $data['scan']  = $this->Admin_model->count('scan_history','id desc');
    // $data['banner']  = $this->Admin_model->count('banner_list','banner_id desc');
    // $data['banner']  = $this->Admin_model->count('banner_list','banner_id desc');
    $this->load->view('sub-admin/header',$data);   
    $this->load->view("sub-admin/dashboard",$data);
        
  }
  
  public function qr_code_reset_list()
  {

    if (!$this->session->userdata('s_id')){
      redirect('SubAdmin');
    }

    $data['title']='QR Reset Code List';
    $this->load->view('sub-admin/header',$data);
    // $data['qr_code']= $this->Admin_model->selectAllData('reset_qr_code_genrater','id desc');
    $data['qr_code']= $this->Admin_model->selectAllById('reset_qr_code_genrater','id desc','scan_status','1');
    // echo "<pre>"; print_r($data['qr_code']);die;
    $this->load->view('sub-admin/qr_code_list',$data);

  }
  
  public function profile()
  {

    if (!$this->session->userdata('s_id')){
      redirect('SubAdmin');
    }

    $data['title']='Profile';
    $this->load->view('sub-admin/header',$data);
    $this->load->view('sub-admin/profile',$data);

  }
  
//..........................03 jun 2023........................//

public function reseset_qr_code($id){

   if (!$this->session->userdata('s_id')){
      redirect('SubAdmin');
    }
    
    // print_r($id);
    // die;
    $wheredata1 = array('field'=>['*'],'table'=>'reset_qr_code_genrater','where'=>array('id'=>$id));
  $DataUpdate['scan_status']='0';

  $this->db->where('id',$id)->update('reset_qr_code_genrater',$DataUpdate);
    $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>Sub Admin Reset Code Successfully ...</div>');

    redirect("SubAdmin/qr_code_reset_list");
   }
   
//..............................................................//
public function reseset_qr_code_all(){

   if (!$this->session->userdata('s_id')){
      redirect('SubAdmin');
    }
    
  $DataUpdate['scan_status']='0';
  $this->db->update('reset_qr_code_genrater',$DataUpdate);
    $this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>Sub Admin All Code Reset Successfully ...</div>');

    redirect("SubAdmin/qr_code_reset_list");
   }

//................................................................//

//................................................................//


  
}