<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



class M_Bukutabungan extends CI_Model
{
    function getBukutabungan()
    {
        $query = $this->db->get('tb_bukutabungan');
        return $query->result();        
    }

    function getBukutabunganbyID($id)
    {
        $query = $this->db->get_where('tb_bukutabungan', array('id' => $id));
        return $query->row();        
    }

    public function getsiswadetail(){
        $this->db->select('nis as id_nasabah, namasiswa as nama_nasabah');
        $this->db->from('tb_siswa');
    	$query = $this->db->get();
        return $query->result_array();
    }  

    public function getstafdetail(){
        $this->db->select('nopegawai as id_nasabah, nama as nama_nasabah');
        $this->db->from('tb_staf');
    	$query = $this->db->get();
        return $query->result_array();
    }  

    public function getnoseri()
    {
        $tahun = date('Y');
        $this->db->select('nomor_seri');
        $this->db->from('tb_bukutabungan');
        $this->db->like('nomor_seri', $tahun.'.');
        $this->db->order_by('nomor_seri', 'desc');
    	$query = $this->db->get();
        return $query->row()->nomor_seri;
    }  

    public function getnamasiswa($nis)
    {
        $this->db->select('namasiswa');
        $this->db->from('tb_siswa');
        $this->db->where('nis', $nis);
    	$query = $this->db->get();
        return $query->row()->namasiswa;
    }  

    public function getnamastaf($nopegawai)
    {
        $this->db->select('nama');
        $this->db->from('tb_staf');
        $this->db->where('nopegawai', $nopegawai);
    	$query = $this->db->get();
        return $query->row()->nama;
    }  

    public function getalamatsiswa($nis)
    {
        $this->db->select('alamat');
        $this->db->from('tb_siswa');
        $this->db->where('nis', $nis);
    	$query = $this->db->get();
        return $query->row()->alamat;
    }  

    public function getalamatstaf($nopegawai)
    {
        $this->db->select('alamat');
        $this->db->from('tb_staf');
        $this->db->where('nopegawai', $nopegawai);
    	$query = $this->db->get();
        return $query->row()->alamat;
    }  

    function tambah($data)
    {
        $this->db->insert('tb_bukutabungan', $data);
    }

    function hapus($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tb_bukutabungan');
    }

}
