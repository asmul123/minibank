<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class M_TanggalLaporan extends CI_Model
{
    function getAll()
    {
        $query = $this->db->query("SELECT * FROM tb_tanggallaporan ORDER BY YEAR(tgl), MONTH(tgl), DAY(tgl)
");
        return $query->result_array();
    }

    function getBYId($id_tanggallaporan)
    {
        $query = $this->db->get_where('tb_tanggallaporan', ['id_tanggallaporan' => $id_tanggallaporan]);
        return $query->row_array();
    }

    function hapus($id_tanggallaporan)
    {
        $this->db->where('id_tanggallaporan', $id_tanggallaporan);
        $this->db->delete('tb_tanggallaporan');
        $this->db->where('id_tanggallaporan', $id_tanggallaporan);
        $this->db->delete('tb_laporan');
    }

    function tambah($data)
    {
        $this->db->insert('tb_tanggallaporan', $data);
    }

    function ubah($id, $data)
    {
        $this->db->where('id_tanggallaporan', $id);
        $this->db->update('tb_tanggallaporan', $data);
    }

    public function getLaporanByTanggalLaporan($idTanggalLaporan)
    {
        $this->db->where(['id_tanggallaporan' => $idTanggalLaporan]);
        $this->db->where(['status' => 'aktif']);
        $this->db->order_by('nama_kategori', 'ASC');
        $query = $this->db->get('tb_laporan')->result_array();
        return $query;
    }
    // public function getSiswaByKelas($idKelas)
    // {
    //     $this->db->where(['id_kelas' => $idKelas]);
    //     $this->db->where(['status' => 'aktif']);
    //     $this->db->order_by('namasiswa', 'ASC');
    //     $query = $this->db->get('tb_siswa')->result_array();
    //     return $query;
    // }
}
