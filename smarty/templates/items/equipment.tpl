{extends file='items/generic.tpl'}

{block name='item_details'}
    <div class="item-random-stats">
    {if $item->craft_sockets || $item->drop_sockets}
        <h3>Sockets</h3>
        <!-- This table is actually being used for tabular data! Gasp! -->
        <!-- The <div>s in each <td> are wrappers because Firefox does not accept the
             relative positioning of a <td>, apparently.  -->
        <table class="chart-table" id="item-socket-table">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    {section name='i' start=0 loop=count($item->craft_sockets)}
                    <td>{$smarty.section.i.index}</td>
                    {/section}
                </tr>
            </thead>
            <tbody>
            {if $item->drop_sockets != $item->craft_sockets}
                <tr>
                    <th style="vertical-align: bottom;">Craft</th>
                    {foreach from=$item->craft_sockets item=p}
                    <td><div><div class="chart" style="height: {($p * 15.0)|ceil}px;">&nbsp;</div>
                    <div class="text">{($p * 100)|number_format:0}%</div></div></td>
                    {/foreach}
                </tr>
                <tr>
                    <th>Drop</th>
                    {foreach from=$item->drop_sockets item=p}
                    <td><div><div class="chart" style="height: {($p * 15.0)|ceil}px;">&nbsp;</div>
                    <div class="text">{($p * 100)|number_format:0}%</div></div></td>
                    {/foreach}
                </tr>
            {else}
                <tr>
                    <th>Sockets</th>
                    {foreach from=$item->craft_sockets item=p}
                    <td><div><div class="chart" style="height: {($p * 15.0)|ceil}px;">&nbsp;</div>
                    <div class="text">{($p * 100)|number_format:0}%</div></div></td>
                    {/foreach}
                </tr>
            {/if}
            {if $item->socket_stones(1) !== null}
                <tr>
                    <th>Stones</th>
                    {foreach from=$item->craft_sockets item=p key=i}
                    <td>{$item->socket_stones($i)|number_format}</td>
                    {/foreach}
                </tr>
            {/if}
            </tbody>
        </table>
    {/if}
    {if !$item->nonrandom_addons}
        <h3>Additional attributes</h3>
        <div id="addon-tabs">
            <ul>
                <li><a href="#addon-table-drop">{if $item->craft_sockets == $item->drop_sockets}General{else}Dropped{/if}</a></li>
                {if $item->craft_sockets != $item->drop_sockets}<li><a href="#addon-table-craft">Crafted</a></li>{/if}
                {if $item->unique_addon_probability != 0}<li><a href="#addon-table-unique">Unique</a></li>{/if}
            </ul>
            <div id="addon-table-drop">
                <table>
                {foreach from=$item->get_addons('drop') item=group key=num}
                    <tr class='item-addon-header' group='{$num}'><th>{$group.title}</th>
                    <th>{$group.addons.0->render_range($group.min, $group.max)}</th>
                    <th>{($group.probability*100)|number_format:2}%</th></tr>
                    {foreach from=$group.addons item=addon}
                        <tr class='item-addon-row group-{$num}'>
                            <td>&nbsp;</td>
                            <td>{$addon->render_range()}</td>
                            <td>{($addon->probability*100)|number_format:2}%</td>
                        </tr>
                    {/foreach}
                {/foreach}
                </table>
            </div>
            {if $item->craft_sockets != $item->drop_sockets}
            <div id="addon-table-craft">
                <table>
                {foreach from=$item->get_addons('craft') item=group key=num}
                    <tr class='item-addon-header' group='{$num}'><th>{$group.title}</th>
                    <th>{$group.addons.0->render_range($group.min, $group.max)}</th>
                    <th>{($group.probability*100)|number_format:2}%</th></tr>
                    {foreach from=$group.addons item=addon}
                        <tr class='item-addon-row group-{$num}'>
                            <td>&nbsp;</td>
                            <td>{$addon->render_range()}</td>
                            <td>{($addon->probability*100)|number_format:2}%</td>
                        </tr>
                    {/foreach}
                {/foreach}
                </table>
            </div>
            {/if}
            {if $item->unique_addon_probability != 0}
            <div id="addon-table-unique">
                <table>
                {foreach from=$item->get_addons('unique') item=group key=num}
                {if $num == 55}
                    {foreach from=$group.addons item=addon}
                    <tr class='item-addon-header group-55'><td colspan="2">{$addon->label()|escape}</td><td>{($addon->probability*100)|number_format:2}%</td></tr>
                    {/foreach}
                {else}
                    <tr class='item-addon-header' group='{$num}'><th>{$group.title}</th>
                    <th>{$group.addons.0->render_range($group.min, $group.max)}</th>
                    <th>{($group.probability*100)|number_format:2}%</th></tr>
                    {foreach from=$group.addons item=addon}
                        <tr class='item-addon-row group-{$num}'>
                            <td>&nbsp;</td>
                            <td>{$addon->render_range()}</td>
                            <td>{($addon->probability*100)|number_format:2}%</td>
                        </tr>
                    {/foreach}
                {/if}
                {/foreach}
                </table>
            </div>
            {/if}
        </div>
    {/if}
    </div>
{/block}

{block name='item_sidebar_list'}
    {if $item->repair_price}
        <li>Repair price: {$item->repair_price|number_format}</li>
    {/if}
    {if $item->nonrandom_addons}
        <li>All addon attributes are always present.</li>
    {/if}
    {if $reforged_using}
        <li>Reforged with {$reforged_using.quantity}{$reforged_using.item->link()}</li>
    {/if}
{/block}

{block name='item_more_sidebars'}
    {if $item->refine_bonus}
    <div class="group">
        <h3>Refine: {$item->refine_bonus->render_title()}</h3>
        <table class="refine-table">
            <tr><td>&nbsp;</td>
            {$titles=$item->refine_change(0)}
            {foreach $titles.totals key=heading item=meh}
                <th>{$heading}</th>
            {/foreach}
            <td>&nbsp;</td>
            </tr>
            {section name=i start=1 loop=13}
            {$refine=$item->refine_change($smarty.section.i.index)}
            <tr><td class="refine-amount">+{$smarty.section.i.index}:</td>
            {foreach from=$refine.totals item=total}
            {if is_array($total)}
                <td>{$total.0|number_format}–{$total.1|number_format}</td>
            {else}
                <td>{$total|number_format}</td>
            {/if}
            {/foreach}
            <td class="refine-delta">(+{$refine.delta|number_format})</td>
            {/section}
        </table>
    </div>
    {/if}
{/block}
