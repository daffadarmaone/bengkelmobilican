<?php
class Karyawan_model extends CI_Model {
    function post($data) {
        $this->db->insert("karyawan",$data);
    }

    function get($id = 0) {
        if(!$id) {
            $this->db->order_by("name","ASC");
            return $this->db->get("karyawan");
        } else {
            return $this->db->get_where("karyawan",['id' => $id]);
        }
    }

    function put($id,$data) {
        $this->db->where("id",$id);
        $this->db->update("karyawan",$data);
    }

    function delete($id) {
        $this->db->delete("karyawan",["id" => $id]);
    }
}