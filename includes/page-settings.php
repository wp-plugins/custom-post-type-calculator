<?php
function cptc_admin_page() {
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>Custom Post Type Calculator Settings</h2>

		<?php
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard_tab';
		if(isset($_GET['tab']))
			$active_tab = $_GET['tab'];
		?>
		<h2 class="nav-tab-wrapper">
			<a href="edit.php?post_type=items&page=cptc_admin_page&amp;tab=dashboard_tab" class="nav-tab <?php echo $active_tab == 'dashboard_tab' ? 'nav-tab-active' : ''; ?>"><?php _e('Dashboard', 'cptc'); ?></a>
			<a href="edit.php?post_type=items&page=cptc_admin_page&amp;tab=settings_tab" class="nav-tab <?php echo $active_tab == 'settings_tab' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'cptc'); ?></a>
		</h2>

		<?php if($active_tab == 'dashboard_tab') {
            echo '
			<div class="wrap">
				<div id="icon-options-general" class="icon32"></div>
				<h2><b>Custom Post Type</b> Calculator</h2>

				<div id="poststuff" class="ui-sortable meta-box-sortables">
					<div class="postbox">
						<h3>Dashboard (Help and general usage)</h3>
						<div class="inside">
							<p>Thank you for using <b>Custom Post Type</b> Calculator, a multi-purpose calculator plugin.</p>
        					<p>
                                <small>You are using <b>Custom Post Type</b> Calculator plugin version <b>' . CPTC_PLUGIN_VERSION . '</b>.</small><br>
                                <small>Dependencies: <a href="http://fontawesome.io/" rel="external">Font Awesome</a> 4.3.0</small>
                            </p>

							<h4>Help with shortcodes</h4>
							<p>
								Use the shortcode tag <code>[cpt-calculator]</code> in any post or page to show the calculator form.<br>
								Use the shortcode tag <code>[cpt-calculator category="construction"]</code> in any post or page to show the calculator form with a fixed category. Use the category <b>slug</b>.<br>
							</p>

							<h4>Help and support</h4>
							<p>Check the <a href="//getbutterfly.com/wordpress-plugins/cpt-calculator/" rel="external">official web site</a> for news, updates and general help.</p>
						</div>
					</div>
				</div>
			</div>';
		} ?>
		<?php if($active_tab == 'settings_tab') { ?>
			<?php
			if(isset($_POST['isGSSubmit'])) {
				update_option('item_admin_email', sanitize_email($_POST['item_admin_email']));

                update_option('item_calculate_button_label', sanitize_text_field($_POST['item_calculate_button_label']));
				update_option('item_surface_label', sanitize_text_field($_POST['item_surface_label']));
				update_option('item_measurement_label', sanitize_text_field($_POST['item_measurement_label']));

                update_option('item_contact_section_title', sanitize_text_field($_POST['item_contact_section_title']));
                update_option('item_contact_button_label', sanitize_text_field($_POST['item_contact_button_label']));

                update_option('item_result_label', sanitize_text_field($_POST['item_result_label']));
                update_option('item_currency', sanitize_text_field($_POST['item_currency']));

                update_option('item_show_quote', sanitize_text_field($_POST['item_show_quote']));

				echo '<div class="updated"><p>Settings updated successfully!</p></div>';
			}
			?>
			<div id="poststuff" class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h3><?php _e('Submission and Display Settings', 'cptc'); ?></h3>
					<div class="inside">
						<form method="post" action="">
							<p>
								<input type="email" name="item_admin_email" id="item_admin_email" value="<?php echo get_option('item_admin_email'); ?>" class="regular-text">
								<label for="item_admin_email">Administrator email address</label>
								<br><small>This is the administrator's email address</small>
							</p>
							<p>
								<input type="text" name="item_surface_label" id="item_surface_label" value="<?php echo get_option('item_surface_label'); ?>" class="text">
								<label for="item_surface_label">Surface label</label>
								<br><small>This is the label of the surface fields (e.g. Surface)</small>
							</p>
							<p>
								<input type="text" name="item_measurement_label" id="item_measurement_label" value="<?php echo get_option('item_measurement_label'); ?>" class="text">
								<label for="item_measurement_label">Measurement label</label>
								<br><small>This is the label of the measurement type (e.g. sqm, mp)</small>
							</p>
							<p>
								<input type="text" name="item_calculate_button_label" id="item_calculate_button_label" value="<?php echo get_option('item_calculate_button_label'); ?>" class="regular-text">
								<label for="item_calculate_button_label">Calculate button label</label>
								<br><small>This is the label of the main calculate button</small>
							</p>
							<p>
								<input type="text" name="item_result_label" id="item_result_label" value="<?php echo get_option('item_result_label'); ?>" class="regular-text">
								<label for="item_result_label">Result label</label>
								<br><small>This is the label of the result section</small>
							</p>
							<p>
								<input type="text" name="item_contact_section_title" id="item_contact_section_title" value="<?php echo get_option('item_contact_section_title'); ?>" class="regular-text">
								<label for="item_contact_section_title">Contact section title</label>
								<br><small>This is the title of the contact section</small>
							</p>
							<p>
								<input type="text" name="item_contact_button_label" id="item_contact_button_label" value="<?php echo get_option('item_contact_button_label'); ?>" class="regular-text">
								<label for="item_contact_button_label">Contact button label</label>
								<br><small>This is the label of the contact form submission button</small>
							</p>
							<p>
								<input type="text" name="item_currency" id="item_currency" value="<?php echo get_option('item_currency'); ?>" class="text">
								<label for="item_currency">Currency</label>
								<br><small>Use the three-letter code for currency (e.g. EUR, USD, CAD)</small>
							</p>
                            <p>
                                <select name="item_show_quote" id="item_show_quote">
                                    <option value="1"<?php if(get_option('item_show_quote') == 1) echo ' selected'; ?>>Show quote form</option>
                                    <option value="0"<?php if(get_option('item_show_quote') == 0) echo ' selected'; ?>>Hide quote form</option>
                                </select> <label for="item_show_quote">Show/hide the main quote form</label>
                            </p>

                            <p>
								<input type="submit" name="isGSSubmit" value="Save Changes" class="button-primary">
							</p>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>	
	<?php
}
?>
