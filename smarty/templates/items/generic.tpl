{extends file='display.tpl'}

{block name=name}{$item->name}{/block}

{block name=tooltip}
    <div id="tooltip-image-holder">
        <img src="{$item->icon_url('m')}" alt="{if $item->gender_icons}Male {/if}icon for {$item->name|escape}">
        {if $item->gender_icons}
        <br><img src="{$item->icon_url('f')}" alt="Female icon for {$item->name|escape}">
        {/if}
    </div>
    {$item->render_tooltip()}
{/block}

{block name='details'}
    {block name=item_details}
    {if $farmed_from}
    <div id="map-tabs">
        <ul>
            {foreach from=$farmed_from item=map key=id}
            <li><a href="#map-{$id}">{$map->get_name()} ({$map->point_count()})</a></li>
            {/foreach}
        </ul>
        {foreach from=$farmed_from item=map key=id}
        <div id="map-{$id}" class="squashed">
            {$map->render()}
            <h3>Coordinate list</h3>
            <p>{foreach from=$map->points() item=point}<span class='map-coord x-{$point.x|round} y-{$point.y|round} z-{$point.z|round}'>{$point.x|round} {$point.y|round} ({$point.z|round})</span>; {/foreach}</p>
        </div>
        {/foreach}
    </div>
    {/if}
    {/block}
{/block}

{block name='sidebar'}
    <div class="group">
        <h3>Facts</h3>
        <ul>
        {if $item->buy_price}
            <li>Buy price: {$item->buy_price|number_format}</li>
        {/if}
        {if $item->stack_count}
        {if $item->stack_count == 1}
            <li>Doesn't stack</li>
        {else}
            <li>Stacks in piles of {$item->stack_count|number_format}</li>
        {/if}
        {/if}
        {if $item->decompose_to}
            <li>Decomposes to{if $item->decompose_amount>1} {$item->decompose_amount}{/if}{$item->decompose_to->link()}{if $item->decompose_price} for {$item->decompose_price|number_format} coins{/if}</li>
        {/if}
        {* This should really be moved elsewhere *}
        {if $item->pet}
            <li>Hatches to{$item->pet->link()}</li>
        {/if}
        {block name='item_sidebar_list'}{/block}
        </ul>
    </div>
    {block name='item_more_sidebars'}{/block}
{/block}

{block name=more}
    {if $created_by || $used_for || $used_to_reforge || $dropped_from || $decomposed_from || $same_model || $same_icon || $children || $parents || $sold_by}
    <h3>More Information</h3>
    <div id="more-tabs">
        <ul>
            {if $children || $parents}<li><a href="#more-item-tree">Item tree</a></li>{/if}
            {if $sold_by}<li><a href="#more-sold-by">Sold by ({$sold_by|count|number_format})</a></li>{/if}
            {if $dropped_from}<li><a href="#more-dropped">Drops from ({$dropped_from|count|number_format})</a></li>{/if}
            {if $created_by}<li><a href="#more-created">Created with ({$created_by|count|number_format})</a></li>{/if}
            {if $used_for}<li><a href="#more-used">Can create ({$used_for|count|number_format})</a></li>{/if}
            {if $used_to_reforge}<li><a href="#more-reforges">Reforges ({$used_to_reforge|count|number_format})</a></li>{/if}
            {if $decomposed_from}<li><a href="#more-decompose">Decomposed from ({$decomposed_from|count|number_format})</a></li>{/if}
            {if $same_model}<li><a href="#more-same-model">Same model ({$same_model|count|number_format})</a></li>{/if}
            {if $same_icon}<li><a href="#more-same-icon">Same icon ({$same_icon|count|number_format})</a></li>{/if}
        </ul>
        {if $children || $parents}
        <div id="more-item-tree">
            {function name=render_parent_tree_start node=0}
                {if count($node) > 1}
                    <ul class='weapon-tree'>
                    <li><em>(Lots of stuff)</em></li>
                {else}
                {foreach $node as $parent}
                    {if $parent->parents}{call render_parent_tree_start node=$parent->parents}{/if}
                    <ul class='weapon-tree'>
                    <li><img src="{$parent->icon_url()}" alt="Icon for {$parent->name}"> {$parent->link()} (grade {$parent->grade}; level {$parent->level})
                    {/foreach}
                {/if}
            {/function}
            {function name=render_parent_tree_end node=0}
                {foreach $node as $parent}
                {if $parent->parents}{if count($node) > 1}{call render_parent_tree_end node=$parent->parents}{/if}{/if}
                </li></ul>
                {/foreach}
            {/function}
            {function name=render_item_tree node=0}
                <ul class='weapon-tree'>
                    {foreach $node as $child}
                    <li><img src="{$child->icon_url()}" alt="Icon for {$child->name}"> {$child->link()} (grade {$child->grade}; level {$child->level}){if $child->children}{call render_item_tree node=$child->children}{/if}</li>
                    {/foreach}
                </ul>
            {/function}
            {call render_parent_tree_start node=$parents}
            <ul class='weapon-tree root'>
                <li><img src="{$item->icon_url()}" alt="Icon for {$item->name}"> {$item->link()} <em>(You are here)</em>{call render_item_tree node=$children}</li>
            </ul>
            {call render_parent_tree_end node=$parents}
        </div>
        {/if}
        {if $sold_by}
        <div id="more-sold-by">
            <table class='recipe-table'>
                <thead>
                    <tr><th>NPC</th><th>Price</th><th>Location</th></tr>
                </thead>
                <tbody>
                    {foreach from=$sold_by item=sale}
                    <tr>
                        <td>
                            {$sale.npc->link()}
                        </td>
                        <td>
                            {$item->buy_price|number_format} coins
                            {if $sale.contribution}
                            and {$sale.contribution|number_format} contribution
                            {/if}
                        </td>
                        <td>
                            <ul>
                            {foreach from=$sale.npc->location_summary() item=location}
                                <li>{$location}</li>
                            {/foreach}
                            </ul>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/if}
        {if $dropped_from}
        <div id="more-dropped">
            <table class='recipe-table'>
                <thead>
                    <tr><th>Mob</th><th>Level</th><th>Location</th><th>Nominal Drop Rate</th><th>Actual Drop Rate</th></tr>
                </thead>
                <tbody>
                    {foreach from=$dropped_from item=drop}
                    <tr>
                        <td>
                            {$drop.mob->link()}
                        </td>
                        <td>
                            <span class="row-mob-level">{$drop.mob->get_level()}</span>
                        </td>
                        <td>
                            <ul>
                            {foreach from=$drop.mob->location_summary() item=location}
                                <li>{$location}</li>
                            {/foreach}
                            </ul>
                        </td>
                        <td>
                            {($drop.rate * 100)|number_format:2}%
                        </td>
                        <td>
                            <span class="level-adjust-drops">{($drop.mob->real_drop_rate($drop.rate) * 100)|number_format:2}</span>%
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/if}
        {if $created_by}
        <div id="more-created">
            <table class='recipe-table'>
                <thead>
                    <tr><th>Ingredients</th><th>Output</th><th>NPC</th></tr>
                </thead>
                <tbody>
                    {foreach from=$created_by item=recipe}
                    {if $recipe->inputs && $recipe->outputs}
                    <tr>
                        <td>
                            <ul>
                            {foreach from=$recipe->inputs item=input}
                                <li>{$input.quantity}{$input.item->link()}</li>
                            {/foreach}
                            {if $recipe->price}
                                <li>{$recipe->price|number_format} coins</li>
                            {/if}
                            </ul>
                        </td>
                        <td>
                            <ul>
                            {foreach from=$recipe->outputs item=output}
                                <li>{if $recipe->quantity != 1}{$recipe->quantity} x {/if}{$output.item->link()}{if $output.probability != 1}({($output.probability*100)|number_format}%){/if}</li>
                            {/foreach}
                            </ul>
                        </td>
                        <td class='npc-list'>
                            <ul>
                            {$i=0}
                            {foreach from=$recipe->get_craft_npcs() item=npc}
                                <li>{$npc->link()}{if $npc->territory}(must own territory){/if}</li>
                                {$i = $i+1}
                                {if $i > 5}
                            </ul>
                            <ul class='more-list' style="display: none;">
                                {/if}
                            {/foreach}
                            </ul>
                            {if $i > 6}
                            <p class='expand-list'>More…</p>
                            {/if}
                        </td>
                    </tr>
                    {/if}
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/if}
        {if $used_for}
        <div id="more-used">
            <table class='recipe-table'>
                <thead>
                    <tr><th>Item</th><th>Requires</th><th>NPC</th></tr>
                </thead>
                <tbody>
                    {foreach from=$used_for item=recipe}
                    {if $recipe->inputs && $recipe->outputs}
                    <tr>
                        <td>
                            <ul>
                            {foreach from=$recipe->outputs item=output}
                                <li>{if $recipe->quantity != 1}{$recipe->quantity} x {/if}{$output.item->link()}{if $output.probability != 1}({($output.probability*100)|number_format}%){/if}</li>
                            {/foreach}
                            </ul>
                        </td>
                        <td>
                            <ul>
                            {foreach from=$recipe->inputs item=input}
                                <li>{$input.quantity}{$input.item->link()}</li>
                            {/foreach}
                            {if $recipe->price}
                                <li>{$recipe->price|number_format} coins</li>
                            {/if}
                            </ul>
                        </td>
                        <td class='npc-list'>
                            <ul>
                            {$i=0}
                            {foreach from=$recipe->get_craft_npcs() item=npc}
                                <li>{$npc->link()}{if $npc->territory}(must own territory){/if}</li>
                                {$i = $i+1}
                                {if $i == 5}
                            </ul>
                            <ul class='more-list' style="display: none;">
                                {/if}
                            {/foreach}
                            </ul>
                            {if $i > 6}
                            <p class='expand-list'>More…</p>
                            {/if}
                        </td>
                    </tr>
                    {/if}
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/if}
        {if $used_to_reforge}
        <div id="more-reforges">
            <table class='recipe-table'>
                <thead>
                    <tr><th>Reforges Item</th><th>Reforge Requirements</th></tr>
                </thead>
                <tbody>
                    {foreach from=$used_to_reforge item=recipe}
                    {if $recipe->inputs && $recipe->outputs}
                    <tr>
                        <td>
                            <ul>
                            {foreach from=$recipe->outputs item=output}
                                <li>{if $recipe->quantity != 1}{$recipe->quantity} x {/if}{$output.item->link()}{if $output.probability != 1}({($output.probability*100)|number_format}%){/if}</li>
                            {/foreach}
                            </ul>
                        </td>
                        <td>
                            <ul>
                            {foreach from=$recipe->inputs item=input}
                                <li>{$input.quantity}{$input.item->link()}</li>
                            {/foreach}
                            </ul>
                        </td>
                    </tr>
                    {/if}
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/if}
        {if $decomposed_from}
        <div id="more-decompose">
            <table class='recipe-table'>
                <thead>
                    <tr><th>Decomposed from</th><th>Quantity produced</th></tr>
                </thead>
                <tbody>
                    {foreach from=$decomposed_from item=from_item}
                    {if $from_item->name}
                    <tr>
                        <td>
                            {$from_item->link()}{if $from_item->decompose_price}and {$from_item->decompose_price|number_format} coins{/if}
                        </td>
                        <td>
                            {$from_item->decompose_amount}{$item->link()}
                        </td>
                    </tr>
                    {/if}
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/if}
        {if $same_model}
        <div id="more-same-model">
            <table class='recipe-table'>
                <thead>
                    <tr><th>Item</th><th>Model</th></tr>
                </thead>
                <tbody>
                    {foreach from=$same_model item=identical}
                    <tr>
                        <td>
                            {$identical.item->link()}
                        </td>
                        <td>
                            <code lang="zh">{$identical.model|@implode:"<br>"}</code><br /><code><span class="translation">{$identical.translated|@implode:"<br>"}</span></code>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/if}
        {if $same_icon}
        <div id="more-same-icon">
            <table class='recipe-table'>
                <thead>
                    <tr><th>Item</th><th colspan=2>Icon</th></tr>
                </thead>
                <tbody>
                    {foreach from=$same_icon item=identical}
                    <tr>
                        <td style="width: 300px;">
                            {$identical.item->link()}
                        </td>
                        <td style="width: 70px;">
                            <img src="{$identical.item->icon_url('m')}" alt="identical icon">
                            {if $identical.item->gender_icons}
                            <img src="{$identical.item->icon_url('f')}" alt="identical icon">
                            {/if}
                        </td>
                        <td>
                            <code lang="zh">{$identical.item->icon}</code><br /><code><span class="translation">{$identical.translated}</span></code>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/if}
    </div>
    {/if}
{/block}
