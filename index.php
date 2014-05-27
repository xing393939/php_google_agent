<?php
error_reporting(0);
ini_set('zlib.output_compression', 'On');
ob_start();
header("Content-Encoding: gzip");
header("Vary: Accept-Encoding");
header("Expires: " . gmdate("r", time() + 28800 + 86400));
header("Cache-Control: max-age=86400,must-revalidate");

$q = isset($_GET['q']) ? urlencode($_GET['q']) : '';
$qv = urldecode($q);
$start = isset($_GET['start']) ? $_GET['start'] : 0;
$search = $resultStats = '';
if ($q) {
    $url = 'https://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=8&q=';
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url . $q . '&start=' . $start);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en; rv:1.9.2) Gecko/20100115 Firefox/3.6 GTBDFff GTB7.0');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $str = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($str, true);
    $search = '';
    foreach ($json['responseData']['results'] as $item) {
        $search .= "<div><a target='_blank' href='{$item['unescapedUrl']}'>{$item['title']}</a>{$item['url']}<br />{$item['content']}|{$item['titleNoFormatting']}<br />&nbsp;</div>";
    }
    $resultStats = $json['responseData']['cursor']['resultCount'] . ' results';
}

?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo $qv; ?> Google搜索</title>
        <style type="text/css">
            body {
                color: #545454;
                font-size: 13px;
            }
            #logo {
                position: relative;
            }
            #resultStats {
                position: absolute;
                left: 200px;
                top: 20px;
            }
            #pages {
                padding: 30px 0 100px 50px;
            }

            #pages a {
                margin-right: 20px;
                height: 20px;
            }
        </style>
    </head>
    <body>
    <div style="margin:18px 0 0 20px">
        <div id="logo">
            <a href="./"><img src="logo.gif" style="width:150px; height:55px; border:0;"/></a>
            <div id="resultStats" style="left:0px;"><?php echo $resultStats; ?></div>
        </div>
        <div style="margin:8px 0 12px 0;">
            <form method="get" action="index.php">
                <input type="text" name="q" style="height:32px; width:400px; line-height:30px"
                       value="<?php echo $qv; ?>"/>&nbsp;<input type="submit" style="height:32px;" value=" Google搜索 "/>
            </form>
        </div>
        <div id="search"><?php echo $search; ?></div>
        <div id="pages">
            <?php
            if ($q && $resultStats) {
                for ($i = 1; $i <= 10; $i++) {
                    $num = ($i - 1) * 10;
                    echo "<a href=\"index.php?q=$q&start=$num\">$i</a>";
                }
                $next = $start + 10;
                echo "<a href=\"index.php?q=$q&start=$next\">下一页</a>";
            }
            ?>
        </div>
    </div>
    </body>
    </html>
<?php
ob_end_flush();
?>