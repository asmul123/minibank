<?php
date_default_timezone_set('Asia/Jakarta');
defined('BASEPATH') or exit('No direct script access allowed');

class Bukutabungan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'h_rand_string'));
		$this->load->library('session');
		$this->load->model('M_Setting');
		$this->load->model('M_Bukutabungan');
		$this->load->model('M_TipeUser');
		$this->load->model('M_Akses');

		cek_login_user();
	}

	public function index()
	{
		// print_r($this->M_Transaksi->getTransaksi());
		$id = $this->session->userdata('tipeuser');
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['bukutabungan'] = $this->M_Bukutabungan->getBukutabungan();
		// echo json_encode();
		// $fruitArrayObject = new ArrayObject($data['transaksi']);
		// $fruitArrayObject->asort();
		// asort($data['transaksi']);
		// print_r($data['transaksi']);
		$data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'Buku Tabungan'])->row()->id_menus;

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_bukutabungan/v_bukutabungan', $data);
		$this->load->view('template/footer');
	}

	public function tambah()
	{
		// print_r($this->M_Transaksi->getTransaksi());
		$id = $this->session->userdata('tipeuser');
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['bukutabungan'] = $this->M_Bukutabungan->getBukutabungan();
		$data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
		$noseri = $this->M_Bukutabungan->getnoseri();
		$pecah = explode(".",$noseri);
		$nourut = sprintf("%05d", intval($pecah[1])+1);
		$data['noseri'] = $noseri;
		$data['noseribaru'] = date('Y').".".$nourut;
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'Buku Tabungan'])->row()->id_menus;

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_bukutabungan/v_bukutabungan-add', $data);
		$this->load->view('template/footer');
	}
	
    public function add_process()
    {
        // $id_user = $this->session-
		$noseri = $this->M_Bukutabungan->getnoseri();
		$pecah = explode(".",$noseri);
		$nourut = sprintf("%05d", intval($pecah[1])+1);
		$noseribaru = date('Y').".".$nourut;
        $data = [
            'jenis_nasabah' => $this->input->post('jenis_nasabah'),
            'id_nasabah' => $this->input->post('id_nasabah'),
            'nomor_seri' => $noseribaru,
            'tgl_buku_tabungan' =>  $this->input->post('tgl_buku_tabungan')
        ];
            $this->M_Bukutabungan->tambah($data);
            $this->session->set_flashdata('message', '<div class="alert alert-success left-icon-alert" role="alert"> <strong>Sukses!</strong> Data Berhasil Ditambahkan</div>');
            redirect('bukutabungan');
    }
	
    public function hapus($id)
    {
        $this->M_Bukutabungan->hapus($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success left-icon-alert" role="alert"> <strong>Sukses!</strong> Data Berhasil Dihapus</div>');
        redirect('bukutabungan');
    }

	public function getNasabah($jenis)
	{
		if($jenis=="siswa")
		{
			$data = $this->M_Bukutabungan->getsiswadetail();
		} else if ($jenis=="staf")
		{			
		$data = $this->M_Bukutabungan->getstafdetail();
		}

		echo json_encode($data);
	}

	public function cetak($id)
	{
		$getbuku = $this->M_Bukutabungan->getBukutabunganbyID($id);
		if ($getbuku->jenis_nasabah == 'siswa') {
			$nama_nasabah = $this->M_Bukutabungan->getnamasiswa($getbuku->id_nasabah);
			$alamat_nasabah = $this->M_Bukutabungan->getalamatsiswa($getbuku->id_nasabah);
		} else if ($getbuku->jenis_nasabah == 'staf') {
			$nama_nasabah = $this->M_Bukutabungan->getnamastaf($getbuku->id_nasabah);
			$alamat_nasabah = $this->M_Bukutabungan->getalamatstaf($getbuku->id_nasabah);
		}

		try {
			$this->load->library('escpos');

			$connector = new Escpos\PrintConnectors\WindowsPrintConnector("epson plq-30");
			// var_dump($connector);
			$printer = new Escpos\Printer($connector);
			// var_dump($printer);
			// $printer->E;
			// die();				
			// Membuat judul
			$printer->initialize();
			$printer->selectPrintMode(Escpos\Printer::MODE_DOUBLE_HEIGHT); // Setting teks menjadi lebih besar
			$printer->setJustification(Escpos\Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
			$printer->text("\n");
			$printer->text("  ");
			$printer->text("SMKN 1 GARUT\n");
			$printer->initialize();
			$printer->text("  ");
			$printer->text("No. Rekening ".$getbuku->id_nasabah);
			$printer->text("           ");
			$printer->text($getbuku->nomor_seri);
			$printer->text("\n  ");
			$printer->text($nama_nasabah);
			$printer->text("\n");
			$printer->text("  ");
			$printer->text($alamat_nasabah);
			$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
														<strong>Sukses!</strong> Berhasil.
													</div>');
			// $printer->cut();
			//   $printer->pulse();
			$printer->close();
		} catch (Exception $e) {
			echo "Ada Masalah: " . $e->getMessage() . "\n";
		}
	}

}
