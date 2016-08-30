<?php
/**
 * User: loveyu
 * Date: 2015/2/6
 * Time: 21:02
 */
/**
 * 修正保存的数据
 * @param $content
 *
 * @return mixed
 */
function fix_data($content)
{
	$content = preg_replace("/<input id=\"[\\d]+\" type=\"checkbox\".*?<\\/label>/", "", $content);
	$content = preg_replace("/ data-episode=\"q\\/[\\d]+\"/", "", $content);
	$content = str_replace("  font-size: ", "font-size:", $content);
	$content = preg_replace("/[ ]{2,}/", " ", $content);
	$content = str_replace("> <", "><", $content);
	$content = explode("\n", $content);
	$new_content = [];
	foreach($content as $line) {
		$line = trim($line);
		if(strlen($line) > 0) {
			$new_content[] = $line;
		}
	}
	$content = implode("\n", $new_content);

	$content = str_replace([
		"<span class=\"sp3\">Monday</span>",
		"<span class=\"sp3\">Tuesday</span>",
		"<span class=\"sp3\">Wednesday</span>",
		"<span class=\"sp3\">Thursday</span>",
		"<span class=\"sp3\">Friday</span>",
		"<span class=\"sp3\">Saturday</span>",
		"<span class=\"sp3\">Sunday</span>",
	], ["<span class=\"sp3\">星期一</span>",
		"<span class=\"sp3\">星期二</span>",
		"<span class=\"sp3\">星期三</span>",
		"<span class=\"sp3\">星期四</span>",
		"<span class=\"sp3\">星期五</span>",
		"<span class=\"sp3\">星期六</span>",
		"<span class=\"sp3\">星期日</span>",], $content);

	$content = str_replace(" href=\"", " href=\"http://www.pogdesign.co.uk/cat/", $content);
	$content = str_replace("http://www.pogdesign.co.uk/cat/./day/9-8-2016", " href=\"http://www.pogdesign.co.uk/cat/", $content);

	return $content;
}


/**
 * 获取当前的月份
 * @param null $m 留空为当前月
 *
 * @return string
 */
function get_month($m = null)
{
	if($m === null) {
		$m = date("j");
	}
	$rt = ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二',];
	if(!isset($rt[$m - 1])) {
		return "未知";
	}

	return $rt[$m - 1];
}

/**
 * 月份相加，返回年份和月份
 * @param $add
 *
 * @return array
 */
function month_add($add)
{
	global $month, $year;
	$n_year = $year;
	$n_year += floor(($month + $add - 1) / 12);
	$n_month = ($month + $add) % 12;
	if($n_month < 1) {
		$n_month += 12;
	}

	return [$n_year, $n_month];
}

/**
 * 显示当前的月
 * @param $p
 *
 * @return string
 */
function month_show($p)
{
	list($year, $month) = month_add($p);

	return "{$year}年".get_month($month)."月";
}

/**
 * 通过一个相加参数返回链接
 * @param $p
 *
 * @return string
 */
function month_param($p)
{
	list($n_year, $n_month) = month_add($p);

	return "y={$n_year}&m={$n_month}";
}

/**
 * 获取当前的日历数据
 * @param $y
 * @param $m
 *
 * @return mixed|string
 */
function get_data($y, $m)
{
	if(is_file("data/{$y}-{$m}.ct") && filemtime("data/{$y}-{$m}.ct") + 18000 > time()) {
		$data = file_get_contents("data/{$y}-{$m}.ct");
		if(!empty($data)) {
			$data = preg_replace("/<!DOCTYPE[\\s\\S]*?<body>/", "", $data);
			$data = str_replace("</body>\n</html>", "", $data);
			return $data;
		} else {
			return "<p class='data-miss'>未找到当前日历数据，或缓存未更新，<a href='?y={$y}&m={$m}&update=force'>点击更新</a>！</p>";
		}
	} else {
		get_source($y, $m);
	}
	return "";
}

/**
 * 获取日历更新的时间
 * @param $y
 * @param $m
 *
 * @return string
 */
function get_update_time($y, $m)
{
	$time = filemtime("data/{$y}-{$m}.ct");
	return "更新于 : ".date("Y/m/d H:i", $time);
}

function parse_title($content)
{
	$translate_map = require __DIR__."/map.php";
	$doc = new DOMDocument();
	$meta = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>";
	@$doc->loadHTML($meta.$content);

	$xpath = new DOMXpath($doc);
	$x_result = $xpath->query("//*/span/p/a[1]");
	$has_new = false;
	foreach($x_result as $item) {
		/**
		 * @var DOMNode $item
		 */
		$name = $item->nodeValue;
		if(isset($translate_map[$name]) && !empty($translate_map[$name])) {
			$chinese = $doc->createElement("span", $translate_map[$name]);
			$chinese->setAttribute("class", "chinese");
			$chinese->setAttribute("style", "display:block");
			$item->appendChild($chinese);
		} else {
			$has_new = true;
			$translate_map[$name] = "";
		}
	}
	if($has_new) {
		save_cfg_map($translate_map);
	}
	return $doc->saveHTML($xpath->document);
}

/**
 * 保存配置信息
 * @param array $cfg
 * @return void
 */
function save_cfg_map($cfg)
{
	ksort($cfg);//以键名排序，便于对GIT的更新
	$cfg_str = var_export($cfg, true);
	$map_file = <<<PHP
<?php
/**
 * 美剧中英文对照表
 */
return {$cfg_str};
PHP;

	file_put_contents(__DIR__."/map.php", $map_file);
}

/**
 * 通过源获取中国的数据
 * @param $y
 * @param $m
 */
function get_source($y, $m)
{
	header("Location: ?y={$y}&m={$m}");
	$opts = array('http' => array('method' => "GET",
		'header' => "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n".
			"User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36\r\n".
			"Accept-Language: zh-CN,zh;q=0.8\r\n".
			"Cookie: CAT_UID=a999bc8431eaca2f0bb6f65aa1927f34\r\n"));
	$context = stream_context_create($opts);
	if((int)date("Y") == (int)$y && (int)date("m") == (int)$m) {
		$data = file_get_contents("http://www.pogdesign.co.uk/cat/", false, $context);
	} else {
		$data = file_get_contents("http://www.pogdesign.co.uk/cat/{$m}-{$y}", false, $context);
	}
	$doc = new DOMDocument();
	@$doc->loadHTML($data);

	$xpath = new DOMXpath($doc);
	$elements = $xpath->query("//*[@id=\"month_box\"]");
	$htmlString = $doc->saveHTML($elements->item(0));
	file_put_contents("data/{$y}-{$m}.ct", parse_title(fix_data($htmlString)));
	exit;
}

