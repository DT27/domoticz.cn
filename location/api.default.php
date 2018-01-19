<?php
/**
 * User: DT27
 * Date: 2017/2/25
 * Time: 17:08
 */
$ip = $_GET["ip"];
if ($ip == 1) {
    $cip = $_SERVER["REMOTE_ADDR"];
    // 初始化一个 cURL 对象
    $curl = curl_init();
    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, 'https://api.map.baidu.com/location/ip?ip='.$cip.'&ak=xxxxxx&coor=bd09ll');
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
    $result = json_decode($data);
    echo "IP定位：您的IP【".$cip."】<br>经度(Longitude)：【" . $result->content->point->x . "】<br>纬度(Latitude)：【" . $result->content->point->y.'】';
}