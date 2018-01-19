<?php
//channel=release&type=release&system=${OS}&machine=${MACH}
//https://www.domoticz.cn/download.php?channel=release&type=release&system=linux&machine=armv7l
$channel = $_GET['channel'];
$type = $_GET['type'];
$system = $_GET['system'];
$machine = $_GET['machine'];
$url = $type."/".$_GET['channel']."/domoticz_".$system."_".$machine.".tgz";

//重定向
header("Location: ".$url);
exit;
?>