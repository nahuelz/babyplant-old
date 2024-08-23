<?php
  date_default_timezone_set("America/Buenos_Aires");
  session_start();
  error_reporting(0);
  $version = 42;
  header('Content-type: text/html; charset=utf-8');
  if(!isset($_SESSION["babyplant-token"]) || !isset($_COOKIE["babyplant-token"])){
    header("Location: index.php");
  }

  if($_SESSION["babyplant-token"] != $_COOKIE["babyplant-token"]){
    header("Location: index.php");
  }
?>