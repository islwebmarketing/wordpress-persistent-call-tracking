<script type="text/javascript">
	function checkVals(frm) {
		if (frm.name.value == '') {
			alert('Please enter a Name.');
			frm.name.focus();
			return false;
		}
		else if (frm.default.value == '') {
			alert('Please enter a Default Phone Number.');
			frm.phn_no.focus();
			return false;
		}
		else if (frm.shortcode.value == '') {
			alert('Please enter a Shortcode.');
			frm.phn_no.focus();
			return false;
		}
	}
	function copyToClipboard(text) {
		window.prompt("Press Ctrl+C to copy", text);
	}
	function stripCharacters(element) {
		element.value = element.value.replace(/\W/g, '');
	}
</script>
<div id="form_container" class="wrap">
	<!-- ISL Header -->
	<div style="margin-bottom: 15px; background-color: #ddd; border: 1px solid grey; padding: 5px;">
		<a href="http://www.isl.ca" target="_blank">
			<img src="<?php echo PLUGIN_BASE_URL ?>images/isl.png" alt="isl" style="padding-right: 10px;"/>
		</a>
		Thanks for using our plugin! If you'd like to know more about us, check out <a href="http://www.isl.ca?utm_source=wordpress+plugin&utm_medium=plugin&utm_content=persistent+call+tracking&utm_campaign=wordpress+plugin" target="_blank">www.isl.ca</a>.
	</div>
	<!-- END ISL Header -->

	<?php
	$this->wp_messages( $data );

	if ( $data['s_id'] > 0 ) {
		?>
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>Edit Shortcode</h2><?php
	} else {
		?><div id="icon-options-general" class="icon32"><br></div><h2>Add New Shortcode</h2><?php
	}?>
	<form class="appfreedom" method="post" action="" onsubmit="return checkVals(this);">
		<div class="form_description">
		</div>
		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row"><label class="description" for="name">Name: </label></th>
				<td><input id="name" name="name" class="element text medium regular-text" type="text" maxlength="200"
				           size="60" value="<?php echo trim( $data['name'] ) ?>"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label class="description" for="phn_no">Default Phone Number: </label></th>
				<td><input id="default" name="default" class="element text medium regular-text" type="text"
				           maxlength="200" size="60" value="<?php echo trim( $data['default'] ) ?>"/></td>
			</tr>
			<?php if ( $data['p_id'] > 0 ) { ?>
				<tr valign="top">
					<th scope="row"><label class="description">Shortcode: </label></th>
					<td><?php echo "[{$data['shortcode']}]" ?> <a href="#" onclick="copyToClipboard('<?php echo "[{$data['shortcode']}]"; ?>');">
							<img title="Copy" style="vertical-align:top;" alt="Copy" src="<?php echo PLUGIN_BASE_URL ?>images/copy.png"/>
						</a>
					</td>
				</tr>
			<?php
			} else { ?>
				<tr valign="top">
					<th scope="row"><label class="description" for="shortcode">Shortcode: </label></th>
					<td><input id="shortcode" name="shortcode" class="element text medium regular-text" type="text"
					           maxlength="200" size="60" value="trackable_number" onblur="stripCharacters(this)"/>
						<br />Shortcode names should be all lowercase and use all letters, but numbers and underscores
						should work fine too.</td>
				</tr>
				<tr><td></td><td></td></tr>
			<?php } ?></tbody>
		</table>
		<input type="hidden" name="s_id" id="s_id" value="<?php echo $data['s_id'] ?>"/>

		<p class="submit"> &nbsp; <a class="button button-primary" href="?page=persistent-call-tracking">Cancel</a> &nbsp;
			<input type="submit" name="submit" id="saveForm" class="button button-primary"
				value="<?php echo ( $data['s_id'] > 0 ) ? 'Update' : 'Save' ?>"></p>
		<input type="hidden" name="action" value="new_record"/>
		<?php wp_nonce_field( 'new-shortcode' ); ?>
	</form>
</div>
