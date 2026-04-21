<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BukuPembantu extends CI_Controller
{      
    public function __construct()
    {
        parent::__construct();
		$this->load->helper(array('form', 'url', 'h_rand_string'));
		$this->load->library('session');
		$this->load->model('M_Setting');
		$this->load->model('M_Akses');
		$this->load->model('M_TipeUser');

		cek_login_user();
    }

    public function index(){
        $id = $this->session->userdata('tipeuser');
		$data['menu'] = $this->M_Setting->getmenu1($id);	
		$data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'Buku Pembantu'])->row()->id_menus;
		$data['tipeuser'] = $this->db->get('tb_tipeuser')->result_array();

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_bukupembantu/index', $data);
		$this->load->view('template/footer');
    }

	public function Cetak($tipe, $id, $mulaiData = 0, $mulaiBaris = 0)
	{
		$saldo = 0;
		$nomorBaris = intval($mulaiBaris);
		
		// Ambil seluruh transaksi berurutan agar saldo berjalan selalu konsisten
		if ($tipe == 'siswa') {
			$query = 'SELECT tb_transaksi.id_transaksi, tb_transaksi.nominal, tb_transaksi.cetak, tb_transaksi.id_user, tb_mastertransaksi.debet, tb_mastertransaksi.kredit, tb_mastertransaksi.kodetransaksi, DATE_FORMAT(tb_transaksi.tgl_update,"%d/%m") AS tgl_cetak, tb_users.kodepegawai FROM tb_transaksi JOIN tb_mastertransaksi ON tb_transaksi.id_jenistransaksi = tb_mastertransaksi.id_mastertransaksi JOIN tb_siswa ON tb_transaksi.id_siswa = tb_siswa.nis LEFT JOIN tb_users ON tb_transaksi.id_user = tb_users.id WHERE tb_transaksi.id_siswa = ' . intval($id) . ' AND tb_transaksi.status = "aktif" ORDER BY tb_transaksi.tgl_update ASC';
		} else {
			$query = 'SELECT tb_transaksi.id_transaksi, tb_transaksi.nominal, tb_transaksi.cetak, tb_transaksi.id_user, tb_mastertransaksi.debet, tb_mastertransaksi.kredit, tb_mastertransaksi.kodetransaksi, DATE_FORMAT(tb_transaksi.tgl_update,"%d/%m") AS tgl_cetak, tb_users.kodepegawai FROM tb_transaksi JOIN tb_mastertransaksi ON tb_transaksi.id_jenistransaksi = tb_mastertransaksi.id_mastertransaksi JOIN tb_staf ON tb_transaksi.id_anggota = tb_staf.id_staf LEFT JOIN tb_users ON tb_transaksi.id_user = tb_users.id WHERE tb_transaksi.id_anggota = ' . intval($id) . ' AND tb_transaksi.status = "aktif" ORDER BY tb_transaksi.tgl_update ASC';
		}

		$data = $this->db->query($query)->result();
		$mulaiDataUrut = intval($mulaiData);
		
		// Jika nomorBaris = 0, cari baris terakhir yang sudah dicetak + 1
		if ($nomorBaris == 0) {
			$this->db->select('tb_transaksi.cetak')
				->from('tb_transaksi')
				->where('tb_transaksi.status', 'aktif')
				->where('tb_transaksi.cetak !=', 0)
				->where('tb_transaksi.cetak IS NOT NULL', null, false);

			if ($tipe == 'siswa') {
				$this->db->where('tb_transaksi.id_siswa', intval($id));
			} else {
				$this->db->where('tb_transaksi.id_anggota', intval($id));
			}

			$lastCetak = $this->db->order_by('tb_transaksi.tgl_update', 'DESC')
				->limit(1)
				->get()
				->row();
			
			if ($lastCetak) {
				// Ekstrak nomor baris dari field cetak (format: "baris_ke_X" atau "X")
				$lastBaris = intval($lastCetak->cetak);
				$nomorBaris = ($lastBaris % 36) + 1;
			} else {
				$nomorBaris = 1;
			}
		}
		
		try {
			$this->load->library('escpos');

			$connector = new Escpos\PrintConnectors\WindowsPrintConnector("epson plq-30");
			$printer = new Escpos\Printer($connector);
			$printer->initialize();
			$printer->setFont(Escpos\Printer::FONT_B);
			$printer->text("\n");
			$printer->text("\n");
			$printer->setJustification(Escpos\Printer::JUSTIFY_LEFT);

			// Jika mulai dari baris tertentu, isi baris kosong sampai posisi tersebut.
			if ($nomorBaris > 1) {
				for ($i = 1; $i < $nomorBaris; $i++) {
					$printer->text("\n");
				}
			}

			// Hitung saldo untuk semua data, lalu cetak hanya baris yang dipilih
			$urutan = 0;
			foreach ($data as $row) {
				$urutan++;
				$debet = '';
				$kredit = '';
				$kodeTransaksi = trim(explode('-', (string) $row->kodetransaksi)[0]);
				$kodePegawai = !empty($row->kodepegawai) ? $row->kodepegawai : '-';

				if ($tipe == $row->debet || ($row->debet == 'koperasi' && $tipe == 'staf')) {
					$saldo = $saldo - intval($row->nominal);
					$debet = number_format($row->nominal, 0, ',', '.');
				} else {
					$saldo = $saldo + intval($row->nominal);
					$kredit = number_format($row->nominal, 0, ',', '.');
				}

				$bolehCetak = false;
				if ($mulaiData == 'B') {
					$bolehCetak = (intval($row->cetak) === 0);
				} else if ($mulaiDataUrut > 0) {
					$bolehCetak = ($urutan >= $mulaiDataUrut);
				} else {
					$bolehCetak = true;
				}

				if (!$bolehCetak) {
					continue;
				}

				if($nomorBaris > 36){
					$nomorBaris = 1;
				}

				$line = str_pad($row->tgl_cetak, 7, " ", STR_PAD_BOTH)
					  . str_pad($kodeTransaksi, 4, " ", STR_PAD_BOTH)
					  . str_pad($debet, 11, " ", STR_PAD_LEFT)
					  . str_pad($kredit, 11, " ", STR_PAD_LEFT)
					  . str_pad(number_format($saldo, 0, ',', '.'), 11, " ", STR_PAD_LEFT)
					  . str_pad($kodePegawai, 10, " ", STR_PAD_BOTH);

				$printer->text($line . "\n");

				// Update status cetak dengan nomor baris buku
				$this->db->where('id_transaksi', $row->id_transaksi);
				$this->db->update('tb_transaksi', ['cetak' => $nomorBaris]);

				$nomorBaris++;
			}

			$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
														<strong>Sukses!</strong> Berhasil dicetak.
													</div>');
			$printer->close();
			echo "
			<script>
			window.close();
			</script>";
		} catch (Exception $e) {
			echo "Ada Masalah: " . $e->getMessage() . "\n";
		}
	}

}