<?php
defined('BASEPATH') or exit('No direct script access allowed');


class TanggalLaporan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->load->model('M_Setting');
        $this->load->model('M_TanggalLaporan');
        $this->load->model('M_Akses');
        cek_login_user();
    }

    public function index()
    {
        $this->load->view('template/header');
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['tanggallaporan'] = $this->M_TanggalLaporan->getAll();
        $data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'tanggal laporan'])->row()->id_menus;

        $this->load->view('template/sidebar', $data);
        $this->load->view('v_tanggallaporan/v_tanggallaporan.php', $data);
        $this->load->view('template/footer');
    }

    public function detail($id_tanggallaporan)
    {
        $this->load->view('template/header');
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['tanggallaporan'] = $this->M_TanggalLaporan->getBYId($id_tanggallaporan);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'tanggal laporan'])->row()->id_menus;
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_tanggallaporan/v_tanggallaporan_detail.php', $data);
        $this->load->view('template/footer');
    }
    public function hapus($id_tanggallaporan)
    {
        $this->M_TanggalLaporan->hapus($id_tanggallaporan);
        $this->session->set_flashdata('message', '<div class="alert alert-success left-icon-alert" role="alert"> <strong>Sukses!</strong> Data Berhasil Dihapus</div>');
        redirect('tanggallaporan');
    }

    public function tambahData()
    {
        $this->load->view('template/header');
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'tanggal laporan'])->row()->id_menus;
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_tanggallaporan/v_tanggallaporan_add.php', $data);
        $this->load->view('template/footer');
    }

    public function tambah()
    {
        // $id_user = $this->session-
        $data = [
            'tgl' => $this->input->post('tgl'),

            'id_user' => $this->session->userdata('id_user')
        ];
        $cekData = $this->db->get_where('tb_tanggallaporan', ['tgl' =>  $data['tgl']])->result();
        if (count($cekData) > 0) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning left-icon-alert" role="alert"> <strong>Perhatian!</strong> Data Sudah ada</div>');
            redirect('tanggallaporan-add');
        } else {
            $this->M_TanggalLaporan->tambah($data);
            $this->session->set_flashdata('message', '<div class="alert alert-success left-icon-alert" role="alert"> <strong>Sukses!</strong> Data Berhasil Ditambahkan</div>');
            redirect('tanggallaporan');
        }
    }

    public function ubahdata($id_tanggallaporan)
    {
        $this->load->view('template/header');
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['tanggallaporan'] = $this->M_TanggalLaporan->getBYId($id_tanggallaporan);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'tanggal laporan'])->row()->id_menus;
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_tanggallaporan/v_tanggallaporan_ubah.php', $data);
        $this->load->view('template/footer');
    }

    public function ubah()
    {
        $id = $this->input->post('id_tanggallaporan');
        $data = [
            'tgl' => $this->input->post('tgl'),

            'id_user' => '01'
        ];
        $cekData = $this->db->get_where('tb_tanggallaporan', ['id_tanggallaporan !=' => $id, 'tgl' =>  $data['tgl']])->result();
        if (count($cekData) > 0) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning left-icon-alert" role="alert"> <strong>Perhatian!</strong> Data Sudah ada</div>');
            redirect('tanggallaporan-ubah/' . $id);
        } else {
            $this->M_TanggalLaporan->ubah($id, $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success left-icon-alert" role="alert"><strong>Sukses!</strong> Data Berhasil Diubah</div>');
            redirect('tanggallaporan');
        }
    }

    public function getTAList($id)
    {
        $ta = $this->db->get_where("tb_tanggallaporan", ['id_tanggallaporan' => $id])->row();
        $tg = $ta->tgl;
        // date_create($row['tglakhir']); echo date_format($tglakhir,"Y");
        $query = $this->db->query("SELECT * FROM tb_tanggallaporan WHERE tgl >= '$tg' AND id_tanggallaporan != $id")->result();
        echo json_encode($query);
    }
}
