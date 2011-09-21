<?php 
$routeTypes = array('round_robin' => "Round Robin", 
	'longest_idle' => "Longest Idle");
?>
<div class="vbx-applet">

	<h2>DreamACD</h2>
	<p><a href="http://disruptive.io/" target="_blank">Disruptive Technologies</a> DreamACD applet. This applet is used to route a call to a user group, with much more support than a specific dial command. See <a href="#">Disruptive.io DreamACD</a> for help.</p>

	<h4>Route to a user or group</h4>
	<?php echo AppletUI::UserGroupPicker('dial-whom-user-or-group'); ?>

	<h4>Choose Route Type</h4>
	<select class="medium" name="acd-route-type">
		<?php 
		$acd_route_type = AppletInstance::getValue('acd-route-type'); 
		foreach ($routeTypes as $routeValue => $routeFriendly): 
		?>
			<option value="<?php echo $routeValue ?>"
				<?php 
				echo $routeValue == $acd_route_type ? 'selected="selected"' : ''; 
				?> name="acd-route-type"><?php echo $routeFriendly ?></option>
		<?php endforeach; ?>
	</select>

</div><!-- .vbx-applet -->
