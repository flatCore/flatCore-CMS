{function name=thread}
    <ul>
        {foreach $items as $item}
            <li>
                <div class="card comment-entry">
                    <div class="row no-gutters">
                        <div class="col-md-1">
                            <img src="{$item['avatar_img_src']}" class="img-avatar img-fluid rounded-circle">
                        </div>
                        <div class="col-md-11">
                            <span class="entry-nbr">#{$item['id']}</span>
                            <p class="h6">{$item['author']}</p>
                            <p class="card-text">{$item['text']}</p>

                    </div>
                    </div>
                    <div class="card-footer p-1 text-muted">
                        <small>{$item['time']}</small>
                        <a href="{$item['url_answer_comment']}" class="btn btn-sm btn-outline-primary float-end">{$lang_answer}</a>
                    </div>
                {if $item['childs']}
                    {call name=thread items=$item['childs']}
                {/if}
                </div>
            </li>
        {/foreach}
    </ul>
{/function}

{call name=thread items=$comments}