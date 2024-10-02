<?php
require_once dirname(__FILE__) . '/../Model/ShopingContents.php';
$row = $_POST;
$deleteShoping = new ShopingContents();
$deleteShoping->deleteShopData($row);
