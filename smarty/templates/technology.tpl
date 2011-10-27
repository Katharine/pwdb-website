{extends file='main.tpl'}

{block name=content}
<h2>Technology</h2>
<p><em>pwdb</em> depends on the following open-source projects:</p>
<ul>
    <li><a href="http://php.net/"><strong>PHP</strong></a>, for the website logic. (But it's honestly a terrible language. Avoid it.)</li>
    <li><a href="http://smarty.net/"><strong>Smarty</strong></a>, the templating engine used to render all these pages. Also it has a handy caching system.</li>
    <li><a href="http://mysql.com/"><strong>MySQL</strong></a>, the primary data store.</li>
    <li><a href="http://mongodb.com/"><strong>MongoDB</strong></a>, for some data that doesn't fit into the relational data model very well; especially items and quests.</li>
    <li><a href="http://memcached.org/"><strong>Memcached</strong></a>, to keep things speedy.</li>
    <li><a href="http://python.org/"><strong>Python</strong></a>, used in my code to interpret Perfect World's data files.</li>
    <li><a href="http://jquery.com/"><strong>jQuery</strong></a> and <a href="http://jqueryui.com/"><strong>jQueryUI</strong></a> for browser interactivity.</li>
    <li><a href="http://git-scm.com/"><strong>git</strong></a>, for managing my sanity and letting me periodically insult my computer (I'm British).</li>
</ul>
<p>Additionally, although their work is not used directly, special thanks go to:</p>
<ul>
    <li><a href="http://pwtools.codeplex.com/">Ronny's <strong>pwTools</strong></a>, without the source to which PW would have been far more impenetrable.</li>
    <li><a href="http://regenesis.tw/">Dreamweaver's <strong>Regenesis</strong></a>, for their ideas and support, and for making PWI worth playing in the first place. <img src="/images/sprout" alt='Regenesis sprout' style='width: 16px; height: 16px; margin-bottom: -3px;'></li>
    <li><a href="http://ecatomb.net/pwi/"><strong>ecatomb</strong></a>, <a href="http://pwi-wiki.perfectworld.com">The <strong>PWI Wiki</strong></a> and <a href="http://pwdatabase.com/pwi/"><strong>pwdatabase</strong></a> for providing information and resources to check against.</li>
    <li><a href="http://pwi.perfectworld.com/"><strong>Perfect World</strong></a>, <!-- for wasting everyone's time and money, and --> for stuffing everything in the client and making this possible.</li>
</ul>
<p>In the same spirit that pwTools are available, I will publish the source for my data parsing libraries eventually; currently they are not in a state in which I can do so.</p>
{/block}
