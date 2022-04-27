<div class="post-product">
    <h1>{$product_title}</h1>

    <div class="card">
        <p><img src="{$product_img_src}" class="img-fluid"><br><small>{$product_img_caption}</small></p>
        <div class="card-img-overlay">
            <div class="price-tag">
                <div class="clearfix">
                    <div class="price-tag-label">{$product_product_price_label}</div>
                </div>
                <div class="price-tag-inner">
                    {$product_currency} {$product_price_gross} <span class="product-amount">{$product_amount}</span> <span class="product-unit">{$product_unit}</span>
                    <span class="price-tag-note">{$product_price_tag_label_gross}</span>
                </div>
                <form action="{$form_action}" method="POST" class="d-inline">
                    <button class="btn btn-success" name="add_to_cart" value="{$product_id}">{$btn_add_to_cart}</button>
                    <input type="hidden" name="csrf_token" value="{$csrf_token}">
                </form>
            </div>

        </div>
    </div>


    <p><span class="post-author">{$product_author}</span> <span class="post-releasedate">{$product_releasedate}</span></p>
    {$product_teaser}
    <p></p>

    <div class="post-text">
        {$product_text}
    </div>

    <div class="post-snippet-text">
        {$product_snippet_text}
    </div>

    {if $product_snippet_text != ""}
        <div class="card p-3 mb-3">
            {$product_snippet_text}
        </div>
    {/if}

    {if $product_snippet_price != ""}
    <div class="card post-snippet-price mb-3">
        <div class="card-header">{$label_prices_snippet}</div>
        <div class="card-body">
            {$product_snippet_price}
        </div>
    </div>
    {/if}

    {if $show_voting == true}
        <div class="mb-3">
            <button class="btn btn-sm btn-outline-secondary" name="upvote" onclick="vote(this.value)"
                    value="up-post-{$product_id}" {$votes_status_up}>
                <i class="bi bi-hand-thumbs-up-fill"></i> <span id="vote-up-nbr-{$product_id}">{$votes_up}</span>
            </button>
            <button class="btn btn-sm btn-outline-secondary" name="dnvote" onclick="vote(this.value)"
                    value="dn-post-{$value.product_id}" {$votes_status_dn}>
                <i class="bi bi-hand-thumbs-down-fill"></i> <span id="vote-dn-nbr-{$product_id}">{$votes_dn}</span>
            </button>
        </div>
    {/if}

</div>