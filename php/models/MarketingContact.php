<?php

class MarketingContact{
  public $id;
  public $name;
  public $telephone;
  public $select_option;
  public $status;
  public $company_id;
  public $mail_sent;
  public $declined_date;
  public $last_seen;

  public function lastSeen(){
    $today = date("Y.m.d", strtotime($this->last_seen));
    return $today;
  }
}