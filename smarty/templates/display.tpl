{extends file='main.tpl'}

{block name='title'}pwdb – {block name='name'}(name block){/block}{/block}

{block name='head'}
<script type="text/javascript" src="/scripts/item.js"></script>
<script type="text/javascript" src="/scripts/lib/superfish.js"></script>
<link rel="stylesheet" type="text/css" href="/css/superfish.css">
{/block}

{block name=content}
    <!-- <ul class="sf-menu">
        <li><a href="#">Items</a>
            <ul>
                <li>Weapons</li>
                <li>Armour</li>
                <li>Ornaments</li>
                <li>Apothecary</li>
                <li>Materials</li>
            </ul>
        </li>
    </ul> -->
    <!-- Have I mentioned how much I hate CSS layout? I want to do this with CSS, but can't
        work out how. So, tables! If you know, please tell me. -->
    <table id="item-root-layout-table" class="layout">
        <tr>
            <td>
                <h2>{block name=name}(name block){/block}</h2>
                {block name=main_content}
                <table class="layout">
                    <tr>
                        <td id="tooltip-wrapper-cell">
                            <div class="tooltip-wrapper">
                                {block name=tooltip}(tooltip block){/block}
                            </div>
                        </td>
                        <td>
                            {block name=details}(detail block){/block}
                        </td>
                    </tr>
                </table>
                {/block}
            </td>
            <td id="item-sidebar">
                {block name=sidebar}<div>(sidebar block)</div>{/block}
            </td>
        </tr>
    </table>
    {block name=more}<h3>(more block)</h3>{/block}
{/block}
