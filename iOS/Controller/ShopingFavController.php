<?php
require_once dirname(__FILE__) . '/../Model/ShopingContents.php';
$row = $_POST;
$favShoping = new ShopingContents();
$favShoping->favShopData($row);
