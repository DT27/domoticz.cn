<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
if(isset($_GET['api'])){
	$api = $_GET['api'];
}else{
	exit();
}
$latitude = $_GET['latitude'];
$longitude = $_GET['longitude'];
$lang = isset($_GET['lang'])?$_GET['lang']:'zh';
$units = isset($_GET['units'])?$_GET['units']:'auto';

// 初始化一个 cURL 对象 
$curl = curl_init(); 
// 设置你需要抓取的URL https://api.darksky.net/forecast/'+_APIKEY_DARKSKY+'/'+latitude+','+longitude+'?lang='+_LANGUAGE.substr(0,2)+'&units=auto
curl_setopt($curl, CURLOPT_URL, 'https://api.darksky.net/forecast/'.$api.'/'.$latitude.','.$longitude.'?lang='.$lang.'&units='.$units); 
// 设置header 响应头是否输出
curl_setopt($curl, CURLOPT_HEADER, 0); 
// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
// 1如果成功只将结果返回，不自动输出任何内容。如果失败返回FALSE 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
// 运行cURL，请求网页 
$data = curl_exec($curl); 
// 关闭URL请求 
curl_close($curl); 
// 显示获得的数据 
print_r($data); 
?>