<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operator extends CI_Controller
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
			'judul'	=> 'Operator',
			'subjudul' => 'Data Operator'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('master/operator/data');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function data()
	{
		$this->output_json($this->master->getDataOperator(), false);
	}

	public function add()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Tambah Operator',
			'subjudul' => 'Tambah Data Operator',
			'mataujian'	=> $this->master->getAllMataujian()
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('master/operator/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function edit($id)
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Edit Operator',
			'subjudul'	=> 'Edit Data Operator',
			'mataujian'	=> $this->master->getAllMataujian(),
			'data' 		=> $this->master->getOperatorById($id)
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('master/operator/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function save()
	{
		$method 	= $this->input->post('method', true);
		$id_operator 	= $this->input->post('id_operator', true);
		$no_identitas 		= $this->input->post('no_identitas', true);
		$nama_operator = $this->input->post('nama_operator', true);
		$email 		= $this->input->post('email', true);
		$mataujian 	= $this->input->post('mataujian', true);
		if ($method == 'add') {
			$u_no_identitas = '|is_unique[operator.no_identitas]';
			$u_email = '|is_unique[operator.email]';
		} else {
			$dbdata 	= $this->master->getOperatorById($id_operator);
			$u_no_identitas		= $dbdata->no_identitas === $no_identitas ? "" : "|is_unique[operator.no_identitas]";
			$u_email	= $dbdata->email === $email ? "" : "|is_unique[operator.email]";
		}
		$this->form_validation->set_rules('no_identitas', 'No Identitas', 'required|numeric|trim|min_length[8]|max_length[12]' . $u_no_identitas);
		$this->form_validation->set_rules('nama_operator', 'Nama Operator', 'required|trim|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email' . $u_email);
		$this->form_validation->set_rules('mataujian', 'Mata Ujian', 'required');

		if ($this->form_validation->run() == FALSE) {
			$data = [
				'status'	=> false,
				'errors'	=> [
					'no_identitas' => form_error('no_identitas'),
					'nama_operator' => form_error('nama_operator'),
					'email' => form_error('email'),
					'mataujian' => form_error('mataujian'),
				]
			];
			$this->output_json($data);
		} else {
			$input = [
				'no_identitas'	=> $no_identitas,
				'nama_operator' => $nama_operator,
				'email' 		=> $email,
				'mataujian_id' 	=> $mataujian
			];
			if ($method === 'add') {
				$action = $this->master->create('operator', $input);
			} else if ($method === 'edit') {
				$action = $this->master->update('operator', $input, 'id_operator', $id_operator);
			}

			if ($action) {
				$this->output_json(['status' => true]);
			} else {
				$this->output_json(['status' => false]);
			}
		}
	}

	public function delete()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			$this->output_json(['status' => false]);
		} else {
			if ($this->master->delete('operator', $chk, 'id_operator')) {
				$this->output_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}

	public function create_user()
	{
		$id = $this->input->get('id', true);
		$data = $this->master->getOperatorById($id);
		$nama = explode(' ', $data->nama_operator);
		$first_name = $nama[0];
		$last_name = end($nama);

		$username = $data->no_identitas;
		$password = $data->no_identitas;
		$email = $data->email;
		$additional_data = [
			'first_name'	=> $first_name,
			'last_name'		=> $last_name
		];
		$group = array('2'); // Sets user to operator.

		if ($this->ion_auth->username_check($username)) {
			$data = [
				'status' => false,
				'msg'	 => 'Username tidak tersedia (sudah digunakan).'
			];
		} else if ($this->ion_auth->email_check($email)) {
			$data = [
				'status' => false,
				'msg'	 => 'Email tidak tersedia (sudah digunakan).'
			];
		} else {
			$this->ion_auth->register($username, $password, $email, $additional_data, $group);
			$data = [
				'status'	=> true,
				'msg'	 => 'User berhasil dibuat. No Identitas digunakan sebagai password pada saat login.'
			];
		}
		$this->output_json($data);
	}

	public function import($import_data = null)
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Operator',
			'subjudul' => 'Import Data Operator',
			'mataujian' => $this->master->getAllMataujian()
		];
		if ($import_data != null) $data['import'] = $import_data;

		$this->load->view('_templates/dashboard/_header', $data);
		$this->load->view('master/operator/import');
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
					'no_identitas' => $sheetData[$i][0],
					'nama_operator' => $sheetData[$i][1],
					'email' => $sheetData[$i][2],
					'mataujian_id' => $sheetData[$i][3]
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
			$data[] = [
				'no_identitas' => $d->no_identitas,
				'nama_operator' => $d->nama_operator,
				'email' => $d->email,
				'mataujian_id' => $d->mataujian_id
			];
		}

		$save = $this->master->create('operator', $data, true);
		if ($save) {
			redirect('operator');
		} else {
			redirect('operator/import');
		}
	}
}
