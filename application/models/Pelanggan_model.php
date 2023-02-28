<?php
class Pelanggan_model extends CI_Model {
    function post($data) {
        $this->db->insert("pelanggan",$data);
    }

    function get($id = 0) {
        if(!$id) {
            $this->db->order_by("name","ASC");
            return $this->db->get("pelanggan");
        } else {
            return $this->db->get_where("pelanggan",['id' => $id]);
        }
    }

    function put($id,$data) {
        $this->db->where("id",$id);
        $this->db->update("pelanggan",$data);
    }

    function delete($id) {
        $this->db->delete("pelanggan",["id" => $id]);
    }
}