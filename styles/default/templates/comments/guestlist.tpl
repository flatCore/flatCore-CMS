<div class="card mb-3">
	<div class="card-body">
		<div class="row">
			<div class="col-md-8">
				<p class="h4">{label_guestlist}</p>
				<p>{description_guestlist}</p>
			</div>
			<div class="col-md-4">
				<dl class="row">
					<dt class="col-sm-9 text-end">{label_nbr_total_available}</dt><dd class="col-sm-3">{nbr_available_total}</dd>
					<dt class="col-sm-9 text-end">{label_nbr_commitments}</dt><dd class="col-sm-3"><span id="nbr-commitments">{nbr_commitments}</span></dd>
				</dl>
			</div>
		</div>
		
		<button class="btn btn-sm btn-outline-secondary" name="sign" onclick="sign_guestlist(this.value)" value="confirm-{id}" {disabled}>{sign_guestlist}</button>
	</div>
</div>