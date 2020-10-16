<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kelompok extends CI_Controller
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
			'judul'	=> 'Kelompok',
			'subjudul' => 'Data Kelompok'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('master/kelompok/data');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function data()
	{
		$this->output_json($this->master->getDataKelompok(), false);
	}

	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Tambah Kelompok',
			'subjudul'	=> 'Tambah Data Kelompok',
			'banyak'	=> $this->input->post('banyak', true),
			'angkatan'	=> $this->master->getAllAngkatan()
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('master/kelompok/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function edit()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			redirect('admin/kelompok');
		} else {
			$kelompok = $this->master->getKelompokById($chk);
			$data = [
				'user' 		=> $this->ion_auth->user()->row(),
				'judul'		=> 'Edit Kelompok',
				'subjudul'	=> 'Edit Data Kelompok',
				'angkatan'	=> $this->master->getAllAngkatan(),
				'kelompok'	=> $kelompok
			];
			$this->load->view('_templates/dashboard/_header.php', $data);
			$this->load->view('master/kelompok/edit');
			$this->load->view('_templates/dashboard/_footer.php');
		}
	}

	public function save()
	{
		$rows = count($this->input->post('nama_kelompok', true));
		$mode = $this->input->post('mode', true);
		for ($i = 1; $i <= $rows; $i++) {
			$nama_kelompok 	= 'nama_kelompok[' . $i . ']';
			$angkatan_id 	= 'angkatan_id[' . $i . ']';
			$this->form_validation->set_rules($nama_kelompok, 'Kelompok', 'required');
			$this->form_validation->set_rules($angkatan_id, 'Angkatan', 'required');
			$this->form_validation->set_message('required', '{field} Wajib diisi');

			if ($this->form_validation->run() === FALSE) {
				$error[] = [
					$nama_kelompok 	=> form_error($nama_kelompok),
					$angkatan_id 	=> form_error($angkatan_id),
				];
				$status = FALSE;
			} else {
				if ($mode == 'add') {
					$insert[] = [
						'nama_kelompok' 	=> $this->input->post($nama_kelompok, true),
						'angkatan_id' 	=> $this->input->post($angkatan_id, true)
					];
				} else if ($mode == 'edit') {
					$update[] = array(
						'id_kelompok'		=> $this->input->post('id_kelompok[' . $i . ']', true),
						'nama_kelompok' 	=> $this->input->post($nama_kelompok, true),
						'angkatan_id' 	=> $this->input->post($angkatan_id, true)
					);
				}
				$status = TRUE;
			}
		}
		if ($status) {
			if ($mode == 'add') {
				$this->master->create('kelompok', $insert, true);
				$data['insert']	= $insert;
			} else if ($mode == 'edit') {
				$this->master->update('kelompok', $update, 'id_kelompok', null, true);
				$data['update'] = $update;
			}
		} else {
			if (isset($error)) {
				$data['errors'] = $error;
			}
		}
		$data['status'] = $status;
		$this->output_json($data);
	}

	public function delete()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			$this->output_json(['status' => false]);
		} else {
			if ($this->master->delete('kelompok', $chk, 'id_kelompok')) {
				$this->output_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}

	public function kelompok_by_angkatan($id)
	{
		$data = $this->master->getKelompokByAngkatan($id);
		$this->output_json($data);
	}

	public function import($import_data = null)
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Kelompok',
			'subjudul' => 'Import Kelompok',
			'angkatan' => $this->master->getAllAngkatan()
		];
		if ($import_data != null) $data['import'] = $import_data;

		$this->load->view('_templates/dashboard/_header', $data);
		$this->load->view('master/kelompok/import');
		$this->load->view('_templates/dashboard/_footer');
	}

	public function preview()
	{
		$config['upload_path']		= './uploads/import/';
		$config['allowed_types']	= 'xls|xlsx|csv';
		$config['max_size']			= 2048;
		$config['encrypt_name']		= true;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('upload_file')) {
			$error = $this->upload->display_errors();
			echo $error;
			die;
		} else {
			$file = $this->upload->data('full_path');
			$ext = $this->upload->data('file_ext');

			switch ($ext) {
				case '.xlsx':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
					break;
				case '.xls':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
					break;
				case '.csv':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
					break;
				default:
					echo "unknown file ext";
					die;
			}

			$spreadsheet = $reader->load($file);
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			$data = [];
			for ($i = 1; $i < count($sheetData); $i++) {
				$data[] = [
					'kelompok' => $sheetData[$i][0],
					'angkatan' => $sheetData[$i][1]
				];
			}

			unlink($file);

			$this->import($data);
		}
	}
	public function do_import()
	{
		$input = json_decode($this->input->post('data', true));
		$data = [];
		foreach ($input as $d) {
			$data[] = ['nama_kelompok' => $d->kelompok, 'angkatan_id' => $d->angkatan];
		}

		$save = $this->master->create('kelompok', $data, true);
		if ($save) {
			redirect('kelompok');
		} else {
			redirect('kelompok/import');
		}
	}
}
