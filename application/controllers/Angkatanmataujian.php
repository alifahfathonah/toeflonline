<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AngkatanMataujian extends CI_Controller
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
			'judul'	=> 'Angkatan Mata Ujian',
			'subjudul' => 'Data Angkatan Mata Ujian'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/angkatanmataujian/data');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function data()
	{
		$this->output_json($this->master->getAngkatanMataujian(), false);
	}

	public function getAngkatanId($id)
	{
		$this->output_json($this->master->getAllAngkatan($id));
	}

	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Tambah Angkatan Mata Ujian',
			'subjudul'	=> 'Tambah Data Angkatan Mata Ujian',
			'mataujian'	=> $this->master->getMataujian()
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/angkatanmataujian/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function edit($id)
	{
		$data = [
			'user' 			=> $this->ion_auth->user()->row(),
			'judul'			=> 'Edit Angkatan Mata Ujian',
			'subjudul'		=> 'Edit Data Angkatan Mata Ujian',
			'mataujian'		=> $this->master->getMataujianById($id, true),
			'id_mataujian'	=> $id,
			'all_angkatan'	=> $this->master->getAllAngkatan(),
			'angkatan'		=> $this->master->getAngkatanByIdMataujian($id)
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/angkatanmataujian/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function save()
	{
		$method = $this->input->post('method', true);
		$this->form_validation->set_rules('mataujian_id', 'Mata Ujian', 'required');
		$this->form_validation->set_rules('angkatan_id[]', 'Angkatan', 'required');

		if ($this->form_validation->run() == FALSE) {
			$data = [
				'status'	=> false,
				'errors'	=> [
					'mataujian_id' => form_error('mataujian_id'),
					'angkatan_id[]' => form_error('angkatan_id[]'),
				]
			];
			$this->output_json($data);
		} else {
			$mataujian_id 	= $this->input->post('mataujian_id', true);
			$angkatan_id = $this->input->post('angkatan_id', true);
			$input = [];
			foreach ($angkatan_id as $key => $val) {
				$input[] = [
					'mataujian_id' 	=> $mataujian_id,
					'angkatan_id'  	=> $val
				];
			}
			if ($method === 'add') {
				$action = $this->master->create('angkatan_mataujian', $input, true);
			} else if ($method === 'edit') {
				$id = $this->input->post('mataujian_id', true);
				$this->master->delete('angkatan_mataujian', $id, 'mataujian_id');
				$action = $this->master->create('angkatan_mataujian', $input, true);
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
			if ($this->master->delete('angkatan_mataujian', $chk, 'mataujian_id')) {
				$this->output_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}
}
