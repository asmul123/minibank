<?php

date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class ImportLaporan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->load->model('M_Setting');
        // $this->load->model('M_Siswa');
        // $this->load->model('M_Provinsi');
        $this->load->model('M_TanggalLaporan');
        // $this->load->model('M_Kota');
        // $this->load->model('M_Kecamatan');
        // $this->load->model('M_Transaksi');
        // $this->load->model('M_Kelas');
        $this->load->model('M_Akses');
    }

    public function upload()
    {
        // var_dump($_FILES['file']);
        $file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
            $fileName = time() . $_FILES['file']['name'];
            $config['upload_path'] = './assets/excel/'; //buat folder dengan nama assets di root folder
            $config['file_name'] = str_replace(" ", "", $fileName);
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = 10000;
            $arr_file = explode('.', $_FILES['file']['name']);
            $extension = end($arr_file);

            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {
            } else {
                $this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
                                                            <strong>Perhatian!</strong> <br>
                                                            <ul>															
                                                                <li>' . $this->upload->display_errors() . '</li>															
                                                            </ul>						
                                                        </div>');
                redirect(base_url('laporan-import/'));
            }

            $media = $this->upload->data('file');
            $inputFileName = './assets/excel/' . $config['file_name'];

            if ('csv' == $extension) {
                $reader = new Csv();
            } else if ('xlsx' == $extension) {
                $reader = new Xlsx();
            } else if ('xls' == $extension) {
                $reader = new Xls();
            }

            try {
                $spreadsheet = $reader->load($inputFileName);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();
                $sheetRows = $spreadsheet->getActiveSheet()->getHighestRow();
                $tgllaporan = $sheetData[4][0];

                // var_dump($sheet);
                // die();              
                $data = [];
                $dataKosong = [];
                $no = 0;
                $kosong = 0;
                $id_tipeuser = $this->db->get_where('tb_tipeuser', ['tipeuser' => 'laporan'])->row_array();
                // // var_dump($id_tipeuser);
                if (intval($sheetRows) >= 31) {
                    for ($i = 31; $i < count($sheetData); $i++) {
                        // var_dump($sheetData[$i]); 
                        $pecah = explode('. ', $sheetData[$i][0]);
                        if (count($pecah) == 2) {
                            $kategori = $pecah[1];
                            $tes = explode('-', $kategori);
                            $apa = $tes[0];
                        } else if (count($pecah) == 3) {
                            $kategori = $pecah[1] . ". " . $pecah[2];
                            $tes = explode('-', $kategori);
                            $apa = $tes[0];
                        }


                        $data[$no++] = array(
                            // "nis" => $sheetData[$i][1],
                            // "rfid" => $sheetData[$i][2],
                            // "namasiswa" => $sheetData[$i][3],
                            // 'jk' => $this->M_Siswa->getJK(str_replace(' ', '', $sheetData[$i][4])),
                            // 'id_kelas' => $this->db->get_where('tb_kelas', ['kelas LIKE' => '%' . $sheetData[$i][5] . '%'])->row()->id_kelas,
                            // 'tempat_tgl_lahir' => $sheetData[$i][6],
                            // 'alamat' => $sheetData[$i][7],
                            // 'tgl_lahir' => (!empty(explode(',', $sheetData[$i][6])[1]) ? explode(',', $sheetData[$i][6])[1] : ''),
                            // 'tempat_lahir' => (!empty(explode(',', $sheetData[$i][6])[0]) ? explode(',', $sheetData[$i][6])[0] : ''),
                            // 'tgl_update' => date("Y-m-d h:i:sa"),
                            // 'id_user' => $this->session->userdata('id_user'),
                            // 'status' => 'aktif',
                            // 'id_tipeuser' => $id_tipeuser['id_tipeuser'],
                            // 'password' => 'siswa123',

                            "nama_produk_nyatu" => $sheetData[$i][0],
                            "nama_kategori" => $apa,
                            "nama_produk" => (!empty(explode('-', $sheetData[$i][0])[1]) ? explode('-', $sheetData[$i][0])[1] : ''),
                            'jumlah' => $sheetData[$i][1],
                            'laba' => $sheetData[$i][2],
                            'nilai' => $sheetData[$i][3],
                            'id_user' => $this->session->userdata('id_user'),
                            'status' => 'aktif',
                            'id_tipeuser' => $id_tipeuser['id_tipeuser'],

                        );
                    }
                } else {
                    $this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
                                                                <strong>Perhatian!</strong> File excel anda kosong.
                                                            </div>');
                    redirect(base_url('laporan-import/'));
                }
                $id = $this->session->userdata('tipeuser');
                $this->session->dataImport = $data;
                $this->session->dataKosongImport = $data;

                if (count($dataKosong) !== 0) {
                    $this->session->set_flashdata('alert', '<div class="alert alert-warning left-icon-alert" role="alert">
                                                            <strong>Perhatian!</strong> Ada data anda yang kosong, Tolong cek kembali dan Upload Kembali.
                                                        </div>');
                    redirect(base_url('laporan-import/'));
                } else {
                    $datas['datalaporan'] = $this->session->dataImport;
                    $datas['countLaporan'] = count($this->session->dataImport);
                    $datas['tglLaporan'] = $tgllaporan;
                    $datas['menu'] = $this->M_Setting->getmenu1($id);
                    // $datas['kelas'] = $this->M_Kelas->getkelas();
                    $datas['tanggallaporan'] = $this->db->query('SELECT id_tanggallaporan AS id, tgl FROM `tb_tanggallaporan`')->result_array();
                    $datas['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'laporan'])->row()->id_menus;

                    $this->load->view('template/header');
                    $this->load->view('template/sidebar', $datas);
                    $this->load->view('v_laporan/v_laporan-import_page', $datas);
                    $this->load->view('template/footer');
                }
            } catch (Exception $e) {
                var_dump($e);
            }
        } else {
            redirect('laporan-import');
        }
    }
}
