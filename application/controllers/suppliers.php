<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class suppliers extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('suppliers_model','suppliers');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('suppliers_view');
	}

	public function ajax_list()
	{
		$list = $this->suppliers->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();
			$row[] = $person->Name_suppliers ;
			$row[] = $person->Country;
			$row[] = $person->City;
            $row[] = $person->Street;
            $row[] = $person->House_number;
            $row[] = $person->Phone;
			//добавить html для действия
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person('."'".$person->Id."'".')"><i class="glyphicon glyphicon-pencil"></i> Редактировать</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_person('."'".$person->Id."'".')"><i class="glyphicon glyphicon-trash"></i> Удалить</a>';

			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->suppliers->count_all(),
						"recordsFiltered" => $this->suppliers->count_filtered(),
						"data" => $data,
				);
		//
        //вывод в формат json
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->suppliers->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate(0);
		$data = array(
            'Name_suppliers' => strip_tags($this->input->post('sp_name')),
            'Country' => strip_tags($this->input->post('country')),
            'City' => strip_tags($this->input->post('city')),
            'Street' => strip_tags($this->input->post('street')),
            'House_number' => strip_tags($this->input->post('num_house')),
            'Phone' => strip_tags($this->input->post('phone')),
			);
		$insert = $this->suppliers->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate(1);

            $data = array(
                'Name_suppliers' => strip_tags($this->input->post('sp_name')),
                'Country' => strip_tags($this->input->post('country')),
                'City' => strip_tags($this->input->post('city')),
                'Street' => strip_tags($this->input->post('street')),
                'House_number' => strip_tags($this->input->post('num_house')),
                'Phone' => strip_tags($this->input->post('phone')),
            );

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

		if(strip_tags($this->input->post('sp_name')) == '')
		{
			$data['inputerror'][] = 'sp_name';
			$data['error_string'][] = 'Наименование - обязательное поле';
			$data['status'] = FALSE;
		}

		if(strip_tags($this->input->post('city')) == '')
		{
			$data['inputerror'][] = 'city';
			$data['error_string'][] = 'Город - обязательное поле';
			$data['status'] = FALSE;
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
