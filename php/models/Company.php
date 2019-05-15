<?php

class Company{
  public $company_name;
  public $company_domain;
  public $company_id;
  public $status;
  public $select_option;
  public $total_contacts;
  public $recipients;
  public $created_at;

  public function link(){
    $x = "company.php?id=" . $this->company_id;
    return $x;
  }

  public function createdAt(){
    $today = date("Y.m.d", strtotime($this->created_at));
    return $today;
  }
}