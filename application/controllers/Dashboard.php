<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		}
		$this->load->model('Dashboard_model', 'dashboard');
		$this->user = $this->ion_auth->user()->row();
	}

	public function admin_box()
	{
		$box = [
			[
				'box' 		=> 'light-blue',
				'total' 	=> $this->dashboard->total('angkatan'),
				'title'		=> 'Angkatan',
				'icon'		=> 'graduation-cap'
			],
			[
				'box' 		=> 'olive',
				'total' 	=> $this->dashboard->total('kelompok'),
				'title'		=> 'Kelompok',
				'icon'		=> 'building-o'
			],
			[
				'box' 		=> 'yellow-active',
				'total' 	=> $this->dashboard->total('operator'),
				'title'		=> 'Operator',
				'icon'		=> 'user-secret'
			],
			[
				'box' 		=> 'red',
				'total' 	=> $this->dashboard->total('peserta'),
				'title'		=> 'Peserta',
				'icon'		=> 'user'
			],
		];
		$info_box = json_decode(json_encode($box), FALSE);
		return $info_box;
	}

	public function index()
	{
		$user = $this->user;
		$data = [
			'user' 		=> $user,
			'judul'		=> 'Dashboard',
			'subjudul'	=> 'Data Aplikasi',
		];

		if ($this->ion_auth->is_admin()) {
			$data['info_box'] = $this->admin_box();
		} elseif ($this->ion_auth->in_group('operator')) {
			$mataujian = ['mataujian' => 'operator.mataujian_id=mataujian.id_mataujian'];
			$data['operator'] = $this->dashboard->get_where('operator', 'no_identitas', $user->username, $mataujian)->row();

			$kelompok = ['kelompok' => 'kelompok_operator.kelompok_id=kelompok.id_kelompok'];
			$data['kelompok'] = $this->dashboard->get_where('kelompok_operator', 'operator_id', $data['operator']->id_operator, $kelompok, ['nama_kelompok' => 'ASC'])->result();
		} else {
			$join = [
				'kelompok b' 	=> 'a.kelompok_id = b.id_kelompok',
				'angkatan c'	=> 'b.angkatan_id = c.id_angkatan'
			];
			$data['peserta'] = $this->dashboard->get_where('peserta a', 'no_identitas', $user->username, $join)->row();
		}

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('dashboard');
		$this->load->view('_templates/dashboard/_footer.php');
	}
}
