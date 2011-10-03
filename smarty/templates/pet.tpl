{extends file='display.tpl'}

{block name=name}{$pet->name}{/block}

{block name=tooltip}
    <div id="tooltip-image-holder">
        <img src="{$pet->icon_url()}" alt="Icon for {$pet->name|escape}">
    </div>
    {$pet->render_tooltip()}
{/block}

{block name=details}
    {if $pet->type == Pet::PET_BATTLE}
    <table class="pet-stats">
        <thead>
            <tr><th>Level</th><th>HP</th><th>Attack</th><th>PDef</th><th>MDef</th><th>Accuracy</th><th>Evasion</th><th>Speed</th></tr>
        </thead>
        <tbody>
            {foreach from=$stat_levels item=level}
            <tr>
                <td>{$level}</th>
                <td>{$pet->get_hp($level)|number_format}</td>
                <td>{$pet->get_attack($level)|number_format}</td>
                <td>{$pet->get_pdef($level)|number_format}</td>
                <td>{$pet->get_mdef($level)|number_format}</td>
                <td>{$pet->get_accuracy($level)|number_format}</td>
                <td>{$pet->get_evasion($level)|number_format}</td>
                <td>{$pet->get_speed($level)|number_format:1}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    {/if}
{/block}

{block name=sidebar}
    <div class="group">
        <h3>Stats</h3>
        <ul>
            {if $egg}
                <li>Hatches from {$egg->link()}</li>
            {/if}
            <li>Maximum level: {$pet->max_pet_level}</li>
            <li>Maximum speed: {$pet->get_speed($pet->max_pet_level)} m/s</li>
            {if $pet->interval}
                <li>Attacks per second: {$pet->get_aps()|number_format:2}</li>
            {/if}
        </ul>
    </div>
    {if $skills}
    <div class="group">
        <h3>Initial Skills</h3>
        <ul>
            {foreach from=$skills item=skill}
            <li>{$skill.name} (level {$skill.level})</li>
            {/foreach}
        </ul>
    </div>
    {/if}
{/block}

{block name=more}{/block}
