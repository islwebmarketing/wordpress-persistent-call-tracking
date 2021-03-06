<script type="text/javascript">
	function copyToClipboard(text) {
		window.prompt("Press Ctrl+C to copy", text);
	}
</script>
<div class="wrap" style="overflow:hidden;">
	<!-- ISL Header -->
	<div style="margin-bottom: 15px; background-color: #ddd; border: 1px solid grey; padding: 5px;">
		<a href="http://www.isl.ca" target="_blank">
			<img src="<?php echo PLUGIN_BASE_URL ?>images/isl.png" alt="isl" style="padding-right: 10px;"/>
		</a>
		Thanks for using our plugin! If you'd like to know more about us, check out <a href="http://www.isl.ca?utm_source=wordpress+plugin&utm_medium=plugin&utm_content=persistent+call+tracking&utm_campaign=wordpress+plugin" target="_blank">www.isl.ca</a>.
	</div>
	<!-- END ISL Header -->

	<div id="icon-index" class="icon32"><br/></div>
	<h2>Shortcodes <a class="add-new-h2" href="?page=add-shortcode">Add Shortcode</a></h2>

	<?php $this->wp_messages( $data ); ?><p></p>

	<p class="description"><strong>Instructions:</strong> Replace any phone number with a shortcode
		and the plugin will update the number based on associated parameter. Append the parameter on the end of any inbound
		link to update the number dynamically.</p>

	<?php $data['shortcodesTable']->views() ?>

	<form id="phones-filter" action="" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
		<?php $data['shortcodesTable']->display() ?>
	</form>
</div>
