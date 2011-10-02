{extends file='main.tpl'}
{block name=title}pwdb – search "{$search|escape}"{/block}
{block name=default_search}{$search|escape}{/block}
{block name=head}<script type="text/javascript" src="/scripts/search.js"></script>{/block}

{block name=content}
<h2>Search results</h2>
{if $result_count == 0}
<p>No results found :(</p>
{else}
<div id="search-tabs">
    <ul>
        {if $weapons}<li><a href="#tab-weapons">Weapons ({$weapons|count|number_format})</a></li>{/if}
        {if $armour}<li><a href="#tab-armour">Armour ({$armour|count|number_format})</a></li>{/if}
        {if $ornaments}<li><a href="#tab-ornaments">Ornaments ({$ornaments|count|number_format})</a></li>{/if}
        {if $tomes}<li><a href="#tab-tomes">Tomes ({$tomes|count|number_format})</a></li>{/if}
        {if $items}<li><a href="#tab-items">Items ({$items|count|number_format})</a></li>{/if}
        {if $mobs}<li><a href="#tab-mobs">Mobs ({$mobs|count|number_format})</a></li>{/if}
    </ul>
    {if $weapons}
    <div id="tab-weapons">
        <table class="recipe-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Grade</th>
                    <th>Required Level</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$weapons item=item}
                <tr>
                    <td>{$item->link()}</td>
                    <td>{$item->grade}</td>
                    <td>{$item->level}</td>
                    <td>{$item->subtype}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
    {if $armour}
    <div id="tab-armour">
        <table class="recipe-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Grade</th>
                    <th>Required Level</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$armour item=item}
                <tr>
                    <td>{$item->link()}</td>
                    <td>{$item->grade}</td>
                    <td>{$item->level}</td>
                    <td>{$item->subtype}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
    {if $ornaments}
    <div id="tab-ornaments">
        <table class="recipe-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Grade</th>
                    <th>Required Level</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$ornaments item=item}
                <tr>
                    <td>{$item->link()}</td>
                    <td>{$item->grade}</td>
                    <td>{$item->level}</td>
                    <td>{$item->subtype}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
    {if $tomes}
    <div id="tab-tomes">
        <table class="recipe-table">
            <thead>
                <tr>
                    <th>Item</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$tomes item=item}
                <tr>
                    <td>{$item->link()}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
    {if $items}
    <div id="tab-items">
        <table class="recipe-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Grade</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$items item=item}
                <tr>
                    <td>{$item->link()}</td>
                    <td>{$item->grade}</td>
                    <td>{$item->type}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
    {if $mobs}
    <div id="tab-mobs">
        <table class="recipe-table">
            <thead>
                <tr>
                    <th>Mob</th>
                    <th>Level</th>
                    <th>HP</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$mobs item=mob}
                <tr>
                    <td>{$mob->link()}</td>
                    <td>{$mob->get_level()}</td>
                    <td>{$mob->hp|number_format}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
</div>
{/if}
{/block}
