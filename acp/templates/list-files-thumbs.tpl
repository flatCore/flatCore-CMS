<div class="masonry-item">
	<div class="masonry-item-inner">
		{preview_img}
		<h5>{short_filename}</h5>
		<div>{show_filetime}<br>{filesize}</div>
		<form action="?tn=filebrowser" method="POST">
			<div class="btn-group btn-group-justified">
				<div class="btn-group" role="group">
					{edit_button}
				</div>
				<div class="btn-group" role="group">
					{delete_button}
				</div>
				<input type="hidden" name="file" value="{short_filename}">
				<input  type="hidden" name="csrf_token" value="{csrf_token}">
			</div>
		</form>
	</div>
</div>