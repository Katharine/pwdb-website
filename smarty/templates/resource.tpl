{extends file='display.tpl'}

{block name=name}{$resource->name}{/block}

{block name=tooltip}{$resource->render_tooltip()}{/block}

{block name=details}
    {if $spawns}
    <div id="map-tabs">
        <ul>
            {foreach from=$spawns item=map key=id}
            <li><a href="#map-{$id}">{$map->get_name()} ({$map->point_count()})</a></li>
            {/foreach}
        </ul>
        {foreach from=$spawns item=map key=id}
        <div id="map-{$id}" class="squashed">
            {$map->render()}
            <h3>Coordinate list</h3>
            <p>{foreach from=$map->points() item=point}<span class='map-coord x-{$point.x|round} y-{$point.y|round} z-{$point.z|round}'>{$point.x|round} {$point.y|round} ({$point.z|round})</span>; {/foreach}</p>
        </div>
        {/foreach}
    </div>
    {block name=item_details}{/block}
    {/if}
{/block}

{block name=sidebar}
    <div class="group">
        <h3>Resource</h3>
        <ul>
            <li>Experience: {$resource->exp|number_format}</li>
            <li>Spirit: {$resource->spirit|number_format}</li>
            <li>Number of items: 
                <table class="chart-table" id="item-socket-table">
                <thead>
                    <tr>
                        {foreach from=$resource->quantities item=p key=i}
                        <td>{$i}</td>
                        {/foreach}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {foreach from=$resource->quantities item=p}
                        <td><div><div class="chart" style="height: {($p * 15.0)|ceil}px;">&nbsp;</div>
                        <div class="text">{($p * 100)|number_format:0}%</div></div></td>
                        {/foreach}
                    </tr>
                </tbody>
                </table>
            </li>
            <li>Time taken: {$resource->min_time}{if $resource->max_time!=$resource->min_time} - {$resource->max_time}{/if} seconds</li>
            {if $resource->uninterruptible}
            <li>Cannot be interrupted while digging</li>
            {/if}
            {if $resource->permanent}
            <li>Does not disappear after digging</li>
            {/if}
        </ul>
    </div>
{/block}

{block name=more}{/block}
