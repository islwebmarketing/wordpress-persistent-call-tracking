<script type="text/javascript">
	//Function to allow only numbers to textbox
	function validate(key) {
		var keycode = (key.which) ? key.which : key.keyCode;
		var phn = document.getElementById('txtPhn');

		if (!(keycode == 8 || keycode == 46) && (keycode < 48 || keycode > 57)) {
			return false;
		}
		else {
			//Condition to check textbox contains ten numbers or not
			if (phn.value.length < 10) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	function checkVals(frm) {
		if (frm.cookie_expiry.value == '') {
			alert('Please enter Cookie Expiry day(s).');
			frm.cookie_expiry.focus();
			return false;
		}
		var tmpVal = parseFloat(frm.cookie_expiry.value);
		if (tmpVal <= 0 || tmpVal > 730) {
			alert('Cookie Expiry should be between 1 to 730 Days');
			frm.cookie_expiry.focus();
			return false;
		}
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

	<div id="icon-options-general" class="icon32"><br></div>
	<h2>Call Tracker Settings</h2>
	<?php $this->wp_messages( $data ); ?>
	<form class="appfreedom" method="post" action="" onsubmit="return checkVals(this);">
		<div class="form_description">
		</div>
		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row"><label class="description" for="name">Cookie Expiry: </label></th>
				<td><input id="cookie_expiry" name="cookie_expiry" onkeypress="return validate(event)"
				           class="element text medium regular-text" maxlength="4" type="text" maxlength="200" size="60"
				           value="<?php echo trim( $data['cookie_expiry'] ) ?>"/>
					<abbr title="Number days for cookie to live">Days</abbr>
					<p class="description">(Maximum: 730)</p>
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit"> &nbsp; <a class="button button-primary" href="?page=persistent-call-tracking">Cancel</a> &nbsp;
			<input type="submit" name="persistent_call_tracking_submit" id="saveForm" class="button button-primary" value="Save Changes">
		</p>
	</form>
</div>
