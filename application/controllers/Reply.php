<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reply extends CI_Controller {

  public function __construct() {
    parent::__construct();

    $this->load->model('MReply');
  }

  /**
   * Add reply
   */
  function add() {
    $redirect_address = 'Plan/detail/'.$this->input->post('plan_date').'/'.$this->input->post('user_id');
    $reply_comment = "";
    if($this->input->post('reply_comment')){
      $reply_comment = $this->input->post('reply_comment');

      // reply_comment NULL check
      if("" == $reply_comment){
        echo "<script>alert('빈값을 허용하지 않습니다.')</script>";
        redirect($redirect_address, 'refresh');
      }

      $data = array(
          'user_id' => $this->input->post('user_id'),
          'write_user' => $this->input->post('write_user'),
          'plan_date' => $this->input->post('plan_date'),
          'reply_comment' => $this->input->post('reply_comment'),
          'up_reply_id' => 0
      );

      $added_reply_count = $this->MReply->add_reply($data);

      if($added_reply_count > 0){
        echo "<script>alert('등록되었습니다.')</script>";
        redirect($redirect_address, 'refresh');
      }
    }else{
      echo "<script>alert('올바르지 않은 요청입니다.')</script>";
      redirect($redirect_address, 'refresh');
    }
  }

  /**
   * Delete reply
   */
  function delete() {
    if($this->uri->segment(3)){
      $reply_id = $this->uri->segment(3);

      $deleted_reply_count = $this->MReply->delete_reply($reply_id);

      if($deleted_reply_count > 0){
        echo "<script>alert('삭제되었습니다..')</script>";
        redirect("Plan/detail/".$this->uri->segment(4).'/'.$this->uri->segment(5), "refresh");
      }
    }else{
      echo "<script>alert('올바르지 않은 요청입니다.')</script>";
      redirect("javascript:history.back(-1);");
    }
  }
}