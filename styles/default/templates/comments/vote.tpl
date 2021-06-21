<button class="btn btn-sm btn-outline-secondary" name="upvote" onclick="vote(this.value)" value="up-{type}-{id}" {status_upv}>
	<i class="bi bi-hand-thumbs-up-fill"></i> <span id="vote-up-nbr-{id}">{nbr_up}</span>
</button>
<button class="btn btn-sm btn-outline-secondary" name="dnvote" onclick="vote(this.value)" value="dn-{type}-{id}" {status_dnv}>
	<i class="bi bi-hand-thumbs-down-fill"></i> <span id="vote-dn-nbr-{id}">{nbr_dn}</span>
</button>