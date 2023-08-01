<?php

date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Laporan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->load->model('M_Setting');
        $this->load->model('M_Laporan');
        // $this->load->model('M_Provinsi');
        $this->load->model('M_TanggalLaporan');
        // $this->load->model('M_Kota');
        // $this->load->model('M_Kecamatan');
        // $this->load->model('M_Transaksi');
        // $this->load->model('M_Kelas');
        $this->load->model('M_Akses');

        cek_login_user();
    }

    public function index()
    {
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['datalaporan'] = $this->M_Laporan->getlaporan();
        $data['tanggallaporan'] = $this->M_TanggalLaporan->getAll();
        // var_dump($data);
        // $data['datalulus'] = $this->M_Siswa->getLulus();
        $data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'laporan'])->row()->id_menus;

        $this->load->view('template/header');
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_laporan/v_laporan', $data);
        $this->load->view('template/footer');
    }

    public function laporan_detail($id_tanggallaporan)
    {
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['datalaporan'] = $this->M_Laporan->getlaporandetail($id_tanggallaporan);
        $data['datalaporanby'] =  $this->db->query("SELECT DISTINCT nama_kategori, sum(laba) as persen, sum(nilai) as omset, sum(nilai)-sum(laba) as tot FROM tb_laporan where id_tanggallaporan='$id_tanggallaporan' group by nama_kategori")->result_array();
        $data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPathDet(), $id);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'laporan'])->row()->id_menus;
        $total_semua = $this->db->query("SELECT * FROM tb_laporan where id_tanggallaporan='$id_tanggallaporan'")->result();
        $totpersen = 0;
        $totomset = 0;
        foreach ($total_semua as $d) {
            $totpersen += $d->laba;
            $totomset += $d->nilai;
        }
        $data['datatotpersen'] = $totpersen;
        $data['datatotomset'] = $totomset;
        $data['datatotpembagian'] = $totomset - $totpersen;

        $this->load->view('template/header');
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_laporan/v_laporan-detail', $data);
        $this->load->view('template/footer');
        // print_r($this->M_Siswa->getsiswadetail($nis));
    }
    public function laporan_import()
    {
        $id = $this->session->userdata('tipeuser');

        // $data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
        $data['datalaporan'] = $this->M_Laporan->getlaporan();
        $data['menu'] = $this->M_Setting->getmenu1($id);

        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'laporan'])->row()->id_menus;

        $this->load->view('template/header');
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_laporan/v_laporan-import', $data);
        $this->load->view('template/footer');
    }

    public function import()
    {
        // echo $this->input->get('id_tahunakademik');	
        $id_tanggallaporan = $this->input->get('id_tanggallaporan');
        $data = $this->session->dataImport;
        $dataRow = 0;
        for ($i = 0; $i < count($data); $i++) {
            unset($data[$i]['nama_produk_nyatu']);
            $data[$i]['id_tanggallaporan'] = $id_tanggallaporan;

            $this->db->insert('tb_laporan', $data[$i]);
            $this->db->where('jumlah is NULL');
            $this->db->delete('tb_laporan');
            $this->session->set_flashdata('alert', '<div class="alert alert-success left-icon-alert" role="alert">
				<strong>Sukses!</strong> Berhasil Import Data Siswa.
				</div>');
        }
        // $this->session->unset_tempdata('dataImport');		
        redirect('laporan');
    }
    public function laporan_export()
    {
        $id = $this->session->userdata('tipeuser');
        $data['datalaporan'] = $this->M_Laporan->getlaporan();
        $data['menu'] = $this->M_Setting->getmenu1($id);
        // $data['kelas'] = $this->db->query('SELECT DISTINCT(tb_kelas.id_kelas), tb_kelas.kelas, COUNT(tb_siswa.id_kelas) AS jmlsiswa FROM tb_siswa JOIN tb_kelas ON tb_siswa.id_kelas = tb_kelas.id_kelas WHERE tb_siswa.status = "aktif" GROUP BY tb_siswa.id_kelas')->result_array();
        $data['tanggallaporan'] = $this->db->query('SELECT DISTINCT(tb_tanggallaporan.id_tanggallaporan), tb_tanggallaporan.tgl FROM tb_laporan JOIN tb_tanggallaporan ON tb_laporan.id_tanggallaporan = tb_tanggallaporan.id_tanggallaporan WHERE tb_laporan.status = "aktif" GROUP BY tb_laporan.id_tanggallaporan')->result_array();
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'laporan'])->row()->id_menus;

        // var_dump($data);
        $this->load->view('template/header');
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_laporan/v_laporan-export', $data);
        $this->load->view('template/footer');
    }

    public function export_process($id)
    {

        // $this->db->select('tb_siswa.*, tb_kelas.kelas, tb_tahunakademik.tglawal, tb_tahunakademik.tglakhir');
        $this->db->select('tb_laporan.*, tb_tanggallaporan.tgl');
        $this->db->join('tb_tanggallaporan', 'tb_laporan.id_tanggallaporan = tb_tanggallaporan.id_tanggallaporan');
        //  $this->db->join('tb_tahunakademik', 'tb_siswa.id_tahunakademik = tb_tahunakademik.id_tahunakademik');
        //  $this->db->where('tb_siswa.id_kelas', $id);
        $this->db->where('tb_laporan.id_tanggallaporan', $id);
        $this->db->order_by('nama_kategori', 'asc');
        $laporan =    $this->db->get('tb_laporan')->result();
        // var_dump($siswa);
        // die();
        // $this->db->query('tb_siswa', ['id_kelas' => $id])->result();
        $name = $this->db->get_where('tb_tanggallaporan', ['id_tanggallaporan' => $id])->row()->tgl;

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
            $sheet->setCellValue('A1', 'NAMA TOKO');
            $sheet->mergeCells('A1:D1');
            // $sheet->setCellValue('A1', 'DATA KELAS ' . $name);
            // $sheet->mergeCells('A1:I1');
            $sheet->setCellValue('A2', '');
            $sheet->mergeCells('A2:D2');

            $sheet->setCellValue('A3', '0000-0000-0000');
            $sheet->mergeCells('A3:D3');
            // $sheet->setCellValue('A2', 'SMKN 1 GARUT ');
            // $sheet->mergeCells('A2:I2');

            $sheet->setCellValue('A4', '');
            $sheet->mergeCells('A4:D4');

            $sheet->setCellValue('A5', 'LAPORAN TANGGAL ' . $name);
            $sheet->mergeCells('A5:D5');
            $sheet->setCellValue('A6', '');
            $sheet->mergeCells('A6:D6');
            $sheet->setCellValue('A7', 'LABA');
            $sheet->mergeCells('A7:B7');
            $sheet->setCellValue('C7', 'OMSET');
            $sheet->mergeCells('C7:D7');
            $sheet->setCellValue('A8', 'tes');
            $sheet->mergeCells('A8:B8');
            $sheet->setCellValue('C8', 'tes');
            $sheet->mergeCells('C8:D8');
            $sheet->setCellValue('A9', '');
            $sheet->mergeCells('A9:D9');
            $sheet->setCellValue('A10', '');
            $sheet->mergeCells('A10:D10');

            $sheet->setCellValue('A11', 'PEMASUKAN');
            $sheet->mergeCells('A11:B11');
            $sheet->setCellValue('C11', 'PENGELUARAN');
            $sheet->mergeCells('C11:D11');
            $sheet->setCellValue('A12', '0');
            $sheet->mergeCells('A12:B12');
            $sheet->setCellValue('C12', '0');
            $sheet->mergeCells('C12:D12');
            $sheet->setCellValue('A13', '');
            $sheet->mergeCells('A13:D13');
            $sheet->setCellValue('A14', '');
            $sheet->mergeCells('A14:D14');

            $sheet->setCellValue('A15', 'ESTIMASI LABA BERSIH (LABA + PEMASUKAN - PENGELUARAN)');
            $sheet->mergeCells('A15:D15');
            $sheet->setCellValue('A16', 'tes');
            $sheet->mergeCells('A16:D16');
            $sheet->setCellValue('A17', '');
            $sheet->mergeCells('A17:D17');
            $sheet->setCellValue('A18', '');
            $sheet->mergeCells('A18:D18');

            $sheet->setCellValue('A19', 'KEUANGAN');
            $sheet->mergeCells('A19:D19');

            $sheet->setCellValue('A20', 'Cashbox');
            $sheet->setCellValue('B20', 'Masuk');
            $sheet->setCellValue('C20', 'Keluar');
            $sheet->setCellValue('D20', 'Saldo');
            $sheet->setCellValue('A21', 'Cashbox');
            $sheet->setCellValue('B21', 'Masuk');
            $sheet->setCellValue('C21', 'Keluar');
            $sheet->setCellValue('D21', 'Saldo');
            $sheet->setCellValue('A22', 'Cashbox');
            $sheet->setCellValue('B22', 'Masuk');
            $sheet->setCellValue('C22', 'Keluar');
            $sheet->setCellValue('D22', 'Saldo');
            $sheet->setCellValue('A23', '');
            $sheet->mergeCells('A23:D23');
            $sheet->setCellValue('A24', '');
            $sheet->mergeCells('A24:D24');

            $sheet->setCellValue('A25', 'TRANSAKSI OPERATOR');
            $sheet->mergeCells('A25:D25');

            $sheet->setCellValue('A26', 'Cashbox');
            $sheet->setCellValue('B26', 'Masuk');
            $sheet->setCellValue('C26', 'Keluar');
            $sheet->setCellValue('D26', 'Saldo');
            $sheet->setCellValue('A27', 'Cashbox');
            $sheet->setCellValue('B27', 'Masuk');
            $sheet->setCellValue('C27', 'Keluar');
            $sheet->setCellValue('D27', 'Saldo');

            $sheet->setCellValue('A28', '');
            $sheet->mergeCells('A28:D28');
            $sheet->setCellValue('A29', '');
            $sheet->mergeCells('A29:D29');

            $sheet->setCellValue('A30', 'PRODUK TERJUAL');
            $sheet->mergeCells('A30:D30');

            $sheet->setCellValue('A31', 'produk');
            $sheet->setCellValue('B31', 'jumlah');
            $sheet->setCellValue('C31', 'laba');
            $sheet->setCellValue('D31', 'nilai');

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);

            $x = 32;

            foreach ($laporan as $row) {
                $sheet->setCellValue('A' . $x, $row->nama_kategori . '-' . $row->nama_produk);
                $sheet->setCellValue('B' . $x, $row->jumlah);
                $sheet->setCellValue('C' . $x, $row->laba);
                $sheet->setCellValue('D' . $x, $row->nilai);
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
                'font' => [
                    'name' => 'Arial',
                ],

            ];
            $fontArray = [

                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'rotation' => 0.0,
                    'startColor' => [
                        'rgb' => 'C0C0C0'
                    ],
                ],
            ];


            $row = $x - 1;
            $sheet->getStyle('A1:D' . $row)->applyFromArray($styleArray);
            $sheet->getStyle('A7:D7')->applyFromArray($fontArray);
            $sheet->getStyle('A11:D11')->applyFromArray($fontArray);
            $sheet->getStyle('A15:D15')->applyFromArray($fontArray);
            $sheet->getStyle('A19:D19')->applyFromArray($fontArray);
            $sheet->getStyle('A20:D20')->applyFromArray($fontArray);
            $sheet->getStyle('A25:D25')->applyFromArray($fontArray);
            $sheet->getStyle('A26:D26')->applyFromArray($fontArray);
            $sheet->getStyle('A30:D30')->applyFromArray($fontArray);
            $sheet->getStyle('A31:D31')->applyFromArray($fontArray);



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
    public function getLaporanByTanggalLaporan($idTanggalLaporan)
    {
        // $this->db->where();
        //echo json_encode($this->db->query("SELECT tb_siswa.*, tb_kelas.kelas, DATE_FORMAT(tb_tahunakademik.tglawal, '%Y') AS tglawal, DATE_FORMAT(tb_tahunakademik.tglakhir, '%Y') AS tglakhir FROM tb_siswa JOIN tb_kelas ON tb_siswa.id_kelas = tb_kelas.id_kelas JOIN tb_tahunakademik ON tb_siswa.id_tahunakademik = tb_tahunakademik.id_tahunakademik WHERE tb_siswa.id_kelas = $idKelas AND tb_siswa.status = 'aktif'")->result());
        echo json_encode($this->db->query("SELECT tb_laporan.*, tb_tanggallaporan.tgl FROM tb_laporan JOIN tb_tanggallaporan ON tb_laporan.id_tanggallaporan = tb_tanggallaporan.id_tanggallaporan WHERE tb_laporan.id_tanggallaporan = $idTanggalLaporan AND tb_laporan.status = 'aktif'")->result());
    }
}
