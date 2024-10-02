<?php
require_once dirname(__FILE__) . '/../Model/ShopingContents.php';
$row = $_POST;
$shopinginsert = new ShopingContents();
$shopinginsert->insertShopdata($row);