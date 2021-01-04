<div class="modal fade" id="uploadGalModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload into {post_id}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
				<form method="post" action="core/files.upload_gallery.php?gal={post_id}" id="myDropzone" class="dropzone dropzone-default">
					<div class="fallback">
						<input name="file" type="file" multiple />
					</div>
					<input type="hidden" name="gal" value="{post_id}">
					<input type="hidden" name="csrf_token" value="{token}">
				</form>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
	$('#uploadGalModal').on('hidden.bs.modal', function (e) {
		window.location.assign("acp.php?tn=posts&sub=edit&post_id={post_id}")
	});
});
</script>