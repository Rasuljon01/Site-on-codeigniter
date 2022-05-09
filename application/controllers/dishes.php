<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class dishes extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('dishes_model','dishes');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('dishes_view');
	}

	public function ajax_list()
	{
		$list = $this->dishes->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();
			$row[] = $person->Name ;
			$row[] = $person->Price;
			//добавить html для действия
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person('."'".$person->Id."'".')"><i class="glyphicon glyphicon-pencil"></i> Редактировать</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_person('."'".$person->Id."'".')"><i class="glyphicon glyphicon-trash"></i> Удалить</a>';

			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->dishes->count_all(),
						"recordsFiltered" => $this->dishes->count_filtered(),
						"data" => $data,
				);
		//
        //вывод в формат json
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->dishes->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate(0);
		$data = array(
            'Name' => strip_tags($this->input->post('dish_name')),
            'Price' => strip_tags($this->input->post('price')),
			);
		$insert = $this->dishes->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate(1);

            $data = array(
                'Name' => strip_tags($this->input->post('dish_name')),
                'Price' => strip_tags($this->input->post('price')),
            );

		$this->dishes->update(array('Id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		$this->dishes->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate($numpass)
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if(strip_tags($this->input->post('dish_name')) == '')
		{
			$data['inputerror'][] = 'dish_name';
			$data['error_string'][] = 'Наименование - обязательное поле';
			$data['status'] = FALSE;
		}

		if(strip_tags($this->input->post('price')) == '')
		{
			$data['inputerror'][] = 'price';
			$data['error_string'][] = 'Цена - обязательное поле';
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
