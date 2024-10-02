<?php
require_once dirname(__FILE__) . '/../Model/ShopingContents.php';
$row = $_POST;
$shopinginsert = new ShopingContents();
$row['Count'] = intval($row['Count']);
$shopinginsert->getSearchShopData($row['Janl'], $row['Prodact'], $row['Count']);
