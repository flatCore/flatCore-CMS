<div class="post-list-link">
	<h1>{post_title}</h1>
	<p><span class="post-author">{post_author}</span> <span class="post-releasedate">{post_releasedate}</span></p>
	
	
	<div class="row">
		<div class="col-md-4">
			<div class="teaser-image">
				<img src="{post_img_src}" class="img-fluid"><br><small>{post_img_caption}</small>
			</div>
		</div>
		<div class="col-md-8">
			{post_teaser}
			<p><a href="{post_external_link}" target="_blank">{post_external_link}</a></p>
		</div>
	</div>
	
	{post_voting}
	
	<div class="post-footer">
		<p class="text-right">{post_cats}</p>
	</div>
</div>