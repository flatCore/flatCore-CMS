<div class="modal fade" id="uploadGalModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload into {post_id}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
				<form method="post" action="core/files.upload_gallery.php?gal={post_id}" id="myDropzone" class="dropzone dropzone-default">
					<div class="fallback">
						<input name="file" type="file" multiple />
					</div>
					<input type="hidden" name="gal" value="{post_id}">
					<input type="hidden" name="w" value="{max_img_width}">
					<input type="hidden" name="w_tmb" value="{max_tmb_width}">
					<input type="hidden" name="h" value="{max_img_height}">
					<input type="hidden" name="h_tmb" value="{max_tmb_height}">
					<input type="hidden" name="csrf_token" value="{token}">
				</form>
      </div>
    </div>
  </div>
</div>

<form action="acp.php?tn=posts&sub=edit" method="POST" id="reload_form">
	<input type="hidden" name="post_id" value="{post_id}">
</form>

<script>
$(function() {
	$('#uploadGalModal').on('hidden.bs.modal', function (e) {
		$("#reload_form").submit();
	});
});
</script>