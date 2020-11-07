<div id="content">
	{$msg_content nocache}
	{$page_content}
</div>

{if $show_page_comments != ''}
	<hr class="shadow">
	<div class="page_comments_form">
		{$comment_form}
	</div>
	<div id="page_comments">
	</div>
			<script>
      $(function () {

        $('form#comment-form').on('submit', function (e) {

          e.preventDefault();

          $.ajax({
            type: 'POST',
            url: '/core/comments-send.php',
            data: $(this).serialize()
          }).done(function(data) {
						$('#form-response').html(data);
						getAjax();
						$('form#comment-form').trigger("reset");
					});

        });
        
        getAjax();

				function getAjax() {
        $.ajax({
					url: "/core/comments-load.php?page_id={$page_id}&post_id={$post_id}"
				}).done(function(data) {
					$('#page_comments').html(data);
				});
				}

      });
    </script>
	
{/if}