<div id="article_list_header">
    <div class="row mb-3">
        <div class="col-md-8 col-sm-12">
            <form action="{$form_action}" method="POST" class="d-inline">
                <div class="btn-group" role="group">
                    <button type="submit" name="sort_by" value="ts" class="btn btn-outline-primary {$class_sort_topseller}">{$lang_label_sort_topseller}</button>
                    <button type="submit" name="sort_by" value="name" class="btn btn-outline-primary {$class_sort_name}">{$lang_label_sort_name}</button>
                    <button type="submit" name="sort_by" value="pasc" class="btn btn-outline-primary {$class_sort_price_asc}">{$lang_label_sort_price_asc}</button>
                    <button type="submit" name="sort_by" value="pdesc" class="btn btn-outline-primary {$class_sort_price_desc}">{$lang_label_sort_price_desc}</button>
                </div>
            </form>
        </div>
        <div class="col-md-4 col-sm-12 text-right">
            {if $show_pagination == true}
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end">
                    <li>
                        <a href="{$pag_prev_href}" aria-label="Previous" class="page-link"><span aria-hidden="true">&laquo;</span></a>
                    </li>
                    {foreach $pagination as $pag}
                        <li class="page-item {$pag.active_class}">
                            <a href="{$pag.href}" class="page-link">{$pag.nbr}</a>
                        </li>
                    {/foreach}
                    <li>
                        <a href="{$pag_next_href}" class="page-link" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>
                    </li>
                </ul>
            </nav>
            {/if}
        </div>
    </div>
</div>

{foreach $products as $product => $value}


    <div class="post-list-product">
        {$value.draft_message}
        <div class="row">
            <div class="col-md-4">
                <div class="teaser-image">
                    <img src="{$value.product_img_src}" class="img-fluid">
                </div>
            </div>
            <div class="col-md-8">

                <div class="price-tag">
                    <div class="clearfix">
                        <div class="price-tag-label">{$post_product_price_label}</div>
                    </div>
                    <div class="price-tag-inner">
                        {$value.product_currency} <span class="product-amount">{$value.product_amount}</span> <span class="product-unit">{$value.product_unit}</span>
                        <span class="price-tag-note">{$value.product_price_gross}</span>
                    </div>
                </div>

                <span class="post-author">{$value.product_author}</span> <span class="post-releasedate">{$pvalue.product_releasedate}</span>
                <a class="post-headline-link" href="{$value.product_href}"><h3>{$value.product_title}</h3></a>
                {$value.product_teaser}
            </div>
        </div>
        <div class="row mt-1 mb-3">
            <div class="col-md-4">
                {if $value.show_voting == true}
                <button class="btn btn-sm btn-outline-secondary" name="upvote" onclick="vote(this.value)" value="up-post-{$value.product_id}" {$value.votes_status_up}>
                    <i class="bi bi-hand-thumbs-up-fill"></i> <span id="vote-up-nbr-{$value.product_id}">{$value.votes_up}</span>
                </button>
                <button class="btn btn-sm btn-outline-secondary" name="dnvote" onclick="vote(this.value)" value="dn-post-{$value.product_id}" {$value.votes_status_dn}>
                    <i class="bi bi-hand-thumbs-down-fill"></i> <span id="vote-dn-nbr-{$value.product_id}">{$value.votes_dn}</span>
                </button>
                {/if}
            </div>
            <div class="col-md-8 text-end">
                <p class="m-0 post-categories">
                    {foreach $value.product_categories as $category}
                        <a href="{$category.cat_href}" class="btn btn-sm btn-link" title="{$category.cat_title}">{$category.cat_title}</a>
                    {/foreach}
                </p>
                <div class="row">
                    <div class="col-md-8 text-end">
                        {if $show_shopping_cart == true}
                        <form action="{$form_action}" method="POST" class="d-inline">
                            <button class="btn btn-success" name="add_to_cart" value="{$value.product_id}">{$btn_add_to_cart}</button>
                            <input type="hidden" name="csrf_token" value="{$csrf_token}">
                        </form>
                        {/if}
                    </div>
                    <div class="col-md-4 text-end">
                        <a class="btn btn-primary w-100 {$read_more_class}" href="{$value.product_href}">{$btn_read_more}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


 {/foreach}

<div id="article_footer">

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li>
                <a href="{$pag_prev_href}" aria-label="Previous" class="page-link"><span
                            aria-hidden="true">&laquo;</span></a>
            </li>
            {foreach $pagination as $pag}
                <li class="page-item {$pag.active_class}">
                    <a href="{$pag.href}" class="page-link">{$pag.nbr}</a>
                </li>
            {/foreach}
            <li>
                <a href="{$pag_next_href}" class="page-link" aria-label="Next"><span
                            aria-hidden="true">&raquo;</span></a>
            </li>
        </ul>
    </nav>

</div>