<?php
require_once dirname(__FILE__) . '/../Model/ShopingContents.php';
$row = $_POST;
$favDelete = new ShopingContents();
$favDelete->favdeleteShopData($row);
