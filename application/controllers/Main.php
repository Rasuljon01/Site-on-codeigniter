<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('user');
		$this->load->helper('url');
		$this->load->helper('form');
		$this->isUserLoggedIn = $this->session->userdata('isUserLoggedIn');
	}

	public function registration(){
		$data = $userData = array();
		if($this->input->post('signupSubmit')){
			if(!empty($_FILES['picture']['name'])){
				$config['upload_path'] = 'uploads/images/';
				$config['allowed_types'] = 'jpg|jpeg|png|gif';
				$config['file_name'] = $_FILES['picture']['name'];
				$config['max_size'] = 2048;
				$config['max_width'] = 1024;
				$config['max_height'] = 768;

				$this->load->library('upload',$config);
				$this->upload->initialize($config);

				if($this->upload->do_upload('picture')){
					$uploadData = $this->upload->data();
					$picture = $uploadData['file_name'];
				}else{
					$picture = '';
				}
			}else{
				$picture = '';
			}
			$this->form_validation->set_rules('first_name', 'First Name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
			$this->form_validation->set_rules('password', 'password', 'required');
			$this->form_validation->set_rules('conf_password', 'confirm password', 'required|matches[password]');

			$userData = array(
				'first_name' => strip_tags($this->input->post('first_name')),
				'last_name' => strip_tags($this->input->post('last_name')),
				'email' => strip_tags($this->input->post('email')),
				'password' => md5($this->input->post('password')),
				'gender' => strip_tags($this->input->post('gender')),
				'phone' => strip_tags($this->input->post('phone')),
				'picture'=>$picture,
			);

			if($this->form_validation->run() == true){
				$insert = $this->user->insert($userData);
				if($insert){
					$this->session->set_userdata('success_msg', 'Регистрация вашей учетной записи прошла успешно. Пожалуйста, войдите в свой аккаунт.');
					redirect('login');
				}else{
					$data['error_msg'] = 'Некоторые проблемы возникли, пожалуйста, попробуйте еще раз.';
				}
			}else{
				$data['error_msg'] = 'Пожалуйста, заполните все обязательные поля.';
			}
		}
		$data['user'] = $userData;

		$this->load->view('head');
		$this->load->view('header_base');
		$this->load->view('registration', $data);
		$this->load->view('footer');
	}

	public function profile()
	{
		$this->load->view('head');
		$this->load->view('header_port');
		$this->load->view('account');
		$this->load->view('footer');
	}
	public function account(){
		$data = array();
		if($this->isUserLoggedIn){
			$con = array(
				'id' => $this->session->userdata('userId')
			);
			$data['user'] = $this->user->getRows($con);
		$this->load->view('head');
		$this->load->view('header_profile');
		$this->load->view('account', $data);
		$this->load->view('footer');
		}else{
			redirect('login');
		}
	}
	public function about()
	{
		$this->load->view('head');
		$this->load->view('header_about');
		$this->load->view('body_about');
		$this->load->view('footer');
	}



	public function index()
	{
		$this->load->view('head');
		$this->load->view('header_main');
		$this->load->view('body_main');
		$this->load->view('footer');
	}


	public function login(){
		$data = array();

		if($this->session->userdata('success_msg')){
			$data['success_msg'] = $this->session->userdata('success_msg');
			$this->session->unset_userdata('success_msg');
		}
		if($this->session->userdata('error_msg')){
			$data['error_msg'] = $this->session->userdata('error_msg');
			$this->session->unset_userdata('error_msg');
		}

		if($this->input->post('loginSubmit')){
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('password', 'password', 'required');

			if($this->form_validation->run() == true){
				$con = array(
					'returnType' => 'single',
					'conditions' => array(
						'email'=> $this->input->post('email'),
						'password' => md5($this->input->post('password')),
						'status' => 1
					)
				);
				$checkLogin = $this->user->getRows($con);
				if($checkLogin){
					$this->session->set_userdata('isUserLoggedIn', TRUE);
					$this->session->set_userdata('role',$checkLogin['role']);
					$this->session->set_userdata('userId', $checkLogin['Id']);
					redirect('profile');
				}else{
					$data['error_msg'] = 'Неверная почта или пароль.';
				}
			}else{
				$data['error_msg'] = 'Пожалуйста, заполните все обязательные поля.';
			}
		}

		$this->load->view('head');
		$this->load->view('header_base');
		$this->load->view('login', $data);
		$this->load->view('footer');
	}
	public function portfolio()
	{
		$this->load->view('head');
		$this->load->view('header_port');
		if($this->isUserLoggedIn){
			if($this->session->userdata('role')=='Администратор'){
				$this->load->helper('url');
				$this->load->view('person_view');
			}
            else{
            	//echo "<script>alert('У вас нет доступа к данной странице. Вы не администратор!')</script>";
				echo "<h2 style='color: black' align='center'>У вас нет доступа к странице! Для того чтобы получить доступ авторизуйтесь!</h2>";

			}
		}
		else{
			echo "<h2 style='color: black' align='center'>У вас нет доступа к странице! Для того чтобы получить доступ авторизуйтесь!</h2>";
		}
	}
	public function suppliers()
	{
		$this->load->view('head');
		$this->load->view('header_port');
		if($this->isUserLoggedIn){
			if($this->session->userdata('role')=='Администратор'){
				$this->load->helper('url');
				$this->load->view('suppliers_view');
			}
			else{
				//echo "<script>alert('У вас нет доступа к данной странице. Вы не администратор!')</script>";
				echo "<h2 style='color: black' align='center'>У вас нет доступа к странице! Для того чтобы получить доступ авторизуйтесь!</h2>";

			}
		}
		else{
			echo "<h2 style='color: black' align='center'>У вас нет доступа к странице! Для того чтобы получить доступ авторизуйтесь!</h2>";
		}
	}
	public function nameproduct()
	{
		$this->load->view('head');
		$this->load->view('header_port');
		if($this->isUserLoggedIn){
			if($this->session->userdata('role')=='Администратор'){
				$this->load->helper('url');
				$this->load->view('nameproduct_view');
			}
			else{
				//echo "<script>alert('У вас нет доступа к данной странице. Вы не администратор!')</script>";
				echo "<h2 style='color: black' align='center'>У вас нет доступа к странице! Для того чтобы получить доступ авторизуйтесь!</h2>";

			}
		}
		else{
			echo "<h2 style='color: black' align='center'>У вас нет доступа к странице! Для того чтобы получить доступ авторизуйтесь!</h2>";
		}
	}

	public function dishes()
	{
		$this->load->view('head');
		$this->load->view('header_port');
		if($this->isUserLoggedIn){
			if($this->session->userdata('role')=='Администратор'){
				$this->load->helper('url');
				$this->load->view('dishes_view');
			}
			else{
				//echo "<script>alert('У вас нет доступа к данной странице. Вы не администратор!')</script>";
				echo "<h2 style='color: black' align='center'>У вас нет доступа к странице! Для того чтобы получить доступ авторизуйтесь!</h2>";

			}
		}
		else{
			echo "<h2 style='color: black' align='center'>У вас нет доступа к странице! Для того чтобы получить доступ авторизуйтесь!</h2>";
		}
	}



	public function logout(){
		$this->session->unset_userdata('isUserLoggedIn');
		$this->session->unset_userdata('userId');
		$this->session->sess_destroy();
		redirect('login');
	}

	public function email_check($str){
		$con = array(
			'returnType' => 'count',
			'conditions' => array(
				'email' => $str
			)
		);
		$checkEmail = $this->user->getRows($con);
		if($checkEmail > 0){
			$this->form_validation->set_message('email_check', 'Данный адрес эл.почты уже занят.');
			return FALSE;
		}else{
			return TRUE;
		}
	}
	function add_order(){

			$this->load->view('head');
			$this->load->view('header_base');
			$this->load->view('footer');
	}
}
