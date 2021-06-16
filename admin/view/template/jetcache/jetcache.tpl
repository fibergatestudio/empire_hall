<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="jetcache-top-heading">
	<div style="float:left; margin-top: 10px;" >
		<img src="<?php echo $icon; ?>" style="height: 24px; margin-left: 10px; " >
	</div>
	<div style="margin-left: 5px; float:left; margin-top: 12px;">
		<ins style="color: #fff;  font-weight: normal;  text-decoration: none; ">
		<a href="index.php?route=jetcache/jetcache&<?php echo $token_name; ?>=<?php echo $token; ?>"><?php echo strip_tags($heading_title); ?></a>
		</ins>
	</div>
	<div class="jetcache-top-copyright">
		<div style="color: #fff; font-size: 12px; margin-top: 2px; line-height: 18px; margin-left: 9px; margin-right: 9px; overflow: hidden;"><?php echo $language->get('heading_dev'); ?></div>
	</div>
</div>
<script>
	function delayer(){
	    window.location = 'index.php?route=jetcache/jetcache&<?php echo $token_name; ?>=<?php echo $token; ?>&jc_save=1';
	}
	$('.jetcache-top-heading').on('click', function() {
		 //window.location = 'https://opencartadmin.com';
	});

</script>

<?php $add_cont_row = 0; ?>
<div class="page-header">
<div class="container-fluid">
	<div id="content1" style="border: none;">
		<div style="clear: both; line-height: 1px; font-size: 1px;"></div>
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($success) { ?>
		<div class="alert alert-success success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<div id="content" style="border: none;">
			<div style="clear: both; line-height: 1px; font-size: 1px;"></div>
			<div class="box1">
				<div class="content">
					<?php
						// echo $agoo_menu;
					?>
					<div id="sticky-anchor"></div>
					<div style="margin:5px; float:right;" id="sticky">
						<a href="#" id="jc_save" class="mbutton jetcache_save"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?php echo $button_save; ?></a>
					</div>

					<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
						<div id="tabs" class="htabs">
							<a href="#tab-options" class="jc-tab"><?php echo $language->get('tab_main'); ?></a>
							<a href="#tab-pages" class="jc-tab"><?php echo $language->get('tab_pages'); ?></a>
							<a href="#tab-cont" class="jc-tab"><?php echo $language->get('tab_cont'); ?></a>
							<a href="#tab-model" class="jc-tab"><?php echo $language->get('tab_model'); ?></a>
							<a href="#tab-query" class="jc-tab"><?php echo $language->get('tab_query'); ?></a>
							<a href="#tab-exceptions" class="jc-tab"><?php echo $language->get('tab_exceptions'); ?></a>
							<a href="#tab-image" class="jc-tab"><?php echo $language->get('tab_image'); ?></a>
							<a href="#tab-minify" class="jc-tab"><?php echo $language->get('tab_minify'); ?></a>
							<a href="#tab-faq" class="jc-tab"><?php echo $language->get('tab_doc'); ?></a>
						</div>
						<div id="tab-options">
							<div id="tabs-options" class="htabs">
								<a href="#tab-options-options" class="jc-tab"><?php echo $language->get('tab_options'); ?></a>
								<a href="#tab-options-install" class="jc-tab" id="sc_install"><?php echo $language->get('entry_install_update'); ?></a>
								<a href="#tab-options-logs" class="jc-tab"><?php echo $language->get('tab_logs'); ?></a>
								<a href="#tab-options-clear" class="jc-tab"><?php echo $language->get('tab_clear'); ?></a>
								<a href="#tab-options-service" class="jc-tab"><?php echo $language->get('tab_service'); ?></a>
								<a href="#tab-options-access" class="jc-tab"><?php echo $language->get('tab_access'); ?></a>
							</div>
							<div id="tab-options-options">
								<div id="mytabs_cache">
									<div class="tabcontent" id="list_default">
										<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
											<tr>
												<td><?php echo $language->get('entry_widget_status'); ?> <?php if (SC_VERSION > 15) { ?><i class="fa fa-dot-circle-o" aria-hidden="true"></i> <?php echo strip_tags($heading_title); ?><?php } ?></td>
												<td>
													<div class="input-group">
														<select class="form-control" name="asc_jetcache_settings[jetcache_widget_status]">
															<?php if (isset($asc_jetcache_settings['jetcache_widget_status']) && $asc_jetcache_settings['jetcache_widget_status']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
														<input type="hidden" name="asc_jetcache_settings[seocms_jetcache_alter]" value="1">
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<?php echo $language->get('entry_jetcache_menu_status'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
													<div class="jetcache-table-help">
														<?php echo $language->get('entry_jetcache_menu_order');  ?>
														<div class="input-group">
															<input class="form-control template" size="11" type="text" name="asc_jetcache_settings[jetcache_menu_order]" value="<?php  if (isset($asc_jetcache_settings['jetcache_menu_order'])) echo $asc_jetcache_settings['jetcache_menu_order']; ?>" size="20" />
														</div>
													</div>
												</td>
												<td>
													<div class="input-group">
														<select class="form-control" name="asc_jetcache_settings[jetcache_menu_status]">
															<?php if (isset($asc_jetcache_settings['jetcache_menu_status']) && $asc_jetcache_settings['jetcache_menu_status']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<?php echo $language->get('entry_jetcache_info_status'); ?>
													<div class="jetcache-table-help">
														<?php echo $language->get('entry_jetcache_info_demo_status'); ?>
														<select class="form-control" name="asc_jetcache_settings[jetcache_info_demo_status]">
															<?php if (isset($asc_jetcache_settings['jetcache_info_demo_status']) && $asc_jetcache_settings['jetcache_info_demo_status']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
													</div>
													<div class="jetcache-table-help">
														<?php echo $language->get('entry_jetcache_index_status'); ?>
														<select class="form-control" name="asc_jetcache_settings[jetcache_index_status]">
															<?php if (isset($asc_jetcache_settings['jetcache_index_status']) && $asc_jetcache_settings['jetcache_index_status']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
													</div>

												</td>
												<td>
													<div class="input-group">
														<select class="form-control" name="asc_jetcache_settings[jetcache_info_status]">
															<?php if (isset($asc_jetcache_settings['jetcache_info_status']) && $asc_jetcache_settings['jetcache_info_status']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<td><?php echo $language->get('entry_cache_mobile_detect'); ?></td>
												<td>
													<div class="input-group">
														<select class="form-control" name="asc_jetcache_settings[cache_mobile_detect]">
															<?php if (isset($asc_jetcache_settings['cache_mobile_detect']) && $asc_jetcache_settings['cache_mobile_detect']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
													</div>
												</td>
											</tr>
											<tr class="jetcache-back jetcache-pro-settings">
												<td>
												<?php echo $language->get('entry_seocms_jetcache_gzip_level'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
													<div class="jetcache-table-help">
														<?php echo $language->get('entry_seocms_jetcache_gzip_level_help');  ?>
													</div>

												</td>
												<td>
													<div class="input-group">
														<select class="form-control" name="asc_jetcache_settings[seocms_jetcache_gzip_level]">
															<?php foreach ($gzip_level as $value => $name) { ?>
															<option value="<?php echo $value; ?>" <?php if (isset($asc_jetcache_settings['seocms_jetcache_gzip_level']) && $asc_jetcache_settings['seocms_jetcache_gzip_level'] == $value) { ?> selected="selected" <?php } ?>><?php echo $name; ?></option>
															<?php } ?>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<td style="width: 220px;"><?php echo $language->get('entry_jetcache_ocmod_refresh'); ?></td>
												<td>
													<div style="margin-bottom: 5px;">
														<a href="#" id="jetcache_ocmod_refresh" onclick="
															$.ajax({
															url: '<?php echo $url_ocmod_refresh; ?>',
															dataType: 'html',
															beforeSend: function()
															{
																$('#div_ocmod_refresh').html('<?php echo $language->get('text_loading_main'); ?>');
															},
															success: function(content) {
															if (content) {
																$('#div_ocmod_refresh').html('<span style=\'color:green\'><?php echo $language->get('text_ocmod_refresh_success'); ?><\/span>');
															}
															},
															error: function(content) {
																$('#div_ocmod_refresh').html('<span style=\'color:red\'><?php echo $language->get('text_ocmod_refresh_fail'); ?><\/span>');
															}
															}); return false;" class="markbuttono sc_button" style=""><?php echo $language->get('text_url_ocmod_refresh'); ?></a>
														<div id="div_ocmod_refresh"></div>
													</div>
												</td>
											</tr>
											<tr>
												<td style="width: 220px;"><?php echo $language->get('entry_jetcache_cache_remove'); ?></td>
												<td>
													<div style="margin-bottom: 5px;">
														<a id="jetcache_cache_remove" class="markbuttono sc_button"><?php echo $language->get('text_url_cache_remove'); ?></a>
														<div id="div_cache_remove"></div>
													</div>
												</td>
											</tr>

											<tr>
												<td style="width: 220px;"><?php echo $language->get('entry_jetcache_builcache_gen'); ?></td>
												<td>
													<div class="">
														<a id="buildcache_start" class="markbuttono sc_button"><i class="icon-hdd"></i> <?php echo $language->get('button_buildcache'); ?></a>
														<a id="buildcache_abort" class="markbuttono sc_button"><i class="icon-remove"></i> <?php echo $language->get('button_buildcache_abort'); ?></a>
													</div>
													<div>
														<table id="table_buildcache">
															<tr>
																<td class="checkboxHeader">
																	<input class="form-control" id="buildcache_with_products" name="asc_jetcache_settings[buildcache_with_products]" type="checkbox">
																</td>
																<td class="textHeader">
																	<label for="buildcache_with_products"><?php echo $language->get('label_buildcache_with_products'); ?></label>
																	&nbsp;&nbsp;<a href="javascript:void(0)" class="infoPopover" data-toggle="popover" data-html="true" data-title="<?php echo $language->get('label_buildcache_with_products_title_info'); ?>" data-content="<?php echo $language->get('label_buildcache_with_products_data_content'); ?>" data-placement="right" data-original-title="" title=""><i class="fa fa-exclamation-circle jetcache-red"></i></a>
																</td>
															</tr>
															<tr>
																<td class="checkboxHeader">
																	<input class="form-control" id="buildcache_with_lang" name="asc_jetcache_settings[buildcache_with_lang]" type="checkbox">
																</td>
																<td class="textHeader">
																	<label for="buildcache_with_lang"><?php echo $language->get('label_buildcache_with_lang'); ?></label>
																	&nbsp;&nbsp;<a href="javascript:void(0)" class="infoPopover" data-toggle="popover" data-html="true" data-title="<?php echo $language->get('label_buildcache_with_lang_title_info'); ?>" data-content="<?php echo $language->get('label_buildcache_with_lang_data_content'); ?>" data-placement="right" data-original-title="" title=""><i class="fa fa-exclamation-circle jetcache-red"></i></a>
																</td>
															</tr>
														</table>
														<style>
															#table_buildcache label {
															font-weight: normal;
															font-size: 13px;
															vertical-align: center;
															padding-bottom: 0px;
															margin-bottom: 0px;
															}

														</style>

														<script>
															$(document).ready(function(){
															    $('[data-toggle="popover"]').popover({
															        placement : 'right',
															        delay: { show: 20, hide: 500 },
															        trigger: 'hover'
															    });
															});
														</script>

													</div>
													<div style="width: 100%;">
														<div id="buildcache_progress" class="progress" style="width: 200px; margin-top: 10px; <?php if (SC_VERSION < 20) { ?> height: 20px; background-color: #FFF; z-index: 10000;<?php } ?>">
															<div id="buildcache_progressbar" class="progress-bar" style="width: 0%; <?php if (SC_VERSION < 20) { ?> height: 20px; background-color: #16A9DE; z-index: 10000;<?php } ?>"></div>
														</div>
														<div id="buildcache_details" style="width: 200px; margin-top: 10px;"></div>
													</div>
													<script type="text/javascript">
														$(document).ready(function() {
															$('#buildcache_details').hide();
															$('#buildcache_progress').hide();
														 jetcache.buildcache.setConfig({
														     array_url : '../index.php?route=jetcache/tool/get_buildcache_array&<?php echo $token_name; ?>=<?php echo $token; ?>',
														     message_aborted : '<?php echo $language->get('message_buildcache_aborted'); ?>',
														     message_complete : '<?php echo $language->get('message_buildcache_complete'); ?>',
														     message_processing : '<?php echo $language->get('message_buildcache_processing'); ?>',
														     message_processing_complete : '<?php echo $language->get('message_buildcache_processing_complete'); ?>',
														     progressbar_selector : '#buildcache_progressbar',
														     progress_selector : '#buildcache_progress',
														     output_selector : '#buildcache_details',
														     to_header : 'JETCACHE_BUILDCACHE'
														 });

														$('#buildcache_start').click(function() {
														 	jetcache.buildcache.setConfig({
														 		buildcache_with_products: $('#buildcache_with_products').is(':checked'),
														 		buildcache_with_lang: $('#buildcache_with_lang').is(':checked')
														 	});

														 	$('#buildcache_details').show();
														 	$('#buildcache_progress').show();
															jetcache.buildcache.start();
														});

														$('#buildcache_abort').click(function() {
														jetcache.buildcache.abort();
														});
														});
													</script>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<div id="tab-options-clear">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
									<tr>
										<td>
											<?php echo $language->get('entry_add_category'); ?> <span class="jetcache-table-help-href">?</span>
											<div class="jetcache-table-help">
												<?php echo $language->get('entry_add_category_help'); ?>
											</div>
										</td>
										<td>
											<div class="sc-flex-block">
												<div class="sc-flex-container">
													<div class="sc-flex-container-left">
														<div class="input-group">
															<select class="form-control" name="asc_jetcache_settings[add_category]">
																<?php if (isset($asc_jetcache_settings['add_category']) && $asc_jetcache_settings['add_category']) { ?>
																<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																<option value="0"><?php echo $text_disabled; ?></option>
																<?php } else { ?>
																<option value="1"><?php echo $text_enabled; ?></option>
																<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="sc-flex-container-left">
														<a href="javascript:void(0)" class="infoPopover" data-toggle="popover" data-html="true" data-title="<?php echo $language->get('label_add_category'); ?>" data-content="<?php echo $language->get('label_add_category_content'); ?>" data-placement="right" data-original-title="" title=""><i class="fa fa-exclamation-circle"></i></a>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<?php echo $language->get('entry_add_product'); ?> <span class="jetcache-table-help-href">?</span>
											<div class="jetcache-table-help">
												<?php echo $language->get('entry_add_product_help'); ?>
											</div>
										</td>
										<td>
											<div class="sc-flex-block">
												<div class="sc-flex-container">
													<div class="sc-flex-container-left">
														<div class="input-group">
															<select class="form-control" name="asc_jetcache_settings[add_product]" id="add_product">
																<?php if (isset($asc_jetcache_settings['add_product']) && $asc_jetcache_settings['add_product']) { ?>
																<option value="1" selected="selected" data-chained="0 1"><?php echo $text_enabled; ?></option>
																<option value="0" data-chained="0 1"><?php echo $text_disabled; ?></option>
																<?php } else { ?>
																<option value="1" data-chained="0 1"><?php echo $text_enabled; ?></option>
																<option value="0" selected="selected" data-chained="0 1"><?php echo $text_disabled; ?></option>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="sc-flex-container-left">
														<a href="javascript:void(0)" class="infoPopover" data-toggle="popover" data-html="true" data-title="<?php echo $language->get('label_add_product'); ?>" data-content="<?php echo $language->get('label_add_product_content'); ?>" data-placement="right" data-original-title="" title=""><i class="fa fa-exclamation-circle"></i></a>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<?php echo $language->get('entry_edit_product'); ?> <span class="jetcache-table-help-href">?</span>
											<div class="jetcache-table-help">
												<?php echo $language->get('entry_edit_product_help'); ?>
											</div>
										</td>
										<td>
											<div class="sc-flex-block">
												<div class="sc-flex-container">
													<div class="sc-flex-container-left">
														<div class="input-group">
															<select class="form-control" name="asc_jetcache_settings[edit_product]" id="edit_product">
																<?php if (isset($asc_jetcache_settings['edit_product']) && $asc_jetcache_settings['edit_product']) { ?>
																<option value="1" selected="selected" data-chained="0 1"><?php echo $text_enabled; ?></option>
																<option value="0" data-chained="0 1"><?php echo $text_disabled; ?></option>
																<?php } else { ?>
																<option value="1" data-chained="0"><?php echo $text_enabled; ?></option>
																<option value="0" selected="selected" data-chained="0 1"><?php echo $text_disabled; ?></option>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="sc-flex-container-left">
														<a href="javascript:void(0)" class="infoPopover" data-toggle="popover" data-html="true" data-title="<?php echo $language->get('label_edit_product'); ?>" data-content="<?php echo $language->get('label_edit_product_content'); ?>" data-placement="right" data-original-title="" title=""><i class="fa fa-exclamation-circle" id="edit_product-icon"></i></a>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<?php echo $language->get('entry_edit_product_id'); ?> <span class="jetcache-table-help-href">?</span>
											<div class="jetcache-table-help">
												<?php echo $language->get('entry_edit_product_id_help'); ?>
											</div>
										</td>
										<td>
											<div class="sc-flex-block">
												<div class="sc-flex-container">
													<div class="sc-flex-container-left">
														<div class="input-group">
															<select class="form-control" name="asc_jetcache_settings[edit_product_id]" id="edit_product_id">
																<?php if (isset($asc_jetcache_settings['edit_product_id']) && $asc_jetcache_settings['edit_product_id']) { ?>
																<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																<option value="0"><?php echo $text_disabled; ?></option>
																<?php } else { ?>
																<option value="1"><?php echo $text_enabled; ?></option>
																<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="sc-flex-container-left">
														<a href="javascript:void(0)" class="infoPopover" data-toggle="popover" data-html="true" data-title="<?php echo $language->get('label_edit_product_id'); ?>" data-content="<?php echo $language->get('label_edit_product_id_content'); ?>" data-placement="right" data-original-title="" title=""><i class="fa fa-exclamation-circle jetcache-red" id="edit_product_id-icon"></i></a>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_cache_auto_clear');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control" size="11" type="text" name="asc_jetcache_settings[cache_auto_clear]" value="<?php  if (isset($asc_jetcache_settings['cache_auto_clear'])) echo $asc_jetcache_settings['cache_auto_clear']; ?>" size="20" />
											</div>
										</td>
									</tr>

									<tr>
										<td class="left"><?php echo $language->get('entry_cart_interval');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control" size="11" type="text" name="asc_jetcache_settings[cart_interval]" value="<?php  if (isset($asc_jetcache_settings['cart_interval'])) echo $asc_jetcache_settings['cart_interval']; ?>" size="20" />
											</div>
										</td>
									</tr>

									<tr>
										<td class="left"><?php echo $language->get('entry_cache_expire');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control template" size="11" type="text" name="asc_jetcache_settings[cache_expire]" value="<?php  if (isset($asc_jetcache_settings['cache_expire'])) echo $asc_jetcache_settings['cache_expire']; ?>" size="20" />
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_cache_max_files');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control template" size="11" type="text" name="asc_jetcache_settings[cache_max_files]" value="<?php  if (isset($asc_jetcache_settings['cache_max_files'])) echo $asc_jetcache_settings['cache_max_files']; ?>" size="20" />
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_cache_maxfile_length');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control template" size="11" type="text" name="asc_jetcache_settings[cache_maxfile_length]" value="<?php  if (isset($asc_jetcache_settings['cache_maxfile_length'])) echo $asc_jetcache_settings['cache_maxfile_length']; ?>" size="20" />
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_cache_max_hache_folders_level');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control template" size="11" type="text" name="asc_jetcache_settings[cache_max_hache_folders_level]" value="<?php  if (isset($asc_jetcache_settings['cache_max_hache_folders_level'])) echo $asc_jetcache_settings['cache_max_hache_folders_level']; ?>" size="4" />
											</div>
										</td>
									</tr>


								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_ex_key'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="odd">
									<td class="left">
										<?php echo $language->get('text_ex_key'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[ex_key]" rows="5" style="width: 100%;"><?php if (isset($asc_jetcache_settings['ex_key'])) { echo $asc_jetcache_settings['ex_key']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>
									<tr>
										<td class="jetcache-table-help left"></td>
										<td></td>
									</tr>



								</table>
							</div>


							<div id="tab-options-service">

							<div style="display: flex;
										flex-direction: column;
							  			align-items: flex-start;
										flex-wrap: wrap;
										align-content: space-between;
										justify-content: space-between;
										width: 100%;
										margin-top: 4px;
										margin-bottom: 11px;">

								<div style="display: flex; align-items: center; margin-bottom: 11px;">
								   <div style="width: 160px;">
								   	 <?php echo $language->get('entry_jc_backup'); ?>&nbsp;
								   </div>
								   <div class="input-group">
										<a href="#" id="jc_backup" onclick="
										$.ajax({
										url: '<?php echo $url_backup; ?>&jc_backup=1',
										dataType: 'html',
										beforeSend: function()
										{
											$('#div_jc_backup').html('<?php echo $language->get('text_loading_main'); ?>').show();
										},
										success: function(content) {
											if (content) {
												content_array = JSON.parse(content);
												$('#div_jc_backup').html(content_array['text']);
												setTimeout(hide_messages, 3000, '#div_jc_backup');
												if (content_array['success']) {
													window.location = '<?php echo $url_backup; ?>';
												}
											}
										},
										error: function(content) {
											$('#div_jc_backup').html('<span style=\'color:red\'><?php echo $language->get('text_jc_backup_fail'); ?><\/span>');
										}
										}); return false;" class="markbuttono sc_button" style=""><i class="fa fa-download" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $language->get('text_jc_url_backup'); ?></a>
										<div id="div_jc_backup">&nbsp;</div>
								   </div>
								</div>

								<div style="display: flex; align-items: center; margin-bottom: 11px;">
								   <div style="width: 160px;">
								   	 <?php echo $language->get('entry_jc_restore'); ?>&nbsp;&nbsp;
								   </div>
								   <div class="input-group">
										<a id="jc_restore" class="markbuttono sc_button"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $language->get('text_jc_url_restore'); ?></a>
										<div id="div_jc_restore">&nbsp;</div>
								   </div>
								</div>
</div>

<script>
$('#jc_restore').on('click', function() {
	$('#jc-form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="jc-form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#jc-form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}
    timer = setInterval(function() {
		if ($('#jc-form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			$.ajax({
				url: '<?php echo $url_restore; ?>',
				type: 'post',
				data: new FormData($('#jc-form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$('#div_jc_restore').html('<?php echo $language->get('text_loading_main_without'); ?>').show();
				},
				success: function(content) {
					if (content) {
						content_array = JSON.parse(content);
						$('#div_jc_restore').html(content_array['text']);
						setTimeout(hide_messages, 3000, '#div_jc_backup');
						if (content_array['success']) {
	                    	$('#jetcache_cache_remove').trigger('click');
	                    	window.location = '<?php echo $url_jetcache; ?>&jc_restore=1';
						}
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					$('#div_jc_restore').html('<span style="color:red"><?php echo $language->get('text_jc_backup_fail'); ?></span><br>' + thrownError + '<br>' + xhr.statusText + '<br>' + xhr.responseText);
				}
			});
		}
	}, 500);

});

</script>

<script>
	function hide_messages(sel) {
		$(sel).html('&nbsp;');
	}
</script>



							</div>


							<div id="tab-options-install">
								<div style="margin-bottom: 5px;">
									<a href="#" id="sc_install_common" onclick="
										$.ajax({
										url: '<?php echo $url_create; ?>',
										dataType: 'html',
										beforeSend: function()
										{
											$('#install_ocmod').html('<?php echo $language->get('text_loading_main'); ?>');
										},
										success: function(json) {
										$('#install_ocmod').html(json);
											setTimeout('delayer()', 2000);
										},
										error: function(json) {
											$('#install_ocmod').html('error');
										}
										}); return false;" class="markbuttono widthbutton sc_button asc_blinking" style=""><?php echo $url_create_text; ?></a>
								</div>
								<div id="install_ocmod" style="color: green;"></div>
								<div id="div_ocmod_refresh_install"></div>
							</div>



							<div id="tab-options-access">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
									<tr>
										<td><?php
											echo $language->get('entry_store');
											?></td>
										<td>
											<div class="scrollbox">
												<?php $class = 'even'; ?>
												<div class="<?php echo $class; ?>">
													<?php if (!isset($asc_jetcache_settings['store']) || in_array(0, $asc_jetcache_settings['store'])) { ?>
													<input type="checkbox" name="asc_jetcache_settings[store][]" value="0" checked="checked" />
													<?php echo $language->get('text_default_store'); ?>
													<?php } else { ?>
													<input type="checkbox" name="asc_jetcache_settings[store][]" value="0" />
													<?php echo $language->get('text_default_store'); ?>
													<?php } ?>
												</div>
												<?php foreach ($stores as $store) { ?>
												<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
												<div class="<?php echo $class; ?>">
													<?php if (isset($asc_jetcache_settings['store']) && in_array($store['store_id'], $asc_jetcache_settings['store'])) { ?>
													<input type="checkbox" name="asc_jetcache_settings[store][]" value="<?php echo $store['store_id']; ?>" checked="checked" />
													<?php echo $store['name']; ?>
													<?php } else { ?>
													<input type="checkbox" name="asc_jetcache_settings[store][]" value="<?php echo $store['store_id']; ?>" <?php if (!isset($asc_jetcache_settings['store'])) { ?> checked="checked" <?php } ?>/>
													<?php echo $store['name']; ?>
													<?php } ?>
												</div>
												<?php } ?>
											</div>
											<a onclick="$(this).parent().find(':checkbox').prop('checked', true);" class="nohref"><?php echo $language->get('text_select_all'); ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);" class="nohref"><?php echo $language->get('text_unselect_all'); ?></a>
										</td>
										</td>
									</tr>
								</table>
							</div>



							<div id="tab-options-logs">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">

									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_query_log_settings'); ?> <span class="jetcache-table-help-href">?</span>
										</td>
									<tr colspan="2" class="jetcache-back jetcache-text-center jetcache-table-help">
										<td colspan="2" class="jetcache-back jetcache-text-center"><?php echo $language->get('entry_query_log_settings_help'); ?></td>
									</tr>

									<tr>
										<td><?php echo $language->get('entry_query_log_status'); ?></td>
										<td>
											<div class="input-group">
												<div class="sc-flex-container">
													<div class="sc-flex-container-left">
														<select class="form-control" name="asc_jetcache_settings[query_log_status]" id="id-query-log-status">
															<?php if (isset($asc_jetcache_settings['query_log_status']) && $asc_jetcache_settings['query_log_status']) { ?>
															<option value="1" selected="selected" data-chained="1"><?php echo $text_enabled; ?></option>
															<option value="0" data-chained="0 1"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1" data-chained="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected" data-chained="0 1"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
													</div>
													<div class="sc-flex-container-left">
														&nbsp;&nbsp;<a href="javascript:void(0)" class="infoPopover" data-toggle="popover" data-html="true" data-title="<?php echo $language->get('entry_query_log_status_title'); ?>" data-content='<?php echo $language->get('entry_query_log_status_content'); ?>' data-placement="right" data-original-title="" title=""><i class="fa fa-exclamation-circle jetcache-orange" id="id-query-log-status-icon"></i></a>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_query_log_maxtime');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control template" size="6" type="text" name="asc_jetcache_settings[query_log_maxtime]" value="<?php  if (isset($asc_jetcache_settings['query_log_maxtime'])) echo $asc_jetcache_settings['query_log_maxtime']; ?>">
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_query_log_file');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control template" size="30" type="text" name="asc_jetcache_settings[query_log_file]" value="<?php  if (isset($asc_jetcache_settings['query_log_file'])) echo $asc_jetcache_settings['query_log_file']; ?>">
											</div>
											<div class="sc-flex-container">
												<div class="sc-flex-container-left">
													<div style="margin-top: 4px;">
														<a href="#" onclick="
															$.ajax({
															url: '<?php echo $url_query_file_view; ?>',
															dataType: 'html',
															beforeSend: function()
															{
															$('#id_query_file_view').html('<?php echo $language->get('text_loading_main'); ?>');
															},
															success: function(html) {
															$('#id_query_file_view').html('');
															$('.modal-body').html(html);
															var if_offset = $('#id-modal-file-view .modal-content').offset();
															var if_height = $(window).height() - if_offset.top - 200;
															$('#id-modal-file-view .modal-body').css('min-height', if_height + 'px');
															$('#id-modal-file-view').modal({show:true});
															},
															error: function(json) {
															$('#id_query_file_view').html('error');
															}
															}); return false;" class="markbutton nohref" style=""><?php echo $entry_log_file_view; ?></a>
													</div>
												</div>
												<script>
													function id_query_hide() {
														$('#id_query_file_unlink').html('');
													}
												</script>
												<div class="sc-flex-container-left">
													<div style="margin-left: 8px;margin-top: 4px;">
														<a href="#" onclick="
															function id_query_hide() {
															$('#id_query_file_unlink').html('');
															}
															$.ajax({
															url: '<?php echo $url_query_file_unlink; ?>',
															dataType: 'html',
															beforeSend: function()
															{
															$('#id_query_file_unlink').html('<?php echo $language->get('text_loading_main'); ?>');
															},
															success: function(json) {
															setTimeout(id_query_hide, 2000);
															$('#id_query_file_unlink').html(json);
															},
															error: function(json) {
															$('#id_query_file_unlink').html('error');
															}
															}); return false;" class="mbutton button_purple sc_button" style=""><?php echo $entry_log_file_unlink; ?></a>
													</div>
												</div>
											</div>
											<div id="id_query_file_view"></div>
											<div id="id_query_file_unlink"></div>
										</td>
									</tr>


<!-- ******************** Log controller ******************** -->

									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_cont_log_settings'); ?> <span class="jetcache-table-help-href">?</span>
										</td>
									<tr colspan="2" class="jetcache-back jetcache-text-center jetcache-table-help">
										<td colspan="2" class="jetcache-back jetcache-text-center"><?php echo $language->get('entry_cont_log_settings_help'); ?></td>
									</tr>
									</tr>
									<tr>
										<td><?php echo $language->get('entry_cont_log_status'); ?></td>
										<td>
											<div class="input-group">
												<div class="sc-flex-container">
													<div class="sc-flex-container-left">
														<select class="form-control" name="asc_jetcache_settings[cont_log_status]" id="id-cont-log-status">
															<?php if (isset($asc_jetcache_settings['cont_log_status']) && $asc_jetcache_settings['cont_log_status']) { ?>
															<option value="1" selected="selected" data-chained="1"><?php echo $text_enabled; ?></option>
															<option value="0" data-chained="0 1"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1" data-chained="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected" data-chained="0 1"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
													</div>
													<div class="sc-flex-container-left">
														&nbsp;&nbsp;<a href="javascript:void(0)" class="infoPopover" data-toggle="popover" data-html="true" data-title="<?php echo $language->get('entry_cont_log_status_title'); ?>" data-content='<?php echo $language->get('entry_cont_log_status_content'); ?>' data-placement="right" data-original-title="" title=""><i class="fa fa-exclamation-circle jetcache-green" id="id-cont-log-status-icon"></i></a>
													</div>
												</div>
											</div>
										</td>
									</tr>

									<tr class="jetcache-pro-settings">
										<td><?php echo $language->get('entry_cont_log_crop'); ?></td>
										<td>
											<div class="input-group">
												<div class="sc-flex-container">
													<div class="sc-flex-container-left">
														<select class="form-control" name="asc_jetcache_settings[cont_log_crop]" id="id-cont-log-status">
															<?php if (isset($asc_jetcache_settings['cont_log_crop']) && $asc_jetcache_settings['cont_log_crop']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1" <?php if (!isset($asc_jetcache_settings['cont_log_crop'])) { ?>selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
															<option value="0" <?php if (isset($asc_jetcache_settings['cont_log_crop']) && !$asc_jetcache_settings['cont_log_crop']) { ?>selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
													</div>
												</div>
											</div>
										</td>
									</tr>



									<tr>
										<td class="left"><?php echo $language->get('entry_cont_log_maxtime');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control template" size="6" type="text" name="asc_jetcache_settings[cont_log_maxtime]" value="<?php  if (isset($asc_jetcache_settings['cont_log_maxtime'])) echo $asc_jetcache_settings['cont_log_maxtime']; ?>">
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_cont_log_file');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control template" size="30" type="text" name="asc_jetcache_settings[cont_log_file]" value="<?php  if (isset($asc_jetcache_settings['cont_log_file'])) echo $asc_jetcache_settings['cont_log_file']; ?>">
											</div>
											<div class="sc-flex-container">
												<div class="sc-flex-container-left">
													<div style="margin-top: 4px;">
														<a href="#" onclick="
															$.ajax({
															url: '<?php echo $url_cont_file_view; ?>',
															dataType: 'html',
															beforeSend: function()
															{
															$('#id_cont_file_view').html('<?php echo $language->get('text_loading_main'); ?>');
															},
															success: function(html) {
															$('#id_cont_file_view').html('');
															$('.modal-body').html(html);
															var if_offset = $('#id-modal-file-view .modal-content').offset();
															var if_height = $(window).height() - if_offset.top - 200;
															$('#id-modal-file-view .modal-body').css('min-height', if_height + 'px');
															$('#id-modal-file-view').modal({show:true});
															},
															error: function(json) {
															$('#id_cont_file_view').html('error');
															}
															}); return false;" class="markbutton nohref" style=""><?php echo $entry_log_file_view; ?></a>
													</div>
												</div>
												<script>
													function id_cont_hide() {
														$('#id_cont_file_unlink').html('');
													}
												</script>
												<div class="sc-flex-container-left">
													<div style="margin-left: 8px;margin-top: 4px;">
														<a href="#" onclick="
															function id_query_hide() {
															$('#id_cont_file_unlink').html('');
															}
															$.ajax({
															url: '<?php echo $url_cont_file_unlink; ?>',
															dataType: 'html',
															beforeSend: function()
															{
															$('#id_cont_file_unlink').html('<?php echo $language->get('text_loading_main'); ?>');
															},
															success: function(json) {
															setTimeout(id_cont_hide, 2000);
															$('#id_cont_file_unlink').html(json);
															},
															error: function(json) {
															$('#id_cont_file_unlink').html('error');
															}
															}); return false;" class="mbutton button_purple sc_button" style=""><?php echo $entry_log_file_unlink; ?></a>
													</div>
												</div>
											</div>
											<div id="id_cont_file_view"></div>
											<div id="id_cont_file_unlink"></div>
										</td>
									</tr>
<!-- ******************************************************************************************* -->
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_session_log_settings'); ?> <span class="jetcache-table-help-href">?</span>
										</td>
									<tr colspan="2" class="jetcache-back jetcache-text-center jetcache-table-help">
										<td colspan="2" class="jetcache-back jetcache-text-center"><?php echo $language->get('entry_session_log_settings_help'); ?></td>
									</tr>
									</tr>
									<tr>
										<td><?php echo $language->get('entry_session_log'); ?></td>
										<td>
											<div class="input-group">
												<select class="form-control" name="asc_jetcache_settings[session_log_status]">
													<?php if (isset($asc_jetcache_settings['session_log_status']) && $asc_jetcache_settings['session_log_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_session_log_file');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control template" size="30" type="text" name="asc_jetcache_settings[session_log_file]" value="<?php  if (isset($asc_jetcache_settings['session_log_file'])) echo $asc_jetcache_settings['session_log_file']; ?>">
											</div>
											<div class="sc-flex-container">
												<div class="sc-flex-container-left">
													<div style="margin-top: 4px;">
														<a href="#" onclick="
															$.ajax({
															url: '<?php echo $url_session_file_view; ?>',
															dataType: 'html',
															beforeSend: function()
															{
															$('#id_session_file_view').html('<?php echo $language->get('text_loading_main'); ?>');
															},
															success: function(html) {
															$('#id_session_file_view').html('');
															$('.modal-body').html(html);
															var if_offset = $('#id-modal-file-view .modal-content').offset();
															var if_height = $(window).height() - if_offset.top - 200;
															$('#id-modal-file-view .modal-body').css('min-height', if_height + 'px');
															$('#id-modal-file-view').modal({show:true});
															},
															error: function(json) {
															$('#id_session_file_view').html('error');
															}
															}); return false;" class="markbutton nohref" style=""><?php echo $entry_log_file_view; ?></a>
													</div>
												</div>
												<div class="sc-flex-container-left">
													<script>
														function id_session_hide() {
															$('#id_session_file_unlink').html('');
														}
													</script>
													<div style="margin-left: 8px; margin-top: 4px;">
														<a href="#" onclick="
															$.ajax({
															url: '<?php echo $url_session_file_unlink; ?>',
															dataType: 'html',
															beforeSend: function()
															{
															$('#id_session_file_unlink').html('<?php echo $language->get('text_loading_main'); ?>');
															},
															success: function(json) {
															$('#id_session_file_unlink').html(json);
															setTimeout(id_session_hide, 2000);
															},
															error: function(json) {
															$('#id_session_file_unlink').html('error');
															}
															}); return false;" class="mbutton button_purple sc_button" style=""><?php echo $entry_log_file_unlink; ?></a>
													</div>
												</div>
											</div>
											<div id="id_session_file_view"></div>
											<div id="id_session_file_unlink"></div>
										</td>
									</tr>


								</table>
							</div>

                        </div>

						<div id="tab-exceptions">
							<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_ex_routes'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_ex_routes_help'); ?>
									</td>
									<td>
										<div style="float: left;">
											<table id="ex_routes" class="list jetcache-table-add">
												<thead>
													<tr>
														<td class="left"><?php echo $language->get('entry_id'); ?></td>
														<td><?php echo $language->get('entry_ex_route'); ?></td>
														<td><?php echo $language->get('entry_status'); ?></td>
														<td></td>
													</tr>
												</thead>
												<?php if (isset($asc_jetcache_settings['ex_route']) && !empty($asc_jetcache_settings['ex_route'])) { ?>
												<?php foreach ($asc_jetcache_settings['ex_route'] as $ex_route_id => $ex_route) { ?>
												<?php $ex_route_row = $ex_route_id; ?>
												<tbody id="ex_route_row<?php echo $ex_route_row; ?>">
													<tr>
														<td class="left">
															<input type="text" name="asc_jetcache_settings[ex_route][<?php echo $ex_route_id; ?>][type_id]" value="<?php if (isset($ex_route['type_id'])) echo $ex_route['type_id']; ?>" size="3">
														</td>
														<td class="right">
															<div style="margin-bottom: 3px;">
																<input type="text" name="asc_jetcache_settings[ex_route][<?php echo $ex_route_id; ?>][route]" value="<?php if (isset($ex_route['route'])) echo $ex_route['route']; ?>" style="width: 300px;">
															</div>
														</td>
														<td class="right">
															<div class="input-group jetcache-text-center">
																<select class="form-control" name="asc_jetcache_settings[ex_route][<?php echo $ex_route_id; ?>][status]">
																	<?php if (isset($ex_route['status']) && $ex_route['status']) { ?>
																	<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																	<option value="0"><?php echo $text_disabled; ?></option>
																	<?php } else { ?>
																	<option value="1"><?php echo $text_enabled; ?></option>
																	<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																	<?php } ?>
																</select>
															</div>
														</td>
														<td class="left"><a onclick="$('#ex_route_row<?php echo $ex_route_row; ?>').remove();" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>
													</tr>
												</tbody>
												<?php } ?>
												<?php } ?>
												<tfoot>
													<tr>
														<td colspan="3"></td>
														<td class="left"><a onclick="addExRoute();" class="markbutton nohref"><?php echo $language->get('entry_add_rule'); ?></a></td>
													</tr>
												</tfoot>
											</table>
										</div>
									</td>
								</tr>
								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_ex_pages'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_ex_pages_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[ex_uri]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['ex_uri'])) { echo $asc_jetcache_settings['ex_uri']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>

								<tr class="jetcache-back ex_session">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_ex_session'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="odd ex_session">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_ex_session_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[ex_session]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['ex_session'])) { echo $asc_jetcache_settings['ex_session']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>



								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_ex_session_black_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="odd">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_ex_session_black_status_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
														<select id="ex_session_black_status" class="form-control" name="asc_jetcache_settings[ex_session_black_status]">
															<?php if (isset($asc_jetcache_settings['ex_session_black_status']) && $asc_jetcache_settings['ex_session_black_status']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
															<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
															<?php } ?>
														</select>
										</div>
									</td>
								</tr>


                                <script>
                                 function ex_session_black_status_change() {
                                 	if ($('#ex_session_black_status').find('option:selected').val() == 1) {
                                 		$('.ex_session').hide(300);
                                 		$('.ex_session_black').show(300);
                                 	} else {
                                 		$('.ex_session').show(300);
                                 		$('.ex_session_black').hide(300);
                                 	}
                                 }

								$('#ex_session_black_status')
								  .change(function () {
									ex_session_black_status_change();
								  });

                                 ex_session_black_status_change();

                                </script>



								<tr class="jetcache-back ex_session_black">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_ex_session_black'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="odd ex_session_black">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_ex_session_black_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[ex_session_black]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['ex_session_black'])) { echo $asc_jetcache_settings['ex_session_black']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>


								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_ex_cookie'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="odd">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_ex_cookie_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[ex_cookie_black]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['ex_cookie_black'])) { echo $asc_jetcache_settings['ex_cookie_black']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>


								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_ex_get'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="odd">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_ex_get_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[ex_get]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['ex_get'])) { echo $asc_jetcache_settings['ex_get']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>






							</table>
						</div>

						<div id="tab-pages">
							<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help"><?php echo $language->get('entry_pages_status_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[pages_status]">
												<?php if (isset($asc_jetcache_settings['pages_status']) && $asc_jetcache_settings['pages_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>

								<tr class="jetcache-back jetcache-pro-settings">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_db_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="jetcache-pro-settings">
									<td class="jetcache-table-help"><?php echo $language->get('entry_pages_db_status_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[pages_db_status]">
												<?php if (isset($asc_jetcache_settings['pages_db_status']) && $asc_jetcache_settings['pages_db_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>

								<tr class="jetcache-back jetcache-pro-settings">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_pages_forsage'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="jetcache-pro-settings">
									<td class="jetcache-table-help"><?php echo $language->get('entry_pages_forsage_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[pages_forsage]">
												<?php if (isset($asc_jetcache_settings['pages_forsage']) && $asc_jetcache_settings['pages_forsage']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>


								<tr class="jetcache-back jetcache-pro-settings">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_lastmod_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="jetcache-pro-settings">
									<td class="jetcache-table-help"><?php echo $language->get('entry_lastmod_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<div class="sc-flex-container">
												<div class="sc-flex-container-left">
													<select class="form-control" name="asc_jetcache_settings[lastmod_status]">
														<?php if (isset($asc_jetcache_settings['lastmod_status']) && $asc_jetcache_settings['lastmod_status']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
														<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
														<?php } ?>
													</select>
												</div>
												<div class="sc-flex-container-left popover-lastmod">
														<style>

														.popover-lastmod .popover {
															max-width: 600px;
															width: 500px;
														}


														</style>


													&nbsp;&nbsp;<a href="javascript:void(0)" class="infoPopover" data-toggle="popover" data-html="true" data-title="<?php echo $language->get('entry_lastmod_status'); ?>" data-content='<?php echo $language->get('entry_lastmod_help'); ?>' data-placement="right" data-original-title="" title=""><i class="fa fa-exclamation-circle jetcache-green"></i></a>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr class="jetcache-back jetcache-pro-settings">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_cachecontrol_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="jetcache-pro-settings">
									<td class="jetcache-table-help"><?php echo $language->get('entry_cachecontrol_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[cachecontrol_status]">
												<?php if (isset($asc_jetcache_settings['cachecontrol_status']) && $asc_jetcache_settings['cachecontrol_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>
								<tr class="jetcache-back jetcache-pro-settings">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_expires_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="jetcache-pro-settings">
									<td class="jetcache-table-help"><?php echo $language->get('entry_expires_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[expires_status]">
												<?php if (isset($asc_jetcache_settings['expires_status']) && $asc_jetcache_settings['expires_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help left"></td>
									<td></td>
								</tr>
							</table>
						</div>

						<div id="tab-cont">

							<div id="tabs-controllers" class="htabs">
								<a href="#tab-cont-options" class="jc-tab"><?php echo $language->get('tab_options'); ?></a>
								<?php if (SC_VERSION < 30) { ?>
								<a href="#tab-cont-categories" class="jc-tab" id="sc_install"><?php echo $language->get('entry_tab_cont_categories'); ?></a>
								<?php } ?>
								<a href="#tab-cont-ajax" class="jc-tab"><?php echo $language->get('entry_tab_cont_ajax'); ?></a>
							</div>

							<div id="tab-cont-options">

							<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help"><?php echo $language->get('entry_cont_status_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[cont_status]" id="id-cont-status">
												<?php if (isset($asc_jetcache_settings['cont_status']) && $asc_jetcache_settings['cont_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>
								<tr class="jetcache-back jetcache-pro-settings">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_db_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="jetcache-pro-settings">
									<td class="jetcache-table-help"><?php echo $language->get('entry_cont_db_status_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[cont_db_status]">
												<?php if (isset($asc_jetcache_settings['cont_db_status']) && $asc_jetcache_settings['cont_db_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>
								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_add_conts'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_add_conts_help'); ?>
									</td>
									<td>
										<div style="float: left;">
											<table id="add_conts" class="list jetcache-table-add">
												<thead>
													<tr>
														<td class="left"><?php echo $language->get('entry_id'); ?></td>
														<td><?php echo $language->get('entry_add_cont'); ?></td>
														<td><?php echo $language->get('entry_no_vars_cont'); ?></td>
														<td style="text-align: center;"><?php echo $language->get('entry_status'); ?></td>
														<td style="text-align: center;"><?php echo $language->get('entry_action'); ?></td>
													</tr>
												</thead>
												<?php if (isset($asc_jetcache_settings['add_cont']) && !empty($asc_jetcache_settings['add_cont'])) { ?>
												<?php
													foreach ($asc_jetcache_settings['add_cont'] as $add_cont_id => $add_cont) {
													?>
												<?php $add_cont_row = (int)$add_cont_id; ?>
												<tbody id="add_cont_row<?php echo $add_cont_row; ?>">
													<tr>
														<td class="left">
															<input type="text" name="asc_jetcache_settings[add_cont][<?php echo $add_cont_id; ?>][type_id]" value="<?php if (isset($add_cont['type_id'])) echo $add_cont['type_id']; ?>" size="3">
														</td>
														<td class="right">
															<div style="margin-bottom: 3px;">
																<input type="text" name="asc_jetcache_settings[add_cont][<?php echo $add_cont_id; ?>][cont]" value="<?php if (isset($add_cont['cont'])) echo $add_cont['cont']; ?>" style="width: 300px;">
															</div>
														</td>
														<td class="right">

															<div class="input-group jetcache-text-center">
																<div class="input-group">
																	<textarea class="form-control" style="height: 39px;" name="asc_jetcache_settings[add_cont][<?php echo $add_cont_id; ?>][only_get]" rows="1" cols="9"><?php if (isset($add_cont['only_get'])) { echo $add_cont['only_get']; } else { echo ''; } ?></textarea>
																</div>
															</div>

															<div class="input-group jetcache-text-center">
																<select class="form-control" name="asc_jetcache_settings[add_cont][<?php echo $add_cont_id; ?>][no_getpost]">
																	<?php if (isset($add_cont['no_getpost']) && $add_cont['no_getpost']) { ?>
																	<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																	<option value="0"><?php echo $text_disabled; ?></option>
																	<?php } else { ?>
																	<option value="1"><?php echo $text_enabled; ?></option>
																	<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																	<?php } ?>
																</select>
															</div>

															<div class="input-group jetcache-text-center" style="margin-bottom: 3px;">
																<select class="form-control" name="asc_jetcache_settings[add_cont][<?php echo $add_cont_id; ?>][no_session]">
																	<?php if (isset($add_cont['no_session']) && $add_cont['no_session']) { ?>
																	<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																	<option value="0"><?php echo $text_disabled; ?></option>
																	<?php } else { ?>
																	<option value="1"><?php echo $text_enabled; ?></option>
																	<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																	<?php } ?>
																</select>
															</div>
															<div class="input-group jetcache-text-center">
																<select class="form-control" name="asc_jetcache_settings[add_cont][<?php echo $add_cont_id; ?>][no_url]">
																	<?php if (isset($add_cont['no_url']) && $add_cont['no_url']) { ?>
																	<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																	<option value="0"><?php echo $text_disabled; ?></option>
																	<?php } else { ?>
																	<option value="1"><?php echo $text_enabled; ?></option>
																	<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																	<?php } ?>
																</select>
															</div>
															<div class="input-group jetcache-text-center">
																<select class="form-control" name="asc_jetcache_settings[add_cont][<?php echo $add_cont_id; ?>][no_route]">
																	<?php if (isset($add_cont['no_route']) && $add_cont['no_route']) { ?>
																	<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																	<option value="0"><?php echo $text_disabled; ?></option>
																	<?php } else { ?>
																	<option value="1"><?php echo $text_enabled; ?></option>
																	<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																	<?php } ?>
																</select>
															</div>
														</td>
														<td class="right">
															<div class="input-group jetcache-text-center">
																<select class="form-control" name="asc_jetcache_settings[add_cont][<?php echo $add_cont_id; ?>][status]">
																	<?php if (isset($add_cont['status']) && $add_cont['status']) { ?>
																	<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																	<option value="0"><?php echo $text_disabled; ?></option>
																	<?php } else { ?>
																	<option value="1"><?php echo $text_enabled; ?></option>
																	<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																	<?php } ?>
																</select>
															</div>
														</td>
														<td class="left"><a onclick="$('#add_cont_row<?php echo $add_cont_row; ?>').remove();" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>
													</tr>
												</tbody>
												<?php } ?>
												<?php } ?>
												<tfoot>
													<tr>
														<td colspan="4"></td>
														<td class="left"><a onclick="addAddCont();" class="markbutton nohref"><?php echo $language->get('entry_add_rule'); ?></a></td>
													</tr>
												</tfoot>
											</table>
										</div>
									</td>
								</tr>


							</table>

						    </div>

                           <?php if (SC_VERSION < 30) { ?>

						    <div id="tab-cont-categories">
						      <table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">

								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_header_categories_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help"><?php echo $language->get('entry_header_categories_status_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[header_categories_status]" id="id-header-categories-status">
												<?php if (isset($asc_jetcache_settings['header_categories_status']) && $asc_jetcache_settings['header_categories_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>

						     </table>

						    </div>
                            <?php } ?>
						    <div id="tab-cont-ajax">

						      <table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">

								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_cont_ajax_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>

								<tr>
									<td class="jetcache-text-center">
										<div class="jetcache-table-help"><?php echo $language->get('entry_cont_ajax_status_help'); ?></div>
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[cont_ajax_status]" id="id-cont-ajax-status">
												<?php if (isset($asc_jetcache_settings['cont_ajax_status']) && $asc_jetcache_settings['cont_ajax_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>

								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_cont_ajax_delay'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>

								<tr>
									<td class="jetcache-text-center">
										<div class="jetcache-table-help"><?php echo $language->get('entry_cont_ajax_delay_help'); ?></div>
										<div class="input-group jetcache-text-center">

                                         	<input class="form-control template" type="text" name="asc_jetcache_settings[cont_ajax_delay]" value="<?php  if (isset($asc_jetcache_settings['cont_ajax_delay'])) echo $asc_jetcache_settings['cont_ajax_delay']; ?>" size="20" />

										</div>
									</td>
								</tr>


              <tr class="jetcache-back">

              <td>
	<style>
	.flex-box {
		display: flex;
		align-items: center;
		align-content: stretch;
		justify-content: space-between;
	}
	.flex-box > div {
		 width: 20%;
	}
	</style>

				<div class="flex-box" style="margin-top: 6px; text-align: center; font-weight: 500; color: #000; background-color: #EFEFEF;">
					<div style="text-align: center; width: 40%;">
                       <?php echo $language->get('text_cont_ajax_route'); ?>
	                </div>

					<div style="text-align: center; width: 15%;">
                    	<?php echo $language->get('text_cont_ajax_status'); ?>
	                </div>

					<div style="text-align: center; width: 15%;">
	                	<?php echo $language->get('text_cont_ajax_scripts'); ?>
	                </div>
					<div style="text-align: center; width: 10%;">
	                	<?php echo $language->get('text_cont_ajax_delay'); ?>
	                </div>

					<div style="text-align: center; width: 10%;">
	                	<?php echo $language->get('text_cont_ajax_md5'); ?>
	                </div>
					<div style="text-align: center; width: 10%;">
	                	<?php echo $language->get('text_cont_ajax_action'); ?>
	                </div>


                </div>


			<div id="ajaxroutes" style="clear: both; width: 100%">


              <?php
	            $ajaxroutes_row = 0;

                if (!empty($asc_jetcache_settings['cont_ajax_routes'])) {
                	foreach ($asc_jetcache_settings['cont_ajax_routes'] as $jc_num => $ajaxroutes) {
              ?>
				<div id="ajaxroutes-<?php echo $ajaxroutes_row; ?>" class="flex-box" style="text-align: center;">
					<div style="text-align: center; padding: 8px; width: 40%;">
						<div class="input-group">
							<input type="text" size="75" class="form-control" name="asc_jetcache_settings[cont_ajax_routes][<?php echo $ajaxroutes_row; ?>][route]" value="<?php if (isset($asc_jetcache_settings['cont_ajax_routes'][$jc_num]['route']) && $asc_jetcache_settings['cont_ajax_routes'][$jc_num]['route'] != '') { echo $asc_jetcache_settings['cont_ajax_routes'][$jc_num]['route']; } else { echo ''; } ?>">
		               	</div>
	                </div>

					<div style="text-align: center; padding: 8px; width: 15%;">

						<div class="input-group jetcache-text-center">
							<select class="form-control" name="asc_jetcache_settings[cont_ajax_routes][<?php echo $ajaxroutes_row; ?>][status]" id="id-cont-ajax-status">
								<?php if (isset($asc_jetcache_settings['cont_ajax_routes'][$jc_num]['status']) && $asc_jetcache_settings['cont_ajax_routes'][$jc_num]['status']) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
						</div>


	                </div>

					<div style="text-align: center; padding: 8px; width: 15%;">

						<div class="input-group jetcache-text-center">
							<select class="form-control" name="asc_jetcache_settings[cont_ajax_routes][<?php echo $ajaxroutes_row; ?>][scripts]" id="id-cont-ajax-status">
								<?php if (isset($asc_jetcache_settings['cont_ajax_routes'][$jc_num]['scripts']) && $asc_jetcache_settings['cont_ajax_routes'][$jc_num]['scripts']) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
						</div>


	                </div>

					<div style="text-align: center; padding: 8px; width: 10%;">
						<div class="input-group">
							<input type="text" size="5"  name="asc_jetcache_settings[cont_ajax_routes][<?php echo $ajaxroutes_row; ?>][delay]" value="<?php if (isset($asc_jetcache_settings['cont_ajax_routes'][$jc_num]['delay']) && $asc_jetcache_settings['cont_ajax_routes'][$jc_num]['delay'] != '') { echo $asc_jetcache_settings['cont_ajax_routes'][$jc_num]['delay']; } else { echo ''; } ?>">
		               	</div>
	                </div>


					<div style="text-align: center; padding: 8px; width: 10%;">
						<div class="input-group">
							<input type="text" size="10"  name="asc_jetcache_settings[cont_ajax_routes][<?php echo $ajaxroutes_row; ?>][md5]" value="<?php if (isset($asc_jetcache_settings['cont_ajax_routes'][$jc_num]['md5']) && $asc_jetcache_settings['cont_ajax_routes'][$jc_num]['md5'] != '') { echo $asc_jetcache_settings['cont_ajax_routes'][$jc_num]['md5']; } else { echo ''; } ?>">
		               	</div>
	                </div>



					<div style="text-align: center; padding: 8px; width: 10%;">
                    	<a onclick="$('#ajaxroutes-<?php echo $ajaxroutes_row; ?>').remove();" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a>
	                </div>

                </div>

              <?php
              			$ajaxroutes_row ++;
					}
				}
              ?>

			</div>


             <div id="ajaxroutes-ajaxroutes-add" style="clear: both; text-align: center; margin-top:10px; width: 100%">
	            <a id="ajaxroutes-ajaxroutes-add-a"  onclick="addRoutes();" class="markbutton nohref"><?php echo $language->get('entry_add_rule'); ?></a>
			</div>





		</td>
		</tr>

<script>

ajaxroutes_row = <?php echo $ajaxroutes_row; ?>;


function addRoutes() {


html  = '';

html += '				<div id="ajaxroutes-' + ajaxroutes_row + '" class="flex-box" style="text-align: center;">';
html += '					<div style="text-align: center; padding: 8px; width: 40%;">';
html += '						<div class="input-group">';
html += '							<input type="text" size="75" class="form-control" name="asc_jetcache_settings[cont_ajax_routes][' + ajaxroutes_row + '][route]" value="">';
html += '		               	</div>';
html += '	                </div>';

html += '					<div style="text-align: center; padding: 8px; width: 15%;">';

html += '						<div class="input-group jetcache-text-center">';
html += '							<select class="form-control" name="asc_jetcache_settings[cont_ajax_routes][' + ajaxroutes_row + '][status]" id="id-cont-ajax-status">';
html += '								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
html += '								<option value="0"><?php echo $text_disabled; ?></option>';
html += '							</select>';
html += '						</div>';
html += '	                </div>';

html += '					<div style="text-align: center; padding: 8px; width: 15%;">';

html += '						<div class="input-group jetcache-text-center">';
html += '							<select class="form-control" name="asc_jetcache_settings[cont_ajax_routes][' + ajaxroutes_row + '][scripts]" id="id-cont-ajax-status">';
html += '								<option value="1"><?php echo $text_enabled; ?></option>';
html += '								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>';
html += '							</select>';
html += '						</div>';

html += '	                </div>';

html += '					<div style="text-align: center; padding: 8px; width: 10%;">';
html += '						<div class="input-group">';
html += '							<input type="text" size="5"  name="asc_jetcache_settings[cont_ajax_routes][' + ajaxroutes_row + '][delay]" value="">';
html += '		               	</div>';
html += '	                </div>';
html += '					<div style="text-align: center; padding: 8px; width: 10%;">';
html += '						<div class="input-group">';
html += '							<input type="text" size="10"  name="asc_jetcache_settings[cont_ajax_routes][' + ajaxroutes_row + '][md5]" value="">';
html += '		               	</div>';
html += '	                </div>';

html += '					<div style="text-align: center; padding: 8px; width: 10%;">';
html += '                    	<a onclick="$(\'#ajaxroutes-' + ajaxroutes_row +'\').remove();" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a>';
html += '	                </div>';

html += '                </div>';


ajaxroutes_row++;
	$('#ajaxroutes').append(html);

}
</script>


                                <!--
								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_cont_ajax_header'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help"><?php echo $language->get('entry_cont_ajax_header_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[cont_ajax_header]" id="id-cont-ajax-status">
												<?php if (isset($asc_jetcache_settings['cont_ajax_header']) && $asc_jetcache_settings['cont_ajax_header']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>
                                -->




						     </table>


						    </div>




						</div>

						<div id="tab-model">
							<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">

								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>

								<tr>
									<!-- <td class="left"><?php echo $language->get('entry_status'); ?></td> -->
									<td class="left jetcache-table-help"><?php echo $language->get('entry_model_status_help'); ?></td>
									<td class="left jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[jetcache_model_status]">
												<?php if (isset($asc_jetcache_settings['jetcache_model_status']) && $asc_jetcache_settings['jetcache_model_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>

								<tr class="jetcache-back jetcache-pro-settings">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_db_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>

								<tr class="jetcache-pro-settings">
									<td class="jetcache-table-help"><?php echo $language->get('entry_model_db_status_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[model_db_status]">
												<?php if (isset($asc_jetcache_settings['model_db_status']) && $asc_jetcache_settings['model_db_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>
								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_model_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td>
										<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
											<tr class="jetcache-back">
												<td colspan="2" class="jetcache-back jetcache-text-center">
													<?php echo $language->get('entry_model_title'); ?> <span class="jetcache-table-help-href">?</span>
												</td>
											</tr>
											<tr>
												<td class="jetcache-table-help left">
													<?php echo $language->get('entry_model_help'); ?>
												</td>
												<td>
													<div style="float: left;">
														<table id="model" class="list jetcache-table-add">
															<thead>
																<tr>
																	<td class="left"><?php echo $language->get('entry_id'); ?></td>
																	<td><?php echo $language->get('entry_query_model'); ?></td>
																	<td><?php echo $language->get('entry_query_method'); ?></td>
																	<td><?php echo $language->get('entry_no_vars'); ?></td>
																	<td><?php echo $language->get('entry_onefile'); ?></td>
																	<td><?php echo $language->get('entry_status'); ?></td>
																	<td></td>
																</tr>
															</thead>
															<?php if (isset($asc_jetcache_settings['model']) && !empty($asc_jetcache_settings['model'])) { ?>
															<?php foreach ($asc_jetcache_settings['model'] as $model_id => $model) { ?>
															<?php $model_row = $model_id; ?>
															<tbody id="model_row<?php echo $model_row; ?>">
																<tr>
																	<td class="left">
																		<input type="text" name="asc_jetcache_settings[model][<?php echo $model_id; ?>][type_id]" value="<?php if (isset($model['type_id'])) echo $model['type_id']; ?>" size="3">
																	</td>
																	<td class="right">
																		<div style="margin-bottom: 3px;">
																			<input type="text" name="asc_jetcache_settings[model][<?php echo $model_id; ?>][model]" value="<?php if (isset($model['model'])) echo $model['model']; ?>" style="width: 250px;">
																		</div>
																	</td>
																	<td class="right">
																		<div style="margin-bottom: 3px;">
																			<input type="text" name="asc_jetcache_settings[model][<?php echo $model_id; ?>][method]" value="<?php if (isset($model['method'])) echo $model['method']; ?>" style="width: 200px;">
																		</div>
																	</td>
																	<td class="right">
																		<div class="input-group jetcache-text-center" style="margin-bottom: 3px;">
																			<select class="form-control" name="asc_jetcache_settings[model][<?php echo $model_id; ?>][no_getpost]">
																				<?php if (isset($model['no_getpost']) && $model['no_getpost']) { ?>
																				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																				<option value="0"><?php echo $text_disabled; ?></option>
																				<?php } else { ?>
																				<option value="1"><?php echo $text_enabled; ?></option>
																				<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																				<?php } ?>
																			</select>
																		</div>
																		<div class="input-group jetcache-text-center" style="margin-bottom: 3px;">
																			<select class="form-control" name="asc_jetcache_settings[model][<?php echo $model_id; ?>][no_session]">
																				<?php if (isset($model['no_session']) && $model['no_session']) { ?>
																				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																				<option value="0"><?php echo $text_disabled; ?></option>
																				<?php } else { ?>
																				<option value="1"><?php echo $text_enabled; ?></option>
																				<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																				<?php } ?>
																			</select>
																		</div>
																		<div class="input-group jetcache-text-center" style="margin-bottom: 3px;">
																			<select class="form-control" name="asc_jetcache_settings[model][<?php echo $model_id; ?>][no_url]">
																				<?php if (isset($model['no_url']) && $model['no_url']) { ?>
																				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																				<option value="0"><?php echo $text_disabled; ?></option>
																				<?php } else { ?>
																				<option value="1"><?php echo $text_enabled; ?></option>
																				<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																				<?php } ?>
																			</select>
																		</div>
																		<div class="input-group jetcache-text-center">
																			<select class="form-control" name="asc_jetcache_settings[model][<?php echo $model_id; ?>][no_route]">
																				<?php if (isset($model['no_route']) && $model['no_route']) { ?>
																				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																				<option value="0"><?php echo $text_disabled; ?></option>
																				<?php } else { ?>
																				<option value="1"><?php echo $text_enabled; ?></option>
																				<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																				<?php } ?>
																			</select>
																		</div>
																	</td>
																	<td class="right">
																		<div class="input-group jetcache-text-center">
																			<select class="form-control" name="asc_jetcache_settings[model][<?php echo $model_id; ?>][onefile]">
																				<?php if (isset($model['onefile']) && $model['onefile']) { ?>
																				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																				<option value="0"><?php echo $text_disabled; ?></option>
																				<?php } else { ?>
																				<option value="1"><?php echo $text_enabled; ?></option>
																				<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																				<?php } ?>
																			</select>
																		</div>
																	</td>
																	<td class="right">
																		<div class="input-group jetcache-text-center">
																			<select class="form-control" name="asc_jetcache_settings[model][<?php echo $model_id; ?>][status]">
																				<?php if (isset($model['status']) && $model['status']) { ?>
																				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																				<option value="0"><?php echo $text_disabled; ?></option>
																				<?php } else { ?>
																				<option value="1"><?php echo $text_enabled; ?></option>
																				<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																				<?php } ?>
																			</select>
																		</div>
																	</td>
																	<td class="left"><a onclick="$('#model_row<?php echo $model_row; ?>').remove();" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>
																</tr>
															</tbody>
															<?php } ?>
															<?php } ?>
															<tfoot>
																<tr>
																	<td colspan="3"></td>
																	<td class="left">
																		<a onclick="addModel();" class="markbutton nohref"><?php echo $language->get('entry_add_rule'); ?></a>
																	</td>
																</tr>
															</tfoot>
														</table>
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
						<div id="tab-query">
							<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help"><?php echo $language->get('entry_query_status_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[jetcache_query_status]" id="id-jetcache-query-status">
												<?php if (isset($asc_jetcache_settings['jetcache_query_status']) && $asc_jetcache_settings['jetcache_query_status']) { ?>
												<option value="1" selected="selected" data-chained="0 1"><?php echo $text_enabled; ?></option>
												<option value="0" data-chained="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1" data-chained="0 1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected" data-chained="0"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>
								<tr class="jetcache-back jetcache-pro-settings">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_db_status'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="jetcache-pro-settings">
									<td class="jetcache-table-help"><?php echo $language->get('entry_query_db_status_help'); ?></td>
									<td class="jetcache-text-center">
										<div class="input-group jetcache-text-center">
											<select class="form-control" name="asc_jetcache_settings[query_db_status]">
												<?php if (isset($asc_jetcache_settings['query_db_status']) && $asc_jetcache_settings['query_db_status']) { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
								</tr>
							</table>
							<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_query_model_title'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_query_model_help'); ?>
									</td>
									<td>
										<div style="float: left;">
											<table id="query_model" class="list jetcache-table-add">
												<thead>
													<tr>
														<td class="left"><?php echo $language->get('entry_id'); ?></td>
														<td><?php echo $language->get('entry_query_model'); ?></td>
														<td><?php echo $language->get('entry_query_method'); ?></td>
														<td><?php echo $language->get('entry_status'); ?></td>
														<td></td>
													</tr>
												</thead>
												<?php if (isset($asc_jetcache_settings['query_model']) && !empty($asc_jetcache_settings['query_model'])) { ?>
												<?php foreach ($asc_jetcache_settings['query_model'] as $query_model_id => $query_model) { ?>
												<?php $query_model_row = $query_model_id; ?>
												<tbody id="query_model_row<?php echo $query_model_row; ?>">
													<tr>
														<td class="left">
															<input type="text" name="asc_jetcache_settings[query_model][<?php echo $query_model_id; ?>][type_id]" value="<?php if (isset($query_model['type_id'])) echo $query_model['type_id']; ?>" size="3">
														</td>
														<td class="right">
															<div style="margin-bottom: 3px;">
																<input type="text" name="asc_jetcache_settings[query_model][<?php echo $query_model_id; ?>][model]" value="<?php if (isset($query_model['model'])) echo $query_model['model']; ?>" style="width: 300px;">
															</div>
														</td>
														<td class="right">
															<div style="margin-bottom: 3px;">
																<input type="text" name="asc_jetcache_settings[query_model][<?php echo $query_model_id; ?>][method]" value="<?php if (isset($query_model['method'])) echo $query_model['method']; ?>" style="width: 300px;">
															</div>
														</td>
														<td class="right">
															<div class="input-group jetcache-text-center">
																<select class="form-control" name="asc_jetcache_settings[query_model][<?php echo $query_model_id; ?>][status]">
																	<?php if (isset($query_model['status']) && $query_model['status']) { ?>
																	<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
																	<option value="0"><?php echo $text_disabled; ?></option>
																	<?php } else { ?>
																	<option value="1"><?php echo $text_enabled; ?></option>
																	<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
																	<?php } ?>
																</select>
															</div>
														</td>
														<td class="left"><a onclick="$('#query_model_row<?php echo $query_model_row; ?>').remove();" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>
													</tr>
												</tbody>
												<?php } ?>
												<?php } ?>
												<tfoot>
													<tr>
														<td colspan="3"></td>
														<td class="left"><a onclick="addQueryModel();" class="markbutton nohref"><?php echo $language->get('entry_add_rule'); ?></a></td>
													</tr>
												</tfoot>
											</table>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<div id="tab-faq">
							<div style="text-align: center; text-decoration: none; margin-bottom: 5px;">
								<a href="https://opencartadmin.com/doc/index.<?php echo substr(strtolower($config_admin_language), 0,2); ?>.jetcache.html" target="_blank" class="markbuttono widthbutton sc_button asc_blinking" style=""><?php echo $language->get('tab_doc'); ?></a>
							</div>
							 <div style="text-align: center; text-decoration: none; margin-bottom: 5px;">
							 <?php echo $language->get('text_jc_doc_faq'); ?>
							 </div>

						</div>

						<div id="tab-minify">
							<div id="tabs-minify" class="htabs">
								<a href="#tab-minify-css" class="jc-tab"><?php echo $language->get('tab_minify_css'); ?></a>
								<a href="#tab-minify-js" class="jc-tab"><?php echo $language->get('tab_minify_js'); ?></a>
								<a href="#tab-replacers" class="jc-tab"><?php echo $language->get('tab_replacers'); ?></a>
                                <a href="#tab-minify-html" class="jc-tab jetcache-pro-settings"><?php echo $language->get('tab_minify_html'); ?></a>
								<!-- <a href="#tab-lazy" class="jc-tab"><?php echo $language->get('tab_lazy'); ?></a> -->
							</div>

						<div id="tab-replacers">
						<?php
						if (function_exists('modification') && file_exists(modification(DIR_TEMPLATE . 'jetcache/replacers.tpl')))  {
							include(modification(DIR_TEMPLATE . 'jetcache/replacers.tpl'));
						} else {
							include(DIR_TEMPLATE . 'jetcache/replacers.tpl');
						}
						?>
						</div>
							<div id="tab-minify-html" class="jetcache-pro-settings">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_html'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>
									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_html_status_help'); ?></td>
										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_html_status]">
													<?php if (isset($asc_jetcache_settings['minify_html_status']) && $asc_jetcache_settings['minify_html_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td class="jetcache-table-help left"></td>
										<td></td>
									</tr>

								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_minify_html_ex_route'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_html_ex_route_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_html_ex_route]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_html_ex_route'])) { echo $asc_jetcache_settings['minify_html_ex_route']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>



								</table>
							</div>
<!--
							<div id="tab-lazy">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_lazy_status'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>
									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_lazy_status_help'); ?>

										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[lazy_tokens]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['lazy_tokens'])) { echo $asc_jetcache_settings['lazy_tokens']; } else { echo ''; } ?></textarea>
										</div>
										</td>
										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[lazy_status]">
													<?php if (isset($asc_jetcache_settings['lazy_status']) && $asc_jetcache_settings['lazy_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td class="jetcache-table-help left"></td>
										<td></td>
									</tr>

								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_lazy_ex_route'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr>
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_lazy_ex_route_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[lazy_ex_route]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['lazy_ex_route'])) { echo $asc_jetcache_settings['lazy_ex_route']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>




								</table>
							</div>

-->



							<div id="tab-minify-css">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_status_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_status]">
													<?php if (isset($asc_jetcache_settings['minify_css_status']) && $asc_jetcache_settings['minify_css_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

								<tr>
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_ex_route'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_css_ex_route_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_css_ex_route]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_css_ex_route'])) { echo $asc_jetcache_settings['minify_css_ex_route']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>


								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_compress_status'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_compress_status_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_compress_status]">
													<?php if (isset($asc_jetcache_settings['minify_css_compress_status']) && $asc_jetcache_settings['minify_css_compress_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_inline_compress_status'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_inline_compress_status_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_inline_compress_status]">
													<?php if (isset($asc_jetcache_settings['minify_css_inline_compress_status']) && $asc_jetcache_settings['minify_css_inline_compress_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>



								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_compress_type'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_compress_type_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_compress_type]">
													<?php if (isset($asc_jetcache_settings['minify_css_compress_type']) && $asc_jetcache_settings['minify_css_compress_type']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>



								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_footer'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_footer_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_footer]">
													<?php if (isset($asc_jetcache_settings['minify_css_footer']) && $asc_jetcache_settings['minify_css_footer']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

								<tr>
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_ex_css_footer'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_css_ex_css_footer_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_css_ex_css_footer]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_css_ex_css_footer'])) { echo $asc_jetcache_settings['minify_css_ex_css_footer']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>

								<tr>
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_in_inline'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>

										</td>
									</tr>

									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_css_in_inline_help'); ?>

											<div class="input-group">
												<input class="form-control" size="11" type="text" name="asc_jetcache_settings[minify_css_inline_tie]" value="<?php  if (isset($asc_jetcache_settings['minify_css_inline_tie'])) echo $asc_jetcache_settings['minify_css_inline_tie']; ?>" size="20" />
											</div>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_css_in_inline]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_css_in_inline'])) { echo $asc_jetcache_settings['minify_css_in_inline']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>





									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_combine_status'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_combine_status_help'); ?>
											<div class="input-group">
												<input class="form-control" size="11" type="text" name="asc_jetcache_settings[minify_css_combine_head]" value="<?php  if (isset($asc_jetcache_settings['minify_css_combine_head'])) echo $asc_jetcache_settings['minify_css_combine_head']; ?>" size="20" />
											</div>

										</td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_combine_status]">
													<?php if (isset($asc_jetcache_settings['minify_css_combine_status']) && $asc_jetcache_settings['minify_css_combine_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_combine_footer'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_combine_footer_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_combine_footer]">
													<?php if (isset($asc_jetcache_settings['minify_css_combine_footer']) && $asc_jetcache_settings['minify_css_combine_footer']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_combine_preload'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_combine_preload_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_combine_preload]">
													<?php if (isset($asc_jetcache_settings['minify_css_combine_preload']) && $asc_jetcache_settings['minify_css_combine_preload']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>



								<tr>
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_ex_combine'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_css_ex_combine_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_css_ex_combine]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_css_ex_combine'])) { echo $asc_jetcache_settings['minify_css_ex_combine']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>


									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_combine_inline'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>
									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_combine_inline_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_combine_inline]">
													<?php if (isset($asc_jetcache_settings['minify_css_combine_inline']) && $asc_jetcache_settings['minify_css_combine_inline']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

								<tr>
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_ex_combine_inline'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_css_ex_combine_inline_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_css_ex_combine_inline]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_css_ex_combine_inline'])) { echo $asc_jetcache_settings['minify_css_ex_combine_inline']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>

								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_inline_footer'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_css_inline_footer_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_css_inline_footer]">
													<?php if (isset($asc_jetcache_settings['minify_css_inline_footer']) && $asc_jetcache_settings['minify_css_inline_footer']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>
								<tr>

								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_css_preload'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_css_preload_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_css_preload]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_css_preload'])) { echo $asc_jetcache_settings['minify_css_preload']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>


									<tr>
										<td class="jetcache-table-help left"></td>
										<td></td>
									</tr>
								</table>
							</div>
							<div id="tab-minify-js">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>&nbsp;<span class="jetcache-table-pro-href"><a href="#pro_show" class="jetcache_pro_show">PRO</a><a href="#pro_hide" class="jetcache_pro_hide">PRO</a></span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_status_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_status]">
													<?php if (isset($asc_jetcache_settings['minify_js_status']) && $asc_jetcache_settings['minify_js_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_preload'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>&nbsp;<span class="jetcache-table-pro-href"><a href="#pro_show" class="jetcache_pro_show">PRO</a><a href="#pro_hide" class="jetcache_pro_hide">PRO</a></span>
										</td>
									</tr>

									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_js_preload_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_js_preload]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_js_preload'])) { echo $asc_jetcache_settings['minify_js_preload']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>


									<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_afterload_time_desktop'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr class="jetcache-pro-settings">
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_afterload_time_desktop_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<input class="form-control template" size="11" type="text" name="asc_jetcache_settings[minify_js_afterload_time_desktop]" value="<?php  if (isset($asc_jetcache_settings['minify_js_afterload_time_desktop'])) echo $asc_jetcache_settings['minify_js_afterload_time_desktop']; ?>" size="20" />
											</div>
										</td>
									</tr>

									<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_afterload_time_mobile'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr class="jetcache-pro-settings">
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_afterload_time_mobile_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<input class="form-control template" size="11" type="text" name="asc_jetcache_settings[minify_js_afterload_time_mobile]" value="<?php  if (isset($asc_jetcache_settings['minify_js_afterload_time_mobile'])) echo $asc_jetcache_settings['minify_js_afterload_time_mobile']; ?>" size="20" />
											</div>
										</td>
									</tr>


									<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_ex_route'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>
                                 <tr class="jetcache-pro-settings">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_js_ex_route_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_js_ex_route]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_js_ex_route'])) { echo $asc_jetcache_settings['minify_js_ex_route']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>


								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_compress_status'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>&nbsp;<span class="jetcache-table-pro-href"><a href="#pro_show" class="jetcache_pro_show">PRO</a><a href="#pro_hide" class="jetcache_pro_hide">PRO</a></span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_compress_status_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_compress_status]">
													<?php if (isset($asc_jetcache_settings['minify_js_compress_status']) && $asc_jetcache_settings['minify_js_compress_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_inline_compress_status'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_inline_compress_status_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_inline_compress_status]">
													<?php if (isset($asc_jetcache_settings['minify_js_inline_compress_status']) && $asc_jetcache_settings['minify_js_inline_compress_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


									<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_compress_type'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr class="jetcache-pro-settings">
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_compress_type_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_compress_type]">
													<?php if (isset($asc_jetcache_settings['minify_js_compress_type']) && $asc_jetcache_settings['minify_js_compress_type']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


									<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_ex_compress'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

                                 <tr class="jetcache-pro-settings">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_js_ex_compress_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_js_ex_compress]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_js_ex_compress'])) { echo $asc_jetcache_settings['minify_js_ex_compress']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>




								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_footer'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>&nbsp;<span class="jetcache-table-pro-href"><a href="#pro_show" class="jetcache_pro_show">PRO</a><a href="#pro_hide" class="jetcache_pro_hide">PRO</a></span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_footer_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_footer]">
													<?php if (isset($asc_jetcache_settings['minify_js_footer']) && $asc_jetcache_settings['minify_js_footer']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_first'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr class="jetcache-pro-settings">
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_first_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_first]">
													<?php if (isset($asc_jetcache_settings['minify_js_first']) && $asc_jetcache_settings['minify_js_first']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>



									<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_ex_js_footer'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

                                 <tr class="jetcache-pro-settings">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_js_ex_js_footer_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_js_ex_js_footer]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_js_ex_js_footer'])) { echo $asc_jetcache_settings['minify_js_ex_js_footer']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>

								<tr>
									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_in_inline'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_js_in_inline_help'); ?>
											<div class="input-group">
												<input class="form-control" size="11" type="text" name="asc_jetcache_settings[minify_js_inline_tie]" value="<?php  if (isset($asc_jetcache_settings['minify_js_inline_tie'])) echo $asc_jetcache_settings['minify_js_inline_tie']; ?>" size="20" />
											</div>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_js_in_inline]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_js_in_inline'])) { echo $asc_jetcache_settings['minify_js_in_inline']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>

									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_combine_status'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>&nbsp;<span class="jetcache-table-pro-href"><a href="#pro_show" class="jetcache_pro_show">PRO</a><a href="#pro_hide" class="jetcache_pro_hide">PRO</a></span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_combine_status_help'); ?>
														<div class="input-group">
															<input class="form-control" size="11" type="text" name="asc_jetcache_settings[minify_js_combine_head]" value="<?php  if (isset($asc_jetcache_settings['minify_js_combine_head'])) echo $asc_jetcache_settings['minify_js_combine_head']; ?>" size="20" />
														</div>
										</td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_combine_status]">
													<?php if (isset($asc_jetcache_settings['minify_js_combine_status']) && $asc_jetcache_settings['minify_js_combine_status']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_combine_footer'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_combine_footer_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_combine_footer]">
													<?php if (isset($asc_jetcache_settings['minify_js_combine_footer']) && $asc_jetcache_settings['minify_js_combine_footer']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>




									<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_ex_combine'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>
                                 <tr class="jetcache-pro-settings">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_js_ex_combine_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_js_ex_combine]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_js_ex_combine'])) { echo $asc_jetcache_settings['minify_js_ex_combine']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>


									<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_combine_inline'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>
									<tr class="jetcache-pro-settings">
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_combine_inline_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_combine_inline]">
													<?php if (isset($asc_jetcache_settings['minify_js_combine_inline']) && $asc_jetcache_settings['minify_js_combine_inline']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


								<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_ex_combine_inline'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>
                                 <tr class="jetcache-pro-settings">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_js_ex_combine_inline_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_js_ex_combine_inline]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_js_ex_combine_inline'])) { echo $asc_jetcache_settings['minify_js_ex_combine_inline']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>

								<tr class="jetcache-back">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_inline_footer'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>&nbsp;<span class="jetcache-table-pro-href"><a href="#pro_show" class="jetcache_pro_show">PRO</a><a href="#pro_hide" class="jetcache_pro_hide">PRO</a></span>
										</td>
									</tr>

									<tr>
										<td class="jetcache-table-help"><?php echo $language->get('entry_minify_js_inline_footer_help'); ?></td>

										<td class="jetcache-text-center">
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[minify_js_inline_footer]">
													<?php if (isset($asc_jetcache_settings['minify_js_inline_footer']) && $asc_jetcache_settings['minify_js_inline_footer']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

								<tr class="jetcache-back jetcache-pro-settings">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_minify_js_ex_inline_footer'); ?>&nbsp;<span class="jetcache-table-help-href">?</span>
										</td>
									</tr>
                                <tr class="jetcache-pro-settings">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_minify_js_ex_inline_footer_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[minify_js_ex_inline_footer]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['minify_js_ex_inline_footer'])) { echo $asc_jetcache_settings['minify_js_ex_inline_footer']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>



									<tr>
										<td class="jetcache-table-help left"></td>
										<td></td>
									</tr>
								</table>
							</div>



						</div>


						<div id="tab-image">


							<input type="hidden" name="asc_jetcache_settings[jc_path_mozjpeg]" value="<?php if (isset($jc_path_mozjpeg)) echo $jc_path_mozjpeg; ?>">
							<input type="hidden" name="asc_jetcache_settings[jc_path_jpegoptim]" value="<?php if (isset($jc_path_jpegoptim)) echo $jc_path_jpegoptim; ?>">
                            <input type="hidden" name="asc_jetcache_settings[jc_path_optipng]" value="<?php if (isset($jc_path_optipng)) echo $jc_path_optipng; ?>">
							<input type="hidden" name="asc_jetcache_settings[jc_path_webp]" value="<?php if (isset($jc_path_webp)) echo $jc_path_webp; ?>">

                            <input type="hidden" name="asc_jetcache_settings[image_exec]" value="<?php echo $asc_jetcache_settings['image_exec']; ?>">
                            <input type="hidden" name="asc_jetcache_settings[image_proc_open]" value="<?php echo $asc_jetcache_settings['image_proc_open']; ?>">

							<div id="tabs-image" class="htabs">
								<a href="#tab-image-options" class="jc-tab"><?php echo $language->get('tab_image_options'); ?></a>
								<a href="#tab-image-ex" class="jc-tab"><?php echo $language->get('tab_image_ex'); ?></a>
							</div>
							<div id="tab-image-options">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">
                                    <?php if ((!isset($image_mozjpeg) || !$image_mozjpeg) || (!isset($image_optipng) || !$image_optipng) || (!isset($image_webp) || !$image_webp)) { ?>
									<tr>
										<td colspan="2" class="jetcache-back-red jetcache-text-center">
											<?php echo $language->get('entry_image_status_error'); ?>&nbsp;<span class="jetcache-table-help-href<?php if ((!isset($image_mozjpeg) || !$image_mozjpeg) || (!isset($image_optipng) || !$image_optipng) || (!isset($image_webp) || !$image_webp)) { ?>-red<?php } ?>">?</span>
										</td>
									</tr>
									<tr>
										<td colspan="2" class="jetcache-back jetcache-color-red jetcache-text-center">
											<?php echo $language->get('entry_image_status_error_text'); ?>
										</td>
									</tr>
									<tr class="jetcache-table-help">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_image_status_error_must_text'); ?>
										</td>
									</tr>

                                    <?php } ?>
									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_status'); ?>
										</td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[image_status]">
													<?php if (isset($asc_jetcache_settings['image_status']) && $asc_jetcache_settings['image_status']) { ?>
													<?php if ((isset($image_mozjpeg) && $image_mozjpeg) || (isset($image_optipng) && $image_optipng) || (isset($image_webp) && $image_webp)) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if ((isset($image_mozjpeg) && $image_mozjpeg) || (isset($image_optipng) && $image_optipng) || (isset($image_webp) && $image_webp)) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr class="jetcache-back jetcache-pro-settings">
										<td style="width: 220px;"><?php echo $language->get('entry_jetcache_cache_image_remove'); ?>
										<span class="jetcache-table-help">
											<div class="input-group">
												<input class="form-control image_remove_ext" type="text" name="asc_jetcache_settings[image_remove_ext]" value="<?php  if (isset($asc_jetcache_settings['image_remove_ext'])) echo $asc_jetcache_settings['image_remove_ext']; ?>" size="5" />
											</div>
										</span>


										</td>
										<td>
											<div style="margin-bottom: 5px;">
												<a class="jetcache_cache_image_remove markbuttono sc_button"><?php echo $language->get('text_url_cache_image_remove'); ?></a>
												<div class="div_cache_image_remove"></div>
											</div>
										</td>
									</tr>

                                    <!-- webp -->
									<tr>
										<td colspan="2" class="jetcache-back <?php if (!isset($image_webp) || !$image_webp) { ?>jetcache-back-red <?php } ?>jetcache-text-center">
											<?php echo $language->get('entry_webp'); ?>&nbsp;<span class="jetcache-table-help-href<?php if (!isset($image_webp) || !$image_webp) { ?>-red<?php } ?>">?</span>
										</td>
									</tr>
									<tr class="jetcache-table-help">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_webp_must'); ?>
										</td>
									</tr>
                                    <?php if (!isset($image_webp) || !$image_webp) {?>
									<tr>
										<td colspan="2" class="jetcache-back jetcache-color-red jetcache-text-center">
											<?php echo $language->get('entry_image_status_error_text'); ?> <?php echo $language->get('entry_webp_text'); ?>
										</td>
									</tr>
                                    <?php } ?>


									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_webp_status'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select id="image-webp-status" class="form-control" name="asc_jetcache_settings[image_webp_status]">
													<?php if (isset($asc_jetcache_settings['image_webp_status']) && $asc_jetcache_settings['image_webp_status']) { ?>
													<?php if (isset($image_webp) && $image_webp) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_webp) && $image_webp) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_webp_lossess'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[image_webp_lossess]">
													<?php if (isset($asc_jetcache_settings['image_webp_lossess']) && $asc_jetcache_settings['image_webp_lossess']) { ?>
													<?php if (isset($image_webp) && $image_webp) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_webp) && $image_webp) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


									<tr class="jetcache-back jetcache-pro-settings">
										<td style="width: 220px;"><?php echo $language->get('entry_image_webp_relative_url'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[image_webp_relative_url]">
													<?php if (isset($asc_jetcache_settings['image_webp_relative_url']) && $asc_jetcache_settings['image_webp_relative_url']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr class="jetcache-back jetcache-pro-settings">
										<td style="width: 220px;"><?php echo $language->get('entry_image_webp_mozjpeg'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[image_webp_mozjpeg]">
													<?php if (isset($asc_jetcache_settings['image_webp_mozjpeg']) && $asc_jetcache_settings['image_webp_mozjpeg']) { ?>
													<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<option value="1"><?php echo $text_enabled; ?></option>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr>
										<td class="left"><?php echo $language->get('entry_image_webp_command');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control" type="text" name="asc_jetcache_settings[image_webp_command]" value="<?php  if (isset($asc_jetcache_settings['image_webp_command'])) echo $asc_jetcache_settings['image_webp_command']; ?>" size="20" />
											</div>
										</td>
									</tr>

                                    <!-- mozjpeg -->
									<tr>
										<td colspan="2" class="jetcache-back <?php if (!isset($image_mozjpeg) || !$image_mozjpeg) { ?>jetcache-back-red <?php } ?>jetcache-text-center">
											<?php echo $language->get('entry_mozjpeg'); ?>&nbsp;<span class="jetcache-table-help-href<?php if (!isset($image_mozjpeg) || !$image_mozjpeg) { ?>-red<?php } ?>">?</span>
										</td>
									</tr>
									<tr class="jetcache-table-help">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_mozjpeg_must'); ?>
										</td>
									</tr>
                                    <?php if (!isset($image_mozjpeg) || !$image_mozjpeg) {?>
									<tr>
										<td colspan="2" class="jetcache-back jetcache-color-red jetcache-text-center">
											<?php echo $language->get('entry_image_status_error_text'); ?> <?php echo $language->get('entry_mozjpeg_text'); ?>
										</td>
									</tr>
                                    <?php } ?>


									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_mozjpeg_status'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control image-other-status" name="asc_jetcache_settings[image_mozjpeg_status]">
													<?php if (isset($asc_jetcache_settings['image_mozjpeg_status']) && $asc_jetcache_settings['image_mozjpeg_status']) { ?>
													<?php if (isset($image_mozjpeg) && $image_mozjpeg) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_mozjpeg) && $image_mozjpeg) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_mozjpeg_optimize'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[image_mozjpeg_optimize]">
													<?php if (isset($asc_jetcache_settings['image_mozjpeg_optimize']) && $asc_jetcache_settings['image_mozjpeg_optimize']) { ?>
													<?php if (isset($image_mozjpeg) && $image_mozjpeg) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_mozjpeg) && $image_mozjpeg) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_mozjpeg_progressive'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[image_mozjpeg_progressive]">
													<?php if (isset($asc_jetcache_settings['image_mozjpeg_progressive']) && $asc_jetcache_settings['image_mozjpeg_progressive']) { ?>
													<?php if (isset($image_mozjpeg) && $image_mozjpeg) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_mozjpeg) && $image_mozjpeg) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_image_mozjpeg_command');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control" type="text" name="asc_jetcache_settings[image_mozjpeg_command]" value="<?php  if (isset($asc_jetcache_settings['image_mozjpeg_command'])) echo $asc_jetcache_settings['image_mozjpeg_command']; ?>" size="20" />
											</div>
										</td>
									</tr>
                                   <!-- /mozjpeg -->
                                   <!-- jpegoptim -->
									<tr>
										<td colspan="2" class="jetcache-back <?php if (!isset($image_jpegoptim) || !$image_jpegoptim) { ?>jetcache-back-red <?php } ?>jetcache-text-center">
											<?php echo $language->get('entry_jpegoptim'); ?>&nbsp;<span class="jetcache-table-help-href<?php if (!isset($image_jpegoptim) || !$image_jpegoptim) { ?>-red<?php } ?>">?</span>
										</td>
									</tr>
									<tr class="jetcache-table-help">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_jpegoptim_must'); ?>
										</td>
									</tr>
                                    <?php if (!isset($image_jpegoptim) || !$image_jpegoptim) {?>
									<tr>
										<td colspan="2" class="jetcache-back jetcache-color-red jetcache-text-center">
											<?php echo $language->get('entry_image_status_error_text'); ?> <?php echo $language->get('entry_jpegoptim_text'); ?>
										</td>
									</tr>
                                    <?php } ?>


									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_jpegoptim_status'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control image-other-status" name="asc_jetcache_settings[image_jpegoptim_status]">
													<?php if (isset($asc_jetcache_settings['image_jpegoptim_status']) && $asc_jetcache_settings['image_jpegoptim_status']) { ?>
													<?php if (isset($image_jpegoptim) && $image_jpegoptim) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_jpegoptim) && $image_jpegoptim) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_jpegoptim_optimize'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[image_jpegoptim_optimize]">
													<?php if (isset($asc_jetcache_settings['image_jpegoptim_optimize']) && $asc_jetcache_settings['image_jpegoptim_optimize']) { ?>
													<?php if (isset($image_jpegoptim) && $image_jpegoptim) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_jpegoptim) && $image_jpegoptim) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_jpegoptim_progressive'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[image_jpegoptim_progressive]">
													<?php if (isset($asc_jetcache_settings['image_jpegoptim_progressive']) && $asc_jetcache_settings['image_jpegoptim_progressive']) { ?>
													<?php if (isset($image_jpegoptim) && $image_jpegoptim) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_jpegoptim) && $image_jpegoptim) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_jpegoptim_strip'); ?></td>
										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control" name="asc_jetcache_settings[image_jpegoptim_strip]">
													<?php if (isset($asc_jetcache_settings['image_jpegoptim_strip']) && $asc_jetcache_settings['image_jpegoptim_strip']) { ?>
													<?php if (isset($image_jpegoptim) && $image_jpegoptim) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_jpegoptim) && $image_jpegoptim) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


									<tr class="jetcache-back jetcache-pro-settings">
										<td class="left"><?php echo $language->get('entry_image_jpegoptim_level');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control" size="2" maxlength="2" type="text" name="asc_jetcache_settings[image_jpegoptim_level]" value="<?php  if (isset($asc_jetcache_settings['image_jpegoptim_level'])) echo $asc_jetcache_settings['image_jpegoptim_level']; ?>">
											</div>
										</td>
									</tr>

									<tr class="jetcache-back jetcache-pro-settings">
										<td class="left"><?php echo $language->get('entry_image_jpegoptim_size');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control" size="2" maxlength="2" type="text" name="asc_jetcache_settings[image_jpegoptim_size]" value="<?php  if (isset($asc_jetcache_settings['image_jpegoptim_size'])) echo $asc_jetcache_settings['image_jpegoptim_size']; ?>">
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_image_jpegoptim_command');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control" type="text" name="asc_jetcache_settings[image_jpegoptim_command]" value="<?php  if (isset($asc_jetcache_settings['image_jpegoptim_command'])) echo $asc_jetcache_settings['image_jpegoptim_command']; ?>" size="20" />
											</div>
										</td>
									</tr>
                                    <!-- /jpegoptim -->

									<!-- optipng -->
									<tr>
										<td colspan="2" class="jetcache-back <?php if (!isset($image_optipng) || !$image_optipng) { ?>jetcache-back-red <?php } ?>jetcache-text-center">
											<?php echo $language->get('entry_optipng'); ?>&nbsp;<span class="jetcache-table-help-href<?php if (!isset($image_optipng) || !$image_optipng) { ?>-red<?php } ?>">?</span>
										</td>
									</tr>
									<tr class="jetcache-table-help">
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_optipng_must'); ?>
										</td>
									</tr>
                                    <?php if (!isset($image_optipng) || !$image_optipng) { ?>
									<tr>
										<td colspan="2" class="jetcache-back jetcache-color-red jetcache-text-center">
											<?php echo $language->get('entry_image_status_error_text'); ?> <?php echo $language->get('entry_optipng_text'); ?>
										</td>
									</tr>
                                    <?php } ?>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_image_optipng_status'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
												<select class="form-control image-other-status" name="asc_jetcache_settings[image_optipng_status]">
													<?php if (isset($asc_jetcache_settings['image_optipng_status']) && $asc_jetcache_settings['image_optipng_status']) { ?>
													<?php if (isset($image_optipng) && $image_optipng) { ?><option value="1" selected="selected"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
													<?php if (isset($image_optipng) && $image_optipng) { ?><option value="1"><?php echo $text_enabled; ?></option><?php } ?>
													<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr class="jetcache-back jetcache-pro-settings">
										<td><?php echo $language->get('entry_optipng_optimize_level'); ?></td>
										<td>
											<div class="input-group">
												<select class="form-control" name="asc_jetcache_settings[optipng_optimize_level]">
													<?php foreach ($optipng_optimize_level as $value => $name) { ?>
													<option value="<?php echo $value; ?>" <?php if (isset($asc_jetcache_settings['optipng_optimize_level']) && $asc_jetcache_settings['optipng_optimize_level'] == $value) { ?> selected="selected" <?php } ?>><?php echo $name; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td class="left"><?php echo $language->get('entry_image_optipng_command');  ?></td>
										<td class="left">
											<div class="input-group">
												<input class="form-control" type="text" name="asc_jetcache_settings[image_optipng_command]" value="<?php  if (isset($asc_jetcache_settings['image_optipng_command'])) echo $asc_jetcache_settings['image_optipng_command']; ?>" size="20" />
											</div>
										</td>
									</tr>

                                   <!-- /optipng -->

									<tr>
										<td colspan="2" class="jetcache-back jetcache-text-center">
											<?php echo $language->get('entry_features_system'); ?>
										</td>
									</tr>


									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_linux_status'); ?><br>
										<?php echo strtolower(PHP_OS); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_status_success['linux']) && $image_status_success['linux']) { echo $language->get('entry_system_yes'); } else { echo $language->get('entry_system_no'); } ?>
											</div>
										</td>
									</tr>
									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_exec_status'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_status_success['exec']) && $image_status_success['exec']) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_proc_open'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_status_success['proc_open']) && $image_status_success['proc_open']) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_webp_perms'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_status_success['webp_perms']) && $image_status_success['webp_perms']) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>
									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_webp_exec'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_webp) && $image_webp) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>



									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_mozjpeg_perms'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_status_success['mozjpeg_perms']) && $image_status_success['mozjpeg_perms']) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>
									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_mozjpeg_exec'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_mozjpeg) && $image_mozjpeg) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_jpegoptim_perms'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_status_success['jpegoptim_perms']) && $image_status_success['jpegoptim_perms']) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>
									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_jpegoptim_exec'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_jpegoptim) && $image_jpegoptim) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_optipng_perms'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_status_success['optipng_perms']) && $image_status_success['optipng_perms']) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>

									<tr>
										<td style="width: 220px;"><?php echo $language->get('entry_system_optipng_exec'); ?>
										</td>

										<td>
											<div class="input-group jetcache-text-center">
                                            <?php if (isset($image_optipng) && $image_optipng) echo $language->get('entry_system_yes'); else echo $language->get('entry_system_no'); ?>

											</div>
										</td>
									</tr>
                                   <?php if (isset($image_webp) && $image_webp) { ?>
									<tr>
										<td class="jetcache-back jetcache-text-center" style="width: 220px;" colspan="2">
											<?php echo $language->get('entry_webp'); ?>
										</td>

										<td>
										<tr>
										<td style="width: 220px; vertical-align: top;">
										<?php echo $language->get('entry_system_image_webp_original'); ?><br>
										<span class="jetcache-orange"><?php echo $image_status_success['webp_exec']['image_original_filesize']; ?> <?php echo $language->get('text_system_byte'); ?></span>

										</td>
										<td style="width: 220px; vertical-align: top;">
										<?php echo $language->get('entry_system_image_webp_lossess'); ?><br>
										<span class="jetcache-green"><?php echo $image_status_success['webp_exec']['image_optimized_filesize']; ?> <?php echo $language->get('text_system_byte'); ?></span><br>
										<span class="jetcache-purple">-<?php echo $image_status_success['webp_exec']['image_optimized_percent']; ?>%</span>

										</td>
										</tr>

										<td>
										<tr>
										<td style="width: 220px;">
											<div class="input-group jetcache-text-center">
                                             <img src="<?php echo $image_status_success['webp_exec']['image_original_url']; ?>">
											</div>

										</td>
										<td style="width: 220px;">
											<div class="input-group jetcache-text-center">
                                             <img src="<?php echo $image_status_success['webp_exec']['image_optimized_url']; ?>">
											</div>

										</td>
										</tr>



										</td>
									</tr>


                                    <?php } ?>


                                   <?php if (isset($image_mozjpeg) && $image_mozjpeg) { ?>
									<tr>
										<td class="jetcache-back jetcache-text-center" style="width: 220px;" colspan="2">
											<?php echo $language->get('entry_mozjpeg'); ?>
										</td>

										<td>
										<tr>
										<td style="width: 220px; vertical-align: top;">
										<?php echo $language->get('entry_system_image_mozjpeg_original'); ?><br>
										<span class="jetcache-orange"><?php echo $image_status_success['mozjpeg_exec']['image_original_filesize']; ?> <?php echo $language->get('text_system_byte'); ?></span>

										</td>
										<td style="width: 220px; vertical-align: top;">
										<?php echo $language->get('entry_system_image_mozjpeg_optimized'); ?><br>
										<span class="jetcache-green"><?php echo $image_status_success['mozjpeg_exec']['image_optimized_filesize']; ?> <?php echo $language->get('text_system_byte'); ?></span><br>
										<span class="jetcache-purple">-<?php echo $image_status_success['mozjpeg_exec']['image_optimized_percent']; ?>%</span>

										</td>
										</tr>

										<td>
										<tr>
										<td style="width: 220px;">
											<div class="input-group jetcache-text-center">
                                             <img src="<?php echo $image_status_success['mozjpeg_exec']['image_original_url']; ?>">
											</div>

										</td>
										<td style="width: 220px;">
											<div class="input-group jetcache-text-center">
                                             <img src="<?php echo $image_status_success['mozjpeg_exec']['image_optimized_url']; ?>">
											</div>

										</td>
										</tr>



										</td>
									</tr>


                                    <?php } ?>


                                   <?php if (isset($image_jpegoptim) && $image_jpegoptim) { ?>
									<tr>
										<td class="jetcache-back jetcache-text-center" style="width: 220px;" colspan="2">
											<?php echo $language->get('entry_jpegoptim'); ?>
										</td>

										<td>
										<tr>
										<td style="width: 220px; vertical-align: top;">
										<?php echo $language->get('entry_system_image_jpegoptim_original'); ?><br>
										<span class="jetcache-orange"><?php echo $image_status_success['jpegoptim_exec']['image_original_filesize']; ?> <?php echo $language->get('text_system_byte'); ?></span>

										</td>
										<td style="width: 220px; vertical-align: top;">
										<?php echo $language->get('entry_system_image_jpegoptim_optimized'); ?><br>
										<span class="jetcache-green"><?php echo $image_status_success['jpegoptim_exec']['image_optimized_filesize']; ?> <?php echo $language->get('text_system_byte'); ?></span><br>
										<span class="jetcache-purple">-<?php echo $image_status_success['jpegoptim_exec']['image_optimized_percent']; ?>%</span>

										</td>
										</tr>

										<td>
										<tr>
										<td style="width: 220px;">
											<div class="input-group jetcache-text-center">
                                             <img src="<?php echo $image_status_success['jpegoptim_exec']['image_original_url']; ?>">
											</div>

										</td>
										<td style="width: 220px;">
											<div class="input-group jetcache-text-center">
                                             <img src="<?php echo $image_status_success['jpegoptim_exec']['image_optimized_url']; ?>">
											</div>

										</td>
										</tr>



										</td>
									</tr>


                                    <?php } ?>


									<!-- ******************************* -->
                                   <?php if (isset($image_optipng) && $image_optipng) { ?>
									<tr>
										<td class="jetcache-back jetcache-text-center" style="width: 220px;" colspan="2">
											<?php echo $language->get('entry_optipng'); ?>
										</td>

										<td>
										<tr>
										<td style="width: 220px; vertical-align: top;">
										<?php echo $language->get('entry_system_image_optipng_original'); ?><br>
										<span class="jetcache-orange"><?php echo $image_status_success['optipng_exec']['image_original_filesize']; ?> <?php echo $language->get('text_system_byte'); ?></span>

										</td>
										<td style="width: 220px; vertical-align: top;">
										<?php echo $language->get('entry_system_image_optipng_optimized'); ?><br>
										<span class="jetcache-green"><?php echo $image_status_success['optipng_exec']['image_optimized_filesize']; ?> <?php echo $language->get('text_system_byte'); ?></span><br>
										<span class="jetcache-purple">-<?php echo $image_status_success['optipng_exec']['image_optimized_percent']; ?>%</span>

										</td>
										</tr>

										<td>
										<tr>
										<td style="width: 220px;">
											<div class="input-group jetcache-text-center">
                                             <img src="<?php echo $image_status_success['optipng_exec']['image_original_url']; ?>">
											</div>

										</td>
										<td style="width: 220px;">
											<div class="input-group jetcache-text-center">
                                             <img src="<?php echo $image_status_success['optipng_exec']['image_optimized_url']; ?>">
											</div>

										</td>
										</tr>



										</td>
									</tr>


                                    <?php } ?>




									<tr>
										<td style="width: 220px;"></td>
										<td></td>
									</tr>

								</table>
							</div>

							<div id="tab-image-ex">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">



								<tr class="jetcache-back">
									<td colspan="2" class="jetcache-back jetcache-text-center">
										<?php echo $language->get('entry_image_ex'); ?> <span class="jetcache-table-help-href">?</span>
									</td>
								</tr>
								<tr class="odd">
									<td class="jetcache-table-help left">
										<?php echo $language->get('entry_image_ex_help'); ?>
									</td>
									<td>
										<div class="input-group"><span class="input-group-addon"></span>
											<textarea class="form-control" name="asc_jetcache_settings[image_ex]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['image_ex'])) { echo $asc_jetcache_settings['image_ex']; } else { echo ''; } ?></textarea>
										</div>
									</td>
								</tr>
									<tr>
										<td class="jetcache-table-help left"></td>
										<td></td>
									</tr>

								</table>
							</div>


						</div>
					</div>
				</form>

<div class="right" style="text-align: right;">

<div class="input-group" style="float: right;">
	<a href="#pro_show" class="jetcache_pro_show markbuttono sc_button"><?php echo $language->get('entry_show_pro_settings'); ?></a>
	<a href="#pro_hide" class="jetcache_pro_hide markbuttono sc_button"><?php echo $language->get('entry_hide_pro_settings'); ?></a>
</div>

<script>
$('.jetcache_pro_show').click(function() {
	sc = $(this).offset().top;
	th = this;

	$('.jetcache-pro-settings').show();
	$('.jetcache_pro_show').hide();
	$('.jetcache_pro_hide').show();
    /*
	setTimeout(function(el, e) {
		$('html, body').animate({
		    scrollTop: el + $(th).offset().top
		}, 500);
	}, 100, sc, th);
    */
	return false;
});

$('.jetcache_pro_hide').click(function() {
	$('.jetcache-pro-settings').hide();
	$('.jetcache_pro_hide').hide();
	$('.jetcache_pro_show').show();
	localStorage.removeItem('jc_pro_click');
	return false;
});


	string_jc_pro_click = localStorage.getItem('jc_pro_click');

	if (string_jc_pro_click == null) {
		var array_jc_pro_click = [];
	} else {
		var array_jc_pro_click = JSON.parse(string_jc_pro_click);

		array_jc_pro_click.forEach(function(item, index, array) {
			$('a[href="'+ item + '"]').click();
			console.log(item);
		});
	}

	$('.jetcache_pro_show').on('click', function() {
		jc_pro_href = $(this).attr('href');
        array_jc_pro_click.push(jc_pro_href);
        if (array_jc_pro_click.length > 1) {
        	array_jc_pro_click.shift();
        }
        localStorage.setItem('jc_pro_click', JSON.stringify(array_jc_pro_click));
	});



</script>

</div>




			</div>
			<script type="text/javascript">
				var array_ex_route_row = Array();
				<?php
					foreach ($asc_jetcache_settings['ex_route'] as $indx => $ex_route) {
					?>
				array_ex_route_row.push(<?php echo $indx; ?>);
				<?php
					}
					?>

				var ex_route_row = <?php echo $ex_route_row + 1; ?>;

				function addExRoute() {

					var aindex = -1;
					for(i = 0; i < array_ex_route_row.length; i++) {
					 flg = jQuery.inArray(i, array_ex_route_row);
					 if (flg == -1) {
					  aindex = i;
					 }
					}
					if (aindex == -1) {
					  aindex = array_ex_route_row.length;
					}
					ex_route_row = aindex;
					array_ex_route_row.push(aindex);

				    html  = '<tbody id="ex_route_row' + ex_route_row + '">';
					html += '  <tr>';
				    html += '  <td class="left">';
				    html += '	<div class="input-group">';
					html += ' 	<input type="text" name="asc_jetcache_settings[ex_route]['+ ex_route_row +'][type_id]" value="'+ ex_route_row +'" class="form-control" size="3">';
					html += '	</div>';
				    html += '  </td>';

				 	html += '  <td class="right">';


					html += '	<div class="input-group" style="margin-bottom: 3px;">';
					html += '		<input type="text" name="asc_jetcache_settings[ex_route]['+ ex_route_row +'][route]" value="" class="form-control" style="width: 300px;">';
					html += '	</div>';



					html += '		<td class="right">';
					html += '		  <div class="input-group jetcache-text-center">';
					html += '		  	<select class="form-control" name="asc_jetcache_settings[ex_route]['+ ex_route_row +'][status]">';
					html += '			      <option value="0"><?php echo $text_disabled; ?></option>';
					html += '			      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
					html += '		  	</select>';
					html += '		  </div>';
					html += '		</td>';




				    html += '  </td>';
				    html += '  <td class="left"><a onclick="$(\'#ex_route_row'+ex_route_row+'\').remove(); array_ex_route_row.remove(ex_route_row);" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>';




					html += '  </tr>';
					html += '</tbody>';

					$('#ex_routes tfoot').before(html);

					ex_route_row++;
				}
			</script>
			<script type="text/javascript">
				var array_query_model_row = Array();
				<?php
					foreach ($asc_jetcache_settings['query_model'] as $indx => $query_model) {
					?>
				array_query_model_row.push(<?php echo $indx; ?>);
				<?php
					}
					?>

				var query_model_row = <?php echo $query_model_row + 1; ?>;

				function addQueryModel() {

					var aindex = -1;
					for(i = 0; i < array_query_model_row.length; i++) {
					 flg = jQuery.inArray(i, array_query_model_row);
					 if (flg == -1) {
					  aindex = i;
					 }
					}
					if (aindex == -1) {
					  aindex = array_query_model_row.length;
					}
					query_model_row = aindex;

					array_query_model_row.push(aindex);

				    html  = '<tbody id="query_model_row' + query_model_row + '">';
					html += '  <tr>';
				    html += '  <td class="left">';
				    html += '	<div class="input-group">';
					html += ' 	<input type="text" name="asc_jetcache_settings[query_model]['+ query_model_row +'][type_id]" value="'+ query_model_row +'" class="form-control" size="3">';
					html += '	</div>';
				    html += '  </td>';

				 	html += '  <td class="right">';
					html += '	<div class="input-group" style="margin-bottom: 3px;">';
					html += '		<input type="text" name="asc_jetcache_settings[query_model]['+ query_model_row +'][model]" value="" class="form-control" style="width: 300px;">';
					html += '	</div>';
				    html += '  </td>';

				    html += '  <td class="right">';
					html += '	<div class="input-group" style="margin-bottom: 3px;">';
					html += '		<input type="text" name="asc_jetcache_settings[query_model]['+ query_model_row +'][method]" value="" class="form-control" style="width: 300px;">';
					html += '	</div>';
				    html += '  </td>';

					html += '		<td class="right">';
					html += '		  <div class="input-group jetcache-text-center">';
					html += '		  	<select class="form-control" name="asc_jetcache_settings[query_model]['+ query_model_row +'][status]">';
					html += '			      <option value="0"><?php echo $text_disabled; ?></option>';
					html += '			      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
					html += '		  	</select>';
					html += '		  </div>';
					html += '		</td>';

				    html += '  <td class="left"><a onclick="$(\'#query_model_row' + query_model_row + '\').remove(); array_query_model_row.remove(query_model_row);" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>';

					html += '  </tr>';
					html += '</tbody>';

					$('#query_model tfoot').before(html);

					query_model_row++;
				}
			</script>

			<script type="text/javascript">
				var array_model_row = Array();
				<?php
					foreach ($asc_jetcache_settings['model'] as $indx => $model) {
					?>
				array_model_row.push(<?php echo $indx; ?>);
				<?php
					}
					?>

				var model_row = <?php echo $model_row + 1; ?>;

				function addModel() {

					var aindex = -1;
					for(i = 0; i < array_model_row.length; i++) {
					 flg = jQuery.inArray(i, array_model_row);
					 if (flg == -1) {
					  aindex = i;
					 }
					}
					if (aindex == -1) {
					  aindex = array_model_row.length;
					}
					model_row = aindex;

					array_model_row.push(aindex);

				html  = '<tbody id="model_row' + model_row + '">';

				html += '<tr>';
				html += '				               <td class="left">';
				html += '								<input type="text" name="asc_jetcache_settings[model]['+ model_row +'][type_id]" value="'+ model_row +'" size="3">';
				html += '				               </td>';

				html += '								<td class="right">';
				html += '									<div style="margin-bottom: 3px;">';
				html += '									<input type="text" name="asc_jetcache_settings[model]['+ model_row +'][model]" value="" style="width: 300px;">';
				html += '									</div>';
				html += '								</td>';

				html += '								<td class="right">';
				html += '									<div style="margin-bottom: 3px;">';
				html += '									<input type="text" name="asc_jetcache_settings[model]['+ model_row +'][method]" value="" style="width: 200px;">';
				html += '									</div>';
				html += '								</td>';


				html += '									<td class="right">';

				html += '						              <div class="input-group jetcache-text-center" style="margin-bottom: 3px;">';
				html += '						              <select class="form-control" name="asc_jetcache_settings[model]['+ model_row +'][no_getpost]">';

				html += '						                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
				html += '						                  <option value="0"><?php echo $text_disabled; ?></option>';

				html += '						                </select>';
				html += '						                </div>';


				html += '						              <div class="input-group jetcache-text-center" style="margin-bottom: 3px;">';
				html += '						              <select class="form-control" name="asc_jetcache_settings[model]['+ model_row +'][no_session]">';

				html += '						                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
				html += '						                  <option value="0"><?php echo $text_disabled; ?></option>';

				html += '						                </select>';
				html += '						                </div>';

				html += '						              <div class="input-group jetcache-text-center" style="margin-bottom: 3px;">';
				html += '						              <select class="form-control" name="asc_jetcache_settings[model]['+ model_row +'][no_url]">';

				html += '						                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
				html += '						                  <option value="0"><?php echo $text_disabled; ?></option>';

				html += '						                </select>';
				html += '						                </div>';

				html += '						              <div class="input-group jetcache-text-center">';
				html += '						              <select class="form-control" name="asc_jetcache_settings[model]['+ model_row +'][no_route]">';

				html += '						                  <option value="1"><?php echo $text_enabled; ?></option>';
				html += '						                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>';

				html += '						                </select>';
				html += '						                </div>';

				html += '									</td>';

				html += '									<td class="right">';
				html += '						              <div class="input-group jetcache-text-center">';
				html += '						              <select class="form-control" name="asc_jetcache_settings[model]['+ model_row +'][onefile]">';

				html += '						                  <option value="1"><?php echo $text_enabled; ?></option>';
				html += '						                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>';

				html += '						                </select>';
				html += '						                </div>';
				html += '									</td>';



				html += '								<td class="right">';
				html += '					              <div class="input-group jetcache-text-center">';
				html += '					              <select class="form-control" name="asc_jetcache_settings[model]['+ model_row +'][status]">';

				html += '					                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
				html += '					                  <option value="0"><?php echo $text_disabled; ?></option>';

				html += '					                </select>';
				html += '					                </div>';
				html += '								</td>';


				html += '  								<td class="left"><a onclick="$(\'#model_row' + model_row + '\').remove(); array_model_row.remove(model_row);" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>';


				html += '				              </tr>';



					html += '</tbody>';

					$('#model tfoot').before(html);

					model_row++;
				}
			</script>

			<script type="text/javascript">
				var array_add_cont_row = Array();
				<?php
					foreach ($asc_jetcache_settings['add_cont'] as $indx => $add_cont) {
					?>
				array_add_cont_row.push(<?php echo(int)$indx; ?>);
				<?php
					}
					?>

				var add_cont_row = <?php echo $add_cont_row + 1; ?>;

				function addAddCont() {

					var cont_index = -1;
					for(i = 0; i < array_add_cont_row.length; i++) {
					 flg = jQuery.inArray(i, array_add_cont_row);
					 if (flg == -1) {
					  cont_index = i;
					 }
					}
					if (cont_index == -1) {
					  cont_index = array_add_cont_row.length;
					}
					add_cont_row = cont_index;
					array_add_cont_row.push(cont_index);

				    html  = '<tbody id="add_cont_row' + add_cont_row + '">';
					html += '  <tr>';
				    html += '  <td class="left">';
				    html += '	<div class="input-group">';
					html += ' 	<input type="text" name="asc_jetcache_settings[add_cont]['+ add_cont_row +'][type_id]" value="'+ add_cont_row +'" class="form-control" size="3">';
				    html += '	</div>';
				    html += '  </td>';

				 	html += '  <td class="right">';


					html += '	<div class="input-group" style="margin-bottom: 3px;">';
					html += '		<input type="text" name="asc_jetcache_settings[add_cont]['+ add_cont_row +'][cont]" value="" class="form-control" style="width: 300px;">';
					html += '	</div>';

					html += '		<td class="right">';

					html += '		<div class="input-group jetcache-text-center">';
					html += '			<div class="input-group">';
					html += '				<textarea class="form-control" style="height: 39px;" name="asc_jetcache_settings[add_cont]['+ add_cont_row +'][only_get]" rows="2" cols="9"></textarea>';
					html += '			</div>';
					html += '		</div>';


					html += '		  <div class="input-group jetcache-text-center">';
					html += '		  	<select class="form-control" name="asc_jetcache_settings[add_cont]['+ add_cont_row +'][no_getpost]">';
					html += '			      <option value="0"><?php echo $text_disabled; ?></option>';
					html += '			      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
					html += '		  	</select>';
					html += '		  </div>';


					html += '						              <div class="input-group jetcache-text-center" style="margin-bottom: 3px;">';
					html += '						              <select class="form-control" name="asc_jetcache_settings[add_cont]['+ add_cont_row +'][no_session]">';

					html += '						                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
					html += '						                  <option value="0"><?php echo $text_disabled; ?></option>';

					html += '						                </select>';
					html += '						                </div>';

					html += '						              <div class="input-group jetcache-text-center" style="margin-bottom: 3px;">';
					html += '						              <select class="form-control" name="asc_jetcache_settings[add_cont]['+ add_cont_row +'][no_url]">';

					html += '						                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
					html += '						                  <option value="0"><?php echo $text_disabled; ?></option>';

					html += '						                </select>';
					html += '						                </div>';

					html += '						              <div class="input-group jetcache-text-center">';
					html += '						              <select class="form-control" name="asc_jetcache_settings[add_cont]['+ add_cont_row +'][no_route]">';

					html += '						                  <option value="1"><?php echo $text_enabled; ?></option>';
					html += '						                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>';

					html += '						                </select>';
					html += '						                </div>';

					html += '		</td>';

					html += '		<td class="right">';
					html += '		  <div class="input-group jetcache-text-center">';
					html += '		  	<select class="form-control" name="asc_jetcache_settings[add_cont]['+ add_cont_row +'][status]">';
					html += '			      <option value="0"><?php echo $text_disabled; ?></option>';
					html += '			      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
					html += '		  	</select>';
					html += '		  </div>';
					html += '		</td>';



				    html += '  </td>';
				    html += '  <td class="left"><a onclick="$(\'#add_cont_row'+add_cont_row+'\').remove(); array_add_cont_row.remove(add_cont_row);" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>';




					html += '  </tr>';
					html += '</tbody>';

					$('#add_conts tfoot').before(html);

					add_cont_row++;
				}
			</script>
			<script type="text/javascript">
				form_submit = function() {
				$('#form').submit();
				return false;
				}
				$('.jetcache_save').bind('click', form_submit);
			</script>
			<script type="text/javascript">
				$('#tabs a').tabs();
				$('#tabs-minify a').tabs();
				$('#tabs-options a').tabs();
				$('#tabs-image a').tabs();
				$('#tabs-controllers a').tabs();



			</script>
			<script type="text/javascript">
				function odd_even() {
					var kz = 0;
					$('table tr').each(function(i,elem) {
					$(this).removeClass('odd');
					$(this).removeClass('even');
						if ($(this).is(':visible')) {
							kz++;
							if (kz % 2 == 0) {
								$(this).addClass('odd');
							}
						}
					});
				}

				$(document).ready(function(){
					odd_even();

					$('.htabs a').click(function() {
						odd_even();
					});

					$('.vtabs a').click(function() {
						odd_even();
					});

				});

				function input_select_change() {

					$('input').each(function(){
						if (!$(this).hasClass('no_change')) {
					        $(this).removeClass('sc_select_enable');
					        $(this).removeClass('sc_select_disable');

							if ( $(this).val() != '' ) {
								$(this).addClass('sc_select_enable');
							} else {
								$(this).addClass('sc_select_disable');
							}
						}
					});

					$('select').each(function(){
						if (!$(this).hasClass('no_change')) {
					        $(this).removeClass('sc_select_enable');
					        $(this).removeClass('sc_select_disable');

							this_val = $(this).find('option:selected').val()

							if (this_val == '1' ) {
								$(this).addClass('sc_select_enable');
							}

							if (this_val == '0' || this_val == '') {
								$(this).addClass('sc_select_disable');
							}

							if (this_val != '0' && this_val != '1' && this_val != '') {
								$(this).addClass('sc_select_other');
							}
						}
					});
				}


				$(document).ready(function(){
					$('.help').hide();

					input_select_change();

					$( "select" )
					  .change(function () {
						input_select_change();

					  });

					$( "input" )
					  .blur(function () {
						input_select_change();
					  });


				});


				$('.jetcache-table-help-href, .jetcache-table-help-href-red').on('click', function() {
					$('.jetcache-table-help').toggle();
				});


				function select_icon_change(id) {
					if (jQuery.type(id) !== 'string') {
						id = $(this).attr('id');
					}

					$('#' + id + '-icon').removeClass('jetcache-red');
					$('#' + id + '-icon').removeClass('jetcache-orange');
					this_val = $('#' + id).find('option:selected').val()

					if (this_val == '1' ) {
						$('#' + id + '-icon').addClass('jetcache-red');
					}

					if (this_val == '0' || this_val == '') {
						$('#' + id + '-icon').removeClass('jetcache-red');
						$('#' + id + '-icon').addClass('jetcache-orange');
					}
				}

				$('#id-query-log-status').on('change', select_icon_change);
				$('#edit_product_id').on('change', select_icon_change);

				select_icon_change('id-query-log-status');
				select_icon_change('edit_product_id');

			</script>

			<script type="text/javascript">
				jQuery(document).ready(function(){
					//jQuery(".image-other-status").chained("#image-webp-status");
					jQuery("#add_product").chained("#edit_product_id");
					jQuery("#edit_product").chained("#edit_product_id");
					jQuery("#id-query-log-status").chained("#id-jetcache-query-status");
				});
			</script>
			<!-- Modal -->
			<div class="modal fade" id="id-modal-file-view" role="dialog">
				<div class="modal-dialog" style="width: 99%; height: 100%;">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"><?php echo $entry_log_file_view; ?></h4>
						</div>
						<textarea class="modal-body" style="width: 100%; overflow: auto;"></textarea>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $text_close; ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<script>
function js_cache_remove() {
	$.ajax({
		url: '<?php echo $url_cache_remove; ?>',
		dataType: 'html',
		beforeSend: function() {
			$('#div_cache_remove').html('<?php echo $language->get('text_loading_main_without'); ?>');
		},
		success: function(content) {
			if (content) {
				$('#div_cache_remove').html('<span style=\'color:green\'>'+content+'<\/span>');
			}
		},
		error: function(content) {
			$('#div_cache_remove').html('<span style=\'color:red\'><?php echo $language->get('text_cache_remove_fail'); ?><\/span>');
		}
	});
	return false;
}
function js_cache_image_remove() {
$.ajax({
	url: '<?php echo $url_cache_image_remove; ?>',
	method: 'POST',
	data: {'ext':$('.image_remove_ext').val()},
	dataType: 'html',
	beforeSend: function() {
		$('.div_cache_image_remove').html('<?php echo $language->get('text_loading_main_without'); ?>');
	},
	success: function(content) {
		if (content) {
			$('.div_cache_image_remove').html('<span style=\'color:green\'>'+content+'<\/span>');
		}
	},
	error: function(content) {
		$('.div_cache_image_remove').html('<span style=\'color:red\'><?php echo $language->get('text_cache_remove_fail'); ?><\/span>');
	}
	});
	return false;
}

if ($.isFunction($.fn.on)) {
	$(document).on('click', '#jetcache_cache_remove', js_cache_remove);
} else {
	$('#jetcache_cache_remove').live('click', js_cache_remove);
}

if ($.isFunction($.fn.on)) {
	$(document).on('click', '.jetcache_cache_image_remove', js_cache_image_remove);
} else {
	$('.jetcache_cache_image_remove').live('click', js_cache_image_remove);
}

<?php if (isset($jc_save) && $jc_save) { ?>
$(document).ready(function() {
	$('#jc_save').click();
});
<?php } ?>


<?php if (isset($refresh_flag) && $refresh_flag) { ?>
$(document).ready(function() {
	$('#jetcache_ocmod_refresh').click();
});
<?php } ?>

</script>

<script>

	string_jc_tabs_click = localStorage.getItem('jc_tabs_click');

	if (string_jc_tabs_click == null) {
		var array_jc_tabs_click = [];
	} else {
		var array_jc_tabs_click = JSON.parse(string_jc_tabs_click);

		array_jc_tabs_click.forEach(function(item, index, array) {
			$('a[href="'+ item + '"]').click();
			console.log(item);
		});
	}

	$('.jc-tab').on('click', function() {
		jc_tab_href = $(this).attr('href');
        array_jc_tabs_click.push(jc_tab_href);
        if (array_jc_tabs_click.length > 3) {
        	array_jc_tabs_click.shift();
        }
        localStorage.setItem('jc_tabs_click', JSON.stringify(array_jc_tabs_click));
	});

</script>
<style>
#sticky {

}

.sticky-back {
background-color: #E1E1E1;
box-shadow: 0 0 11px rgba(0, 0, 0, 0.2) !important;
}

#sticky.stick {
    position: fixed;
    top: 0;
    z-index: 10000;

}


</style>

<script>
function sticky_relocate() {
    var window_top = $(window).scrollTop();
    var div_top = $('#sticky-anchor').offset().top;

    if (window_top > div_top) {
        $('#sticky').addClass('stick');
        $('#sticky').addClass('sticky-back');
        $('#sticky').css( { "right" : "0px" } );

    } else {
        $('#sticky').removeClass('stick');
        $('#sticky').removeClass('sticky-back');
        $('#sticky').css( { "margin-left" : "0px" } );
    }
}

$(function () {
    div_left = $('#sticky').offset().left-60;
    $(window).scroll(sticky_relocate);
    sticky_relocate();
});
</script>



	<?php echo $footer; ?>
</div>
