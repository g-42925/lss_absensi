<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model {

    public function getMenu() {
        $query = "SELECT * FROM m_menu WHERE parent IS NULL AND is_active='1'";
        return $this->db->query($query)->result_array();
    }

    public function getSubMenu($role_id,$parent) {
        $query = "SELECT * FROM m_menu a LEFT JOIN m_role_access b ON a.menu_id=b.id_menu AND b.id_role='$role_id' WHERE parent='$parent' AND is_active='1'";
        return $this->db->query($query)->result_array();
    }

    public function getSubMenurow($role_id,$parent) {
        $query = "SELECT * FROM m_menu a JOIN m_role_access b ON a.menu_id=b.id_menu WHERE menu_id='$parent' AND b.id_role='$role_id' AND is_active='1'";
        return $this->db->query($query)->num_rows();
    }

    public function showMenu($role_id) {
        if ($role_id==1 || $role_id==2) {
            $query = "SELECT * FROM m_menu WHERE parent IS NULL AND is_active='1' ORDER BY urutan ASC";
            return $this->db->query($query)->result_array();
        }else{
            $haktemp="";
            $hak_a = $this->db->query("SELECT * FROM m_role_access a JOIN m_role b ON a.id_role=b.role_id WHERE id_role='$role_id' AND b.is_status='y' AND b.is_del='n'");
            foreach($hak_a->result_array() as $hak_akses) :
                $haktemp=$haktemp."".$hak_akses['id_menu'].",";
            endforeach;
            $akses_menu=rtrim($haktemp,',');
            $array_akses_menu=explode(',',$akses_menu);

            if ($hak_a->num_rows() > 0) {
                $query = "SELECT * FROM m_menu WHERE parent IS NULL AND is_active='1' AND menu_id IN($akses_menu) ORDER BY urutan ASC";
                return $this->db->query($query)->result_array();
            }else{
                return 'no';
            }
        }
    }

    public function showSubMenu($role_id, $parent) {
      $this->db->select('*');
      $this->db->from('m_menu');
      $this->db->where('parent', $parent);
      $this->db->where('is_active', '1');

      if ($role_id != 1 && $role_id != 2) {

        $hak_a = $this->db->select('a.id_menu')
            ->from('m_role_access a')
            ->join('m_role b', 'a.id_role=b.role_id')
            ->where('a.id_role', $role_id)
            ->where('b.is_status', 'y')
            ->where('b.is_del', 'n')
            ->get()
            ->result_array();

        if (empty($hak_a)) {
            return []; // ⬅️ PENTING: hindari IN()
        }

        $akses_menu = array_column($hak_a, 'id_menu');
        $this->db->where_in('menu_id', $akses_menu);
      }

      $this->db->order_by('urutan', 'ASC');
      return $this->db->get()->result_array();
    }
}