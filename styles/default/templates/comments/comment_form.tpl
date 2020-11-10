<form id="comment-form" action="/core/comments-send.php" method="POST">

	
	<div class="form-group">
    <label for="input_mail">{$label_name}</label>
    <input type="text" class="form-control" id="input_name" name="input_name" value="{$input_name}" {$comment_name_readonly}>
  </div>
  
	<div class="form-group">
    <label for="input_mail">{$label_mail}</label>
    <input type="email" class="form-control" id="input_mail" aria-describedby="mail_help" name="input_mail" value="{$input_mail}" {$comment_mail_readonly}>
    <small id="mail_help" class="form-text text-muted">{$mail_help}</small>
  </div>
  
  <div class="form-group">
    <label for="input_comment">{$label_comment}</label>
    <textarea class="form-control" id="input_comment" rows="3" name="input_comment"></textarea>
  </div>
  
  <button class="btn btn-primary" type="submit">{$btn_send_comment}</button>
  
  <input type="hidden" name="page_id" value="{$page_id}">
  <input type="hidden" name="post_id" value="{$post_id}">
	
</form>

<div id="form-response"></div>