<div class="post-list-file">
	<div class="row">
		<div class="col-md-4">
			<div class="teaser-image">
				<img src="{post_img_src}" class="img-fluid">
			</div>
		</div>
		<div class="col-md-8">
			<span class="post-author">{post_author}</span> <span class="post-releasedate">{post_releasedate}</span>
			<a class="post-headline-link" href="{post_href}"><h3>{post_title}</h3></a>
			{post_teaser}
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-md-4">
			{post_voting}
		</div>
		<div class="col-md-8 text-end">
			<p class="m-0 post-categories">{post_cats}</p>
			
			<form action="{form_action}" method="POST">
				<button type="submit" class="btn btn-secondary"><i class="bi bi-arrow-down-circle"></i> {lang_download} {post_file_version}</button>
				<input type="hidden" name="post_attachment" value="{post_file_attachment}">
				<input type="hidden" name="post_attachment_external" value="{post_file_attachment_external}">
				<p class="text-muted">{post_file_attachment_external} {post_file_license}</p>
			</form>
			
			<p><a class="btn btn-primary {read_more_class}" href="{post_href}">{read_more_text}</a></p>
		</div>
	</div>
</div>