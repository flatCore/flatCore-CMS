<div class="row files-list">
	<div class="col-md-6">
		<a href="{filename}" target="_blank">{short_filename}</a>
	</div>
	<div class="col-md-2">
		{filesize}
	</div>
	<div class="col-md-2">
		{show_filetime}
	</div>
	<div class="col-md-2 text-right">
		<form action="?tn=filebrowser" method="POST">
			<div class="btn-group">
				{edit_button}
				{delete_button}
				<input type="hidden" name="file" value="{short_filename}">
				<input  type="hidden" name="csrf_token" value="{csrf_token}">
			</div>
		</form>	
	</div>
</div>

