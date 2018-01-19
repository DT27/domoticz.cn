<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
if (isset($_GET['citycode'])) {
    $citycode = $_GET['citycode'];
} else {
    exit("仅限Domoticz调用！");
}

//七日天气
// 初始化一个 cURL 对象
$curl = curl_init();
// 设置你需要抓取的URL https://api.darksky.net/forecast/'+_APIKEY_DARKSKY+'/'+latitude+','+longitude+'?lang='+_LANGUAGE.substr(0,2)+'&units=auto
curl_setopt($curl, CURLOPT_URL, 'http://www.weather.com.cn/weather/' . $citycode . '.shtml');
// 设置header 响应头是否输出
curl_setopt($curl, CURLOPT_HEADER, 0);
// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
// 1如果成功只将结果返回，不自动输出任何内容。如果失败返回FALSE 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// 运行cURL，请求网页 
$html = curl_exec($curl);
// 关闭URL请求 
curl_close($curl);
// 显示获得的数据 
//print_r($html);

//当前天气
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://d1.weather.com.cn/sk_2d/' . $citycode . '.html');
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:45.0) Gecko/20100101 Firefox/45.0');
curl_setopt($curl, CURLOPT_REFERER, 'http://www.weather.com.cn/');
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$html_today = curl_exec($curl);
curl_close($curl);

//正则匹配数据
$result_city = preg_match_all('/<title>【(.*?)天气】/', $html, $city);
$result_day = preg_match_all('/<h1>(\d+)日（(.*?)）<\/h1>/', $html, $day);
$result_weathers = preg_match_all('/title="(.*?)" class="wea">/', $html, $weathers);
$result_temps = preg_match_all('/<p class="tem">\n(?:\<span\>(-?\d+).*\<\/span\>\/)?<i>(-?\d+)/', $html, $temps);
$result_icons = preg_match_all('/png40 d(\d+)/', $html, $icons);

$result_temp = preg_match_all('/temp":"(-?\d+)"/', $html_today, $temp);
$result_tempf = preg_match_all('/tempf":"(-?\d+)"/', $html_today, $tempf);
$result_weather = preg_match_all('/"weather":"(.*?)"/', $html_today, $weather);
$result_icon = preg_match_all('/"weathere":"(\w+)"/', $html_today, $icon);


//处理数据
$city = $city[1];
$day = $day[1];
date_default_timezone_set("Asia/Shanghai");
$day = preg_replace('/(\d+)/', date('Y-m-$1 00:00:00'), $day);

$weathers = $weathers[1];
$icons = $icons[1];
$temps_max = $temps[1];
if (count($temps_max) == 6)
    array_unshift($temps_max, "0");
$temps_max[0]="0";
$temps_min = $temps[2];


$temp = $temp[1];
$tempf = $tempf[1];
$weather = $weather[1];
$icon = $icon[1];

$currently = array(
    "time"=>date('Y-m-d H:i:s'),
    "city"=>$city,
    "summary"=>$weather,
    "icon"=>$icon,
    "temperature"=>$temp,
    "temperaturef"=>$tempf
);

$daily=array();

//从第二天的预报开始获取
for ($i = 1; $i<6; $i++){
    //为图标转换天气代码
    if (strpos($weathers[$i],"霾")!==false||strpos($weathers[$i],"雾")!==false){
        $icon_d = "fog";
    }elseif (strpos($weathers[$i],"雨夹雪")!==false){
        $icon_d = "sleet";
    }elseif (strpos($weathers[$i],"雪")!==false){
        $icon_d = "snow";
    }elseif (strpos($weathers[$i],"雨")!==false){
        $icon_d = "rain";
    }elseif (strpos($weathers[$i],"多云")!==false||strpos($weathers[$i],"阴")!==false){
        $icon_d = "cloudy";
    }elseif (strpos($weathers[$i],"晴")!==false){
        $icon_d = "sunny";
    }else{
        $icon_d = "clear-day";
    }
    $date = new DateTime($day[$i]);
    $day[$i] = $date->format('Y-m-d H:i:s');
    $daily[]=array(
        "time"=>$day[$i],
        "summary"=>$weathers[$i],
        "icon"=>$icon_d,
        "temperatureMax"=>$temps_max[$i],
        "temperatureMin"=>$temps_min[$i]
    );
}

$output = array("currently"=>$currently,"daily"=>$daily);

//转为json并输出
echo json_encode($output);
