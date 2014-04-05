<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resource extends CI_Controller {

	public function index(){
		$res_id = $this->input->get('id');
		$this->load->helper('download');
		$this->load->model('Resource_model');
		$res = $this->Resource_model->getResource($res_id);
		if($res){
			$data = @file_get_contents("assets/res/" . $res->id);
			if(!$data) return;
			$file_name = $res->filename;
			force_download($file_name, $data);
		}
	}
	
	public function d($resource_id){
		$this->load->helper('download');
		$this->load->model('Resource_model');
		$res = $this->Resource_model->getResource($resource_id);
		if($res){
			$data = @file_get_contents("assets/res/" . $res->id);
			if(!$data) return;
			$file_name = $res->filename;
			force_download($file_name, $data);
		}
	}
	
	public function g(){
		$res_id = $this->input->get('id');
		$this->load->helper('download');
		$this->load->model('Resource_model');
		$res = $this->Resource_model->getResource($res_id);
		if($res){
			$data = @file_get_contents("assets/res/" . $res->id);
			if(!$data) return;
			$file_name = $res->filename;
			force_download($file_name, $data);
		}
	}
	
	public function u(){
		$targetFolder = '/../Digime/assets/res'; // Relative to the root (of server !!!)
		if (!empty($_FILES)) {
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
			$fileName = $_FILES['Filedata']['name'];
			$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
	
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);
	
			if (in_array($fileParts['extension'],$fileTypes)) {
				
				// add entry in resources
				$this->load->model('Resource_model');
				$res_id = $this->Resource_model->addResource($fileName);
				$res_id = sprintf("%011d", $res_id);
				$targetFile = rtrim($targetPath,'/') . '/' . $res_id;
				move_uploaded_file($tempFile,$targetFile);
				echo '1';
			} else {
				echo 'Invalid file type.';
			}
		}else{
			echo 'file is empty';
		}
	}
	
}