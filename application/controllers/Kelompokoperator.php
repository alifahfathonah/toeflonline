<?php
defined('BASEPATH') or exit('No direct script access allowed');

class KelompokOperator extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		} else if (!$this->ion_auth->is_admin()) {
			show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
		$this->load->library(['datatables', 'form_validation']); // Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->form_validation->set_error_delimiters('', '');
	}

	public function output_json($data, $encode = true)
	{
		if ($encode) $data = json_encode($data);
		$this->output->set_content_type('application/json')->set_output($data);
	}

	public function index()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Kelompok Operator',
			'subjudul' => 'Data Kelompok Operator'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/kelompokoperator/data');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function data()
	{
		$this->output_json($this->master->getKelompokOperator(), false);
	}

	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Tambah Kelompok Operator',
			'subjudul'	=> 'Tambah Data Kelompok Operator',
			'operator'		=> $this->master->getAllOperator(),
			'kelompok'	    => $this->master->getAllKelompok()
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/kelompokoperator/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function edit($id)
	{
		$data = [
			'user' 			=> $this->ion_auth->user()->row(),
			'judul'			=> 'Edit Kelompok Operator',
			'subjudul'		=> 'Edit Data Kelompok Operator',
			'operator'		=> $this->master->getOperatorById($id),
			'id_operator'	=> $id,
			'all_kelompok'	=> $this->master->getAllKelompok(),
			'kelompok'	    => $this->master->getKelompokByOperator($id)
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/kelompokoperator/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function save()
	{
		$method = $this->input->post('method', true);
		$this->form_validation->set_rules('operator_id', 'Operator', 'required');
		$this->form_validation->set_rules('kelompok_id[]', 'Kelompok', 'required');

		if ($this->form_validation->run() == FALSE) {
			$data = [
				'status'	=> false,
				'errors'	=> [
					'operator_id' => form_error('operator_id'),
					'kelompok_id[]' => form_error('kelompok_id[]'),
				]
			];
			$this->output_json($data);
		} else {
			$operator_id = $this->input->post('operator_id', true);
			$kelompok_id = $this->input->post('kelompok_id', true);
			$input = [];
			foreach ($kelompok_id as $key => $val) {
				$input[] = [
					'operator_id'  => $operator_id,
					'kelompok_id' => $val
				];
			}
			if ($method === 'add') {
				$action = $this->master->create('kelompok_operator', $input, true);
			} else if ($method === 'edit') {
				$id = $this->input->post('operator_id', true);
				$this->master->delete('kelompok_operator', $id, 'operator_id');
				$action = $this->master->create('kelompok_operator', $input, true);
			}
			$data['status'] = $action ? TRUE : FALSE;
		}
		$this->output_json($data);
	}

	public function delete()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			$this->output_json(['status' => false]);
		} else {
			if ($this->master->delete('kelompok_operator', $chk, 'operator_id')) {
				$this->output_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}
}
