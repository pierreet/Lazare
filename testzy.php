<?php
echo(" ");
$id = 118;
$location = "http://www.maisonlazare.com/wp-admin/admin.php?page=wplazare_m_orders&action=deleteok&saveditem=".$id;
$status = "302";
header("Location: $location", true, $status);