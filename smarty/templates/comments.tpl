{if $comments}
    {foreach from=$comments item=comment}
        <div class="comment-container">
            <h3 class='commenter'>{$comment->commenter|escape}</h3>
            <p class='date'>{$comment->date|date_format:"%e %b %Y %H:%M"}</p>
            <p class='comment'>{$comment->comment|escape|nl2br}</p>
        </div>
    {/foreach}
{/if}
<form action="/comment" id="comment_form" method="post">
    <p>Your name: <input type="text" name="name" id="comment_name"></p>
    <textarea name="comment" id="comment_content"></textarea>
    <input type="hidden" value="{$comment_id}" name="id">
    <input type="hidden" value="{$comment_class}" name="kind">
    <p><input type="submit" value="Submit"></p>
</form>
