<div class="post-product">
    <h1>{$event_title}</h1>

    <div class="row mb-3">
        <div class="col-md-2">
            <div class="event-date">
                <div class="event-date-header">
                    <span class="event-start-day">{$event_start_day}.</span>
                    <span class="event-start-month">{$event_start_month_text}</span>
                </div>
                <span class="event-start-year">{$event_start_year}</span>
                <div class="event-date-footer">
                    <span class="event-end-date">{$event_end_day}.{$event_end_month}.{$event_end_year}</span>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <p><span class="post-author">{$event_author}</span> <span class="post-releasedate">{$event_releasedate}</span></p>
            {$event_teaser}
        </div>
        <div class="col-md-3">
            <p><img src="{$event_img_src}" class="img-fluid" alt="{$event_img_caption}"><br><small>{$event_img_caption}</small></p>
        </div>
    </div>

    <div class="post-text mb-3">
        {$event_text}
    </div>

    {if $show_guestlist == true}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <p class="h4">{$label_guestlist}</p>
                    <p>{$description_guestlist}</p>
                </div>
                <div class="col-md-4">
                    <dl class="row">
                        <dt class="col-sm-9 text-end">{$label_nbr_total_available}</dt>
                        <dd class="col-sm-3">{$nbr_available_total}</dd>
                        <dt class="col-sm-9 text-end">{$label_nbr_commitments}</dt>
                        <dd class="col-sm-3"><span id="nbr-commitments">{$nbr_commitments}</span></dd>
                    </dl>
                </div>
            </div>

            <button class="btn btn-sm btn-outline-secondary" name="sign" onclick="sign_guestlist(this.value)" value="confirm-{$event_id}" {$disabled}>{$sign_guestlist}</button>
        </div>
    </div>
    {/if}

    <div class="post-text mb-3">
        {$event_price_note}
    </div>

    {if $show_voting == true}
        <div class="mb-3">
            <button class="btn btn-sm btn-outline-secondary" name="upvote" onclick="vote(this.value)"
                    value="up-post-{$event_id}" {$votes_status_up}>
                <i class="bi bi-hand-thumbs-up-fill"></i> <span id="vote-up-nbr-{$product_id}">{$votes_up}</span>
            </button>
            <button class="btn btn-sm btn-outline-secondary" name="dnvote" onclick="vote(this.value)"
                    value="dn-post-{$value.product_id}" {$votes_status_dn}>
                <i class="bi bi-hand-thumbs-down-fill"></i> <span id="vote-dn-nbr-{$product_id}">{$votes_dn}</span>
            </button>
        </div>
    {/if}

</div>