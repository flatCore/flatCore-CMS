<button class="btn btn-sm btn-outline-secondary" name="upvote" onclick="vote(this.value)" value="up-{type}-{id}"><i class="bi bi-hand-thumbs-up-fill"></i> <span id="vote-up-nbr">{nbr_up}</span></button>
<button class="btn btn-sm btn-outline-secondary" name="dnvote" onclick="vote(this.value)" value="dn-{type}-{id}"><i class="bi bi-hand-thumbs-down-fill"></i> <span id="vote-dn-nbr">{nbr_dn}</span></button>

<script type="text/javascript">
function vote($val){

	$.ajax({
  	type: 'POST',
    url: '/core/ajax.votings.php',
    data: { 
    	val: $val
    },
    success: function(response) { 
	    		
			votes = JSON.parse(response);
			var cnt_upv = document.getElementById('vote-up-nbr');
			cnt_upv.innerHTML = votes['upv'];
			var cnt_dnv = document.getElementById('vote-dn-nbr');
			cnt_dnv.innerHTML = votes['dnv'];
			
    }
  });
}
</script>