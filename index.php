<?php
session_start();

$SecurityKey = "Dvf054kpsdkjv05mN50z055454054K29";
$YOUR_SITE_URL = urlencode("https://www.betcrane.com/");

$Balance = $_SESSION['balance'] ?? 100.11;
$Userid = isset($_SESSION['Userid']) ? $_SESSION['Userid'] : 2;
$Currency = "UGX";
$SysId = 66;
$ApiKey = MD5("$Balance:$UserId:$SecurityKey:$Currency:$SysId");

$BASE_URL = "https://fast-g.info/srvlotoSys.ashx?";

$IframeUrl = $BASE_URL ."HallId=1&UserId=$UserId&Balance=$Balance";
$IframeUrl .= "&SysId=$SysId&currency=$Currency&lang=EN";
$IframeUrl .= "&KeySess=$UserId&ApiKey=$ApiKey&";
$IframeUrl .= "newsite=1&game=3&backurl=$YOUR_SITE_URL&locklang=1";

$iframe = '<iframe src="' . $IframeUrl . '" style="height:400px;width:100%;border:none" title="Betting"></iframe>';

echo "<div style='margin: auto; width:50%'>$iframe</div>";
