<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{block name=title}pwdb{/block}</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js"></script>
    <script type="text/javascript" src="/scripts/lib/qtip.js"></script>
    <script type="text/javascript" src="/scripts/lib/tablesorter.js"></script>
    <script type="text/javascript" src="/scripts/lib/cookie.js"></script>
    <script type="text/javascript" src="/scripts/lib/util.js"></script>
    <script type="text/javascript" src="/scripts/lib/fancybox/fancybox.js"></script>
    <script type="text/javascript" src="/scripts/tooltip.js"></script>
    <script type="text/javascript" src="/scripts/main.js"></script>
    <link type="text/css" href="/css/theme/jquery-ui-1.8.16.custom.css" rel="stylesheet" /> 
    <link rel="stylesheet" href="/css/main.css" type="text/css">
    <link rel="stylesheet" href="/css/tooltip.css" type="text/css">
    <link rel="stylesheet" href="/scripts/lib/fancybox/fancybox.css" type="text/css">
    {block name=head}{/block}
    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-25958311-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
</head>
<body>
{block name=body}{/block}
<div id='footer'>
    <p id='poweredby'>jQuery 1.6.3 / PHP 5.3.2 / MySQL 5.1.41 / Python 2.7.1 / Perfect World 1.4.4 (560) / Kathikins<br />
    {block name=generationstats}MySQL: {MySQL::instance()->query_count()|number_format} queries in {MySQL::instance()->query_time()|number_format:3} seconds. Memcache: {MemoryCache::instance()->hit_count()|number_format}/{MemoryCache::instance()->get_count()|number_format} gets, {MemoryCache::instance()->set_count()|number_format} sets. {$fresh=1}Page {nocache}{if isset($fresh)}freshly{/if}{/nocache} generated {$smarty.now|date_format}.{/block}</p>
</div>
<!-- {MySQL::instance()->log()|print_r:true} -->
<!-- {MemoryCache::instance()->missed_keys()|print_r:true} -->
</body>
</html>
