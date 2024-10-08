<?php

date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Siswa extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('session');
		$this->load->model('M_Setting');
		$this->load->model('M_Siswa');
		$this->load->model('M_Provinsi');
		$this->load->model('M_TahunAkademik');
		$this->load->model('M_Kota');
		$this->load->model('M_Kecamatan');
		$this->load->model('M_Transaksi');
		$this->load->model('M_Kelas');
		$this->load->model('M_Akses');

		cek_login_user();
	}

	public function index()
	{
		$id = $this->session->userdata('tipeuser');
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['datasiswa'] = $this->M_Siswa->getsiswa();
		// var_dump($data);
		$data['datalulus'] = $this->M_Siswa->getLulus();
		$data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'siswa'])->row()->id_menus;

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_siswa/v_siswa', $data);
		$this->load->view('template/footer');
	}

	public function siswa_detail($nis)
	{
		$id = $this->session->userdata('tipeuser');
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['nis'] = $nis;
		$data['datasiswa'] = $this->M_Siswa->getsiswadetail($nis);
		$data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPathDet(), $id);
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'siswa'])->row()->id_menus;

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_siswa/v_siswa-detail', $data);
		$this->load->view('template/footer');
		// print_r($this->M_Siswa->getsiswadetail($nis));
	}

	public function siswa_add()
	{
		$id = $this->session->userdata('tipeuser');
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['prov'] = $this->M_Provinsi->getprovinsi();
		$data['tahunaka'] = $this->M_TahunAkademik->getAll();
		$data['kelas'] = $this->M_Kelas->getkelas();
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'siswa'])->row()->id_menus;

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_siswa/v_siswa-add', $data);
		$this->load->view('template/footer');
	}

	public function getKota($idProv)
	{
		$data = $this->M_Kota->getkotadetail($idProv);

		echo json_encode($data);
	}

	public function getSiswaByKelas($idKelas)
	{
		// $this->db->where();
		echo json_encode($this->db->query("SELECT tb_siswa.*, tb_kelas.kelas, DATE_FORMAT(tb_tahunakademik.tglawal, '%Y') AS tglawal, DATE_FORMAT(tb_tahunakademik.tglakhir, '%Y') AS tglakhir FROM tb_siswa JOIN tb_kelas ON tb_siswa.id_kelas = tb_kelas.id_kelas JOIN tb_tahunakademik ON tb_siswa.id_tahunakademik = tb_tahunakademik.id_tahunakademik WHERE tb_siswa.id_kelas = $idKelas AND tb_siswa.status = 'aktif'")->result());
	}

	public function getKecamatan($idkota)
	{
		$data = $this->M_Kecamatan->getkecadetail($idkota);

		echo json_encode($data);
	}

	public function add_process()
	{
		$id_tipeuser = $this->db->get_where('tb_tipeuser', ['tipeuser' => 'siswa'])->row_array();
		$cekNis = $this->M_Siswa->cekNis($this->input->post('nis', true));
		$nis = $this->input->post('nis', true);
		$nama = $this->input->post('nama', true);
		$alamat = $this->input->post('alamat', true);
		$jk = $this->input->post('jk', true);
		$kelas = $this->input->post('kelas', true);
		$prov = $this->input->post('prov', true);
		$kota = $this->input->post('kota', true);
		$kecamatan = $this->input->post('kecamatan', true);
		$rfid = $this->input->post('rfid', true);
		$tmp_lahir = $this->input->post('tempat_lahir', true);
		$tgl_lahir = $this->input->post('tanggal_lahir', true);
		$tahunakademik = $this->input->post('tahun_akademik', true);
		$data = array(
			'nis' => $nis,
			'namasiswa' => $nama,
			'alamat' => $alamat,
			'tempat_lahir' => $tmp_lahir,
			'tgl_lahir' => $tgl_lahir,
			'provinsi' => $prov,
			'kota' => $kota,
			'kecamatan' => $kecamatan,
			'jk' => $jk,
			'id_kelas' => $kelas,
			'tgl_update' => date("Y-m-d h:i:sa"),
			'id_user' => $this->session->userdata('id_user'),
			'status' => 'aktif',
			'id_tipeuser' => $id_tipeuser['id_tipeuser'],
			'password' => 'siswa123',
			'rfid' => $rfid,
			'id_tahunakademik' => $tahunakademik
		);

		if ($cekNis == 'kosong') {
			if ($this->M_Siswa->cekRfid($this->input->post('rfid', true))) {
				if (count($id_tipeuser) === 1 || $id_tipeuser !== null) {

					$this->M_Siswa->addSiswa($data);
					$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
																<strong>Sukses!</strong> Berhasil Menambahkan Data Siswa.
															</div>');
					redirect(base_url('siswa/'));
				} else {
					$this->session->set_flashdata('alert', '<div class="alert alert-danger left-icon-alert" role="alert">
		                                            		<strong>Gagal!</strong> Mohon tambahkan Tipe User siswa terlebih dulu.
		                                        		</div>');
					redirect(base_url('siswa/'));
				}
			} else {
				$this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
		                                            		<strong>Perhatian!</strong> RFID sudah ada, Coba lagi.
		                                        		</div>');
				redirect(base_url('siswa/'));
			}
		} else if ($cekNis == 'lulus') {
			$this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
														<strong>Perhatian!</strong> Sudah Ada Alumni yang mempunyai NIS yang sama.
													</div>');
			redirect(base_url('siswa/'));
		} else if ($cekNis == 'update') {
			if ($this->M_Siswa->cekRfid($this->input->post('rfid', true))) {
				if (count($id_tipeuser) === 1 || $id_tipeuser !== null) {
					$this->db->where('nis', $this->input->post('nis', true));
					$this->db->update('tb_siswa', $data);
					$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
																<strong>Sukses!</strong> Berhasil Menambahkan Data Siswa.
															</div>');
					redirect(base_url('siswa/'));
				} else {
					$this->session->set_flashdata('alert', '<div class="alert alert-danger left-icon-alert" role="alert">
		                                            		<strong>Gagal!</strong> Mohon tambahkan Tipe User siswa terlebih dulu.
		                                        		</div>');
					redirect(base_url('siswa/'));
				}
			} else {
				$this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
		                                            		<strong>Perhatian!</strong> RFID sudah ada, Coba lagi.
		                                        		</div>');
				redirect(base_url('siswa/'));
			}
		} else {
			$this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
	                                            		<strong>Perhatian!</strong> NIS sudah ada, Coba lagi.
	                                        		</div>');
			redirect(base_url('siswa/'));
		}
	}

	public function siswa_delete($nis)
	{
		$this->M_Siswa->delSiswa($nis);
		$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
                                            		<strong>Sukses!</strong> Berhasil Menghapus Data Siswa.
                                        		</div>');
		redirect(base_url('siswa/'));
	}

	public function siswa_edit($nis)
	{
		$id = $this->session->userdata('tipeuser');
		$data['datasiswa'] = $this->M_Siswa->getsiswadetail($nis);
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['prov'] = $this->M_Provinsi->getprovinsi();
		$data['tahunaka'] = $this->M_TahunAkademik->getAll();
		$data['kelas'] = $this->M_Kelas->getkelas();
		$data['kota'] = $this->M_Kota->getkotadetail($this->M_Siswa->getsiswadetail($nis)['provinsi']);
		$data['keca'] = $this->M_Kecamatan->getkecadetail($this->M_Siswa->getsiswadetail($nis)['kota']);
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'siswa'])->row()->id_menus;

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_siswa/v_siswa-edit', $data);
		$this->load->view('template/footer');
	}

	public function edt_process()
	{
		// echo $this->input->post('alumni');

		// if ($this->input->post('alumni') == 1) {
		// 	$status = 'alumni';
		// } else {
		// 	if ($this->input->post('status') === 'alumni') {
		// 		$status = 'aktif';
		// 	} else {
		// 		$status = 'aktif';
		// 	}
		// }

		$date = date_create($this->input->post('tanggal_lahir', true));

		$data = array(
			// 'nis' => $this->input->post('nis', true),
			'namasiswa' => $this->input->post('nama', true),
			'alamat' => $this->input->post('alamat', true),
			'tempat_lahir' => $this->input->post('tempat_lahir', true),
			'tgl_lahir' => date_format($date, "d-m-Y"),
			'provinsi' => $this->input->post('prov', true),
			'kota' => $this->input->post('kota', true),
			'kecamatan' => $this->input->post('kecamatan', true),
			'jk' => $this->input->post('jk', true),
			'id_kelas' => $this->input->post('kelas', true),
			'tgl_update' => date("Y-m-d h:i:sa"),
			// 'status' => $status,
			// 'rfid' => $this->input->post('rfid', true)
		);

		if ($this->input->post('nisOld') === $this->input->post('nis')) {
			$data['nis'] = $this->input->post('nis', true);
			if ($this->input->post('rfidOld') === $this->input->post('rfid')) {
				$data['rfid'] = $this->input->post('rfid', true);
				$this->M_Siswa->editSiswa($data, $this->input->post('nisOld'));
				$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
                                    		<strong>Sukses!</strong> Berhasil Mengubah Data Siswa.
                                		</div>');
				redirect(base_url('siswa/'));
			} else {
				if ($this->db->get_where('tb_siswa', ['rfid' => $this->input->post('rfid')])->num_rows() == 0) {
					$data['rfid'] = $this->input->post('rfid', true);
					$this->M_Siswa->editSiswa($data, $this->input->post('nisOld'));
					$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
                                        		<strong>Sukses!</strong> Berhasil Mengubah Data Siswa.
                                    		</div>');
					redirect(base_url('siswa/'));
				} else {
					$this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
                                        		<strong>Perhatian!</strong> RFID sudah ada 2.
                                    		</div>');
					redirect(base_url('siswa/'));
				}
			}
		} else {
			if ($this->M_Siswa->cekNis($this->input->post('nis'))) {
				$data['nis'] = $this->input->post('nis', true);
				if ($this->input->post('rfidOld') === $this->input->post('rfid')) {
					$data['rfid'] = $this->input->post('rfid', true);
					$this->M_Siswa->editSiswa($data, $this->input->post('nisOld'));
					$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
                                        		<strong>Sukses!</strong> Berhasil Mengubah Data Siswa.
                                    		</div>');
					redirect(base_url('siswa/'));
				} else {
					if ($this->db->get_where('tb_siswa', ['rfid' => $this->input->post('rfid')])->num_rows() != 0) {
						$data['rfid'] = $this->input->post('rfid', true);
						$this->M_Siswa->editSiswa($data, $this->input->post('nisOld'));
						$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
                                            		<strong>Sukses!</strong> Berhasil Mengubah Data Siswa.
                                        		</div>');
						redirect(base_url('siswa/'));
					} else {
						$this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
                                            		<strong>Perhatian!</strong> RFID sudah ada 1.
                                        		</div>');
						redirect(base_url('siswa/'));
					}
				}
			} else {
				$this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
                                            		<strong>Perhatian!</strong> NIS sudah ada.
                                        		</div>');
				redirect(base_url('siswa/'));
			}
		}
	}

	public function siswa_graduate()
	{
		$id = $this->session->userdata('tipeuser');
		$data['datalulus'] = $this->M_Siswa->getLulus();
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['kelas'] = $this->M_Kelas->getkelas();
		$data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'siswa lulus'])->row()->id_menus;


		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_siswa/v_siswa-graduate', $data);
		$this->load->view('template/footer');
	}

	public function grad_process($id)
	{
		$data = ['status' => 'alumni'];
		$this->db->where('id_kelas', $id);
		if ($this->db->update('tb_siswa', $data)) {
			echo 'berhasil';
		} else {
			echo 'salah';
		}
	}

	public function siswa_export()
	{
		$id = $this->session->userdata('tipeuser');
		$data['datasiswa'] = $this->M_Siswa->getsiswa();
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['kelas'] = $this->db->query('SELECT DISTINCT(tb_kelas.id_kelas), tb_kelas.kelas, COUNT(tb_siswa.id_kelas) AS jmlsiswa FROM tb_siswa JOIN tb_kelas ON tb_siswa.id_kelas = tb_kelas.id_kelas WHERE tb_siswa.status = "aktif" GROUP BY tb_siswa.id_kelas')->result_array();
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'siswa'])->row()->id_menus;

		// var_dump($data);
		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_siswa/v_siswa-export', $data);
		$this->load->view('template/footer');
	}

	public function export_process($id)
	{

		$this->db->select('tb_siswa.*, tb_kelas.kelas, tb_tahunakademik.tglawal, tb_tahunakademik.tglakhir');
		$this->db->join('tb_kelas', 'tb_siswa.id_kelas = tb_kelas.id_kelas');
		$this->db->join('tb_tahunakademik', 'tb_siswa.id_tahunakademik = tb_tahunakademik.id_tahunakademik');
		$this->db->where('tb_siswa.id_kelas', $id);
		$siswa =	$this->db->get('tb_siswa')->result();
		// var_dump($siswa);
		// die();
		// $this->db->query('tb_siswa', ['id_kelas' => $id])->result();
		$name = $this->db->get_where('tb_kelas', ['id_kelas' => $id])->row()->kelas;

		try {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$spreadsheet->getProperties()
				->setCreator("HOSTERWEB")
				->setLastModifiedBy("HOSTERWEB")
				->setTitle("SIMBMS")
				->setSubject("EXCEL SISWA")
				->setDescription(
					"Data Siswa " . $name . " SMAN 1 WRINGIN ANOM"
				)
				->setKeywords("HOSTERWEB")
				->setCategory("excel");
			$spreadsheet->setActiveSheetIndex(0);
			$sheet->setCellValue('A1', 'DATA KELAS ' . $name);
			$sheet->mergeCells('A1:I1');

			$sheet->setCellValue('A2', 'SMKN 1 GARUT ');
			$sheet->mergeCells('A2:I2');

			$sheet->setCellValue('A3', '');
			$sheet->mergeCells('A3:I3');

			$sheet->setCellValue('A4', ' ~Untuk Format Tempat tanggal lahir jangan lupa dipisah dengan koma ( , )');
			$sheet->mergeCells('A4:I4');

			$sheet->setCellValue('A5', 'No');
			$sheet->setCellValue('B5', 'NIS');
			$sheet->setCellValue('C5', 'RFID');
			$sheet->setCellValue('D5', 'Nama Lengkap');
			$sheet->setCellValue('E5', 'Jenis Kelamin');
			$sheet->setCellValue('F5', 'Kelas');
			$sheet->setCellValue('G5', 'Tempat, Tanggal Lahir');
			$sheet->setCellValue('H5', 'Alamat');
			$sheet->setCellValue('I5', 'Tahun Akademik');

			$sheet->getColumnDimension('A')->setAutoSize(true);
			$sheet->getColumnDimension('B')->setAutoSize(true);
			$sheet->getColumnDimension('C')->setAutoSize(true);
			$sheet->getColumnDimension('D')->setAutoSize(true);
			$sheet->getColumnDimension('E')->setAutoSize(true);
			$sheet->getColumnDimension('F')->setAutoSize(true);
			$sheet->getColumnDimension('G')->setAutoSize(true);
			$sheet->getColumnDimension('H')->setAutoSize(true);
			$sheet->getColumnDimension('I')->setAutoSize(true);

			$x = 6;
			$no = 1;
			foreach ($siswa as $row) {
				$sheet->setCellValue('A' . $x, $no++);
				$sheet->setCellValue('B' . $x, $row->nis);
				$sheet->setCellValue('C' . $x, $row->rfid);
				$sheet->setCellValue('D' . $x, $row->namasiswa);
				$sheet->setCellValue('E' . $x, $row->jk);
				$sheet->setCellValue('F' . $x, $row->kelas);
				$sheet->setCellValue('G' . $x, $row->tempat_lahir . ',' . $row->tgl_lahir);
				$sheet->setCellValue('H' . $x, $row->alamat);
				$sheet->setCellValue('I' . $x, $row->tglawal . ' - ' . $row->tglakhir);
				$x++;
			}
			$styleArray = [
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				],
				'borders' => [
					'allBorders' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						'color' => ['argb' => '00000000'],
					],
				],

			];
			$row = $x - 1;
			$sheet->getStyle('A1:H' . $row)->applyFromArray($styleArray);

			$writer = new Xlsx($spreadsheet);
			$filename = $name . time();

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
			header('Cache-Control: max-age=0');

			$writer->save('php://output');
		} catch (Exception $e) {
			echo 'Message: ' . $e->getMessage();
		}
	}

	public function siswa_import()
	{
		$id = $this->session->userdata('tipeuser');

		// $data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
		$data['datasiswa'] = $this->M_Siswa->getsiswa();
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['kelas'] = $this->M_Kelas->getkelas();
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'siswa'])->row()->id_menus;

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_siswa/v_siswa-import', $data);
		$this->load->view('template/footer');
	}

	public function import()
	{
		// echo $this->input->get('id_tahunakademik');	
		$id_tahunakademik = $this->input->get('id_tahunakademik');
		$data = $this->session->dataImport;
		$dataRow = 0;
		for ($i = 0; $i < count($data); $i++) {
			unset($data[$i]['tempat_tgl_lahir']);
			$data[$i]['id_tahunakademik'] = $id_tahunakademik;
			if ($this->db->get_where('tb_siswa', ['nis' => $data[$i]['nis']])->num_rows() === 0) {
				$this->db->insert('tb_siswa', $data[$i]);
				$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
				<strong>Sukses!</strong> Berhasil Import Data Siswa.
				</div>');
			} else {
				$dataRow = $dataRow + 1;
				$this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
				<strong>Perhatian!</strong> Ada ' . $dataRow . ' Data Siswa Yang Sudah Ada Dalam Database.
				</div>');
			}
		}
		// $this->session->unset_tempdata('dataImport');		
		redirect('siswa');
	}

	public function getSiswa()
	{
		echo json_encode($this->M_Siswa->getsiswa());
	}

	public function downloadTMP($idkelas)
	{
		// $data['kelas'] = $this->db->get_where('tb_kelas', ['id_kelas' => $kelas])->row()->kelas;
		// $this->load->view('v_siswa/v_siswa-download-tmp', $data);
		$kelas = $this->db->get_where('tb_kelas', ['id_kelas' => $idkelas])->row()->kelas;
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'DATA KELAS ' . $kelas);
		$sheet->mergeCells('A1:H1');

		$sheet->setCellValue('A2', 'SMKN 1 GARUT ');
		$sheet->mergeCells('A2:H2');

		$sheet->setCellValue('A3', ' ~Field dengan tanda ( * ) wajib di isi');
		$sheet->mergeCells('A3:H3');

		$sheet->setCellValue('A4', ' ~Untuk Format Tempat tanggal lahir jangan lupa dipisah dengan koma ( , )');
		$sheet->mergeCells('A4:H4');

		$sheet->setCellValue('A5', 'No');
		$sheet->setCellValue('B5', 'NIS*');
		$sheet->setCellValue('C5', 'RFID*');
		$sheet->setCellValue('D5', 'Nama Lengkap*');
		$sheet->setCellValue('E5', 'Jenis Kelamin*');
		$sheet->setCellValue('F5', 'Kelas*');
		$sheet->setCellValue('G5', 'Tempat, Tanggal Lahir');
		$sheet->setCellValue('H5', 'Alamat');

		$sheet->setCellValue('A6', ' ');
		$sheet->setCellValue('B6', ' ');
		$sheet->setCellValue('C6', ' ');
		$sheet->setCellValue('D6', ' ');
		$sheet->setCellValue('E6', ' ');
		$sheet->setCellValue('F6', $kelas);
		$sheet->setCellValue('G6', ' ');
		$sheet->setCellValue('H6', ' ');

		$styleArray = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],

		];
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		$sheet->getStyle('A1:H6')->applyFromArray($styleArray);

		$writer = new Xlsx($spreadsheet);
		$filename = $kelas . time();

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		// $siswa = $this->siswa_model->getAll();
		// $no = 1;
		// $x = 2;
		// foreach($siswa as $row)
		// {
		// 	$sheet->setCellValue('A'.$x, $no++);
		// 	$sheet->setCellValue('B'.$x, $row->nama);
		// 	$sheet->setCellValue('C'.$x, $row->kelas);
		// 	$sheet->setCellValue('D'.$x, $row->jenis_kelamin);
		// 	$sheet->setCellValue('E'.$x, $row->alamat);
		// 	$x++;
		// }	
	}

	public function graduate_page()
	{
		$id = $this->session->userdata('tipeuser');
		// $data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
		$data['menu'] = $this->M_Setting->getmenu1($id);
		$data['kelas'] = $this->db->query('SELECT DISTINCT(tb_kelas.id_kelas), tb_kelas.kelas, COUNT(tb_siswa.id_kelas) AS jmlsiswa FROM tb_siswa JOIN tb_kelas ON tb_siswa.id_kelas = tb_kelas.id_kelas WHERE tb_siswa.status = "aktif" AND tb_kelas.kelas LIKE "%XII%" GROUP BY tb_siswa.id_kelas')->result_array();
		$data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'siswa lulus'])->row()->id_menus;

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $data);
		$this->load->view('v_siswa/v_siswa-graduate-page', $data);
		$this->load->view('template/footer');
	}

	public function getSiswaSrch($key)
	{
		echo json_encode($this->db->query("SELECT tb_siswa.*, tb_kelas.kelas, tb_tahunakademik.tglawal, tb_tahunakademik.tglakhir FROM tb_siswa JOIN tb_kelas ON tb_siswa.id_kelas = tb_kelas.id_kelas JOIN tb_tahunakademik ON tb_siswa.id_tahunakademik = tb_tahunakademik.id_tahunakademik WHERE tb_siswa.status = 'aktif' AND (tb_siswa.nis LIKE '%$key%' OR tb_siswa.namasiswa LIKE '%$key%') AND tb_kelas.kelas LIKE '%XII%'")->result());
	}

	public function gradByOne($id)
	{
		if ($this->M_Siswa->siswaGraduate($id)) {
			echo 'berhasil';
		} else {
			echo 'gagal';
		}
	}

	public function getSiswaDetail($nis)
	{
		echo json_encode($this->db->query("SELECT * FROM tb_siswa JOIN tb_kelas ON tb_siswa.id_kelas = tb_kelas.id_kelas WHERE tb_siswa.nis = $nis")->row());
	}

	public function naikKelas()
	{

		$newKelas 	= $this->input->get("newKelas");
		$oldKelas 	= $this->input->get("oldKelas");
		$TA 	= $this->input->get("tahun_akademik");
		$data = [
			'id_kelas' => $newKelas,
			'id_tahunakademik' => $TA
		];
		$this->db->where("id_kelas", $oldKelas);
		$this->db->update("tb_siswa", $data);

		$this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
														<strong>Berhasil!</strong> Menaikan kan Kelas Siswa .
													</div>');
		redirect(base_url('naikkelas/'));
	}

	public function printOutButab($nis)
	{
		$id = $this->input->get('id_transaksi');
		$tipe = $this->input->get('tipe');
		// $saldo = intval($this->input->get('ss'));
		$saldo = 0;
		if ($tipe == 'siswa') {
			$query = $this->db->query("SELECT tb_transaksi.*, tb_siswa.namasiswa, tb_siswa.nis, tb_siswa.id_kelas, tb_users.kodepegawai, tb_mastertransaksi.debet, tb_mastertransaksi.kredit, tb_mastertransaksi.kategori FROM tb_transaksi JOIN tb_mastertransaksi ON tb_transaksi.id_jenistransaksi = tb_mastertransaksi.id_mastertransaksi JOIN tb_siswa ON tb_transaksi.id_siswa = tb_siswa.nis JOIN tb_users ON tb_transaksi.id_user = tb_users.id WHERE tb_transaksi.id_transaksi = $id")->row();
			$query->nama = '';
			$query->namaTransaksi = $query->namasiswa;
			$query->kosong = false;
			$saldo = 0;
			$kelas = $this->db->get_where('tb_kelas', ['id_kelas' => $query->id_kelas])->row()->kelas;
			$kredit = $this->db->query("SELECT tb_transaksi.nominal FROM tb_transaksi JOIN tb_mastertransaksi ON tb_transaksi.id_jenistransaksi = tb_mastertransaksi.id_mastertransaksi JOIN tb_siswa ON tb_transaksi.id_siswa = tb_siswa.nis WHERE tb_siswa.nis = $query->id_siswa AND tb_mastertransaksi.kredit = 'siswa'")->result_array();
			$debet = $this->db->query("SELECT tb_transaksi.nominal FROM tb_transaksi JOIN tb_mastertransaksi ON tb_transaksi.id_jenistransaksi = tb_mastertransaksi.id_mastertransaksi JOIN tb_siswa ON tb_transaksi.id_siswa = tb_siswa.nis WHERE tb_siswa.nis = $query->id_siswa AND tb_mastertransaksi.debet = 'siswa'")->result_array();
			foreach ($kredit as $row) {
				$saldo += $row['nominal'];
			}
			foreach ($debet as $row) {
				$saldo -= $row['nominal'];
			}
		} else if ($tipe == 'staf') {
			$query = $this->db->query("SELECT tb_transaksi.*, tb_staf.nama, tb_staf.nopegawai FROM tb_transaksi JOIN tb_mastertransaksi ON tb_transaksi.id_jenistransaksi = tb_mastertransaksi.id_mastertransaksi JOIN tb_staf ON tb_transaksi.id_anggota = tb_staf.id_staf WHERE tb_transaksi.id_transaksi = $id")->row();
			$query->namasiswa = '';
			$query->namaTransaksi = $query->nama;
			$query->kosong = false;
			$kredit = $this->db->query("SELECT tb_transaksi.nominal FROM tb_transaksi JOIN tb_mastertransaksi ON tb_transaksi.id_jenistransaksi = tb_mastertransaksi.id_mastertransaksi JOIN tb_staf ON tb_transaksi.id_anggota = tb_staf.id_staf WHERE tb_staf.id_staf = $query->id_anggota AND tb_mastertransaksi.kredit = 'koperasi'")->result_array();
			$debet = $this->db->query("SELECT tb_transaksi.nominal FROM tb_transaksi JOIN tb_mastertransaksi ON tb_transaksi.id_jenistransaksi = tb_mastertransaksi.id_mastertransaksi JOIN tb_staf ON tb_transaksi.id_anggota = tb_staf.id_staf WHERE tb_staf.id_staf = $query->id_anggota AND tb_mastertransaksi.debet = 'koperasi'")->result_array();
			foreach ($kredit as $row) {
				$saldo += $row['nominal'];
			}
			foreach ($debet as $row) {
				$saldo -= $row['nominal'];
			}
			// $kasmasuk = $this->db->query("SELECT SUM(nominal) as saldoKM FROM tb_kasmasuk")->row_array();
			// $tnsksi = $this->db->query("SELECT SUM(nominal) as jumlah FROM tb_transaksi WHERE id_anggota = '" . $id_staf . "'")->row_array();

			// $saldo = intval($kasmasuk['saldoKM'] - $tnsksi['jumlah']);		
		}
		$idlogin = $this->session->userdata('id_user');
		$staf = $this->db->get_where('tb_users', ['id' => $idlogin])->row()->nama;
		$date = date_format(date_create($query->tgl_update), "d-m-Y H:i:s");
		// $print_status = "none";			
		// var_dump($query);	
		// $data['query'] = $query;
		// $data['staf'] = $staf;
		// $data['sisasaldo'] = $saldo;
		// $this->load->view('v_transaksi/v_transaksi-print', $data);
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
			// $printer->selectPrintMode(Escpos\Printer::MODE_DOUBLE_HEIGHT); // Setting teks menjadi lebih besar
			$printer->setJustification(Escpos\Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
			$printer->text("\n");
			$printer->text("\n");
			$printer->text("\n");
			$printer->text("\n");
			$printer->text("\n");
			$printer->text("          ");
			$printer->text($query->kodetransaksi." ".$query->kodepegawai." ".$date);
			$printer->text("\n");
			$printer->text("          ");
			if ($tipe == 'staf') {
				$norek = $query->nopegawai;
			} else
			if ($tipe == 'siswa') {
				$norek = $query->nis;
			}
			if($query->debet <> ""){
				$kt = "D";
			} else if ($query->kredit <> ""){
				$kt = "K";
			}
			$printer->text($norek ." ". number_format($query->nominal,2) . " ".$kt."\n");
			$printer->text("          ");
			$printer->text($query->namaTransaksi . " ".$query->kategori);	
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
