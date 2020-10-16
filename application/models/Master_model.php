<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_model extends CI_Model
{

    public function create($table, $data, $batch = false)
    {
        if ($batch === false) {
            $insert = $this->db->insert($table, $data);
        } else {
            $insert = $this->db->insert_batch($table, $data);
        }
        return $insert;
    }

    public function update($table, $data, $pk, $id = null, $batch = false)
    {
        if ($batch === false) {
            $insert = $this->db->update($table, $data, array($pk => $id));
        } else {
            $insert = $this->db->update_batch($table, $data, $pk);
        }
        return $insert;
    }

    public function delete($table, $data, $pk)
    {
        $this->db->where_in($pk, $data);
        return $this->db->delete($table);
    }

    /**
     * Data Kelompok
     */

    public function getDataKelompok()
    {
        $this->datatables->select('id_kelompok, nama_kelompok, id_angkatan, nama_angkatan');
        $this->datatables->from('kelompok');
        $this->datatables->join('angkatan', 'angkatan_id=id_angkatan');
        $this->datatables->add_column('bulk_select', '<div class="text-center"><input type="checkbox" class="check" name="checked[]" value="$1"/></div>', 'id_kelompok, nama_kelompok, id_angkatan, nama_angkatan');
        return $this->datatables->generate();
    }

    public function getKelompokById($id)
    {
        $this->db->where_in('id_kelompok', $id);
        $this->db->order_by('nama_kelompok');
        $query = $this->db->get('kelompok')->result();
        return $query;
    }

    /**
     * Data Angkatan
     */

    public function getDataAngkatan()
    {
        $this->datatables->select('id_angkatan, nama_angkatan');
        $this->datatables->from('angkatan');
        $this->datatables->add_column('bulk_select', '<div class="text-center"><input type="checkbox" class="check" name="checked[]" value="$1"/></div>', 'id_angkatan, nama_angkatan');
        return $this->datatables->generate();
    }

    public function getAngkatanById($id)
    {
        $this->db->where_in('id_angkatan', $id);
        $this->db->order_by('nama_angkatan');
        $query = $this->db->get('angkatan')->result();
        return $query;
    }

    /**
     * Data Peserta
     */

    public function getDataPeserta()
    {
        $this->datatables->select('a.id_peserta, a.nama, a.no_identitas, a.email, b.nama_kelompok, c.nama_angkatan');
        $this->datatables->select('(SELECT COUNT(id) FROM users WHERE username = a.no_identitas) AS ada');
        $this->datatables->from('peserta a');
        $this->datatables->join('kelompok b', 'a.kelompok_id=b.id_kelompok');
        $this->datatables->join('angkatan c', 'b.angkatan_id=c.id_angkatan');
        return $this->datatables->generate();
    }

    public function getPesertaById($id)
    {
        $this->db->select('*');
        $this->db->from('peserta');
        $this->db->join('kelompok', 'kelompok_id=id_kelompok');
        $this->db->join('angkatan', 'angkatan_id=id_angkatan');
        $this->db->where(['id_peserta' => $id]);
        return $this->db->get()->row();
    }

    public function getAngkatan()
    {
        $this->db->select('id_angkatan, nama_angkatan');
        $this->db->from('kelompok');
        $this->db->join('angkatan', 'angkatan_id=id_angkatan');
        $this->db->order_by('nama_angkatan', 'ASC');
        $this->db->group_by('id_angkatan');
        $query = $this->db->get();
        return $query->result();
    }

    public function getAllAngkatan($id = null)
    {
        if ($id === null) {
            $this->db->order_by('nama_angkatan', 'ASC');
            return $this->db->get('angkatan')->result();
        } else {
            $this->db->select('angkatan_id');
            $this->db->from('angkatan_mataujian');
            $this->db->where('mataujian_id', $id);
            $angkatan = $this->db->get()->result();
            $id_angkatan = [];
            foreach ($angkatan as $j) {
                $id_angkatan[] = $j->angkatan_id;
            }
            if ($id_angkatan === []) {
                $id_angkatan = null;
            }

            $this->db->select('*');
            $this->db->from('angkatan');
            $this->db->where_not_in('id_angkatan', $id_angkatan);
            $mataujian = $this->db->get()->result();
            return $mataujian;
        }
    }

    public function getKelompokByAngkatan($id)
    {
        $query = $this->db->get_where('kelompok', array('angkatan_id' => $id));
        return $query->result();
    }

    /**
     * Data Operator
     */

    public function getDataOperator()
    {
        $this->datatables->select('a.id_operator,a.no_identitas, a.nama_operator, a.email, a.mataujian_id, b.nama_mataujian, (SELECT COUNT(id) FROM users WHERE username = a.no_identitas OR email = a.email) AS ada');
        $this->datatables->from('operator a');
        $this->datatables->join('mataujian b', 'a.mataujian_id=b.id_mataujian');
        return $this->datatables->generate();
    }

    public function getOperatorById($id)
    {
        $query = $this->db->get_where('operator', array('id_operator' => $id));
        return $query->row();
    }

    /**
     * Data Ujian
     */

    public function getDataMataujian()
    {
        $this->datatables->select('id_mataujian, nama_mataujian');
        $this->datatables->from('mataujian');
        return $this->datatables->generate();
    }

    public function getAllMataujian()
    {
        return $this->db->get('mataujian')->result();
    }

    public function getMataujianById($id, $single = false)
    {
        if ($single === false) {
            $this->db->where_in('id_mataujian', $id);
            $this->db->order_by('nama_mataujian');
            $query = $this->db->get('mataujian')->result();
        } else {
            $query = $this->db->get_where('mataujian', array('id_mataujian' => $id))->row();
        }
        return $query;
    }

    /**
     * Data Kelompok Operator
     */

    public function getKelompokOperator()
    {
        $this->datatables->select('kelompok_operator.id, operator.id_operator, operator.no_identitas, operator.nama_operator, GROUP_CONCAT(kelompok.nama_kelompok) as kelompok');
        $this->datatables->from('kelompok_operator');
        $this->datatables->join('kelompok', 'kelompok_id=id_kelompok');
        $this->datatables->join('operator', 'operator_id=id_operator');
        $this->datatables->group_by('operator.nama_operator');
        return $this->datatables->generate();
    }

    public function getAllOperator($id = null)
    {
        $this->db->select('operator_id');
        $this->db->from('kelompok_operator');
        if ($id !== null) {
            $this->db->where_not_in('operator_id', [$id]);
        }
        $operator = $this->db->get()->result();
        $id_operator = [];
        foreach ($operator as $d) {
            $id_operator[] = $d->operator_id;
        }
        if ($id_operator === []) {
            $id_operator = null;
        }

        $this->db->select('id_operator, no_identitas, nama_operator');
        $this->db->from('operator');
        $this->db->where_not_in('id_operator', $id_operator);
        return $this->db->get()->result();
    }


    public function getAllKelompok()
    {
        $this->db->select('id_kelompok, nama_kelompok, nama_angkatan');
        $this->db->from('kelompok');
        $this->db->join('angkatan', 'angkatan_id=id_angkatan');
        $this->db->order_by('nama_kelompok');
        return $this->db->get()->result();
    }

    public function getKelompokByOperator($id)
    {
        $this->db->select('kelompok.id_kelompok');
        $this->db->from('kelompok_operator');
        $this->db->join('kelompok', 'kelompok_operator.kelompok_id=kelompok.id_kelompok');
        $this->db->where('operator_id', $id);
        $query = $this->db->get()->result();
        return $query;
    }
    /**
     * Data Angkatan Mataujian
     */

    public function getAngkatanMataujian()
    {
        $this->datatables->select('angkatan_mataujian.id, mataujian.id_mataujian, mataujian.nama_mataujian, angkatan.id_angkatan, GROUP_CONCAT(angkatan.nama_angkatan) as nama_angkatan');
        $this->datatables->from('angkatan_mataujian');
        $this->datatables->join('mataujian', 'mataujian_id=id_mataujian');
        $this->datatables->join('angkatan', 'angkatan_id=id_angkatan');
        $this->datatables->group_by('mataujian.nama_mataujian');
        return $this->datatables->generate();
    }

    public function getMataujian($id = null)
    {
        $this->db->select('mataujian_id');
        $this->db->from('angkatan_mataujian');
        if ($id !== null) {
            $this->db->where_not_in('mataujian_id', [$id]);
        }
        $mataujian = $this->db->get()->result();
        $id_mataujian = [];
        foreach ($mataujian as $d) {
            $id_mataujian[] = $d->mataujian_id;
        }
        if ($id_mataujian === []) {
            $id_mataujian = null;
        }

        $this->db->select('id_mataujian, nama_mataujian');
        $this->db->from('mataujian');
        $this->db->where_not_in('id_mataujian', $id_mataujian);
        return $this->db->get()->result();
    }

    public function getAngkatanByIdMataujian($id)
    {
        $this->db->select('angkatan.id_angkatan');
        $this->db->from('angkatan_mataujian');
        $this->db->join('angkatan', 'angkatan_mataujian.angkatan_id=angkatan.id_angkatan');
        $this->db->where('mataujian_id', $id);
        $query = $this->db->get()->result();
        return $query;
    }
}
