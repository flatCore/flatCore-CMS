<div class="post-list-file">
	<h1>{post_title}</h1>
	<p><span class="post-author">{post_author}</span> <span class="post-releasedate">{post_releasedate}</span></p>
	<p><img src="{post_img_src}" class="img-fluid"></p>
	{post_teaser}
		
	<form action="{form_action}" method="POST">
		<button type="submit" class="btn btn-primary">{lang_download} {post_file_version}</button>
		<input type="hidden" name="post_attachment" value="{post_file_attachment}">
		<input type="hidden" name="post_attachment_external" value="{post_file_attachment_external}">
		<p class="text-muted">{post_file_attachment_external} {post_file_license}</p>
	</form>
		
	<div class="post-footer">
		<p class="text-right">{post_cats}</p>
	</div>
</div>