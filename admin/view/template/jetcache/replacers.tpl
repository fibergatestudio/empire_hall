			<div class="flex-box" style="text-align: center; background-color: #EDF0F2; padding: 2px; margin-top: 8px;">
				<div>
					&nbsp;
				</div>

				<div>
					<span class="nohref" style="font-size: 16px;"><?php echo $language->get('entry_replacers_status'); ?></span>
				</div>

                 <div>
						<div class="input-group jetcache-text-center">
							<select class="form-control" name="asc_jetcache_settings[replacers_status]" id="id-replacers_status">
								<?php if (isset($asc_jetcache_settings['replacers_status']) && $asc_jetcache_settings['replacers_status']) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
						</div>
                 </div>

				<div>
					&nbsp;
				</div>
            </div>

			<div class="flex-box" style="margin-top: 6px; text-align: center; font-weight: 500; background-color: #EDF0F2;">

				<div style="text-align: center; width: 10%;">
                   	<?php echo $language->get('text_replacer_comment'); ?>
	            </div>

				<div style="text-align: center; width: 30%;">
                   	<?php echo $language->get('text_replacer_in'); ?>
	            </div>

				<div style="text-align: center; width: 30%;">
                   	<?php echo $language->get('text_replacer_out'); ?>
	            </div>

				<div style="text-align: center; width: 10%;">
	               	<?php echo $language->get('text_replacer_all'); ?>
	            </div>

				<div style="text-align: center; width: 10%;">
	               	<?php echo $language->get('text_replacer_status'); ?>
	            </div>

				<div style="text-align: center; width: 10%;">
	               	<?php echo $language->get('text_replacer_action'); ?>
	            </div>

			</div>

			<div id="replacers" style="clear: both; width: 100%">
            <?php
				if (!empty($asc_jetcache_settings['replacers'])) {
					foreach ($asc_jetcache_settings['replacers'] as $replacer_num => $replacer) {
            ?>
				<div id="replacer-<?php echo $replacer_num; ?>" class="flex-box" style="text-align: center;">

					<div style="text-align: center; padding: 8px; width: 10%;">
						<div class="input-group">
							<textarea class="form-control" cols="50" rows="3" name="asc_jetcache_settings[replacers][<?php echo $replacer_num; ?>][comment]"><?php if (isset($replacer['comment']) && $replacer['comment'] != '') { echo $replacer['comment']; } else { echo ''; } ?></textarea>
		               	</div>
	                </div>

					<div style="text-align: center; padding: 8px; width: 30%;">
						<div class="input-group">
							<span class="input-group-addon"></span>
							<textarea class="form-control" cols="50" rows="3" name="asc_jetcache_settings[replacers][<?php echo $replacer_num; ?>][in]"><?php if (isset($replacer['in']) && $replacer['in'] != '') { echo $replacer['in']; } else { echo ''; } ?></textarea>
		               	</div>
	                </div>

					<div style="text-align: center; padding: 8px; width: 30%;">
						<div class="input-group">
							<span class="input-group-addon"></span>
							<textarea class="form-control" cols="50" rows="3" name="asc_jetcache_settings[replacers][<?php echo $replacer_num; ?>][out]"><?php if (isset($replacer['out']) && $replacer['out'] != '') { echo $replacer['out']; } else { echo ''; } ?></textarea>
		               	</div>
	                </div>

					<div style="text-align: center; padding: 8px; width: 10%;">
						<div class="input-group jetcache-text-center">
							<select class="form-control" name="asc_jetcache_settings[replacers][<?php echo $replacer_num; ?>][all]">
								<?php if (isset($replacer['all']) && $replacer['all']) { ?>
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
						<div class="input-group jetcache-text-center">
							<select class="form-control" name="asc_jetcache_settings[replacers][<?php echo $replacer_num; ?>][status]">
								<?php if (isset($replacer['status']) && $replacer['status']) { ?>
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
                    	<a onclick="$('#replacer-<?php echo $replacer_num; ?>').remove();" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a>
	                </div>
                </div>

              <?php
              			$replacer_num++;
					}
				}
              ?>
			</div>

            <div id="replacers-add" style="clear: both; text-align: center; margin-top:10px; width: 100%">
	            <a id="replacers-add-a" onclick="addReplacer();" class="markbutton nohref"><?php echo $language->get('entry_add_rule'); ?></a>
			</div>



			<div class="flex-box" style="text-align: center; background-color: #EDF0F2; padding: 2px; margin-top: 16px; margin-bottom: 8px;">
				<div>
					&nbsp;
				</div>

				<div>
					<?php echo $language->get('entry_lazy_ex_route'); ?>
				</div>

				<div>
					&nbsp;
				</div>
            </div>


			<div class="input-group"><span class="input-group-addon"></span>
				<textarea class="form-control" name="asc_jetcache_settings[replacers_ex_route]" rows="5" cols="50"><?php if (isset($asc_jetcache_settings['replacers_ex_route'])) { echo $asc_jetcache_settings['replacers_ex_route']; } else { echo ''; } ?></textarea>
			</div>





<script>
var replacer_num_array = new Array();
//replacer_num_array.push(0);
<?php

if (!empty($asc_jetcache_settings['replacers'])) {
	foreach ($asc_jetcache_settings['replacers'] as $replacer_num => $replacer) {
?>
replacer_num_array.push(<?php echo $replacer_num; ?>);
<?php
	}
}
?>
</script>


<script>
function addReplacer() {

	var replacer_index = -1;
	for(replacer_i = 0; replacer_i < replacer_num_array.length; replacer_i++) {
	 replacer_flg = jQuery.inArray(replacer_i, replacer_num_array);
	 if (replacer_flg == -1) { replacer_index = replacer_i; }
	}
	if (replacer_index == -1) { replacer_index = replacer_num_array.length; }
	replacer_num = replacer_index;
	replacer_num_array.push(replacer_index);
	console.log(replacer_index);

html  = '				<div id="replacer-' + replacer_num + '" class="flex-box" style="text-align: center;">';

html += '					<div style="text-align: center; padding: 8px; width: 10%;">';
html += '						<div class="input-group">';
html += '							<span class="input-group-addon"></span>';
html += '							<textarea class="form-control" cols="50" rows="3" name="asc_jetcache_settings[replacers][' + replacer_num + '][comment]"></textarea>';
html += '		               	</div>';
html += '	                </div>';
html += '					<div style="text-align: center; padding: 8px; width: 30%;">';
html += '						<div class="input-group">';
html += '							<span class="input-group-addon"></span>';
html += '							<textarea class="form-control" cols="50" rows="3" name="asc_jetcache_settings[replacers][' + replacer_num + '][in]"></textarea>';
html += '		               	</div>';
html += '	                </div>';

html += '					<div style="text-align: center; padding: 8px; width: 30%;">';
html += '						<div class="input-group">';
html += '							<span class="input-group-addon"></span>';
html += '							<textarea class="form-control" cols="50" rows="3" name="asc_jetcache_settings[replacers][' + replacer_num + '][out]"></textarea>';
html += '		               	</div>';
html += '	                </div>';


html += '					<div style="text-align: center; padding: 8px; width: 10%;">';
html += '						<div class="input-group jetcache-text-center">';
html += '							<select class="form-control" name="asc_jetcache_settings[replacers][' + replacer_num + '][all]">';
html += '								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
html += '								<option value="0"><?php echo $text_disabled; ?></option>';
html += '							</select>';
html += '						</div>';
html += '					</div>';

html += '					<div style="text-align: center; padding: 8px; width: 10%;">';
html += '						<div class="input-group jetcache-text-center">';
html += '							<select class="form-control" name="asc_jetcache_settings[replacers][' + replacer_num + '][status]">';
html += '								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
html += '								<option value="0"><?php echo $text_disabled; ?></option>';
html += '							</select>';
html += '						</div>';
html += '					</div>';
html += '					<div style="text-align: center; padding: 8px; width: 10%;">';
html += '                    	<a onclick="$(\'#replacer-' + replacer_num + '\').remove();" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a>';
html += '	                </div>';
html += '                </div>';


	$('#replacers').append(html);

}
</script>

<style>
.flex-box {
	display: flex;
	align-items: center;
	align-content: stretch;
	justify-content: space-between;
}
.flex-box > div {
	 width: 33.3%;
}
</style>
