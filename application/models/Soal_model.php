<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Soal_model extends CI_Model
{

    public function getDataSoal($id, $operator)
    {
        $this->datatables->select('a.id_soal, a.soal, FROM_UNIXTIME(a.created_on) as created_on, FROM_UNIXTIME(a.updated_on) as updated_on, b.nama_mataujian, c.nama_operator');
        $this->datatables->from('tb_soal a');
        $this->datatables->join('mataujian b', 'b.id_mataujian=a.mataujian_id');
        $this->datatables->join('operator c', 'c.id_operator=a.operator_id');
        if ($id !== null && $operator === null) {
            $this->datatables->where('a.mataujian_id', $id);
        } else if ($id !== null && $operator !== null) {
            $this->datatables->where('a.operator_id', $operator);
        }
        return $this->datatables->generate();
    }

    public function getSoalById($id)
    {
        return $this->db->get_where('tb_soal', ['id_soal' => $id])->row();
    }

    public function getMataujianOperator($no_identitas)
    {
        $this->db->select('mataujian_id, nama_mataujian, id_operator, nama_operator');
        $this->db->join('mataujian', 'mataujian_id=id_mataujian');
        $this->db->from('operator')->where('no_identitas', $no_identitas);
        return $this->db->get()->row();
    }

    public function getAllOperator()
    {
        $this->db->select('*');
        $this->db->from('operator a');
        $this->db->join('mataujian b', 'a.mataujian_id=b.id_mataujian');
        return $this->db->get()->result();
    }
}
