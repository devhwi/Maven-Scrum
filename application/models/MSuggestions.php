<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MSuggestions extends CI_Model{

  public function __construct() {
    parent::__construct();
  }

  function get_suggestions() {
    $sql = "SELECT s.*
                 , u.user_name AS user_name
                 , u.user_img AS user_img
            FROM scrum_suggestion s
               , scrum_user u
            WHERE s.user_id = u.user_id
            ORDER BY s.suggestion_timestamp DESC
            ";
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  function add_suggestion($data) {
    $query = $this->db->insert('scrum_suggestion', $data);
  }

  function delete_suggestion($id) {
    $this->db->where('suggestion_id', $id);
    $this->db->delete('scrum_suggestion');
  }

  function check_valid_if_delete($id) {
    $sql = "SELECT user_id FROM scrum_suggestion WHERE suggestion_id = '$id'";
    $query = $this->db->query($sql);
    $row = $query->row();
    return $row->user_id;
  }
}
