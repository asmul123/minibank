<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class M_Staff extends CI_Model
{
    function getAll()
    {   
        $this->db->where('status', 'aktif');
        $this->db->where('id_tipeuser', 1);
        $query = $this->db->get('tb_staf');
        return $query->result_array();
    }

    function hapus($id_staf)
    {
        $this->db->where('id_staf', $id_staf);
        $this->db->update('tb_staf', ['status' => 'tidak aktif']);
    }

    function tambah($data)
    {
        $this->db->insert('tb_staf', $data);
    }
    function getById($id_staf)
    {
        return $this->db->get_where('tb_staf', ['id_staf' => $id_staf])->row_array();
    }

    function getByNoPegawai($nopegawai)
    {
        return $this->db->get_where('tb_staf', ['nopegawai' => $nopegawai, 'status' => 'aktif'])->num_rows();
    }

    function ubah($data, $id_staf)
    {
        $this->db->where('id_staf', $id_staf);
        $this->db->update('tb_staf', $data);
    }
}
