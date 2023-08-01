<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Laporan extends CI_Model
{

    function getlaporan()
    {
        return $this->db->query("SELECT tb_laporan.*, tb_tanggallaporan.tgl FROM tb_laporan JOIN tb_tanggallaporan ON tb_laporan.id_tanggallaporan = tb_tanggallaporan.id_tanggallaporan")->result();
    }

    function getlaporandetail($id_tanggallaporan)
    {
        // $this->db->where('nis', $nis)
        $this->db->where(['id_tanggallaporan' => $id_tanggallaporan]);
        $this->db->order_by('nama_kategori', 'ASC');
        $query = $this->db->get('tb_laporan')->result_array();
        return $query;
    }
    function getlaporandetailby($id_tanggallaporan)
    {
        // $this->db->where('nis', $nis)
       $this->db->where(['id_tanggallaporan' => $id_tanggallaporan]);

        $this->db->order_by('nama_kategori', 'ASC');
        $query = $this->db->get('tb_laporan')->result_array();
        return $query;
    }
}
