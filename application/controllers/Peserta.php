<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Peserta extends CI_Controller
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
			'judul'	=> 'Peserta',
			'subjudul' => 'Data Peserta'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('master/peserta/data');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function data()
	{
		$this->output_json($this->master->getDataPeserta(), false);
	}

	public function add()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Peserta',
			'subjudul' => 'Tambah Data Peserta'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('master/peserta/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function edit($id)
	{
		$peserta = $this->master->getPesertaById($id);
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Peserta',
			'subjudul'	=> 'Edit Data Peserta',
			'angkatan'	=> $this->master->getAngkatan(),
			'kelompok'	=> $this->master->getKelompokByAngkatan($peserta->angkatan_id),
			'peserta' 	=> $peserta
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('master/peserta/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function validasi_peserta($method)
	{
		$id_peserta 	= $this->input->post('id_peserta', true);
		$no_identitas 	= $this->input->post('no_identitas', true);
		$email 			= $this->input->post('email', true);
		if ($method == 'add') {
			$u_no_identitas = '|is_unique[peserta.no_identitas]';
			$u_email = '|is_unique[peserta.email]';
		} else {
			$dbdata 			= $this->master->getPesertaById($id_peserta);
			$u_no_identitas		= $dbdata->no_identitas === $no_identitas ? "" : "|is_unique[peserta.no_identitas]";
			$u_email			= $dbdata->email === $email ? "" : "|is_unique[peserta.email]";
		}
		$this->form_validation->set_rules('no_identitas', 'No Identitas', 'required|numeric|trim|min_length[8]|max_length[12]' . $u_no_identitas);
		$this->form_validation->set_rules('nama', 'Nama', 'required|trim|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email' . $u_email);
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');
		$this->form_validation->set_rules('angkatan', 'Angkatan', 'required');
		$this->form_validation->set_rules('kelompok', 'Kelompok', 'required');

		$this->form_validation->set_message('required', 'Kolom {field} wajib diisi');
	}

	public function save()
	{
		$method = $this->input->post('method', true);
		$this->validasi_peserta($method);

		if ($this->form_validation->run() == FALSE) {
			$data = [
				'status'	=> false,
				'errors'	=> [
					'no_identitas' => form_error('no_identitas'),
					'nama' => form_error('nama'),
					'email' => form_error('email'),
					'jenis_kelamin' => form_error('jenis_kelamin'),
					'angkatan' => form_error('angkatan'),
					'kelompok' => form_error('kelompok'),
				]
			];
			$this->output_json($data);
		} else {
			$input = [
				'no_identitas' 	=> $this->input->post('no_identitas', true),
				'email' 		=> $this->input->post('email', true),
				'nama' 			=> $this->input->post('nama', true),
				'jenis_kelamin' => $this->input->post('jenis_kelamin', true),
				'kelompok_id' 	=> $this->input->post('kelompok', true),
			];
			if ($method === 'add') {
				$action = $this->master->create('peserta', $input);
			} else if ($method === 'edit') {
				$id = $this->input->post('id_peserta', true);
				$action = $this->master->update('peserta', $input, 'id_peserta', $id);
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
			if ($this->master->delete('peserta', $chk, 'id_peserta')) {
				$this->output_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}

	public function create_user()
	{
		$id = $this->input->get('id', true);
		$data = $this->master->getPesertaById($id);
		$nama = explode(' ', $data->nama);
		$first_name = $nama[0];
		$last_name = end($nama);

		$username = $data->no_identitas;
		$password = $data->no_identitas;
		$email = $data->email;
		$additional_data = [
			'first_name'	=> $first_name,
			'last_name'		=> $last_name
		];
		$group = array('3'); // Sets user to dosen.

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
			'judul'	=> 'Peserta',
			'subjudul' => 'Import Data Peserta',
			'kelompok' => $this->master->getAllKelompok()
		];
		if ($import_data != null) $data['import'] = $import_data;

		$this->load->view('_templates/dashboard/_header', $data);
		$this->load->view('master/peserta/import');
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
					'nama' => $sheetData[$i][1],
					'email' => $sheetData[$i][2],
					'jenis_kelamin' => $sheetData[$i][3],
					'kelompok_id' => $sheetData[$i][4]
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
				'nama' => $d->nama,
				'email' => $d->email,
				'jenis_kelamin' => $d->jenis_kelamin,
				'kelompok_id' => $d->kelompok_id
			];
		}

		$save = $this->master->create('peserta', $data, true);
		if ($save) {
			redirect('peserta');
		} else {
			redirect('peserta/import');
		}
	}
}
