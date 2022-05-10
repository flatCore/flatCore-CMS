<div class="list-group mb-4">
    {foreach $categories as $category => $value}
    <a href="{$value.cat_href}" title="{$value.cat_title}" class="list-group-item list-group-item-action {$value.cat_class}">{$value.cat_name}</a>
    {/foreach}
</div>
