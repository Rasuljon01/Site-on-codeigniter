<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Person extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('person_model','person');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('person_view');
	}

	public function ajax_list()
	{
		$list = $this->person->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();
			$row[] = $person->first_name ;
			$row[] = $person->last_name;
			$row[] = $person->gender;
			$row[] = $person->email;
			$row[] = $person->phone;
			$row[] = $person->role;
			if($person->status==1){
				$row[] = 'Подтверждён';
			}
			else{
				$row[] = 'Неподтверждён';
			}
			//добавить html для действия
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person('."'".$person->Id."'".')"><i class="glyphicon glyphicon-pencil"></i> Редактировать</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_person('."'".$person->Id."'".')"><i class="glyphicon glyphicon-trash"></i> Удалить</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->person->count_all(),
						"recordsFiltered" => $this->person->count_filtered(),
						"data" => $data,
				);
		//
        //вывод в формат json
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->person->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate(0);
		$data = array(
            'first_name' => strip_tags($this->input->post('firstName')),
            'last_name' => strip_tags($this->input->post('lastName')),
            'email' => strip_tags($this->input->post('email')),
            'phone' => strip_tags($this->input->post('phone')),
            'gender' => strip_tags($this->input->post('gender')),
            'status' => strip_tags($this->input->post('status')),
            'password' => md5($this->input->post('new_password')),
			'role' => strip_tags($this->input->post('role')),
			);
		$insert = $this->person->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate(1);
		if(strip_tags($this->input->post('new_password'))!==''){
            $data = array(
                'first_name' => strip_tags($this->input->post('firstName')),
                'last_name' => strip_tags($this->input->post('lastName')),
                'phone' => strip_tags($this->input->post('phone')),
                'email' => strip_tags($this->input->post('email')),
                'gender' => strip_tags($this->input->post('gender')),
                'status' => strip_tags($this->input->post('status')),
				'role' => strip_tags($this->input->post('role')),
                'password'=>md5($this->input->post('new_password')),
            );
        }
		else{
            $data = array(
                'first_name' => strip_tags($this->input->post('firstName')),
                'last_name' => strip_tags($this->input->post('lastName')),
                'phone' => strip_tags($this->input->post('phone')),
                'email' => strip_tags($this->input->post('email')),
                'gender' => strip_tags($this->input->post('gender')),
				'role' => strip_tags($this->input->post('role')),
                'status' => strip_tags($this->input->post('status')),
            );
        }
		$this->person->update(array('Id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		$this->person->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate($numpass)
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if(strip_tags($this->input->post('firstName')) == '')
		{
			$data['inputerror'][] = 'firstName';
			$data['error_string'][] = 'Имя - обязательное поле';
			$data['status'] = FALSE;
		}

		if(strip_tags($this->input->post('lastName')) == '')
		{
			$data['inputerror'][] = 'lastName';
			$data['error_string'][] = 'Фамилия - обязательное поле';
			$data['status'] = FALSE;
		}

        if(strip_tags($this->input->post('email')) == '')
        {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'Эл.почта - обязательное поле';
            $data['status'] = FALSE;
        }



        if($numpass==0){
            $ps=$this->input->post('new_password');
            if($this->input->post('new_password')!=''){
                if(!$this->regex("/(?=^.{8,20}$)(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?!.*\s).*$/",$this->input->post('new_password'))){
                    $data['inputerror'][] = 'new_password';
                    $data['error_string'][] = 'Пароль должен содержать минимум один строчный латинский символ и символ в верхнем регистре, а также спец.символ и цифру. Длина пароля от 8 до 20 символов (включительно).';
                    $data['status'] = FALSE;
                }
            }
            else{
                $data['inputerror'][] = 'new_password';
                $data['error_string'][] = 'Пароль - обязательное поле.';
                $data['status'] = FALSE;
            }
        }
        else if($numpass==1){
            if($this->input->post('new_password')!=''){
                if(!$this->regex("/(?=^.{8,20}$)(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?!.*\s).*$/",$this->input->post('new_password'))){
                    $data['inputerror'][] = 'new_password';
                    $data['error_string'][] = 'Пароль должен содержать минимум один строчный латинский символ и символ в верхнем регистре, а также спец.символ и цифру. Длина пароля от 8 до 20 символов (включительно).';
                    $data['status'] = FALSE;
                }
            }
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
	}

    public function regex($pattern,$field){
        if (preg_match($pattern, $field)) {
            return true;
        }
        else{
            return false;
        }
    }

}
