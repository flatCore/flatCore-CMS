<div class="card">
	<div class="card-header p-1">{short_filename}</div>
	{preview_img}
	<div class="card-body p-1">
		<p class="m-0"><small>{show_filetime} / {filesize}</small></p>
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
