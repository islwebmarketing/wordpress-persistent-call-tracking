<script type="text/javascript">
	function checkVals(frm) {
		if (frm.name.value == '') {
			alert('Please enter Name/Source.');
			frm.name.focus();
			return false;
		}
		else if (frm.phn_no.value == '') {
			alert('Please enter a Phone Number.');
			frm.phn_no.focus();
			return false;
		}
		else if (frm.shortcode.value == '' || frm.shortcode.value == '-1') {
			alert('Please select a Shortcode.');
			frm.phn_no.focus();
			return false;
		}
	}
	function copyToClipboard(text) {
		window.prompt("Press Ctrl+C to copy", text);
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

	if ( $data['p_id'] > 0 ) {
		?>
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>Edit Phone Number</h2><?php
	} else {
		?><div id="icon-options-general" class="icon32"><br></div><h2>Add New Phone Number</h2><?php
	}?>
	<form class="appfreedom" method="post" action="" onsubmit="return checkVals(this);">
		<div class="form_description">
		</div>
		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row"><label class="description" for="name">Name/Source: </label></th>
				<td><input id="name" name="name" class="element text medium regular-text" type="text" maxlength="200"
				           size="60" value="<?php echo trim( $data['name'] ) ?>"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label class="description" for="phn_no">Phone Number: </label></th>
				<td><input id="phn_no" name="phn_no" class="element text medium regular-text" type="text"
				           maxlength="200" size="60" value="<?php echo trim( $data['phn_no'] ) ?>"/></td>
			</tr>
            <tr valign="top">
              <th scope="row"><label class="description" for="shortcode">Shortcode: </label></th>
              <td>
                <select name="shortcode" id="shortcode">
                <option value="-1">Select a shortcode...</option>
                <?php
                global $wpdb;

                $sql  = "SELECT DISTINCT shortcode, s_id FROM " . PERSISTENT_CALL_TRACKING_TABLE_SHORTCODES;

                $shortcodes = $wpdb->get_results( $sql, ARRAY_A );

                foreach ( $shortcodes as $shortcode ) {
                    $selected = $data['shortcode'] == $shortcode['s_id'] ? " selected='selected'" : "";

                    echo "<option value='{$shortcode['s_id']}'{$selected}>{$shortcode['shortcode']}</option>";
                }

                ?>
                </select>
                </td>
            </tr>
			<?php if ( $data['p_id'] > 0 ) {
				$twillo_url = '?src=' . $data['p_id']?>
				<tr valign="top">
					<th scope="row"><label class="description">Parameter: </label></th>
					<td><?php echo $twillo_url ?> <a href="#" onclick="copyToClipboard('<?php echo $twillo_url ?>');">
							<img title="Copy" style="vertical-align:top;" alt="Copy" src="<?php echo PLUGIN_BASE_URL ?>images/copy.png"/>
						</a>
					</td>
				</tr>
			<?php
			} ?>
            </tbody>
		</table>
		<input type="hidden" name="p_id" id="p_id" value="<?php echo $data['p_id'] ?>"/>

		<p class="submit"> &nbsp; <a class="button button-primary" href="?page=persistent-call-tracking">Cancel</a> &nbsp;
			<input type="submit" name="submit" id="saveForm" class="button button-primary"
				value="<?php echo ( $data['p_id'] > 0 ) ? 'Update' : 'Save' ?>"></p>
		<input type="hidden" name="action" value="new_record"/>
		<?php wp_nonce_field( 'new-phone' ); ?>
	</form>
</div>
