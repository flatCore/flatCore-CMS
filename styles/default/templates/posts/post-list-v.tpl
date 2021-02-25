<div class="post-list-video">
	<div class="row">
		<div class="col-md-12">
			<span class="post-author">{post_author}</span> <span class="post-releasedate">{post_releasedate}</span>
			<a class="post-headline-link" href="{post_href}"><h3>{post_title}</h3></a>
			<div class="well well-sm">
				<iframe id="video-player" type="text/html" width="100%" height="450px"
	    src="https://www.youtube.com/embed/{video_id}?rel=0&showinfo=0&color=white&iv_load_policy=3" frameborder="0" allowfullscreen></iframe>
				{post_teaser}
			</div>
		</div>
	</div>
	<div class="text-end">
		<p class="m-0 post-categories">{post_cats}</p>
		<p><a class="btn btn-primary {read_more_class}" href="{post_href}">{read_more_text}</a></p>
	</div>
</div>

