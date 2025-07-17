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

    public function showSubMenu($role_id,$parent) {
        if ($role_id==1 || $role_id==2) {
            $query = "SELECT * FROM m_menu WHERE parent='$parent' AND is_active='1' ORDER BY urutan ASC";
            return $this->db->query($query)->result_array();
        }else{
            $haktemp="";
            $hak_a = $this->db->query("SELECT * FROM m_role_access a JOIN m_role b ON a.id_role=b.role_id WHERE id_role='$role_id' AND b.is_status='y' AND b.is_del='n'");
            foreach($hak_a->result_array() as $hak_akses) :
                $haktemp=$haktemp."".$hak_akses['id_menu'].",";
            endforeach;
            $akses_menu=rtrim($haktemp,',');
            $array_akses_menu=explode(',',$akses_menu);

            $query = "SELECT * FROM m_menu WHERE parent='$parent' AND is_active='1' AND menu_id IN($akses_menu) ORDER BY urutan ASC";
            return $this->db->query($query)->result_array();
        }
    }

}