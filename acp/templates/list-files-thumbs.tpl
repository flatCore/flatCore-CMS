<div class="masonry-item">
	<div class="masonry-item-inner">
		{preview_img}
		<h6>{short_filename}</h6>
		<div><small>{show_filetime}<br>{filesize}</small></div>
		<form action="?tn=filebrowser" method="POST">
			<div class="btn-group d-flex">

					{edit_button}
					{delete_button}

				<input type="hidden" name="file" value="{short_filename}">
				<input  type="hidden" name="csrf_token" value="{csrf_token}">
			</div>
		</form>
	</div>
</div>
