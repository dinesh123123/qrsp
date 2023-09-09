<?php
if(!defined('BASEPATH')) exit ('No direct script access allowed');
require_once ('vendor/autoload.php');
// require_once ('vendor1/autoload.php');
// use Dompdf\Dompdf;
class Genrate extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Admin_model');
    $this->load->model('UserModel');
    $this->load->library('form_validation');
    $this->load->helper('text');
    date_default_timezone_set('Asia/Calcutta');
  }

  public function index()
{
   
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

$formarray=array();


$unique_id= $this->input->post('unique_id');
$result_fetch=$this->db->select('id,unique_id')
                      ->where('unique_id',$unique_id)
                      ->get('qr_code_genrater')
                      ->row();

     if($result_fetch){
      $un_id=$result_fetch->unique_id;
     $messge = array('message' => 'Unique ID Already Exits','class' => 'alert alert-danger in');
    $this->session->set_flashdata('item', $messge);
    redirect('Genrate/qr_code','refresh');
     }else{
     $messge = array('message' => 'QR Code Genrated Successfully','class' => 'alert alert-success in');
    $this->session->set_flashdata('item', $messge);
     }                 



$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }
     if(!empty($unique_id))
    {
      $formarray['unique_id'] = $unique_id;
      $unique_id_type='YES';
    }


if(!empty($_FILES['image']['name']))
        {
            $config['upload_path'] = './assets/qr_image/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 10000000;
            $config['file_name'] = $_FILES['image']['name'];

            $this->load->library('upload',$config);
            $this->upload->initialize($config);

            if($this->upload->do_upload('image'))
            {
                $uploadData = $this->upload->data();
                $image = $uploadData['file_name'];
                $formarray['image']=$image;
    
            }
            else
            {
               $image = '';    
               $formarray['image']=$image;
        
        }
        $img_type="YES";
    }else{

    }
    
    if($this->input->post('type') || $this->input->post('title') || $this->input->post('unit') || $this->input->post('information') || isset($image)){
  
    }else{
     $messge = array('message' => 'Please enter atleast one of type, unit, title, image, information','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
    redirect('Genrate/qr_code','refresh');
    }


 $formarray['created_date']=date('Y-m-d');


 $this->db->insert('qr_code_genrater',$formarray);
 $insert_id=$this->db->insert_id();

 if($insert_id){
$result=$this->db->where('id',$insert_id)->get('qr_code_genrater')->row();
if($result){
 
    $unique_id=$result->unique_id;
}

 }



   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';

$qrcode = $path.time().".png";
// $qrcode = time().".png";

 
$data_value=array(

'unique_id'=>$unique_id);

// $data = json_encode($data_value); 



$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);

 $merge = "$".$random1.$unique_id."&".$random2;

 $value=strrev(($merge));

$new_value=md5($value);



$msg = 'To scan the code use the QRSP app';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);



// echo "<img src='".$qrcode."'>";


 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);

 $this->db->where('id',$insert_id)->update('qr_code_genrater',$image);
 
//  print_r($image);
// die;

 redirect('Genrate/qr_code');
 

}

//..............................04 march 2023.................//

public function qr_code(){

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='QR Code';
    // $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
    $data['qr_code'] = $this->Admin_model->get_qr_code_table();


    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code',$data);
  }
  
  public function qr_code2(){

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='QR Code';
    // $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
    $data['qr_code'] = $this->Admin_model->get_qr_code_table();


    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code2',$data);
  }
  
  public function save_qr_code()
{
   
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

$formarray=array();


$unique_id= $this->input->post('unique_id');
$result_fetch=$this->db->select('id,unique_id')
                      ->where('unique_id',$unique_id)
                      ->get('qr_code_genrater')
                      ->row();

     if($result_fetch){
      $un_id=$result_fetch->unique_id;
     $messge = array('message' => 'Unique ID Already Exits','class' => 'alert alert-danger in');
    $this->session->set_flashdata('item', $messge);
    redirect('Genrate/qr_code','refresh');
     }else{
     $messge = array('message' => 'QR Code Genrated Successfully','class' => 'alert alert-success in');
    $this->session->set_flashdata('item', $messge);
     }                 



/*$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }*/
     if(!empty($unique_id))
    {
      $formarray['unique_id'] = $unique_id;
      $unique_id_type='YES';
    }


/*if(!empty($_FILES['image']['name']))
        {
            $config['upload_path'] = './assets/qr_image/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 10000000;
            $config['file_name'] = $_FILES['image']['name'];

            $this->load->library('upload',$config);
            $this->upload->initialize($config);

            if($this->upload->do_upload('image'))
            {
                $uploadData = $this->upload->data();
                $image = $uploadData['file_name'];
                $formarray['image']=$image;
    
            }
            else
            {
               $image = '';    
               $formarray['image']=$image;
        
        }
        $img_type="YES";
    }else{

    }
    
    if($this->input->post('type') || $this->input->post('title') || $this->input->post('unit') || $this->input->post('information') || isset($image)){
  
    }else{
     $messge = array('message' => 'Please enter atleast one of type, unit, title, image, information','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
    redirect('Genrate/qr_code','refresh');
    }*/


 $formarray['created_date']=date('Y-m-d H:i:s');


 $this->db->insert('qr_code_genrater',$formarray);
 $insert_id=$this->db->insert_id();

 if($insert_id){
$result=$this->db->where('id',$insert_id)->get('qr_code_genrater')->row();
if($result){
 
    $unique_id=$result->unique_id;
}

 }



   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';

$qrcode = $path.time().".png";
// $qrcode = time().".png";

 
$data_value=array(

'unique_id'=>$unique_id);

// $data = json_encode($data_value); 



$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);

 $merge = "$".$random1.$unique_id."&".$random2;

 $value=strrev(($merge));

$new_value=md5($value);



// $msg = 'To scan the code use the QRSP app';

$msg = 'To Scan the QR use OriVeri App. Click here to download the app';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);



// echo "<img src='".$qrcode."'>";


 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);

 $this->db->where('id',$insert_id)->update('qr_code_genrater',$image);
 
//  print_r($image);
// die;

 redirect('Genrate/qr_list');
 

}


public function import(){
        $data = array();
        $memData = array();
        
        // If import request is submitted
        if($this->input->post('submit')){
            // Form field validation rules
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
            
            // Validate submitted form data
            if($this->form_validation->run() == true){
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
                
                // If file uploaded
                if(is_uploaded_file($_FILES['file']['tmp_name'])){
                    // Load CSV reader library
                    $this->load->library('CSVReader');
                    
                    // Parse data from CSV file
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
                    /*print_r($csvData);die;*/
                    // Insert/update CSV data into database
                    if(!empty($csvData)){
                        foreach($csvData as $row){ $rowCount++;
                            
                            // Prepare data for DB insertion
                            $memData = array(
                               'created_date' => date('Y-m-d H:i:s'),
                                'update_date' => date('Y-m-d H:i:s'),
                                'unique_id' => $row['UniqueID'],
                                /*'phone' => $row['Phone'],
                                'status' => $row['Status'],*/
                            );
                            
                            // Check whether email already exists in the database
                            $con = array(
                                'where' => array(
                                    'unique_id' => $row['UniqueID']
                                ),
                                'returnType' => 'count'
                            );
                            $prevCount = $this->Admin_model->getRows($con);
                            
                            if($prevCount > 0){
                                // Update qr data
                                // $condition = array('unique_id' => $row['UniqueID']);
                                // $update = $this->Admin_model->update_qr($memData, $condition);
                                
                                // if($update){
                                //     $updateCount++;
                                // }
                            }else{
                                // Insert qr data
                                $insert = $this->Admin_model->insert_qr($memData);
                                
                                if($insert){
                                    $insertCount++;
                                }
                                
                                require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';

$qrcode = $path.time().".png";

 
$data_value=array(

'unique_id'=>$row['UniqueID']);

$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);

 $merge = "$".$random1.$row['UniqueID']."&".$random2;

 $value=strrev(($merge));

$new_value=md5($value);

// $msg = 'To scan the code use the QRSP app';
$msg = 'To Scan the QR use OriVeri App. Click here to download the app';

$data ="$msg 
 
$new_value
";

QRcode::png("$data",$qrcode, 'H', 4, 4);
 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);

 $this->db->where('id',$insert)->update('qr_code_genrater',$image);
                            }
                        }
                        
                        // Status message with imported data count
                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'QR imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                }else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            }else{
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('Genrate/qr_list');
    }
    
    
    public function import2(){
        $data = array();
        $memData = array();
        
        // If import request is submitted
        if($this->input->post('submit')){
            // Form field validation rules
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
            
            // Validate submitted form data
            if($this->form_validation->run() == true){
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
                
                // If file uploaded
                if(is_uploaded_file($_FILES['file']['tmp_name'])){
                    // Load CSV reader library
                    $this->load->library('CSVReader');
                    
                    // Parse data from CSV file
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
                    /*print_r($csvData);die;*/
                    // Insert/update CSV data into database
                    if(!empty($csvData)){
                        foreach($csvData as $row){ $rowCount++;
                            
                            // Prepare data for DB insertion
                            $memData = array(
                                'title' => $row['Title'],
                                'unique_id' => $row['UniqueID'],
                                'type' => $row['Type'],
                                'unit' => $row['Unit'],
                                'information' => $row['Information'],
                            );
                            
                            // Check whether email already exists in the database
                            $con = array(
                                'where' => array(
                                    'unique_id' => $row['UniqueID']
                                ),
                                'returnType' => 'count'
                            );
                            $prevCount = $this->Admin_model->getRows($con);
                            
                            if($prevCount > 0){
                                // Update qr data
                                $condition = array('unique_id' => $row['UniqueID']);
                                $update = $this->Admin_model->update_qr($memData, $condition);
                                
                                if($update){
                                    $updateCount++;
                                }
                            }else{
                                // Insert qr data
                                $insert = $this->Admin_model->insert_qr($memData);
                                
                                if($insert){
                                    $insertCount++;
                                }
                                
                            }
                        }
                        
                        // Status message with imported data count
                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'QR imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                }else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            }else{
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('Genrate/qr_code_list');
    }
    
    /*
     * Callback function to check file value and type during validation
     */
    public function file_check($str){
        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != ""){
            $mime = get_mime_by_extension($_FILES['file']['name']);
            $fileAr = explode('.', $_FILES['file']['name']);
            $ext = end($fileAr);
            if(($ext == 'csv') && in_array($mime, $allowed_mime_types)){
                return true;
            }else{
                $this->form_validation->set_message('file_check', 'Please select only CSV file to upload.');
                return false;
            }
        }else{
            $this->form_validation->set_message('file_check', 'Please select a CSV file to upload.');
            return false;
        }
    }

  public function qr_code_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='QR Code List';
    $this->load->view('admin/header',$data);
    $data['qr_code']= $this->Admin_model->selectAllData('qr_code_genrater','id desc');
    // echo "<pre>"; print_r($data['qr_code']);die;
    $this->load->view('admin/qr_code_list',$data);

  }
  
  public function qr_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='QR Code List';
    $this->load->view('admin/header',$data);
    $data['qr_code']= $this->Admin_model->selectAllData('qr_code_genrater','id desc');
    // echo "<pre>"; print_r($data['qr_code']);die;
    $this->load->view('admin/qr_list',$data);

  }
  
   public function QrDelete($id){

    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    
    
    $resultscan=$this->db->select('id,unique_id')
                  ->where('id',$id)
                  ->get('qr_code_genrater')
                  ->row();
   if($resultscan){
          $unique_id=$resultscan->unique_id;

          $this->db->where('unique_id',$unique_id)->delete('scan_history');
        }
    
//     $wheredata1 = array('field'=>['*'],'table'=>'qr_code_genrater','where'=>array('id'=>$id));
//   $image = $this->Admin_model->select_single_row_specific($wheredata1);  
//   if($image->qr_code && file_exists($image->qr_code)){
//     unlink($image->qr_code);}
//     if($image->image && file_exists('assets/qr_image/'.$image->image)){
//     unlink('assets/qr_image/'.$image->image);}
    
    
    $this->db->query("Delete from `qr_code_genrater` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-danger alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>QR Deleted successfully...</div>');

    redirect("Genrate/qr_code_list");
   }
   
   public function QrDelete2($id){

    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    
    
    $resultscan=$this->db->select('id,unique_id')
                  ->where('id',$id)
                  ->get('qr_code_genrater')
                  ->row();
   if($resultscan){
          $unique_id=$resultscan->unique_id;

          $this->db->where('unique_id',$unique_id)->delete('scan_history');
        }
    
//     $wheredata1 = array('field'=>['*'],'table'=>'qr_code_genrater','where'=>array('id'=>$id));
//   $image = $this->Admin_model->select_single_row_specific($wheredata1);  
//   if($image->qr_code && file_exists($image->qr_code)){
//     unlink($image->qr_code);}
//     if($image->image && file_exists('assets/qr_image/'.$image->image)){
//     unlink('assets/qr_image/'.$image->image);}
    
    
    $this->db->query("Delete from `qr_code_genrater` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-danger alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>QR Deleted successfully...</div>');

    redirect("Genrate/qr_list");
   }
   
   public function qrcodeview($id)
   {

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='QR Code';
    // $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
    $data['qr_code'] = $this->Admin_model->get_qr_codeimage($id);


    $this->load->view('admin/header',$data);
    $this->load->view('admin/qrcodeview',$data);
   }

     public function qr_code_edit($id)
   {

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='QR Code Update';
    // $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
     $data['qr_code'] = $this->Admin_model->get_qr_code_edit($id);


    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code_edit',$data);
   }

public function update_qr_code($id)
   {

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }


    $data['title']='Update QR Code';
$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }

     


if(!empty($_FILES['image']['name']))
        {
            $config['upload_path'] = './assets/qr_image/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 10000000;
            $config['file_name'] = $_FILES['image']['name'];

            $this->load->library('upload',$config);
            $this->upload->initialize($config);

            if($this->upload->do_upload('image'))
            {
                $uploadData = $this->upload->data();
                $image = $uploadData['file_name'];
                $formarray['image']=$image;
    
            }
            else
            {
               $image = '';    
               $formarray['image']=$image;
        
        }
        $img_type="YES";
    }else{

    }


 $formarray['update_date']=date('Y-m-d H:i:s');

$this->db->where('id',$id)->update('qr_code_genrater',$formarray);


$this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong> Update QR Data successfully...</div>');

redirect("Genrate/qr_code_list");

}

//.................22 march 2023...............................// 

public function add_qr()
{
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

$formarray=array();


$unique_id= $this->input->post('unique_id');
$result_fetch=$this->db->select('id,unique_id')
                      ->where('unique_id',$unique_id)
                      ->get('qr_code_genrater')
                      ->row();

                      

     if($result_fetch){
      $un_id=$result_fetch->unique_id;
     $messge = array('message' => 'successfully Add Data','class' => 'alert alert-success in');
    $this->session->set_flashdata('item', $messge);
       }else{
        // die('else');
     $messge = array('message' => 'Unique ID Not Found','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
      redirect('Genrate/qr_code','refresh');

     }                 



$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
$url=$this->input->post('url');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }
     if(!empty($unique_id))
    {
      $formarray['unique_id'] = $unique_id;
      $unique_id_type='YES';
    }
     if(!empty($url))
	{
	  $formarray['image'] = $url;
	  $unique_id_type='YES';
	}


// if(!empty($_FILES['image']['name']))
//         {
//             $config['upload_path'] = './assets/qr_image/';
//             $config['allowed_types'] = 'jpg|jpeg|png|gif';
//             $config['max_size'] = 10000000;
//             $config['file_name'] = $_FILES['image']['name'];

//             $this->load->library('upload',$config);
//             $this->upload->initialize($config);

//             if($this->upload->do_upload('image'))
//             {
//                 $uploadData = $this->upload->data();
//                 $image = $uploadData['file_name'];
//                 $formarray['image']=$image;
    
//             }
//             else
//             {
//               $image = '';    
//               $formarray['image']=$image;
        
//         }
//         $img_type="YES";
//     }else{

//     }
    
    if($this->input->post('type') || $this->input->post('title') || $this->input->post('unit') || $this->input->post('information') || isset($url)){
  
    }else{
     $messge = array('message' => 'Please enter atleast one of type, unit, title, image, information','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
    redirect('Genrate/qr_code','refresh');
    }


 $formarray['update_date']=date('Y-m-d H:i:s');

$this->db->where('unique_id',$unique_id)->update('qr_code_genrater',$formarray);

 redirect('Genrate/qr_code');
}

//......................29 march 2023.................................//

public function importUpdate(){


        $data = array();
        $memData = array();
  
        if($this->input->post('submit')){
           
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
            
            // Validate submitted form data
            if($this->form_validation->run() == true){
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
               
                if(is_uploaded_file($_FILES['file']['tmp_name'])){
                
                    $this->load->library('CSVReader');
                    
                    // Parse data from CSV file
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
                    
                    if(!empty($csvData)){
                        foreach($csvData as $row){ $rowCount++;
                            
                          
                            if(!empty($row['Unit'])){

                            }else{
                                $row['Unit']=0;
                            }



                            $memData = array( 
                                // 'unique_id' => $row['UniqueID'],
                                'type' => $row['Type'],
                                'unit' => $row['Unit'],
                                'title' => $row['Title'],
                                'image' => $row['Image'],
                                'information' => $row['Information'],   
                                'update_date' => date('Y-m-d H:i:s'),   
                            );
                            
                            // Check whether email already exists in the database
                            $con = array(
                                'where' => array(
                                    'unique_id' => $row['UniqueID']
                                ),
                                'returnType' => 'count'
                            );
                            $prevCount = $this->Admin_model->getRows($con);
                            
                            if($prevCount > 0){
                                // Update qr data
                                $condition = array('unique_id' => $row['UniqueID']);
                                $update = $this->Admin_model->update_qr($memData, $condition);
                                
                                if($update){
                                    $updateCount++;
                                }
                            }

                        }
                        
                        // Status message with imported data count
                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'QR imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                }else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            }else{
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('Genrate/qr_code_list');
    }

//........................07 April 2023...................................//

public function add_image_multiple()
{
if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Terms Conditions';
    $this->load->view('admin/header',$data);
    $this->load->view('admin/add_image_multiple');
}


 public function  gallary_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Feedback List';
    $this->load->view('admin/header',$data);
    // $data['feedback']= $this->Admin_model->selectAllData('feedback','id DESC');
    $data['gallery']= $this->Admin_model->gallary_list();

    
   // echo "<pre>"; print_r($data['gallery']);die;
    $this->load->view('admin/gallary_list',$data);

  }

public function add_multiple()
{
	    $uploads_dir = 'assets/gallary/';

foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
  $file_name = $_FILES['files']['name'][$key];
  $file_size = $_FILES['files']['size'][$key];
  $file_tmp = $_FILES['files']['tmp_name'][$key];
  $file_type = $_FILES['files']['type'][$key];
  $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

  $new_file_name = uniqid() . '.' . $file_ext;
  $target_file = $uploads_dir . $new_file_name;
  // echo "<pre>";

  // print_r($target_file);

  if (move_uploaded_file($file_tmp, $target_file)) {
    

    $data['image']=$new_file_name;
    $data['date']=date('d-m-Y');
    // print_r($data);
    $this->db->insert('images',$data);
    // echo "File $file_name has been uploaded successfully.<br>";
  } else {
    // echo "Error uploading file $file_name.<br>";
  }
}
redirect('Genrate/gallary_list');

}

//......................phase 2 start 17 jun 2023............................//
//..........................One time Qr genrate.............................//
 public function one_time_scan(){

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='One time Scan Genrates';

    $this->load->view('admin/header',$data);
    $this->load->view('admin/one_time_scan',$data);
  }
  
  public function one_qr_genrate_old()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
      }

    $unique_id= $this->input->post('unique_id');
    
   $fetch=$this->db->order_by('id','desc')
                   ->get('one_qr_code_genrater')
                   ->row();
   if($fetch){
      $unique_last_id=$fetch->unique_id;
      $str = ltrim($unique_last_id, 'O');
      $unique_id=$unique_id;
      $end=$str+$unique_id;
      $start=$str+1;
      
        for($x = $start; $x <= $end; $x++){

         	$result=sprintf("%013d", $x);

    		$data['unique_id']='O'.$result;
    		$data['created_date']=date('d-m-Y');
    		$data['created_time']=date('H:i');
         	$insert=$this->db->insert('one_qr_code_genrater',$data);

    }
     
   }else{
       $unique_id=$unique_id;
       for($x = 1; $x <= $unique_id; $x++){

	$result=sprintf("%013d", $x);

		$data['unique_id']='O'.$result;
		$data['created_date']=date('d-m-Y');
		$data['created_time']=date('H:i');
	$insert=$this->db->insert('one_qr_code_genrater',$data);

    }
   }                   

   redirect('Genrate/one_time_list');
  }
  
//............One time list.............................//
public function one_time_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='One Time QR Code List';
    // $data['title']='. ';
    $this->load->view('admin/header',$data);
    // $data['qr_code']= $this->Admin_model->selectAllData('one_qr_code_genrater','id desc');
    $data['qr_code']= $this->Admin_model->selectAllById('one_qr_code_genrater','id desc','unique_id!=',' ');
    $data['qr_code_pdf']= $this->Admin_model->selectAllById_one();
    // echo "<pre>"; print_r($data['qr_code']);die;
    $this->load->view('admin/one_time_list',$data);

  }
  
    public function one_time_delete($id){

    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    
    
    $resultscan=$this->db->select('id,unique_id')
                  ->where('id',$id)
                  ->get('one_qr_code_genrater')
                  ->row();
   if($resultscan){
          $unique_id=$resultscan->unique_id;

          $this->db->where('unique_id',$unique_id)->delete('scan_history');
        }

    
    
    $this->db->query("Delete from `one_qr_code_genrater` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-danger alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>QR Deleted successfully...</div>');

    redirect("Genrate/one_time_list");
   }
   

//...............20 jun 2023........................//
public function one_qr_genrate()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
      }

    $unique_id= $this->input->post('unique_id');
    
   $fetch_start=$this->db->order_by('id','asc')
                   ->where('unique_id','')
                   ->get('one_qr_code_genrater')
                   ->row();
    $fetch_end=$this->db->order_by('id','desc')
                   ->where('unique_id','')
                   ->get('one_qr_code_genrater')
                   ->row();               
   if($fetch_start){
      $start_id=$fetch_start->id;
      $end_id=$fetch_end->id;
     
     
        for($x = $start_id; $x <= $end_id; $x++){
            
     

         	$result=sprintf("%013d", $x);

    		$data['unique_id']='O'.$result;
    		$unique_id=	$data['unique_id'];
    		$data['created_date']=date('d-m-Y');
  
    		$data['created_time']=date('H:i');
  
    
    		
    
   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;
 
 

 $value=strrev(($merge));

$new_val=md5($value);

 $new_value = "O".$new_val;


// $msg = 'To Scan the QR use OriVeri App. Click here to download the app';
$msg = 'To scan QR download app OriVeri.com';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 $data=array('unique_id'=>$unique_id,
             'created_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

    		
        //  	$insert=$this->db->insert('one_qr_code_genrater',$data);
         	$insert=$this->db->where('id',$x)
         	                ->update('one_qr_code_genrater',$data);

    }
  
   }else{
       die('else');
       $unique_id=$unique_id;
       for($x = 1; $x <= $unique_id; $x++){

	$result=sprintf("%013d", $x);
// print_r($result);
// die;
		$data['unique_id']='O'.$result;
		$unique=	'O'.$result;
		$data['created_date']=date('d-m-Y');
		$data['update_date']=date('d-m-Y');
		$data['created_time']=date('H:i');
		$data['update_time']=date('H:i');

   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
// $qrcode = $path.time().".png";
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;
//  $merge = "$".$random1.$unique."&".$random2;

 $value=strrev(($merge));

$new_val=md5($value);
 $new_value = "O".$new_val;


// $msg = 'To Scan the QR use OriVeri App. Click here to download the app';
$msg = 'To scan QR download app OriVeri.com';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 
 $data=array('unique_id'=>$unique,
             'created_date'=>date('d-m-Y'),
             'update_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'update_time'=>date('H:i'),
              'qr_type'=>'ONE',
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

	$insert=$this->db->insert('one_qr_code_genrater',$data);

    }
    
   }                

   redirect('Genrate/one_time_list');
  }
  
//................................QR Management..................................//
  public function qr_code_one_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='One Time Data List';
    $this->load->view('admin/header',$data);
    $data['qr_code']= $this->Admin_model->selectAllData('one_qr_code_genrater','id desc');
    // echo "<pre>"; print_r($data['qr_code']);die;
    $this->load->view('admin/qr_code_one_list',$data);

  } 
   public function qr_code_one_edit($id)
   {
       

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='One Time QR Code Update';

     $data['qr_code'] = $this->Admin_model->qr_code_one_edit($id);

    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code_one_edit',$data);
   }
//...................................................//

public function update_qr_code_one($id)
   {

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }


    $data['title']='Update QR Code';
$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
$image=$this->input->post('image');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }
     if(!empty($image))
    {
      $formarray['image'] = $image;
      $unit_type='YES';
    }

     



 $formarray['update_date']=date('d-m-Y');
 $formarray['update_time']=date('H:i');

$this->db->where('id',$id)->update('one_qr_code_genrater',$formarray);


$this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong> Update QR Data successfully...</div>');

redirect("Genrate/qr_code_one_list");

}
//...............................................................//
public function qr_code_one(){

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='QR Code';
    // $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
    $data['qr_code'] = $this->Admin_model->get_qr_code_table_one();
     $data['fetch_one']  =$this->db->order_by('id','asc')
                   ->where('unique_id','')
                   ->get('one_qr_code_genrater')
                   ->row();


    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code_one',$data);
  }
  
//.......................................................//
public function add_qr_one()
{
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

$formarray=array();


$unique_id= $this->input->post('unique_id');
$result_fetch=$this->db->select('id,unique_id')
                      ->where('unique_id',$unique_id)
                      ->get('one_qr_code_genrater')
                      ->row();

                      

     if($result_fetch){
      $un_id=$result_fetch->unique_id;
     $messge = array('message' => 'successfully Add Data','class' => 'alert alert-success in');
    $this->session->set_flashdata('item', $messge);
       }else{
        // die('else');
     $messge = array('message' => 'Unique ID Not Found','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
      redirect('Genrate/qr_code_one','refresh');

     }                 



$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
$url=$this->input->post('url');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }
     if(!empty($unique_id))
    {
      $formarray['unique_id'] = $unique_id;
      $unique_id_type='YES';
    }
     if(!empty($url))
	{
	  $formarray['image'] = $url;
	  $unique_id_type='YES';
	}


    
    if($this->input->post('type') || $this->input->post('title') || $this->input->post('unit') || $this->input->post('information') || isset($url)){
  
    }else{
     $messge = array('message' => 'Please enter atleast one of type, unit, title, image, information','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
    redirect('Genrate/qr_code_one','refresh');
    }


 $formarray['update_date']=date('d-m-Y');
 $formarray['update_time']=date('H:i');

$this->db->where('unique_id',$unique_id)->update('one_qr_code_genrater',$formarray);

 redirect('Genrate/qr_code_one_list');
}

//.....................21 jun 2023 ......one import data update.................//
public function importUpdate_one(){

        $data = array();
        $memData = array();
  
        if($this->input->post('submit')){
           
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
            
          
            if($this->form_validation->run() == true){
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
               
                if(is_uploaded_file($_FILES['file']['tmp_name'])){
                
                    $this->load->library('CSVReader');
                    
        
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
                   
                    
                    if(!empty($csvData)){
                        foreach($csvData as $row){ $rowCount++;
                            
                          
                            if(!empty($row['Unit'])){

                            }else{
                                $row['Unit']=0;
                            }
                             if(!empty($row['S.no'])){
                   
                           
                            $memData = array( 
                                // 's.no' => $row['S.no'],
                                'type' => $row['Type'],
                                'unit' => $row['Unit'],
                                'title' => $row['Title'],
                                'image' => $row['Image'],
                                'information' => $row['Information'],   
                                'update_date' => date('d-m-Y'),   
                                'update_time' => date('H:i'),   
                            );
                            

                            $con = array(
                                'where' => array(
                                    // 'unique_id' => $row['UniqueID']
                                ),
                                'returnType' => 'count'
                            );
                            // $prevCount = $this->Admin_model->getRows_one($con);
                            
                            // if($prevCount > 0){
                    
                            //     // $condition = array('unique_id' => $row['UniqueID']);
                            //     // $update = $this->Admin_model->update_qr_one($memData, $condition);
                                
                            //     if($update){
                            //         $updateCount++;
                            //     }
                            // }
                        $this->db->insert('one_qr_code_genrater',$memData) ;   
                            
                        }
                            
                        } 
                    // die;

                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'QR imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                }else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            }else{
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('Genrate/qr_code_one_list');
    }
//.......................30 jun 2023................................................//
  public function two_time_scan(){

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='Two time Scan Genrates';

    $this->load->view('admin/header',$data);
    $this->load->view('admin/two_time_scan',$data);
  }
//......................................................................//
   
   public function two_qr_genrate_old()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
      }

    $unique_id= $this->input->post('unique_id');
    
   $fetch=$this->db->order_by('id','desc')
                   ->get('two_qr_code_genrater')
                   ->row();
   if($fetch){
      $unique_last_id=$fetch->unique_id;
      $str = ltrim($unique_last_id, 'T');
      $unique_id=$unique_id;
      $end=$str+$unique_id;
      $start=$str+1;
      
        for($x = $start; $x <= $end; $x++){

         	$result=sprintf("%013d", $x);

    		$data['unique_id']='T'.$result;
    		$unique_id=	$data['unique_id'];
    		$data['created_date']=date('d-m-Y');
    		$data['update_date']=date('d-m-Y');
    		$data['created_time']=date('H:i');
    		$data['update_time']=date('H:i');
    
    		
    
   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
// $qrcode = $path.time().".png";
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;
 
 

 $value=strrev(($merge));

$new_val=md5($value);

 $new_value = "T".$new_val;

$msg = 'To Scan the QR use OriVeri App. Click here to download the app';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 
 $data=array('unique_id'=>$unique_id,
             'created_date'=>date('d-m-Y'),
             'update_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'update_time'=>date('H:i'),
             'qr_type'=>'TWO',
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

    		
         	$insert=$this->db->insert('two_qr_code_genrater',$data);

    } 
     
   }else{
       $unique_id=$unique_id;
       for($x = 1; $x <= $unique_id; $x++){

	$result=sprintf("%013d", $x);

		$data['unique_id']='T'.$result;
		$unique=	'T'.$result;
		$data['created_date']=date('d-m-Y');
		$data['update_date']=date('d-m-Y');
		$data['created_time']=date('H:i');
		$data['update_time']=date('H:i');

   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
// $qrcode = $path.time().".png";
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;

 $value=strrev(($merge));

$new_val=md5($value);
 $new_value = "T".$new_val;


$msg = 'To Scan the QR use OriVeri App. Click here to download the app';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 
 $data=array('unique_id'=>$unique,
             'created_date'=>date('d-m-Y'),
             'update_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'update_time'=>date('H:i'),
              'qr_type'=>'TWO',
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

		
	$insert=$this->db->insert('two_qr_code_genrater',$data);

    }
   }                   

   redirect('Genrate/two_time_list');
  }
//............................................................//
 public function two_time_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Two Time QR Code List';
    $this->load->view('admin/header',$data);
    // $data['qr_code']= $this->Admin_model->selectAllData('two_qr_code_genrater','id desc');
    $data['qr_code']= $this->Admin_model->selectAllById('two_qr_code_genrater','id desc','unique_id!=',' ');
    $data['qr_code_pdf']= $this->Admin_model->selectAllById_two();
    // echo "<pre>"; print_r($data['qr_code']);die;
    $this->load->view('admin/two_time_list',$data);

  }
//..............................................................//
  public function qr_code_two_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Two Time Data List';
    $this->load->view('admin/header',$data);
    $data['qr_code']= $this->Admin_model->selectAllData('two_qr_code_genrater','id desc');
    $this->load->view('admin/qr_code_two_list',$data);

  }  
//................................................................//
   public function qr_code_two_edit($id)
   {
     if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='Two Time QR Code Update';

     $data['qr_code'] = $this->Admin_model->qr_code_two_edit($id);

    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code_two_edit',$data);
   }
//....................................................................//
public function update_qr_code_two($id)
   {

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }


    $data['title']='Update QR Code';
$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
$image=$this->input->post('image');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }
     if(!empty($image))
    {
      $formarray['image'] = $image;
      $unit_type='YES';
    }

  
 $formarray['update_date']=date('d-m-Y');
 $formarray['update_time']=date('H:i');

$this->db->where('id',$id)->update('two_qr_code_genrater',$formarray);


$this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong> Update QR Data successfully...</div>');

redirect("Genrate/qr_code_two_list");

}
//..........................................................//
public function qr_code_two(){

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='Two QR Code random data add ';
    // $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
    $data['qr_code'] = $this->Admin_model->get_qr_code_table_two();
     $data['fetch_two']  =$this->db->order_by('id','asc')
                   ->where('unique_id','')
                   ->get('two_qr_code_genrater')
                   ->row();



    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code_two',$data);
  }
//..................................................................//
public function add_qr_two()
{
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

$formarray=array();


$unique_id= $this->input->post('unique_id');
$result_fetch=$this->db->select('id,unique_id')
                      ->where('unique_id',$unique_id)
                      ->get('two_qr_code_genrater')
                      ->row();

                      

     if($result_fetch){
      $un_id=$result_fetch->unique_id;
     $messge = array('message' => 'successfully Add Data','class' => 'alert alert-success in');
    $this->session->set_flashdata('item', $messge);
       }else{
        // die('else');
     $messge = array('message' => 'Unique ID Not Found','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
      redirect('Genrate/qr_code_two','refresh');

     }                 



$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
$url=$this->input->post('url');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }
     if(!empty($unique_id))
    {
      $formarray['unique_id'] = $unique_id;
      $unique_id_type='YES';
    }
     if(!empty($url))
	{
	  $formarray['image'] = $url;
	  $unique_id_type='YES';
	}


    
    if($this->input->post('type') || $this->input->post('title') || $this->input->post('unit') || $this->input->post('information') || isset($url)){
  
    }else{
     $messge = array('message' => 'Please enter atleast one of type, unit, title, image, information','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
    redirect('Genrate/qr_code_two','refresh');
    }


 $formarray['update_date']=date('d-m-Y');
 $formarray['update_time']=date('H:i');

$this->db->where('unique_id',$unique_id)->update('two_qr_code_genrater',$formarray);

 redirect('Genrate/qr_code_two_list');
}
//....................................................................//
public function importUpdate_two_old(){

        $data = array();
        $memData = array();
  
        if($this->input->post('submit')){
           
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
            
            // Validate submitted form data
            if($this->form_validation->run() == true){
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
               
                if(is_uploaded_file($_FILES['file']['tmp_name'])){
                
                    $this->load->library('CSVReader');
                    
                    // Parse data from CSV file
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
                    
                    if(!empty($csvData)){
                        foreach($csvData as $row){ $rowCount++;
                            
                          
                            if(!empty($row['Unit'])){

                            }else{
                                $row['Unit']=0;
                            }
                            
                          



                            $memData = array( 
                                // 'unique_id' => $row['UniqueID'],
                                'type' => $row['Type'],
                                'unit' => $row['Unit'],
                                'title' => $row['Title'],
                                'image' => $row['Image'],
                                'information' => $row['Information'],   
                                'update_date' => date('d-m-Y'),   
                                'update_time' => date('H:i'),   
                            );
                            
                            // Check whether email already exists in the database
                            $con = array(
                                'where' => array(
                                    'unique_id' => $row['UniqueID']
                                ),
                                'returnType' => 'count'
                            );
                            $prevCount = $this->Admin_model->getRows_two($con);
                            
                            if($prevCount > 0){
                                // Update qr data
                                $condition = array('unique_id' => $row['UniqueID']);
                                $update = $this->Admin_model->update_qr_two($memData, $condition);
                                
                                if($update){
                                    $updateCount++;
                                }
                            }

                        }
                        
                        // Status message with imported data count
                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'QR imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                }else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            }else{
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('Genrate/qr_code_two_list');
    }
//..........................REset.........................................//
//........................................................//
 public function reset_time_scan(){

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='Reset Time Scan Genrates';

    $this->load->view('admin/header',$data);
    $this->load->view('admin/reset_time_scan',$data);
  }
//..................................................................//

   public function reset_qr_genrate_old()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
      }

    $unique_id= $this->input->post('unique_id');
    
   $fetch=$this->db->order_by('id','desc')
                   ->get('reset_qr_code_genrater')
                   ->row();
   if($fetch){
      $unique_last_id=$fetch->unique_id;
      $str = ltrim($unique_last_id, 'R');
      $unique_id=$unique_id;
      $end=$str+$unique_id;
      $start=$str+1;
      
        for($x = $start; $x <= $end; $x++){

         	$result=sprintf("%013d", $x);

    		$data['unique_id']='R'.$result;
    		$unique_id=	$data['unique_id'];
    		$data['created_date']=date('d-m-Y');
    		$data['update_date']=date('d-m-Y');
    		$data['created_time']=date('H:i');
    		$data['update_time']=date('H:i');
    
    		
    
   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
// $qrcode = $path.time().".png";
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;
 
 

 $value=strrev(($merge));

$new_val=md5($value);

 $new_value = "R".$new_val;

$msg = 'To Scan the QR use OriVeri App. Click here to download the app';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 
 $data=array('unique_id'=>$unique_id,
             'created_date'=>date('d-m-Y'),
             'update_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'update_time'=>date('H:i'),
             'qr_type'=>'RESET',
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

    		
         	$insert=$this->db->insert('reset_qr_code_genrater',$data);

    } 
     
   }else{
       $unique_id=$unique_id;
       for($x = 1; $x <= $unique_id; $x++){

	$result=sprintf("%013d", $x);

		$data['unique_id']='R'.$result;
		$unique=	'R'.$result;
		$data['created_date']=date('d-m-Y');
		$data['update_date']=date('d-m-Y');
		$data['created_time']=date('H:i');
		$data['update_time']=date('H:i');

   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
// $qrcode = $path.time().".png";
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;

 $value=strrev(($merge));

$new_val=md5($value);
 $new_value = "R".$new_val;


$msg = 'To Scan the QR use OriVeri App. Click here to download the app';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 
 $data=array('unique_id'=>$unique,
             'created_date'=>date('d-m-Y'),
             'update_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'update_time'=>date('H:i'),
              'qr_type'=>'RESET',
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

		
	$insert=$this->db->insert('reset_qr_code_genrater',$data);

    }
   }                   

   redirect('Genrate/reset_time_list');
  }
//......................................................//
public function reset_time_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Reset Time QR Code List';
    $this->load->view('admin/header',$data);
    // $data['qr_code']= $this->Admin_model->selectAllData('reset_qr_code_genrater','id desc');
    $data['qr_code']= $this->Admin_model->selectAllById('reset_qr_code_genrater','id desc','unique_id!=',' ');
    $data['qr_code_pdf']= $this->Admin_model->selectAllById_reset();
    // echo "<pre>"; print_r($data['qr_code']);die;
    $this->load->view('admin/reset_time_list',$data);

  }
//........................................................//
 public function qr_code_reset_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Reset Time Data List';
    $this->load->view('admin/header',$data);
    $data['qr_code']= $this->Admin_model->selectAllData('reset_qr_code_genrater','id desc');
    $this->load->view('admin/qr_code_reset_list',$data);

  } 
  
//.....................................................................//
  public function qr_code_reset_edit($id)
   {
     if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='Reset Time QR Code Update';

     $data['qr_code'] = $this->Admin_model->qr_code_reset_edit($id);

    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code_reset_edit',$data);
   }
//..............................................................................//
public function update_qr_code_reset($id)
   {

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }


    $data['title']='Update QR Code';
$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
$image=$this->input->post('image');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }
     if(!empty($image))
    {
      $formarray['image'] = $image;
      $unit_type='YES';
    }

  
 $formarray['update_date']=date('d-m-Y');
 $formarray['update_time']=date('H:i');

$this->db->where('id',$id)->update('reset_qr_code_genrater',$formarray);


$this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong> Update QR Data successfully...</div>');

redirect("Genrate/qr_code_reset_list");

}
//............................................................//
public function qr_code_reset(){

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='Reset QR Code random data add ';
    // $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
    $data['qr_code'] = $this->Admin_model->get_qr_code_table_reset();
     $data['fetch_reset']  =$this->db->order_by('id','asc')
                   ->where('unique_id','')
                   ->get('reset_qr_code_genrater')
                   ->row();


    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code_reset',$data);
  }
//...........................................................................//
public function add_qr_reset()
{
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

$formarray=array();


$unique_id= $this->input->post('unique_id');
$result_fetch=$this->db->select('id,unique_id')
                      ->where('unique_id',$unique_id)
                      ->get('reset_qr_code_genrater')
                      ->row();

                      

     if($result_fetch){
      $un_id=$result_fetch->unique_id;
     $messge = array('message' => 'successfully Add Data','class' => 'alert alert-success in');
    $this->session->set_flashdata('item', $messge);
       }else{
        // die('else');
     $messge = array('message' => 'Unique ID Not Found','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
      redirect('Genrate/qr_code_reset','refresh');

     }                 



$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
$url=$this->input->post('url');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }
     if(!empty($unique_id))
    {
      $formarray['unique_id'] = $unique_id;
      $unique_id_type='YES';
    }
     if(!empty($url))
	{
	  $formarray['image'] = $url;
	  $unique_id_type='YES';
	}


    
    if($this->input->post('type') || $this->input->post('title') || $this->input->post('unit') || $this->input->post('information') || isset($url)){
  
    }else{
     $messge = array('message' => 'Please enter atleast one of type, unit, title, image, information','class' => 'alert alert-warning in');
    $this->session->set_flashdata('item', $messge);
    redirect('Genrate/qr_code_reset','refresh');
    }


 $formarray['update_date']=date('d-m-Y');
 $formarray['update_time']=date('H:i');

$this->db->where('unique_id',$unique_id)->update('reset_qr_code_genrater',$formarray);

 redirect('Genrate/qr_code_reset_list');
}
//........................................................................//
public function importUpdate_reset_old(){

        $data = array();
        $memData = array();
  
        if($this->input->post('submit')){
           
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
            
            // Validate submitted form data
            if($this->form_validation->run() == true){
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
               
                if(is_uploaded_file($_FILES['file']['tmp_name'])){
                
                    $this->load->library('CSVReader');
                    
                    // Parse data from CSV file
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
                    
                    if(!empty($csvData)){
                        foreach($csvData as $row){ $rowCount++;
                            
                          
                            if(!empty($row['Unit'])){

                            }else{
                                $row['Unit']=0;
                            }
                            
                          



                            $memData = array( 
                                // 'unique_id' => $row['UniqueID'],
                                'type' => $row['Type'],
                                'unit' => $row['Unit'],
                                'title' => $row['Title'],
                                'image' => $row['Image'],
                                'information' => $row['Information'],   
                                'update_date' => date('d-m-Y'),   
                                'update_time' => date('H:i'),   
                            );
                            
                            // Check whether email already exists in the database
                            $con = array(
                                'where' => array(
                                    'unique_id' => $row['UniqueID']
                                ),
                                'returnType' => 'count'
                            );
                            $prevCount = $this->Admin_model->getRows_reset($con);
                            
                            if($prevCount > 0){
                                // Update qr data
                                $condition = array('unique_id' => $row['UniqueID']);
                                $update = $this->Admin_model->update_qr_reset($memData, $condition);
                                
                                if($update){
                                    $updateCount++;
                                }
                            }

                        }
                        
                        // Status message with imported data count
                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'QR imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                }else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            }else{
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('Genrate/qr_code_reset_list');
    }
//...............................13 july 2023................................//
public function importUpdate_two(){

        $data = array();
        $memData = array();
  
        if($this->input->post('submit')){
           
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
            
          
            if($this->form_validation->run() == true){
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
               
                if(is_uploaded_file($_FILES['file']['tmp_name'])){
                
                    $this->load->library('CSVReader');
                    
        
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
                   
                    
                    if(!empty($csvData)){
                        foreach($csvData as $row){ $rowCount++;
                            
                          
                            if(!empty($row['Unit'])){

                            }else{
                                $row['Unit']=0;
                            }
                             if(!empty($row['S.no'])){
                   
                           
                            $memData = array( 
                                // 's.no' => $row['S.no'],
                                'type' => $row['Type'],
                                'unit' => $row['Unit'],
                                'title' => $row['Title'],
                                'image' => $row['Image'],
                                'qr_type' => 'TWO',
                                'information' => $row['Information'],   
                                'update_date' => date('d-m-Y'),   
                                'update_time' => date('H:i'),   
                            );
                            

                            $con = array(
                                'where' => array(
                                    // 'unique_id' => $row['UniqueID']
                                ),
                                'returnType' => 'count'
                            );
                            // $prevCount = $this->Admin_model->getRows_one($con);
                            
                            // if($prevCount > 0){
                    
                            //     // $condition = array('unique_id' => $row['UniqueID']);
                            //     // $update = $this->Admin_model->update_qr_one($memData, $condition);
                                
                            //     if($update){
                            //         $updateCount++;
                            //     }
                            // }
                        $this->db->insert('two_qr_code_genrater',$memData) ;   
                            
                        }
                            
                        } 
                    // die;

                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'QR imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                }else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            }else{
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('Genrate/qr_code_two_list');
    }
//.............................................................................//
public function two_qr_genrate()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
      }

    $unique_id= $this->input->post('unique_id');
    
   $fetch_start=$this->db->order_by('id','asc')
                   ->where('unique_id','')
                   ->get('two_qr_code_genrater')
                   ->row();
    $fetch_end=$this->db->order_by('id','desc')
                   ->where('unique_id','')
                   ->get('two_qr_code_genrater')
                   ->row();               
   if($fetch_start){
      $start_id=$fetch_start->id;
      $end_id=$fetch_end->id;
     
     
        for($x = $start_id; $x <= $end_id; $x++){
            
     

         	$result=sprintf("%013d", $x);

    		$data['unique_id']='T'.$result;
    		$unique_id=	$data['unique_id'];
    		$data['created_date']=date('d-m-Y');
  
    		$data['created_time']=date('H:i');
  
    
    		
    
   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;
 
 

 $value=strrev(($merge));

$new_val=md5($value);

 $new_value = "T".$new_val;


// $msg = 'To Scan the QR use OriVeri App. Click here to download the app';
$msg = 'To scan QR download app OriVeri.com';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 $data=array('unique_id'=>$unique_id,
             'created_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

    		
        //  	$insert=$this->db->insert('one_qr_code_genrater',$data);
         	$insert=$this->db->where('id',$x)
         	                ->update('two_qr_code_genrater',$data);

    }
  
   }else{
       die('else');
       $unique_id=$unique_id;
       for($x = 1; $x <= $unique_id; $x++){

	$result=sprintf("%013d", $x);
// print_r($result);
// die;
		$data['unique_id']='O'.$result;
		$unique=	'O'.$result;
		$data['created_date']=date('d-m-Y');
		$data['update_date']=date('d-m-Y');
		$data['created_time']=date('H:i');
		$data['update_time']=date('H:i');

   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
// $qrcode = $path.time().".png";
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;
//  $merge = "$".$random1.$unique."&".$random2;

 $value=strrev(($merge));

$new_val=md5($value);
 $new_value = "O".$new_val;


// $msg = 'To Scan the QR use OriVeri App. Click here to download the app';
$msg = 'To scan QR download app OriVeri.com';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 
 $data=array('unique_id'=>$unique,
             'created_date'=>date('d-m-Y'),
             'update_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'update_time'=>date('H:i'),
              'qr_type'=>'ONE',
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

	$insert=$this->db->insert('one_qr_code_genrater',$data);

    }
    
   }                

   redirect('Genrate/two_time_list');
  }
//..........................................................................//
public function importUpdate_reset(){

        $data = array();
        $memData = array();
  
        if($this->input->post('submit')){
           
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
            
          
            if($this->form_validation->run() == true){
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
               
                if(is_uploaded_file($_FILES['file']['tmp_name'])){
                
                    $this->load->library('CSVReader');
                    
        
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
                   
                    
                    if(!empty($csvData)){
                        foreach($csvData as $row){ $rowCount++;
                            
                          
                            if(!empty($row['Unit'])){

                            }else{
                                $row['Unit']=0;
                            }
                             if(!empty($row['S.no'])){
                   
                           
                            $memData = array( 
                                // 's.no' => $row['S.no'],
                                'type' => $row['Type'],
                                'unit' => $row['Unit'],
                                'title' => $row['Title'],
                                'image' => $row['Image'],
                                'qr_type' => 'RESET',
                                'information' => $row['Information'],   
                                'update_date' => date('d-m-Y'),   
                                'update_time' => date('H:i'),   
                            );
                            

                            $con = array(
                                'where' => array(
                                    // 'unique_id' => $row['UniqueID']
                                ),
                                'returnType' => 'count'
                            );
                            // $prevCount = $this->Admin_model->getRows_one($con);
                            
                            // if($prevCount > 0){
                    
                            //     // $condition = array('unique_id' => $row['UniqueID']);
                            //     // $update = $this->Admin_model->update_qr_one($memData, $condition);
                                
                            //     if($update){
                            //         $updateCount++;
                            //     }
                            // }
                        $this->db->insert('reset_qr_code_genrater',$memData) ;   
                            
                        }
                            
                        } 
                    // die;

                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'QR imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                }else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            }else{
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('Genrate/qr_code_reset_list');
    }
//..............................................................................//
public function reset_qr_genrate()
  {
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
      }

    $unique_id= $this->input->post('unique_id');
    
   $fetch_start=$this->db->order_by('id','asc')
                   ->where('unique_id','')
                   ->get('reset_qr_code_genrater')
                   ->row();
    $fetch_end=$this->db->order_by('id','desc')
                   ->where('unique_id','')
                   ->get('reset_qr_code_genrater')
                   ->row();               
   if($fetch_start){
      $start_id=$fetch_start->id;
      $end_id=$fetch_end->id;
     
     
        for($x = $start_id; $x <= $end_id; $x++){
            
     

         	$result=sprintf("%013d", $x);

    		$data['unique_id']='R'.$result;
    		$unique_id=	$data['unique_id'];
    		$data['created_date']=date('d-m-Y');
  
    		$data['created_time']=date('H:i');
  
    
    		
    
   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;
 
 

 $value=strrev(($merge));

$new_val=md5($value);

 $new_value = "R".$new_val;


// $msg = 'To Scan the QR use OriVeri App. Click here to download the app';
$msg = 'To scan QR download app OriVeri.com';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 $data=array('unique_id'=>$unique_id,
             'created_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

    		
        //  	$insert=$this->db->insert('one_qr_code_genrater',$data);
         	$insert=$this->db->where('id',$x)
         	                ->update('reset_qr_code_genrater',$data);

    }
  
   }else{
       die('else');
       $unique_id=$unique_id;
       for($x = 1; $x <= $unique_id; $x++){

	$result=sprintf("%013d", $x);
// print_r($result);
// die;
		$data['unique_id']='O'.$result;
		$unique=	'O'.$result;
		$data['created_date']=date('d-m-Y');
		$data['update_date']=date('d-m-Y');
		$data['created_time']=date('H:i');
		$data['update_time']=date('H:i');

   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
// $qrcode = $path.time().".png";
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique_id);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;
//  $merge = "$".$random1.$unique."&".$random2;

 $value=strrev(($merge));

$new_val=md5($value);
 $new_value = "O".$new_val;


// $msg = 'To Scan the QR use OriVeri App. Click here to download the app';
$msg = 'To scan QR download app OriVeri.com';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	

 
 
 $data=array('unique_id'=>$unique,
             'created_date'=>date('d-m-Y'),
             'update_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'update_time'=>date('H:i'),
              'qr_type'=>'ONE',
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             );

	$insert=$this->db->insert('one_qr_code_genrater',$data);

    }
    
   }                

   redirect('Genrate/reset_time_list');
  }
//......................infinite time scan...................................//
public function qr_code_infinite_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Infinite Time Data List';
    $this->load->view('admin/header',$data);
    $data['qr_code']= $this->Admin_model->selectAllData('infinite_qr_code_genrater','id desc');
    // echo "<pre>"; print_r($data['qr_code']);die;
    $this->load->view('admin/qr_code_infinite_list',$data);

  } 
public function infinite_time_list()
  {

    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

    $data['title']='Infinite Time QR Code List';
    $this->load->view('admin/header',$data);
    // $data['qr_code']= $this->Admin_model->selectAllData('one_qr_code_genrater','id desc');
    $data['qr_code']= $this->Admin_model->selectAllById('infinite_qr_code_genrater','id desc','unique_id!=',' ');
    $data['qr_code_pdf']= $this->Admin_model->selectAllById_infinte();
    // echo "<pre>"; print_r($data['qr_code']);die;
    $this->load->view('admin/infinite_time_list',$data);

  }  
public function qr_code_infinite(){

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='QR Code Infinite Genrate';
    // $data['datas'] = $this->Admin_model->select_single_row('restaurants','id',$id); 
    $data['qr_code'] = $this->Admin_model->get_qr_code_table_one();
     $data['fetch_infinite']  =$this->db->order_by('id','asc')
                   ->where('unique_id','')
                   ->get('infinite_qr_code_genrater')
                   ->row();


    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code_infinite',$data);
  }  
//..........................................................................//
public function add_qr_infinite()
{
    if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }

$formarray=array();


$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
$url=$this->input->post('url');
$code_start=$this->input->post('code_start');
$code_end=$this->input->post('code_end');

$start=date("d-m-Y H:i", strtotime($code_start));
$end=date("d-m-Y H:i", strtotime($code_end));
$dataadd=array('code_start'=>$start,'code_end'=>$end);
//.................................................................//

   $fetch=$this->db->order_by('id','desc')
                   ->get('infinite_qr_code_genrater')
                   ->row();
    if($fetch){
        $id=$fetch->id;
        $x=($id+1);
         
    }else{
        $x='1';
    } 
    
       $result=sprintf("%013d", $x);
	
		
		$data['unique_id']='I'.$result;
	    $unique=	'I'.$result;
	    $unique_id=	'I'.$result;
		$data['created_date']=date('d-m-Y');
		$data['update_date']=date('d-m-Y');
		$data['created_time']=date('H:i');
		$data['update_time']=date('H:i');

   require_once 'phpqrcode/qrlib.php';

$path   = 'assets/qr_codes/';
// $qrcode = $path.time().".png";
$qrcode = $path.time().$x.".png";

$data_value=array(

'unique_id'=>$unique);


$this->load->helper('string');
$random1= random_string('alpha',3);
$random2= random_string('alpha',5);


 $merge = "$".$random1.$unique_id."&".$random2;

 $value=strrev(($merge));

$new_val=md5($value);
 $new_value = "I".$new_val;


// $msg = 'To Scan the QR use OriVeri App. Click here to download the app';
$msg = 'To scan QR download app OriVeri.com';

$data ="$msg 
 
$new_value
";



QRcode::png("$data",$qrcode, 'H', 4, 4);

 
 $image=array('qr_code'=>$qrcode,
'code'=>$new_value);	


 
 if(empty($type)){
     $type='';
 }
 if(empty($information)){
     $information='';
 }
 if(empty($title)){
     $title='';
 }
 if(empty($unit)){
     $unit='';
 }
 if(empty($url)){
     $url='';
 }
 
 $formarray=array('unique_id'=>$unique,
             'created_date'=>date('d-m-Y'),
             'update_date'=>date('d-m-Y'),
             'created_time'=>date('H:i'),
             'update_time'=>date('H:i'),
              'qr_type'=>'INFINITE',
             'qr_code'=>$qrcode,
             'code'=>$new_value,
             'type'=>$type,
             'information'=>$information,
             'title'=>$title,
             'unit'=>$unit,
             'image'=>$url,
             'code_start'=>$start,
             'code_end'=>$end,
             );

$this->db->insert('infinite_qr_code_genrater',$formarray);

 redirect('Genrate/qr_code_infinite_list');
}

//........................14 july 2023........................//
  public function two_time_delete($id){

    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    
    
    $resultscan=$this->db->select('id,unique_id')
                  ->where('id',$id)
                  ->get('two_qr_code_genrater')
                  ->row();
   if($resultscan){
          $unique_id=$resultscan->unique_id;

          $this->db->where('unique_id',$unique_id)->delete('scan_history');
        }

    
    
    $this->db->query("Delete from `two_qr_code_genrater` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-danger alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>QR Deleted successfully...</div>');

    redirect("Genrate/two_time_list");
   }
   
//........................................................................//
public function reset_time_delete($id){

    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    
    
    $resultscan=$this->db->select('id,unique_id')
                  ->where('id',$id)
                  ->get('reset_qr_code_genrater')
                  ->row();
   if($resultscan){
          $unique_id=$resultscan->unique_id;

          $this->db->where('unique_id',$unique_id)->delete('scan_history');
        }

    
    
    $this->db->query("Delete from `reset_qr_code_genrater` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-danger alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>QR Deleted successfully...</div>');

    redirect("Genrate/reset_time_list");
   }
//.................................................................//
public function infinite_time_delete($id){

    if (!$this->session->userdata('admin_id') ) 
    {
      redirect('admin');
    }
    
    
    $resultscan=$this->db->select('id,unique_id')
                  ->where('id',$id)
                  ->get('infinite_qr_code_genrater')
                  ->row();
   if($resultscan){
          $unique_id=$resultscan->unique_id;

          $this->db->where('unique_id',$unique_id)->delete('scan_history');
        }

    
    
    $this->db->query("Delete from `infinite_qr_code_genrater` where id = '".$id."'");
    $this->session->set_flashdata("success_req",'<div class="alert alert-danger alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong>QR Deleted successfully...</div>');

    redirect("Genrate/infinite_time_list");
   }
//..............................................................//
  public function qr_code_infinite_edit($id)
   {
       

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }
    $data['title']='Infinite Time Scan QR Code Update';

     $data['qr_code'] = $this->Admin_model->qr_code_infinite_edit($id);

    $this->load->view('admin/header',$data);
    $this->load->view('admin/qr_code_infinite_edit',$data);
   }
   
//................................................................//
public function update_qr_code_infinite($id)
   {

if (!$this->session->userdata('admin_id')){
      redirect('admin');
    }


    $data['title']='Update QR Code';
$type=$this->input->post('type');
$information=$this->input->post('information');
$title=$this->input->post('title');
$unit=$this->input->post('unit');
$image=$this->input->post('image');
    if(!empty($type))
    {
      $formarray['type'] = $type;
      $type_type='YES';
    }
    if(!empty($information))
    {
      $formarray['information'] = $information;
      $information_type='YES';
    }
    if(!empty($title))
    {
      $formarray['title'] = $title;
      $title_type='YES';
    }
    if(!empty($unit))
    {
      $formarray['unit'] = $unit;
      $unit_type='YES';
    }
     if(!empty($image))
    {
      $formarray['image'] = $image;
      $unit_type='YES';
    }

  
 $formarray['update_date']=date('d-m-Y');
 $formarray['update_time']=date('H:i');

$this->db->where('id',$id)->update('infinite_qr_code_genrater',$formarray);


$this->session->set_flashdata("success_req",'<div class="alert alert-success alert-dismissible" role="alert">
      <i class="fa fa-check"></i>
      <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Success!</strong> Update QR Data successfully...</div>');

redirect("Genrate/qr_code_infinite_list");

}

//.......................one pdf..........................//
 public function generate_pdf_code_one(){
    $data['title']='One time Qr code';
    // $codes = $this->Admin_model->selectAllById('one_qr_code_genrater','id ASC','unique_id !=','');
    $codes = $this->Admin_model->selectAllById_one();
    if(count($codes))
    {
       $html = "<!DOCTYPE html>
              <html>
              <head style = 'margin-right: 300px;'> <title style='color: white';> . </title> </head>
              <body>";
      foreach ($codes as $key => $value) 
      {
        $img = file_get_contents( base_url().$value['qr_code']); 
        $image_base = base64_encode($img); 
        $html .="<div class='testing' style='width:25%; float:left; margin-bottom: 5px; '>";
        $html .="<img height='90' width='90'  style='padding-left: 30px;' src='data:image/png;base64, $image_base' alt='Red dot'/><div style='margin-top: -11px;'><b style='margin-left: 40px; font-size:8px;'>Powered By OriVeri</b></div>";
        // $html .="<b><p style='padding-left: 10px; font-size: 12px'>OriVeri No. {$value['id']} </p></b>";
        $html .="<b><p style='padding-left: 20px; font-size: 15px ; ' ></p></b>";
        // $html .="<b><p style='padding-left: 10px; font-size: 12px'>OriVeri No. {$value['id']} </p></b>";
        $html .="</div>";
      }
      $pdfData['pdf']='1';
    //   $this->db->update('one_qr_code_genrater',$pdfData);
       $html .= "</body>
              </html>";
     print_r($html);   
     
    }
  }
 
//....................two pdf......................//
 public function generate_pdf_code_two(){
    //  die('two');
    $data['title']='Two time Qr code';
    // $codes = $this->Admin_model->selectAllById('two_qr_code_genrater','id ASC','unique_id !=','');
      $codes = $this->Admin_model->selectAllById_two();
    if(count($codes))
    {
       $html = "<!DOCTYPE html>
              <html>
                <head style = 'margin-right: 300px;'> <title style='color: white';> . </title> </head>
              <body>";
      foreach ($codes as $key => $value) 
      {
        $img = file_get_contents( base_url().$value['qr_code']); 
        $image_base = base64_encode($img); 
        $html .="<div class='testing' style='width:25%; float:left; margin-bottom: 5px; '>";
        $html .="<img height='90' width='90'  style='padding-left: 30px;' src='data:image/png;base64, $image_base' alt='Red dot'/><div style='margin-top: -11px;'><b style='margin-left: 40px; font-size:8px;'>Powered By OriVeri</b></div>";
        // $html .="<b><p style='padding-left: 10px; font-size: 12px'>OriVeri No. {$value['id']} </p></b>";
        $html .="<b><p style='padding-left: 20px; font-size: 15px ; ' ></p></b>";
        // $html .="<b><p style='padding-left: 10px; font-size: 12px'>OriVeri No. {$value['id']} </p></b>";
        $html .="</div>";
      }
      $pdfData['pdf']='1';
    //   $this->db->update('two_qr_code_genrater',$pdfData);
       $html .= "</body>
              </html>";
     print_r($html);  
    
     
    }
  }
  
//......................reset  pdf................................//
 public function generate_pdf_code_reset(){
    //  die('two');
    $data['title']='Reset time Qr code';
    // $codes = $this->Admin_model->selectAllById('reset_qr_code_genrater','id ASC','unique_id !=','');
    $codes = $this->Admin_model->selectAllById_reset();
    if(count($codes))
    {
       $html = "<!DOCTYPE html>
              <html>
               <head style = 'margin-right: 300px;'> <title style='color: white';> . </title> </head>
              <body>";
      foreach ($codes as $key => $value) 
      {
        $img = file_get_contents( base_url().$value['qr_code']); 
        $image_base = base64_encode($img); 
        $html .="<div class='testing' style='width:25%; float:left; margin-bottom: 5px; '>";
        $html .="<img height='90' width='90'  style='padding-left: 30px;' src='data:image/png;base64, $image_base' alt='Red dot'/><div style='margin-top: -11px;'><b style='margin-left: 40px; font-size:8px;'>Powered By OriVeri</b></div>";
        // $html .="<b><p style='padding-left: 10px; font-size: 12px'>OriVeri No. {$value['id']} </p></b>";
        $html .="<b><p style='padding-left: 20px; font-size: 15px ; ' ></p></b>";
        // $html .="<b><p style='padding-left: 10px; font-size: 12px'>OriVeri No. {$value['id']} </p></b>";
        $html .="</div>";
      }
            $pdfData['pdf']='1';
    //   $this->db->update('reset_qr_code_genrater',$pdfData);
       $html .= "</body>
              </html>";
     print_r($html);  
    
     
    }
  }
  
//......................infinite pdf................................//
 public function generate_pdf_code_infinite(){
    //  die('two');
    $data['title']='Infinite time Qr code';
    // $codes = $this->Admin_model->selectAllById('infinite_qr_code_genrater','id ASC','unique_id !=','');
     $codes = $this->Admin_model->selectAllById_infinte();
    if(count($codes))
    {
       $html = "<!DOCTYPE html>
              <html>
               <head style = 'margin-right: 300px;'> <title style='color: white';> . </title> </head>
              <body>";
      foreach ($codes as $key => $value) 
      {
        $img = file_get_contents( base_url().$value['qr_code']); 
        $image_base = base64_encode($img); 
        $html .="<div class='testing' style='width:25%; float:left; margin-bottom: 5px; '>";
        $html .="<img height='90' width='90'  style='padding-left: 30px;' src='data:image/png;base64, $image_base' alt='Red dot'/><div style='margin-top: -11px;'><b style='margin-left: 40px; font-size:8px;'>Powered By OriVeri</b></div>";
        // $html .="<b><p style='padding-left: 10px; font-size: 12px'>OriVeri No. {$value['id']} </p></b>";
        $html .="<b><p style='padding-left: 20px; font-size: 15px ; ' ></p></b>";
        // $html .="<b><p style='padding-left: 10px; font-size: 12px'>OriVeri No. {$value['id']} </p></b>";
        $html .="</div>";
      }
       $pdfData['pdf']='1';
    //   $this->db->update('infinite_qr_code_genrater',$pdfData);
       $html .= "</body>
              </html>";
     print_r($html);  
    
     
    }
  }  
//.............................................................................//




}
?>