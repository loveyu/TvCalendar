<?php require_once __DIR__."/common.php" ?>
<!doctype html>
<html lang="zh-CN" style="background: radial-gradient(ellipse at center, rgba(81,148,193,1) 1%,rgba(13,48,112,1) 99%);">
<head>
	<meta charset="UTF-8">
	<title>美剧日历 <?php echo $year."年".get_month($month)."月" ?></title>
	<link rel="stylesheet" href="style/style.css"/>
	<link rel="shortcut icon" href="favicon.ico"/>
</head>
<body>
<div class="month_name">
	<div class="prev-month"><a href="?<?php echo month_param(-1) ?>"><span></span>
			<strong><?php echo month_show(-1) ?></strong></a></div>
	<h1>美剧日历 <?php echo $year."年".get_month($month)."月" ?>
		<small>(中国时间)<span class="update_time"><?php echo get_update_time($year, $month) ?></span></small>
	</h1>

	<div class="next-month"><a href="?<?php echo month_param(1) ?>"><span></span>
			<strong><?php echo month_show(1) ?></strong></a></div>
</div>
<?php echo get_data($year, $month) ?>
<div class="month_name">
	<div class="prev-month"><a href="?<?php echo month_param(-1) ?>"><span></span>
			<strong><?php echo month_show(-1) ?></strong></a></div>
	<h1>美剧日历 <?php echo $year."年".get_month($month)."月" ?></h1>

	<div class="next-month"><a href="?<?php echo month_param(1) ?>"><span></span>
			<strong><?php echo month_show(1) ?></strong></a></div>
</div>
<div id="copyright_footer">
	<p>数据采集来源于 :
		<a href="http://www.pogdesign.co.uk/cat/" rel="nofollow">Pogdesign</a>，版权为原网站所有，
		<a href="https://www.loveyu.org">Loveyu</a>整理，
		<a rel="nofollow" class="update" href='?y=<?php echo $year ?>&m=<?php echo $month ?>&update=force'
		   title="强制更新当月的数据">更新</a>。
	</p>
</div>
<?php if($month == date("n") && $year == date("Y")): ?>
	<script>
		setTimeout(function () {
			location.href = "<?php echo "#d_".date("j")."_{$month}_{$year}"?>";
		}, 1500)
	</script>
<?php endif; ?>
<!-- Piwik -->
<script type="text/javascript">
	var _paq = _paq || [];
	_paq.push(["setCookieDomain", "*.loveyu.info"]);
	_paq.push(['trackPageView']);
	_paq.push(['enableLinkTracking']);
	(function () {
		var u = "//tj.loveyu.info/";
		_paq.push(['setTrackerUrl', u + 'piwik.php']);
		_paq.push(['setSiteId', 5]);
		var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
		g.type = 'text/javascript';
		g.async = true;
		g.defer = true;
		g.src = u + 'piwik.js';
		s.parentNode.insertBefore(g, s);
	})();
</script>
<noscript><p><img src="//tj.loveyu.info/piwik.php?idsite=5" style="border:0;" alt=""/></p></noscript>
<!-- End Piwik Code -->
</body>
</html>