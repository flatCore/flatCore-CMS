<?php
//prohibit unauthorized access
require 'core/access.php';
?>

<h3>UI Tests</h3>

<div class="row">
	<div class="col-md-9">

		<h4 id="headings">Headings</h4>
		
		<h1>.h1 flatCore Content Management System</h1>
		<h2>.h2 flatCore Content Management System</h2>
		<h3>.h3 flatCore Content Management System</h3>
		<h4>.h4 flatCore Content Management System</h4>
		<h5>.h5 flatCore Content Management System</h5>
		<h6>.h6 flatCore Content Management System</h6>
		<p>&lt;p&gt; flatCore Content Management System</p>
		<p>This is <code>Code &lt;code&gt;</code></p>
		<hr>
		
		<h4 id="buttons">Buttons</h4>
		
		<p><a href="#" class="btn btn-fc">btn dark</a> <a href="#" class="btn btn-primary">btn primary</a> <a href="#" class="btn btn-success">btn success</a> <a href="#" class="btn btn-danger">btn danger</a> <a href="#" class="btn btn-secondary">btn secondary</a> <a href="#" class="btn btn-info">btn info</a></p>
		<p><a href="#" class="btn btn-outline-primary">btn btn-outline-primary</a> <a href="#" class="btn btn-outline-secondary">btn btn-outline-secondary</a> <a href="#" class="btn btn-outline-success">btn btn-outline-success</a></p>
		<p><a href="#" class="btn btn-fc">btn fc</a> <a href="#" class="btn btn-save">btn save</a></p>
		
		<hr>
		
		<h4 id="container">Container</h4>
		
		<div class="well">well</div>
		<div class="well well-sm">well sm</div>
		
		<div class="alert alert-success">alert success</div>
		<div class="alert alert-danger">alert danger</div>
		<div class="alert alert-warning">alert warning</div>
		<div class="alert alert-info">alert info</div>
		<div class="alert alert-secondary">alert secondary</div>
		<div class="alert alert-fc">alert fc</div>
		
		<fieldset class="mt-4">
			<legend>fieldset .mt-4</legend>
			<p>flatCore Content Management System</p>
		</fieldset>
		
		<pre>&lt;pre&gt; -> flatCore Content Management System</pre>
		
		<hr>
		
		<h4 id="icons">Icons</h4>
		
		<?php
			
			echo '<table class="table table-sm table-bordered"">';
			foreach($icon as $k => $v) {
				
				echo '<tr>';
				echo '<td><span class="text-secondary h2">'.$v.'</span></td> ';
				echo '<td><code>$icon[\''.$k.'\']</code></td>';
				
				echo '</tr>';
				
			}
			echo '</table>';
					
		?>
		

	</div>
	<div class="col-md-3">
		<ul>
		<li><a href="#headings">headings</a></li>
		<li><a href="#buttons">buttons</a></li>
		<li><a href="#container">container</a></li>
		<li><a href="#icons">icons</a></li>
		</ul>
	</div>
</div>