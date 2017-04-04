<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MPoint extends CI_Model{

  public function __construct() {
    parent::__construct();
  }

  function get_user_point($user) {
    $sql = "SELECT *
                 , (month_attendance_count * 10
                  + month_plan_count * 10
                  + month_reply_count * 1) AS month_point
                 , (attendance_count * 10
                  + plan_count * 10
                  + reply_count * 1) AS accml_point
            FROM (SELECT COUNT(*) AS month_attendance_count
                  FROM scrum_user u
                     , scrum_attendance a
                  WHERE u.user_id = '$user'
                  AND u.user_id = a.user_id
                  AND DATE_FORMAT(a.attendance_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')) AS month_attendance
               , (SELECT COUNT(*) AS month_plan_count
                  FROM scrum_user u
                     , scrum_plan_info p
                  WHERE u.user_id = '$user'
                  AND u.user_id = p.user_id
                  AND DATE_FORMAT(p.plan_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')) AS month_plan
               , (SELECT COUNT(*) AS month_reply_count
                  FROM scrum_user u
                     , scrum_reply r
                  WHERE u.user_id = '$user'
                  AND u.user_id = r.write_user
                  AND r.user_id != r.write_user
                  AND DATE_FORMAT(r.plan_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')) AS month_reply
               , (SELECT COUNT(*) AS attendance_count
                  FROM scrum_user u
                     , scrum_attendance a
                  WHERE u.user_id = '$user'
                  AND u.user_id = a.user_id
                  AND DATE_FORMAT(a.attendance_date, '%Y-%m') > '2017-03') AS attendance
               , (SELECT COUNT(*) AS plan_count
                  FROM scrum_user u
                     , scrum_plan_info p
                  WHERE u.user_id = '$user'
                  AND u.user_id = p.user_id
                  AND DATE_FORMAT(p.plan_date, '%Y-%m') > '2017-03') AS plan
               , (SELECT COUNT(*) AS reply_count
                  FROM scrum_user u
                     , scrum_reply r
                  WHERE u.user_id = '$user'
                  AND u.user_id = r.write_user
                  AND r.user_id != r.write_user
                  AND DATE_FORMAT(r.plan_date, '%Y-%m')  > '2017-03') AS reply";
    $query = $this->db->query($sql);

    return $query->row();
  }

  function get_rank_of_this_month() {
    $sql = "SELECT user_id
                 , user_name
                 , attendance_count
                 , plan_count
                 , reply_count
                 , (attendance_count * 10 + plan_count * 10 + reply_count) AS point
                 , IFNULL(CASE WHEN @prev = (attendance_count * 10 + plan_count * 10 + reply_count) THEN @rank
                          WHEN @prev := (attendance_count * 10 + plan_count * 10 + reply_count) THEN @rank := @rank + 1
                          END, @rank) AS rank
            FROM (SELECT u.user_id
                       , u.user_name
                       , count(p.plan_date) AS plan_count
                       , count(r.reply_id) AS reply_count
                       , (SELECT COUNT(*) AS count
                          FROM scrum_attendance
                          WHERE user_id = u.user_id
                          AND DATE_FORMAT(attendance_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')) AS attendance_count
                  FROM scrum_user u
                  LEFT JOIN scrum_plan_info p ON u.user_id = p.user_id
                  AND DATE_FORMAT(p.plan_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                  LEFT JOIN scrum_reply r ON u.user_id = r.write_user
                  AND r.plan_date = p.plan_date
                  AND DATE_FORMAT(r.plan_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                  GROUP BY u.user_id) a
                , (SELECT @rank := 0, @prev := NULL) AS temp
            ORDER BY point DESC";
    $query = $this->db->query($sql);

    return $query->result_array();
  }
}