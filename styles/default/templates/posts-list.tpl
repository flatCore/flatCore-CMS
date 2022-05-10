<div id="article_list_header">
    <div class="row mb-3">
        <div class="col-md-8 col-sm-12">
            <p>
                {$lang_entries} {$post_start_nbr} - {$post_end_nbr} ({$lang_entries_total}: {$post_cnt})<br>
                <small class="text-muted">{$category_filter}</small>
            </p>
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

{foreach $posts as $post => $value}


    <div class="post-list-{$value.post_type} {$value.post_css_classes}">
        {$value.draft_message}



        {* post type message *}
        {if $value.post_type == "m"}

            <div class="row">
                {if $value.post_tmb_src != ""}
                    <div class="col-md-4">
                        <div class="teaser-image">
                            <img src="{$value.post_tmb_src}" class="img-fluid">
                        </div>
                    </div>
                {/if}
                <div class="col">
                    <span class="post-author">{$value.post_author}</span> <span class="post-releasedate">{$value.post_releasedate_str}</span>
                    <a class="post-headline-link" href="{$value.post_href}"><h3>{$value.post_title}</h3></a>
                    {$value.post_teaser}
                </div>
            </div>

        {/if}
        {* post type message end *}

        {* post type gallery *}
        {if $value.post_type == "g"}

            <span class="post-author">{$value.post_author}</span> <span class="post-releasedate">{$value.post_releasedate_str}</span>
            <a class="post-headline-link" href="{$value.post_href}"><h3>{$value.post_title}</h3></a>
            {$value.post_teaser}

            <div class="post-thumbnails clearfix border-1">
                <section data-featherlight-gallery data-featherlight-filter="a">
                    {if $value.post_thumbnails == true}
                        {foreach $value.post_thumbnails as $thumb}
                            <a href="{$thumb.img_src}" class="post-thumbnail lightbox" style="background-image: url({$thumb.tmb_src})"></a>
                        {/foreach}
                    {/if}
                </section>
            </div>
        {/if}
        {* post type gallery end *}

        {* post type image *}
        {if $value.post_type == "i"}
            <span class="post-author">{$value.post_author}</span> <span class="post-releasedate">{$value.post_releasedate_str}</span>
            <a class="post-headline-link" href="{$value.post_href}"><h3>{$value.post_title}</h3></a>
            {$value.post_teaser}

            <div class="card p-3">
                <img src="{$value.post_tmb_src}" class="img-fluid">
            </div>
        {/if}
        {* post type image end *}

        {* post type video *}
        {if $value.post_type == "v"}

            <span class="post-author">{$value.post_author}</span> <span class="post-releasedate">{$value.post_releasedate_str}</span>
            <a class="post-headline-link" href="{$value.post_href}"><h3>{$value.post_title}</h3></a>
            {$value.post_teaser}

            <div class="card p-3">
                <iframe id="video-player"
                        type="text/html"
                        width="100%"
                        height="450px"
                        src="https://www.youtube.com/embed/{$value.video_id}?rel=0&showinfo=0&color=white&iv_load_policy=3"
                        frameborder="0"
                        allowfullscreen>
                </iframe>
            </div>

        {/if}
        {* post type video end *}

        {* post type link *}
        {if $value.post_type == "l"}

            <span class="post-author">{$value.post_author}</span> <span class="post-releasedate">{$value.post_releasedate_str}</span>
            <a class="post-headline-link" href="{$value.post_href}"><h3>{$value.post_title}</h3></a>
            {$value.post_teaser}

            <p>
               <a href="{$value.post_external_redirect}" title="{$value.post_external_link}" target="_blank">{$value.post_external_link}</a>
            </p>

        {/if}
        {* post type link end *}

        {* post type file (download) *}
        {if $value.post_type == "f"}

            <span class="post-author">{$value.post_author}</span> <span class="post-releasedate">{$value.post_releasedate_str}</span>
            <a class="post-headline-link" href="{$value.post_href}"><h3>{$value.post_title}</h3></a>
            {$value.post_teaser}

            <form action="{$form_action}" method="POST">
                <button type="submit" class="btn btn-secondary"><i class="bi bi-arrow-down-circle"></i> {$btn_download} {$value.post_file_version}</button>
                <input type="hidden" name="post_attachment" value="{$value.post_file_attachment}">
                <input type="hidden" name="post_attachment_external" value="{$value.post_file_attachment_external}">
                <p class="text-muted">{$value.post_file_attachment_external} {$value.post_file_license}</p>
            </form>
        {/if}
        {* post type file end *}

        <div class="row">

            {if $value.show_voting == true}
                <div class="col-3">
                    <button class="btn btn-sm btn-outline-secondary" name="upvote" onclick="vote(this.value)" value="up-post-{$value.post_id}" {$value.votes_status_up}>
                        <i class="bi bi-hand-thumbs-up-fill"></i> <span id="vote-up-nbr-{$value.post_id}">{$value.votes_up}</span>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" name="dnvote" onclick="vote(this.value)" value="dn-post-{$value.post_id}" {$value.votes_status_dn}>
                        <i class="bi bi-hand-thumbs-down-fill"></i> <span id="vote-dn-nbr-{$value.post_id}">{$value.votes_dn}</span>
                    </button>
                </div>
        {/if}
        {if $value.post_categories == true}
            <div class="col-3">
                <div class="m-0 post-categories">
                {foreach $value.post_categories as $category}
                    <a href="{$category.cat_href}" class="btn btn-sm btn-link" title="{$category.cat_title}">{$category.cat_title}</a>
                {/foreach}
                </div>
            </div>
        {/if}
            <div class="col text-end">
            <a class="btn btn-primary {$read_more_class}" href="{$value.post_href}">{$value.btn_open_post}</a>
        </div>

        </div>



    </div><hr>


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