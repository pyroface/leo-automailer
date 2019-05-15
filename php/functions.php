<?php

function dd($data) {
  die(var_dump($data));
}

function renderView ($name, $data = []) {
  extract($data);
  require_once "../views/{$name}.php";
}