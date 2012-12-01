
<p style="padding:15px 0;">
<?php echo"$lang[msg_form]"; ?>
</p>


<form action="index.php" method="POST">

<div class="block">
<div class="left"><?php echo"$lang[username]"; ?></div>
<div class="right"><input type="text" name="username" value="" class="txtfield"></div>
<div style="clear: both;"></div>
</div>

<div class="block">
<div class="left"><?php echo"$lang[email]"; ?></div>
<div class="right"><input type="text" name="mail" value="" class="txtfield"></div>
<div style="clear: both;"></div>
</div>

<div class="block">
<div class="left"><?php echo"$lang[password]"; ?></div>
<div class="right"><input type="password" name="psw" value="" class="txtfield"></div>
<div style="clear: both;"></div>
</div>


<p>
<input type="submit" class="submit" name="step3" value="<?php echo"$lang[start_install]"; ?>"><br />
<input type="submit" class="submit" name="step1" value="<?php echo"$lang[step]"; ?> 1">
</p>

</form>