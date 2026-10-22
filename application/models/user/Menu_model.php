<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model {

    public function getMenu() {
        $query = "SELECT * FROM m_menu WHERE parent IS NULL AND is_active='1'";
        return $this->db->query($query)->result_array();
    }

    public function getSubMenu($role_id,$parent) {
        return $this->db->get_where('m_menu', ['parent' => $parent,'is_active' => '1'])->result_array();
    }

    public function getSubMenurow($role_id,$parent) {
        return $this->db->get_where('m_menu', ['menu_id' => $parent,'is_active' => '1'])->num_rows();
    }

    public function showMenu($role_id) {
        $query = "SELECT * FROM m_menu WHERE parent IS NULL AND is_active='1' ORDER BY urutan ASC";
        return $this->db->query($query)->result_array();
    }

    public function showSubMenu($role_id, $parent) {
        return $this->db->select('*')
                    ->from('m_menu')
                    ->where('parent', $parent)
                    ->where('is_active', '1')
                    ->order_by('urutan', 'ASC')
                    ->get()
                    ->result_array();
    }
}