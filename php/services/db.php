<?php

try {
  $dbHost = "localhost";
  $dbUsername = "leomac";
  $dbPassword = "";
  $dbName = "testdb";
  $dbPort = 5432;

  return new PDO("pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}", $dbUsername, $dbPassword);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}