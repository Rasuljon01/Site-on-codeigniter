<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class nameproduct extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('nameproduct_model','nameproduct');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('nameproduct_view');
	}

	public function ajax_list()
	{
		$list = $this->nameproduct->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
            $no++;
            $row = array();
            $row[] = $person->Name ;
            if($person->Is_beverages==1){
                $row[] = 'Да';
            }
            else if($person->Is_beverages==0){
                $row[] = 'Нет';
            }
            if($person->Is_Ingredient==1){
                $row[] = 'Да';
            }
            else if($person->Is_Ingredient==0){
                $row[] = 'Нет';
            }
            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person('."'".$person->Id."'".')"><i class="glyphicon glyphicon-pencil"></i> Редактировать</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_person('."'".$person->Id."'".')"><i class="glyphicon glyphicon-trash"></i> Удалить</a>';

            $data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->nameproduct->count_all(),
						"recordsFiltered" => $this->nameproduct->count_filtered(),
						"data" => $data,
				);
		//
        //вывод в формат json
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->nameproduct->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate(0);
		$data = array(
            'Name' => strip_tags($this->input->post('dish_name')),
            'Is_beverages' => strip_tags($this->input->post('drink')),
            'Is_Ingredient' => strip_tags($this->input->post('ingredient')),
			);
		$insert = $this->nameproduct->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate(1);

            $data = array(
                'Name' => strip_tags($this->input->post('dish_name')),
                'Is_beverages' => strip_tags($this->input->post('drink')),
                'Is_Ingredient' => strip_tags($this->input->post('ingredient')),
            );

		$this->bi->update(array('Id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		$this->bi->delete_by_id($id);
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

        if(strip_tags($this->input->post('drink')) == 0 && strip_tags($this->input->post('ingredient')) == 0)
        {
            $data['inputerror'][] = 'ingredient';
            $data['error_string'][] = 'Нужно указать что это. Напиток или Ингредиент';
            $data['inputerror'][] = 'drink';
            $data['error_string'][] = 'Нужно указать что это. Напиток или Ингредиент';
            $data['status'] = FALSE;
        }

        if(strip_tags($this->input->post('drink')) == 1 && strip_tags($this->input->post('ingredient')) == 1)
        {
            $data['inputerror'][] = 'ingredient';
            $data['error_string'][] = 'Нужно указать что это. Материал или сырьё';
            $data['inputerror'][] = 'drink';
            $data['error_string'][] = 'Нужно указать что это. Материал или сырьё';
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
