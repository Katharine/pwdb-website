{extends file='layout.tpl'}
{block name=head}
    <link rel="stylesheet" type="text/css" href="/css/index.css">
    <script type="text/javascript" src="/scripts/index.js"></script>
{/block}
{block name=body}
    <div id="index_container">
        <h1>pwdb</h1>
        <form action="/search" method="GET" id="index_search_form">
            <p><input id="search_box" name="q" placeholder="Type to search…"></p>
            <input type="submit">
        </form>
    </div>
{/block}

{* Hide the footer line (also avoids connecting to mysql) *}
{block name=generationstats}{/block}
