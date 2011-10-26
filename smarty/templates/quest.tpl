{extends file='display.tpl'}

{block name=name}{$quest->plain_name()}{/block}

{block name=tooltip}
{$quest->render_tooltip()}
{/block}

{block name=details}
<div id="quest-prompt">
    {Text::ToHTML($quest->conversation->prompt)}
</div>

{if $quest->reward_success->item_groups}
<div id="quest-rewards">
    <h3>Rewards</h3>
    {if count($quest->reward_success->item_groups) > 1}
    <p>Choose from one of the following groups:</p>
    {/if}
    <table class='recipe-table'>
        <thead>
            <tr><th>Item</th><th>Probability</th></tr>
        </thead>
        <tbody>
        {foreach from=$quest->reward_success->item_groups item=group key=group_num}
        {if count($quest->reward_success->item_groups) > 1}
        <tr><th colspan="2">Group {$group_num+1}</th></tr>
        {/if}
        {foreach from=$group item=item}
        <tr>
            {$o=Item::FromID($item->id)}
            {* We do this here to avoid doing it for every quest ever *}
            <td>{if $item->amount > 1}{$item->amount|number_format}{/if}{if $o}{$o->link()}{/if}</td>
            <td>{($item->probability * 100)|number_format:3}%</td>
        </tr>
        {/foreach}
        {/foreach}
    </table>
</div>
{/if}
{/block}

{block name=sidebar}
<div class="group">
    <h3>Details</h3>
    <ul>
        {if $quest->quest_npc}<li>Given by:{$quest->quest_npc->link()}</li>{/if}
        {if $quest->reward_npc}<li>Returned to:{$quest->reward_npc->link()}</li>{/if}
        {if $quest->time_limit}<li>Time limit: {$quest->time_limit|format_time_interval}</li>{/if}
        {if $quest->required_gender == Element::GENDER_MALE}
            <li>Must be <strong>male</strong></li>
        {/if}
        {if $quest->required_gender == Element::GENDER_FEMALE}
            <li>Must be <strong>female</strong></li>
        {/if}
        {if $quest->instant_teleport->x != 400}
            <li>Instantly teleports to <em>{Map::FromID($quest->instant_teleport->map)->get_place_name($quest->instant_teleport->x, $quest->instant_teleport->y)}</em> {(int)$quest->instant_teleport->x} {(int)$quest->instant_teleport->y} ({(int)$quest->instant_teleport->z})</li>
        {/if}
        {if $quest->reward_success->teleport->map}
            <li>Teleports to <em>{Map::FromID($quest->reward_success->teleport->map)->get_place_name($quest->reward_success->teleport->x, $quest->reward_success->teleport->y)}</em> {(int)$quest->reward_success->teleport->x} {(int)$quest->reward_success->teleport->y} ({(int)$quest->reward_success->teleport->z}) on success</li>
        {/if}
        {if $quest->reward_failure->teleport->map}
            <li>Teleports to <em>{Map::FromID($quest->reward_failure->teleport->map)->get_place_name($quest->reward_failure->teleport->x, $quest->reward_failure->teleport->y)}</em> {(int)$quest->reward_failure->teleport->x} {(int)$quest->reward_failure->teleport->y} ({(int)$quest->reward_failure->teleport->z}) on failure</li>
        {/if}
        {if $quest->required_coins}
            <li>Requires {$quest->required_coins|number_format} coins to start</li>
        {/if}
        {if $quest->required_blacksmith}
            <li>Requires Blacksmithing {$quest->required_blacksmith}</li>
        {/if}
        {if $quest->required_tailor}
            <li>Requires Tailoring {$quest->required_tailor}</li>
        {/if}
        {if $quest->required_craftsman}
            <li>Requires Crafting {$quest->required_craftsman}</li>
        {/if}
        {if $quest->required_apothecary}
            <li>Requires Apothecary {$quest->required_apothecary}</li>
        {/if}
    </ul>
</div>

{if $quest->reward_success->cultivation || $quest->reward_success->chi || $quest->reward_success->storage_slots || $quest->reward_success->wardrobe_slots || $quest->reward_success->account_stash_slots || $quest->reward_success->inventory_slots || $quest->reward_success->pet_bag_slots}
<div class="group">
    <h3>Special Rewards</h3>
    <ul>
        {if $quest->reward_success->cultivation}<li>Increases your cultivation to <em>{Element::CultivationString($quest->reward_success->cultivation)}</em></li>{/if}
        {if $quest->reward_success->chi}<li>Increases your maximum chi to {$quest->reward_success->chi}</li>{/if}
        {if $quest->reward_success->storage_slots}<li>Increases your bank's capacity to {$quest->reward_success->storage_slots} slots</li>{/if}
        {if $quest->reward_success->wardrobe_slots}<li>Increases your wardrobe's capacity to {$quest->reward_success->wardrobe_slots} slots</li>{/if}
        {if $quest->reward_success->account_stash_slots}<li>Increases your account stash's capacity to {$quest->reward_success->account_stash_slots} slots</li>{/if}
        {if $quest->reward_success->inventory_slots}<li>Increases your inventory capacity to {$quest->reward_success->inventory_slots}</li>{/if}
        {if $quest->reward_success->pet_bag_slots}<li>Increases your pet bag's capacity to {$quest->reward_success->pet_bag_slots}</li>{/if}
    </ul>
</div>
{/if}

{function quest_tree}
    <li>{if $q->id == $quest->id}<strong><em>{$q->link()}</em></strong>{else}{$q->link()}{/if}
    {if $q->children}
        <ul>
            {foreach from=$q->children item=child}
            {$qc=Quest::FromID($child)}
            {quest_tree q=$qc}
            {/foreach}
        </ul>
    {/if}
    </li>
{/function}
{$chain=$root->get_chain()}
<div class="group">
    <h3>Quest Chain</h3>
    <ol>
        {foreach from=$chain item=q}
        {quest_tree q=$q}
        {/foreach}
    </ol>
</div>
{if !$chain && $quest->required_quests}
<div class="group">
    <h3>Previous Quests</h3>
    <ul>
        {foreach from=$quest->required_quests item=required}
        {$r=Quest::FromID($required)}
        <li>{$r->link()}</li>
        {/foreach}
    </ul>
</div>
{/if}
{/block}

{block name=more}{/block}
