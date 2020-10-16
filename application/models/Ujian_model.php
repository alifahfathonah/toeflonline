<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ujian_model extends CI_Model
{

    public function getDataUjian($id)
    {
        $this->datatables->select('a.id_ujian, a.token, a.nama_ujian, b.nama_mataujian, a.jumlah_soal, CONCAT(a.tgl_mulai, " <br/> (", a.waktu, " Menit)") as waktu, a.jenis');
        $this->datatables->from('m_ujian a');
        $this->datatables->join('mataujian b', 'a.mataujian_id = b.id_mataujian');
        if ($id !== null) {
            $this->datatables->where('operator_id', $id);
        }
        return $this->datatables->generate();
    }

    public function getListUjian($id, $kelompok)
    {
        $this->datatables->select("a.id_ujian, e.nama_operator, d.nama_kelompok, a.nama_ujian, b.nama_mataujian, a.jumlah_soal, CONCAT(a.tgl_mulai, ' <br/> (', a.waktu, ' Menit)') as waktu,  (SELECT COUNT(id) FROM h_ujian h WHERE h.peserta_id = {$id} AND h.ujian_id = a.id_ujian) AS ada");
        $this->datatables->from('m_ujian a');
        $this->datatables->join('mataujian b', 'a.mataujian_id = b.id_mataujian');
        $this->datatables->join('kelompok_operator c', "a.operator_id = c.operator_id");
        $this->datatables->join('kelompok d', 'c.kelompok_id = d.id_kelompok');
        $this->datatables->join('operator e', 'e.id_operator = c.operator_id');
        $this->datatables->where('d.id_kelompok', $kelompok);
        return $this->datatables->generate();
    }

    public function getUjianById($id)
    {
        $this->db->select('*');
        $this->db->from('m_ujian a');
        $this->db->join('operator b', 'a.operator_id=b.id_operator');
        $this->db->join('mataujian c', 'a.mataujian_id=c.id_mataujian');
        $this->db->where('id_ujian', $id);
        return $this->db->get()->row();
    }

    public function getIdOperator($no_identitas)
    {
        $this->db->select('id_operator, nama_operator')->from('operator')->where('no_identitas', $no_identitas);
        return $this->db->get()->row();
    }

    public function getJumlahSoal($operator)
    {
        $this->db->select('COUNT(id_soal) as jml_soal');
        $this->db->from('tb_soal');
        $this->db->where('operator_id', $operator);
        return $this->db->get()->row();
    }

    public function getIdPeserta($no_identitas)
    {
        $this->db->select('*');
        $this->db->from('peserta a');
        $this->db->join('kelompok b', 'a.kelompok_id=b.id_kelompok');
        $this->db->join('angkatan c', 'b.angkatan_id=c.id_angkatan');
        $this->db->where('no_identitas', $no_identitas);
        return $this->db->get()->row();
    }

    public function HslUjian($id, $peserta)
    {
        $this->db->select('*, UNIX_TIMESTAMP(tgl_selesai) as waktu_habis');
        $this->db->from('h_ujian');
        $this->db->where('ujian_id', $id);
        $this->db->where('peserta_id', $peserta);
        return $this->db->get();
    }

    public function getSoal($id)
    {
        $ujian = $this->getUjianById($id);
        $order = $ujian->jenis === "acak" ? 'rand()' : 'id_soal';

        $this->db->select('id_soal, soal, file, tipe_file, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban');
        $this->db->from('tb_soal');
        $this->db->where('operator_id', $ujian->operator_id);
        $this->db->where('mataujian_id', $ujian->mataujian_id);
        $this->db->order_by($order);
        $this->db->limit($ujian->jumlah_soal);
        return $this->db->get()->result();
    }

    public function ambilSoal($pc_urut_soal1, $pc_urut_soal_arr)
    {
        $this->db->select("*, {$pc_urut_soal1} AS jawaban");
        $this->db->from('tb_soal');
        $this->db->where('id_soal', $pc_urut_soal_arr);
        return $this->db->get()->row();
    }

    public function getJawaban($id_tes)
    {
        $this->db->select('list_jawaban');
        $this->db->from('h_ujian');
        $this->db->where('id', $id_tes);
        return $this->db->get()->row()->list_jawaban;
    }

    public function getHasilUjian($no_identitas = null)
    {
        $this->datatables->select('b.id_ujian, b.nama_ujian, b.jumlah_soal, CONCAT(b.waktu, " Menit") as waktu, b.tgl_mulai');
        $this->datatables->select('c.nama_mataujian, d.nama_operator');
        $this->datatables->from('h_ujian a');
        $this->datatables->join('m_ujian b', 'a.ujian_id = b.id_ujian');
        $this->datatables->join('mataujian c', 'b.mataujian_id = c.id_mataujian');
        $this->datatables->join('operator d', 'b.operator_id = d.id_operator');
        $this->datatables->group_by('b.id_ujian');
        if ($no_identitas !== null) {
            $this->datatables->where('d.no_identitas', $no_identitas);
        }
        return $this->datatables->generate();
    }

    public function HslUjianById($id, $dt = false)
    {
        if ($dt === false) {
            $db = "db";
            $get = "get";
        } else {
            $db = "datatables";
            $get = "generate";
        }

        $this->$db->select('d.id, a.nama, b.nama_kelompok, c.nama_angkatan, d.jml_benar, d.nilai');
        $this->$db->from('peserta a');
        $this->$db->join('kelompok b', 'a.kelompok_id=b.id_kelompok');
        $this->$db->join('angkatan c', 'b.angkatan_id=c.id_angkatan');
        $this->$db->join('h_ujian d', 'a.id_peserta=d.peserta_id');
        $this->$db->where(['d.ujian_id' => $id]);
        return $this->$db->$get();
    }

    public function bandingNilai($id)
    {
        $this->db->select_min('nilai', 'min_nilai');
        $this->db->select_max('nilai', 'max_nilai');
        $this->db->select_avg('FORMAT(FLOOR(nilai),0)', 'avg_nilai');
        $this->db->where('ujian_id', $id);
        return $this->db->get('h_ujian')->row();
    }
}
