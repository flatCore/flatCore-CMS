<div class="row files-list">
	<div class="col-md-1 p-1">
		{preview_img}
	</div>
	<div class="col-md-5">
		<a href="{preview_link}">{short_filename}</a> {labels}
	</div>
	<div class="col-md-2">
		{filesize}
	</div>
	<div class="col-md-2">
		{show_filetime}
	</div>
	<div class="col-md-2">
		<div class="card-footer p-1">
				<div  class="row">
					<div class="col">
						<form action="?tn=filebrowser&sub=edit" class="" method="POST">
							{edit_button}
							<input type="hidden" name="file" value="{filename}">
							<input  type="hidden" name="csrf_token" value="{csrf_token}">
						</form>
					</div>
					<div class="col">
						<form action="?tn=filebrowser" class="" method="POST">
							{delete_button}
							<input type="hidden" name="file" value="{short_filename}">
							<input  type="hidden" name="csrf_token" value="{csrf_token}">
						</form>
					</div>
				</div>
			</div>
	</div>
</div>

