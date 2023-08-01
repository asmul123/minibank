<?php date_default_timezone_set("Asia/Jakarta");  ?>
<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Bulanan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->load->model('M_Setting');
        $this->load->model('M_Bulanan');
        $this->load->model('M_Akses');

        cek_login_user();
    }

    public function index()
    {
        $this->load->view('template/header');
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['bulanan'] = $this->M_Bulanan->getAll();
        $data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPath(), $id);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'bulanan'])->row()->id_menus;

        $this->load->view('template/sidebar', $data);
        $this->load->view('v_bulanan/v_bulanan.php', $data);
        $this->load->view('template/footer');
    }
    public function hapus($id_staf)
    {
        $this->M_Staff->hapus($id_staf);
        $this->session->set_flashdata('message', '<div class="alert alert-success left-icon-alert" role="alert"> <strong>Sukses!</strong> Data Berhasil Dihapus</div>');
        redirect('Staff');
    }

    public function tambahdata()
    {
        $this->load->view('template/header');
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'bulanan'])->row()->id_menus;
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_bulanan/v_bulanan_add.php', $data);
        $this->load->view('template/footer');
    }

    public function getkota($id_provinsi)
    {
        $query = $this->db->query("SELECT * FROM tb_kota WHERE id_provinsi ='" . $id_provinsi . "'")->result_array();
        echo json_encode($query);
    }
    public function getkecamatan($id_kota)
    {
        $query = $this->db->query("SELECT * FROM tb_kecamatan WHERE id_kota ='" . $id_kota . "'")->result_array();
        echo json_encode($query);
    }

    public function tambah()
    {
        $bulananlaporan = $this->input->post('bulananlaporan');
        $data = [
            'bulananlaporan' => $bulananlaporan,
            'status' => 'aktif',
            'id_tipeuser' => 1,
            'id_user' => $this->session->userdata('id_user'),
        ];

        if ($this->M_Bulanan->getByBulananLaporan($bulananlaporan) >= 1) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger left-icon-alert " role="alert">
		                                            		<strong>Gagal!</strong> Bulan : "' . $bulananlaporan . ' ", sudah ada
		                                        		</div>');
            redirect('bulanan/tambahData');
        } else {
            $this->M_Bulanan->tambah($data);
            $this->session->set_flashdata('message', '<div class="alert alert-success left-icon-alert" role="alert">
		                                            		<strong>Sukses!</strong> Berhasil Menambahkan Data.
		                                        		</div>');
            redirect('bulanan');
        }
    }

    public function detail($id_staf)
    {
        $this->load->view('template/header');
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['staf'] = $this->M_Staff->getById($id_staf);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'guru dan anggota'])->row()->id_menus;
        $data['akses'] = $this->M_Akses->getByLinkSubMenu(urlPathDet(), $id);

        $this->load->view('template/sidebar', $data);
        $this->load->view('v_staff/v_staff_detail.php', $data);
        $this->load->view('template/footer');
    }

    public function ubahdata($id_staf)
    {
        $this->load->view('template/header');
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['staf'] = $this->M_Staff->getById($id_staf);
        $data['activeMenu'] = $this->db->get_where('tb_submenu', ['submenu' => 'guru dan anggota'])->row()->id_menus;
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_staff/v_staff_ubah.php', $data);
        $this->load->view('template/footer');
    }

    public function ubah()
    {
        $id_staf = $this->input->post('id_staf');
        $data = [
            'nopegawai' => $this->input->post('nopegawai'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'provinsi' => $this->input->post('s_provinsi'),
            'kota' => $this->input->post('s_kota'),
            'kecamatan' => $this->input->post('s_kecamatan'),
            'tlp' => $this->input->post('telp'),
            'tgl_upddate' => date('Y-m-d h:i:s'),
            'id_user' => $this->session->userdata('id_user'),
        ];
        $this->M_Staff->ubah($data, $id_staf);
        if ($this->input->post('profile')) {
            $session = array(
                'authenticated' => true, // Buat session authenticated dengan value true
                'nopegawai' => $data['nopegawai'],  // Buat session nip
                'nama' => $data['nama'],
                'id_user' => $id_staf, // Buat session authenticated
                'tipeuser' => $data['id_tipeuser'],
                'login' => true
            );
            $this->session->set_userdata($session);
            $this->session->set_flashdata('message', '<div class="alert alert-success left-icon-alert" role="alert">
		                                            		<strong>Sukses!</strong> Data Berhasil Diubah.
		                                        		</div>');
            redirect('profile');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-success left-icon-alert" role="alert"> <strong>Sukses!</strong> Data Berhasil Diubah</div>');
            redirect('staff');
        }
    }

    public function getStaff()
    {
        echo json_encode($this->db->get_where('tb_staf', ['id_tipeuser' => 1, 'status' => 'aktif'])->result_array());
    }
}
