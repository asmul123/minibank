<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Backup extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url', 'download'));
        $this->load->model('M_Setting');
        cek_login_user();
        
        // Cek apakah user adalah admin
        $id_tipe = $this->session->userdata('tipeuser');
        $this->db->where('id_tipeuser', $id_tipe);
        $tipe = $this->db->get('tb_tipeuser')->row();
        
        // if (!$tipe || $tipe->nama_tipeuser !== 'Admin') {
        //     redirect('');
        // }
    }

    public function index()
    {
        $id = $this->session->userdata('tipeuser');
        $data['menu'] = $this->M_Setting->getmenu1($id);
        $data['activeMenu'] = '';
        $data['title'] = 'Backup Database';
        
        // Ambil list file backup
        $backup_dir = FCPATH . 'assets/backup/';
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0777, true);
        }
        
        $files = array();
        if ($handle = opendir($backup_dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $file_info = array(
                        'name' => $file,
                        'size' => filesize($backup_dir . $file),
                        'date' => filemtime($backup_dir . $file),
                        'date_formatted' => date('d M Y H:i:s', filemtime($backup_dir . $file))
                    );
                    $files[] = $file_info;
                }
            }
            closedir($handle);
        }
        
        // Sort by date descending
        usort($files, function($a, $b) {
            return $b['date'] - $a['date'];
        });
        
        $data['backup_files'] = $files;

        $this->load->view('template/header');
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_backup/index', $data);
        $this->load->view('template/footer');
    }

    public function create_backup()
    {
        $this->load->model('M_Backup');
        $result = $this->M_Backup->backup_database();
        
        if ($result['status']) {
            $this->session->set_flashdata('success', 'Backup database berhasil dibuat: ' . $result['filename']);
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat backup: ' . $result['message']);
        }
        
        redirect('backup');
    }

    public function download($filename = '')
    {
        if (empty($filename)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan');
            redirect('backup');
        }

        $backup_dir = FCPATH . 'assets/backup/';
        $file_path = $backup_dir . $filename;

        if (!file_exists($file_path)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan');
            redirect('backup');
        }

        // Security: Prevent directory traversal
        $real_path = realpath($file_path);
        $real_backup_dir = realpath($backup_dir);
        
        if ($real_path === false || strpos($real_path, $real_backup_dir) !== 0) {
            $this->session->set_flashdata('error', 'Akses tidak sah');
            redirect('backup');
        }

        force_download($filename, file_get_contents($file_path));
    }

    public function delete($filename = '')
    {
        if (empty($filename)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan');
            redirect('backup');
        }

        $backup_dir = FCPATH . 'assets/backup/';
        $file_path = $backup_dir . $filename;

        if (!file_exists($file_path)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan');
            redirect('backup');
        }

        // Security: Prevent directory traversal
        $real_path = realpath($file_path);
        $real_backup_dir = realpath($backup_dir);
        
        if ($real_path === false || strpos($real_path, $real_backup_dir) !== 0) {
            $this->session->set_flashdata('error', 'Akses tidak sah');
            redirect('backup');
        }

        if (unlink($file_path)) {
            $this->session->set_flashdata('success', 'File backup berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus file backup');
        }

        redirect('backup');
    }

    public function restore($filename = '')
    {
        if (empty($filename)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan');
            redirect('backup');
        }

        $backup_dir = FCPATH . 'assets/backup/';
        $file_path = $backup_dir . $filename;

        if (!file_exists($file_path)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan');
            redirect('backup');
        }

        // Security: Prevent directory traversal
        $real_path = realpath($file_path);
        $real_backup_dir = realpath($backup_dir);
        
        if ($real_path === false || strpos($real_path, $real_backup_dir) !== 0) {
            $this->session->set_flashdata('error', 'Akses tidak sah');
            redirect('backup');
        }

        $this->load->model('M_Backup');
        $result = $this->M_Backup->restore_database($file_path);

        if ($result['status']) {
            $this->session->set_flashdata('success', 'Database berhasil di-restore dari file: ' . $filename);
        } else {
            $this->session->set_flashdata('error', 'Gagal me-restore database: ' . $result['message']);
        }

        redirect('backup');
    }
}
?>
