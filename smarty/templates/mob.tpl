{extends file='display.tpl'}

{block name=name}{$mob->name}{/block}

{block name=tooltip}{$mob->render_tooltip()}{/block}

{block name=sidebar}
    <div class="group">
        <h3>Aggression</h3>
        <ul>
            {if $mob->aggressive}
            <li>Aggressive</li>
            {else}
            <li>Non-aggressive</li>
            {/if}
            <li>Reset distance: {$mob->aggro_range|number_format:1} metres</li>
            <li>Reset time: {$mob->aggro_time|number_format} seconds</li>
        </ul>
    </div>
    <div class="group">
        <h3>Rewards</h3>
        <ul>
            <li>Coins: {($mob->coins_mean - $mob->coins_variance)|max:0|number_format} – {($mob->coins_mean + $mob->coins_variance)|number_format}</li>
            {if $dq_sell > 0}
            <li><abbr title="Average Dragon Quest points per kill">DQ points</abbr>: <span class='level-adjust-drops'>{$dq_sell|number_format:2}</span></li>
            {/if}
            {if $mob->egg}
            <li>Becomes{$mob->egg->link()}when tamed</li>
            {/if}
            <li>Drop count: 
                <table class="chart-table" id="item-socket-table">
                <thead>
                    <tr>
                        {foreach from=$mob->drop_distribution item=p key=i}
                        <td>{$i * $mob->drop_multiplier}</td>
                        {/foreach}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {foreach from=$mob->drop_distribution item=p}
                        <td><div><div class="chart" style="height: {($p * 15.0)|ceil}px;">&nbsp;</div>
                        <div class="text">{($p * 100)|number_format:0}%</div></div></td>
                        {/foreach}
                    </tr>
                </tbody>
                </table>
            </li>
            <li>Experience: <span class='level-adjust-exp'>{$mob->exp|number_format}</span> <span class="hyper-exp">(x12: <span class='level-adjust-exp'>{($mob->exp * 12)|number_format}</span>)</span></li>
            <li>Spirit: <span class='level-adjust-exp'>{$mob->spirit|number_format}</span> <span class="hyper-exp">(x12: <span class='level-adjust-exp'>{($mob->spirit * 12)|number_format}</span>)</span></li>
        </ul>
    </div>
    <div class="group">
        <h3>Attack</h3>
        <ul>
            <li>Physical: {$mob->min_patk|number_format}–{$mob->max_patk|number_format} ({$mob->range|number_format} metre range)</li>
            <li>Attacks per Second: {(1/$mob->interval)|number_format:1}</li>
            <li>Magic: {$mob->min_matk|number_format}–{$mob->max_matk|number_format}</li>
        </ul>
    </div>
    <div class="group">
        <h3>Defense</h3>
            <ul>
                <li>Physical: {$mob->phys_def|number_format}</li>
                <li><span class='pw-element-MT'>Metal</span>: {$mob->metal_def|number_format}</li>
                <li><span class='pw-element-WD'>Wood</span>: {$mob->wood_def|number_format}</li>
                <li><span class='pw-element-WT'>Water</span>: {$mob->water_def|number_format}</li>
                <li><span class='pw-element-FR'>Fire</span>: {$mob->fire_def|number_format}</li>
                <li><span class='pw-element-ET'>Earth</span>: {$mob->metal_def|number_format}</li>
            </ul>
        </ul>
    </div>
{/block}

{block name=more}
{if $drops || $same_model}
<h3>More information</h3>
<div id="more-tabs">
    <ul>
        {if $drops}<li><a href="#tab-drops">Drops ({$drops|count|number_format})</a></li>{/if}
        {if $same_model}<li><a href="#tab-same-model">Same model ({$same_model|count|number_format})</a></li>{/if}
    </ul>
    {if $drops}
    <div id="tab-drops">
        <table class='recipe-table'>
            <thead>
                <tr><th>Item</th><th>Nominal Drop Rate</th><th>Actual Drop Rate</th></tr>
            </thead>
            <tbody>
                {foreach from=$drops item=drop}
                <tr>
                    <td>
                        {$drop.item->link()}
                    </td>
                    <td>
                        {($drop.rate * 100)|number_format:2}%
                    </td>
                    <td>
                        <span class='level-adjust-drops'>{($mob->real_drop_rate($drop.rate) * 100)|number_format:2}</span>%
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
    {if $same_model}
    <div id="tab-same-model">
        <table class='recipe-table'>
            <thead>
                <tr><th>Mob</th><th>Level</th><th>Element</th><th>Model</th></tr>
            </thead>
            <tbody>
                {foreach from=$same_model item=identical}
                <tr>
                    <td>
                        {$identical.mob->link()}
                    </td>
                    <td>
                        {$identical.mob->get_level()}
                    </td>
                    <td>
                        <span class='pw-element-{$identical.mob->strong_element}'>{$identical.mob->strong_element}</span>
                    </td>
                    <td>
                        <code lang="cn-ZH">{$identical.mob->model}</code><br /><code><span class="translation">{$identical.translated}</span></code>
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

{block name=details}
{if $mob->spawn_points()}
    <div id="map-tabs">
        <ul>
            {foreach from=$mob->spawn_points() item=map key=id}
            <li><a href="#map-{$id}">{$map->get_name()} ({$map->point_count()})</a></li>
            {/foreach}
        </ul>
        {foreach from=$mob->spawn_points() item=map key=id}
        <div id="map-{$id}" class="squashed">
            {$map->render()}
            <h3>Coordinate list</h3>
            <p>{foreach from=$map->points() item=point}<span class='map-coord x-{$point.x|round} y-{$point.y|round} z-{$point.z|round}'>{$point.x|round} {$point.y|round} ({$point.z|round})</span>; {/foreach}</p>
        </div>
        {/foreach}
    </div>
{/if}
{/block}
