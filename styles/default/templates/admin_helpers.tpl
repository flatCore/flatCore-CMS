{if $smarty.session.user_class == "administrator"}
<div class="card p-1 mt-2">
	<a class="btn btn-secondary w-100" data-bs-toggle="collapse" href="#collapseAdminHelpers" role="button" aria-expanded="false" aria-controls="collapseAdminHelpers">
    Admin Helpers
  </a>
	<div class="collapse" id="collapseAdminHelpers">
		<div class="card-body">
			<fieldset>
				<legend>Snippets</legend>
				{foreach $admin_helpers_snippets as $helper}
		     {$helper}
				{/foreach}
			</fieldset>
			<fieldset>
				<legend>Shortcodes</legend>
				{foreach $admin_helpers_shortcodes as $helper}
		     {$helper}
				{/foreach}
			</fieldset>
			<fieldset>
				<legend>Images</legend>
				<ul>
				{foreach $admin_helpers_images as $helper}
		    	<li>{$helper}</li>
				{/foreach}
				</ul>
			</fieldset>
			<fieldset>
				<legend>Files</legend>
				<ul>
				{foreach $admin_helpers_files as $helper}
		    	<li>{$helper}</li>
				{/foreach}
				</ul>
			</fieldset>
			<fieldset>
				<legend>Plugins</legend>
				<ul>
				{foreach $admin_helpers_plugins as $helper}
		    	<li>{$helper}</li>
				{/foreach}
				</ul>
			</fieldset>
		</div>
	</div>
</div>
{/if}