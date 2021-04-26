<div class="card mb-3">
	<div class="card-body">
	<p class="h4">{label_guestlist} <span id="nbr-commitments"></span></p>
	<p>{description_guestlist}</p>
		<button class="btn btn-sm btn-outline-secondary" name="sign" onclick="sign_guestlist(this.value)" value="confirm-{id}">{sign_guestlist}</button>
	</div>
</div>

<script type="text/javascript">
function sign_guestlist($val){

	$.ajax({
  	type: 'POST',
    url: '/core/ajax.guestlist.php',
    data: { 
    	val: $val
    },
    success: function(response) { 
	    		
			commiters = JSON.parse(response);
			var cnt_commit = document.getElementById('nbr-commitments');
			cnt_commit.innerHTML = commiters['evc'];
		
    }
  });
}
</script>