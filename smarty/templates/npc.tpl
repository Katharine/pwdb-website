{extends file='display.tpl'}

{block name=name}{$npc->name}{/block}

{block name=main_content}
<blockquote class='npc-greeting'>{$npc->introduction|nl2br}</blockquote>
<div id="npc-services">
    {if $spawns}
    <h3><a href="#">Location</a></h3>
    <div>
    <div id="map-tabs">
        <ul>
            {foreach from=$spawns item=map key=id}
            <li><a href="#map-{$id}">{$map->get_name()} ({$map->point_count()})</a></li>
            {/foreach}
        </ul>
        {foreach from=$spawns item=map key=id}
        <div id="map-{$id}" class="squashed">
            {$map->render()}
            <p>{foreach from=$map->points() item=point}<span class='map-coord x-{$point.x|round} y-{$point.y|round} z-{$point.z|round}'>{$point.x|round} {$point.y|round} ({$point.z|round})</span>; {/foreach}</p>
        </div>
        {/foreach}
    </div>
    {/if}
    </div>
    {foreach from=$services item=service}
    {if get_class($service) != 'NPCService'}
    <h3><a href="#">{$service->name()}</a></h3>
    <div>
        <div class='npc-tabs'>
        <ul>
        {$i=1}
        {foreach from=$service->tabs item=items key=tab}
            {if count($items) > 0}
            <li><a href="#service-{$service->id}-tab-{$i}">{$tab}</a></li>
            {/if}
            {$i=$i+1}
        {/foreach}
        </ul>
        {$i=1}
        {foreach from=$service->tabs item=items key=tab}
            {if count($items) > 0}
            <div id="service-{$service->id}-tab-{$i}">
            {* What we do here is a function of what service this is... *}
            {if $service instanceof NPCServiceSell}
            <table class="recipe-table">
                <thead>
                    <tr><th>Item</th><th>Price</th></tr>
                </thead>
                <tbody>
                    {foreach from=$items item=item}
                    <tr>
                        <td>{$item.item->link()}</td>
                        <td>{$item.item->buy_price|number_format} coins{if $item.contribution} and {$item.contribution|number_format} contribution{/if}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            {/if}
            {if $service instanceof NPCServiceCraft}
            <table class="recipe-table">
                <thead>
                    <tr><th>Output</th><th>Requirements</th></tr>
                </thead>
                <tbody>
                    {foreach from=$items item=recipe}
                    <tr>
                        <td>
                            <ul>
                            {foreach from=$recipe->outputs item=output}
                                <li>{$output.item->link()}{if $output.probability != 1} ({($output.probability * 100)|number_format}){/if}</li>
                            {/foreach}
                            </ul>
                        </td>
                        <td>
                            <ul>
                            {foreach from=$recipe->inputs item=input}
                                <li>{if $input.quantity != 1}{$input.quantity|number_format}x{/if}{$input.item->link()}</li>
                            {/foreach}
                            </ul>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            {/if}
            </div>
            {/if}
            {$i=$i+1}
        {/foreach}
        </div>
    </div>
    {/if}
    {/foreach}
</div>
{/block}

{block name=sidebar}
{if $spawns}
<div class='group'>
    <h3>Location</h3>
    <ul>
        {foreach from=$spawns item=map}
        {foreach from=$map->points() item=spawn}
            <li><em>{$map->get_place_name($spawn.x, $spawn.y)}</em>: {$spawn.x|number_format} {$spawn.y|number_format} ({$spawn.z|number_format})</li>
        {/foreach}
        {/foreach}
    </ul>
</div>
{/if}
<div class='group'>
    <h3>Additional Services</h3>
    <ul>
    {foreach from=$services item=service}
        {if get_class($service) == 'NPCService'}
        <li>{$service->name()}</li>
        {/if}
    {/foreach}
    </ul>
</div>
{/block}

{block name=more}{/block}
