<?php

$db = require('../services/db.php');
require '../functions.php';

if (isset($_POST['submit']) ){
  $mergedArray = [];
  //nestlar arrayen så man kan köra lite SQL på den
  foreach($_POST['template'] as $key => $val) {
    $mergedArray[$key] = [$val, $_POST['status'][$key]];
  }
  //loopar igenom arrayen och sedan uppdaterar tabellen
  foreach($mergedArray as $id => $values){
    $sql = "UPDATE companies SET (select_option, status) = (:select_option, :status)  WHERE id=:id";
    $order = $db->prepare($sql)->execute([
      'select_option' => $values[0],
      'status' => $values[1],
      'id' => $id
      ]);
  }
  $sec = "0";
}

header("Refresh: $sec; index.php");