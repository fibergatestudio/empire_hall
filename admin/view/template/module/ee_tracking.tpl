<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="hide-screen">
    <div class="spinner-wrapper"><i class="fa fa-spinner fa-pulse fa-2x" aria-hidden="true"></i></div>
  </div>
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button onclick="saveSettings($(this))" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-pulse'></i> <?php echo $text_loading; ?>"><i class="fa fa-floppy-o"></i></button>
        <button onclick="changeTranslator()" data-toggle="tooltip" title="<?php echo $button_translator; ?>" class="btn <?php if (isset($translator) && $translator) { ?>btn-info<?php } else { ?>btn-default<?php } ?>"><i class="fa fa-language"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1>
        <i class="fa fa-line-chart"></i> <?php echo $heading_title_main; ?>
        <div class="version"><?php echo $module_version; ?></div>
        <div class="developer"><a href="<?php echo $dev_site; ?>" target="_blank">by <?php echo $author; ?></a></div>
      </h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="message-wrapper">
      <?php if (isset($error['warning'])) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['warning']; ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php } ?>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">
          <div class="col-sm-6">
            <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
          </div>
          <div class="col-sm-6">
            <div class="pull-right">
              <?php if (isset($translator) && $translator) { ?>
                <div id="google_translate_element" style="margin-bottom: -13px;margin-top: -13px;"></div>
                <script type="text/javascript">
                  function googleTranslateElementInit() {
                    new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.VERTICAL, autoDisplay: false, multilanguagePage: true}, 'google_translate_element');
                  }
                </script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
          <input type="hidden" name="translator" id="translator" value="<?php echo isset($translator) ? $translator : ''; ?>"/>
          <input type="hidden" name="ee_tracking_module_version" value="<?php echo isset($module_version) ? $module_version : ''; ?>"/>
          <?php if (count($stores) > 1) { ?>
          <div class="form-group" id="form-group-store_id" <?php if ($store_id == 0 && (!isset($ee_tracking_multistore) || $ee_tracking_multistore == 0)) { ?>style="display: none;"<?php } ?>>
            <label class="col-sm-2 control-label" for="select-store_id"><?php echo $entry_store; ?></label>
            <div class="col-sm-10">
              <select name="store_id" id="select-store_id" class="form-control">
                <?php foreach ($stores as $store) { ?>
                  <option value="<?php echo $store['store_id']; ?>" <?php if ($store['store_id'] == $store_id) { ?>selected="selected"<?php } ?>><?php echo $store['name']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <?php } ?>
          <div class="col-xs-3">
          <ul class="nav nav-tabs tabs-left">
            <li class="active"><a href="#tab-general" data-toggle="tab"><i class="fa fa-home tab-icon"></i> <?php echo $tab_general; ?></a></li>
            <li><a href="#tab-advanced" data-toggle="tab"><i class="fa fa-expand tab-icon"></i> <?php echo $tab_advanced; ?></a></li>
            <li><a href="#tab-impression" data-toggle="tab"><i class="fa fa-eye tab-icon"></i> <?php echo $tab_impression; ?></a></li>
            <li><a href="#tab-click" data-toggle="tab"><i class="fa fa-check tab-icon"></i> <?php echo $tab_click; ?></a></li>
            <li><a href="#tab-detail" data-toggle="tab"><i class="fa fa-search-plus tab-icon"></i> <?php echo $tab_detail; ?></a></li>
            <li><a href="#tab-cart" data-toggle="tab"><i class="fa fa-shopping-cart tab-icon"></i> <?php echo $tab_cart; ?></a></li>
            <li><a href="#tab-checkout" data-toggle="tab"><i class="fa fa-truck tab-icon"></i> <?php echo $tab_checkout; ?></a></li>
            <li><a href="#tab-transaction" data-toggle="tab"><i class="fa fa-money tab-icon"></i> <?php echo $tab_transaction; ?></a></li>
            <li><a href="#tab-refund" data-toggle="tab"><i class="fa fa-reply tab-icon"></i> <?php echo $tab_refund; ?></a></li>
            <li><a href="#tab-promotion" data-toggle="tab"><i class="fa fa-bullhorn tab-icon"></i> <?php echo $tab_promotion; ?></a></li>
            <li><a href="#tab-custom-dimension" data-toggle="tab"><i class="fa fa-arrows-alt tab-icon"></i> <?php echo $tab_custom_dimension; ?></a></li>
            <li><a href="#tab-filter" data-toggle="tab"><i class="fa fa-filter tab-icon"></i> <?php echo $tab_filter; ?></a></li>
            <li><a href="#tab-log" data-toggle="tab"><i class="fa fa-bars tab-icon"></i> <?php echo $tab_log; ?></a></li>
            <?php if ($store_id == 0) { ?>
            <li><a href="#tab-help" data-toggle="tab" onclick="loadIframe('iframe-support-request');"><i class="fa fa-life-ring tab-icon"></i> <?php echo $tab_help; ?></a></li>
            <?php } ?>
          </ul>
          </div>
          <div class="col-xs-9">
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <fieldset>
                <legend><?php echo $legend_bulk; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label">
                    <span data-toggle="tooltip" title="<?php echo $help_all_status; ?>"><?php echo $entry_status; ?></span>
                    <p>(<?php echo $text_configuring_only; ?>)</p>
                  </label>
                  <div class="col-sm-2">
                    <input type="hidden" name="ee_tracking_all_status" value="<?php echo isset($ee_tracking_all_status) ? $ee_tracking_all_status : 0; ?>"/>
                    <?php if (isset($ee_tracking_all_status) && $ee_tracking_all_status) { ?>
                      <a onclick="clickAll($(this),$('.btn-status'));changeStatus($(this));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="clickAll($(this),$('.btn-status'));changeStatus($(this));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                  <label class="col-sm-2 control-label">
                    <span data-toggle="tooltip" title="<?php echo $help_all_debug; ?>"><?php echo $entry_debug; ?></span>
                    <p>(<?php echo $text_configuring_only; ?>)</p>
                  </label>
                  <div class="col-sm-2">
                    <input type="hidden" name="ee_tracking_all_debug" value="<?php echo isset($ee_tracking_all_debug) ? $ee_tracking_all_debug : 0; ?>"/>
                    <?php if (isset($ee_tracking_all_debug) && $ee_tracking_all_debug) { ?>
                      <a onclick="clickAll($(this),$('.btn-debug'));changeStatus($(this));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="clickAll($(this),$('.btn-debug'));changeStatus($(this));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                  <label class="col-sm-2 control-label">
                    <span data-toggle="tooltip" title="<?php echo $help_all_log; ?>"><?php echo $entry_log; ?></span>
                    <p>(<?php echo $text_configuring_only; ?>)</p>
                  </label>
                  <div class="col-sm-2">
                    <input type="hidden" name="ee_tracking_all_log" value="<?php echo isset($ee_tracking_all_log) ? $ee_tracking_all_log : 0; ?>"/>
                    <?php if (isset($ee_tracking_all_log) && $ee_tracking_all_log) { ?>
                      <a onclick="clickAll($(this),$('.btn-log'));changeStatus($(this));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="clickAll($(this),$('.btn-log'));changeStatus($(this));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <legend><?php echo $legend_general; ?></legend>
                <?php if (count($stores) > 1 && $store_id == 0) { ?>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-multistore"><span data-toggle="tooltip" title="<?php echo $help_multistore; ?>"><?php echo $entry_multistore; ?></span></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_multistore" value="<?php echo isset($ee_tracking_multistore) ? $ee_tracking_multistore : 0; ?>"/>
                    <?php if (isset($ee_tracking_multistore) && $ee_tracking_multistore) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#form-group-store_id'));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#form-group-store_id'));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <?php } ?>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-status"><span data-toggle="tooltip" title="<?php echo $help_status; ?>"><?php echo $entry_global_status; ?></span></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_status" value="<?php echo isset($ee_tracking_status) ? $ee_tracking_status : 0; ?>"/>
                    <?php if (isset($ee_tracking_status) && $ee_tracking_status) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('.general-group'));changeOpacity($('.opacity-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('.general-group'));changeOpacity($('.opacity-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset class="general-group" <?php if (isset($ee_tracking_status) && $ee_tracking_status) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-tracking-id"><span data-toggle="tooltip" title="<?php echo $help_tracking_id; ?>"><?php echo $entry_tracking_id; ?></span></label>
                    <div class="col-sm-10">
                      <input type="text" name="ee_tracking_tracking_id" value="<?php echo isset($ee_tracking_tracking_id) ? $ee_tracking_tracking_id : ''; ?>" placeholder="<?php echo $entry_tracking_id; ?>" id="input-tracking-id" class="form-control notranslate" />
                      <div class="error-wrapper error_tracking_id"></div>
                      <p class="text-info"><?php echo $note_tracking_id; ?></p>
                    </div>
                  </div>
                  <?php if ($store_id == 0) { ?>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-order-id"><span data-toggle="tooltip" title="<?php echo $help_order_id; ?>"><?php echo $entry_order_id; ?></span></label>
                    <div class="col-sm-10">
                      <input type="text" name="ee_tracking_order_id" value="<?php echo isset($ee_tracking_order_id) ? $ee_tracking_order_id: ''; ?>" placeholder="<?php echo $entry_order_id; ?>" id="input-order-id" class="form-control notranslate" />
                      <div class="error-wrapper error_order_id"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-js_position"><span data-toggle="tooltip" title="<?php echo $help_js_position; ?>"><?php echo $entry_js_position; ?></span></label>
                    <div class="col-sm-10">
                      <div class="input-group">
                        <select name="ee_tracking_js_position" id="input-js_position" class="form-control">
                          <option value="0" <?php if (!isset($ee_tracking_js_position) || $ee_tracking_js_position == 0) { ?>selected="selected"<?php } ?>><?php echo $text_js_position_0; ?></option>
                          <option value="1" <?php if (isset($ee_tracking_js_position) && $ee_tracking_js_position == 1) { ?>selected="selected"<?php } ?>><?php echo $text_js_position_1; ?></option>
                          <option value="2" <?php if (isset($ee_tracking_js_position) && $ee_tracking_js_position == 2) { ?>selected="selected"<?php } ?>><?php echo $text_js_position_2; ?></option>
                          <option value="3" <?php if (isset($ee_tracking_js_position) && $ee_tracking_js_position == 3) { ?>selected="selected"<?php } ?>><?php echo $text_js_position_3; ?></option>
                        </select>
                        <?php if ($js_position) { ?>
                        <span class="js_position_status input-group-addon success"  data-toggle="tooltip" title="<?php echo $help_success_js_position; ?>"><i class="fa fa-check" aria-hidden="true"></i></span>
                        <?php } else { ?>
                        <?php if ($js_default) { ?>
                        <span class="js_position_status input-group-addon danger" data-toggle="tooltip" title="<?php echo $help_danger_js_position; ?>"><i class="fa fa-times" aria-hidden="true"></i></span>
                        <?php } else { ?>
                        <span class="js_position_status input-group-addon warning" data-toggle="tooltip" title="<?php echo $help_warning_js_position; ?>"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                        <?php } ?>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                  <?php } ?>
                </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-advanced">
              <fieldset>
                <legend><?php echo $legend_advanced; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-advanced_settings"><span data-toggle="tooltip" title="<?php echo $help_advanced_settings; ?>"><?php echo $entry_advanced_settings; ?></span></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_advanced_settings" value="<?php echo isset($ee_tracking_advanced_settings) ? $ee_tracking_advanced_settings : 0; ?>"/>
                    <?php if (isset($ee_tracking_advanced_settings) && $ee_tracking_advanced_settings) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-settings-group'));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-settings-group'));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset id="advanced-settings-group" <?php if (isset($ee_tracking_advanced_settings) && $ee_tracking_advanced_settings) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-language"><span data-toggle="tooltip" title="<?php echo $help_language; ?>"><?php echo $entry_language; ?></span></label>
                    <div class="col-sm-10">
                      <select name="ee_tracking_language_id" id="input-language" class="form-control">
                        <option value="0" selected="selected"><?php echo $text_multilingual; ?></option>
                        <?php foreach ($languages as $language) { ?>
                          <option value="<?php echo $language['language_id']; ?>" <?php if (isset($ee_tracking_language_id) && $ee_tracking_language_id == $language['language_id']) { ?>selected="selected"<?php } ?>><?php echo $language['name']; ?> (<?php echo $language['code']; ?>)</option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-currency"><span data-toggle="tooltip" title="<?php echo $help_currency; ?>"><?php echo $entry_currency; ?></span></label>
                    <div class="col-sm-10">
                      <select name="ee_tracking_currency" id="input-currency" class="form-control">
                        <option value="0" selected="selected"><?php echo $text_multicurrency; ?></option>
                        <?php foreach ($currencies as $currency) { ?>
                          <?php if (isset($ee_tracking_currency) && $ee_tracking_currency == $currency['code']) { ?>
                            <option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['title']; ?> (<?php echo $currency['code']; ?>)</option>
                          <?php } else { ?>
                            <option value="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?> (<?php echo $currency['code']; ?>)</option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                      <p class="text-info"><?php echo $note_currency; ?></p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-tax"><span data-toggle="tooltip" title="<?php echo $help_tax; ?>"><?php echo $entry_tax; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_tax" value="<?php echo isset($ee_tracking_tax) ? $ee_tracking_tax : 0; ?>"/>
                      <?php if (isset($ee_tracking_tax) && $ee_tracking_tax) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-total_shipping"><span data-toggle="tooltip" title="<?php echo $help_total_shipping; ?>"><?php echo $entry_total_shipping; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_total_shipping" value="<?php echo isset($ee_tracking_total_shipping) ? $ee_tracking_total_shipping : 0; ?>"/>
                      <?php if (isset($ee_tracking_total_shipping) && $ee_tracking_total_shipping) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-total_tax"><span data-toggle="tooltip" title="<?php echo $help_total_tax; ?>"><?php echo $entry_total_tax; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_total_tax" value="<?php echo isset($ee_tracking_total_tax) ? $ee_tracking_total_tax : 0; ?>"/>
                      <?php if (isset($ee_tracking_total_tax) && $ee_tracking_total_tax) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-affiliation">
                      <span data-toggle="tooltip" title="<?php echo $help_affiliation; ?>"><?php echo $entry_affiliation; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="text" name="ee_tracking_affiliation" value="<?php echo isset($ee_tracking_affiliation) ? $ee_tracking_affiliation : ''; ?>" placeholder="<?php echo $entry_affiliation; ?>" id="input-affiliation" class="form-control notranslate" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-product-id">
                      <span data-toggle="tooltip" title="<?php echo $help_product_id; ?>"><?php echo $entry_product_id; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <select name="ee_tracking_product_id" id="input-product_id" class="form-control">
                        <option value="product_id" <?php if (!isset($ee_tracking_product_id) || $ee_tracking_product_id == 'product_id') { ?>selected="selected"<?php } ?>><?php echo $text_product_id_0; ?></option>
                        <option value="sku" <?php if (isset($ee_tracking_product_id) && $ee_tracking_product_id == 'sku') { ?>selected="selected"<?php } ?>><?php echo $text_product_id_1; ?></option>
                        <option value="model" <?php if (isset($ee_tracking_product_id) && $ee_tracking_product_id == 'model') { ?>selected="selected"<?php } ?>><?php echo $text_product_id_2; ?></option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-product-category"><span data-toggle="tooltip" title="<?php echo $help_product_category; ?>"><?php echo $entry_product_category; ?></span></label>
                    <div class="col-sm-10">
                      <select name="ee_tracking_product_category" id="input-product-category" class="form-control">
                        <option value="0" <?php if (!isset($ee_tracking_product_category) || $ee_tracking_product_category == 0) { ?>selected="selected"<?php } ?>><?php echo $text_product_category_0; ?></option>
                        <option value="1" <?php if (isset($ee_tracking_product_category) && $ee_tracking_product_category == 1) { ?>selected="selected"<?php } ?>><?php echo $text_product_category_1; ?></option>
                        <option value="2" <?php if (isset($ee_tracking_product_category) && $ee_tracking_product_category == 2) { ?>selected="selected"<?php } ?>><?php echo $text_product_category_2; ?></option>
                        <option value="3" <?php if (isset($ee_tracking_product_category) && $ee_tracking_product_category == 3) { ?>selected="selected"<?php } ?>><?php echo $text_product_category_3; ?></option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-compatibility"><span data-toggle="tooltip" title="<?php echo $help_compatibility; ?>"><?php echo $entry_compatibility; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_compatibility" value="<?php echo isset($ee_tracking_compatibility) ? $ee_tracking_compatibility : 0; ?>"/>
                      <?php if (isset($ee_tracking_compatibility) && $ee_tracking_compatibility) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                      <p class="text-info"><?php echo $note_compatibility; ?></p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-generate_cid"><span data-toggle="tooltip" title="<?php echo $help_generate_cid; ?>"><?php echo $entry_generate_cid; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_generate_cid" value="<?php echo isset($ee_tracking_generate_cid) ? $ee_tracking_generate_cid : 0; ?>"/>
                      <?php if (isset($ee_tracking_generate_cid) && $ee_tracking_generate_cid) { ?>
                      <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                      <?php } else { ?>
                      <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                      <?php } ?>
                      <p class="text-info"><?php echo $note_generate_cid; ?></p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ga_callback"><span data-toggle="tooltip" title="<?php echo $help_ga_callback; ?>"><?php echo $entry_ga_callback; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_ga_callback" value="<?php echo isset($ee_tracking_ga_callback) ? $ee_tracking_ga_callback : 0; ?>"/>
                      <?php if (isset($ee_tracking_ga_callback) && $ee_tracking_ga_callback) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#callback-info'));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                      <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#callback-info'));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group" id="callback-info" <?php if (isset($ee_tracking_ga_callback) && $ee_tracking_ga_callback) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-10">
                      <p class="text-info"><?php echo $note_callback; ?></p>
                      <div class="tab-pane" id="tab-callback">
                        <ul class="nav nav-tabs">
                          <li class="active"><a href="#tab-gtag" data-toggle="tab"><i class="fa fa-code tab-icon" aria-hidden="true"></i> Global Site Tag (gtag.js)</a></li>
                          <li><a href="#tab-ga" data-toggle="tab"><i class="fa fa-code tab-icon" aria-hidden="true"></i> analytics.js</a></li>
                          <li><a href="#tab-gtm" data-toggle="tab"><i class="fa fa-code tab-icon" aria-hidden="true"></i> Google Tag Manager</a></li>
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane active" id="tab-gtag">
                            <p class="text-info"><?php echo $text_gtag_tab; ?></p>
                            <?php echo $text_before_changes; ?>
                            <textarea wrap="off" rows="8" class="form-control notranslate">
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXXXXX-YY"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-XXXXXXXX-YY');
</script>
                            </textarea>
                            <?php echo $text_after_changes; ?>
                            <textarea wrap="off" rows="10" class="form-control notranslate">
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXXXXX-YY"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-XXXXXXXX-YY', { 'send_page_view': false });

  gtag('event', 'page_view', { 'event_callback': function() { ee_start = 1; } });
</script>
                            </textarea>
                          </div>
                          <div class="tab-pane" id="tab-ga">
                            <p class="text-info"><?php echo $text_ga_tab; ?></p>
                            <?php echo $text_before_changes; ?>
                            <textarea wrap="off" rows="8" class="form-control notranslate">
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-XXXXX-Y', 'auto');
ga('send', 'pageview');
</script>
                            </textarea>
                            <?php echo $text_after_changes; ?>
                            <textarea wrap="off" rows="8" class="form-control notranslate">
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-XXXXXXXX-YY', 'auto');
ga('send', 'pageview', { 'hitCallback': function() { ee_start = 1; } });
</script>
                            </textarea>
                          </div>
                          <div class="tab-pane" id="tab-gtm">
                            <p class="text-info"><?php echo $text_gtm_tab; ?></p>
                            <textarea wrap="off" rows="5" class="form-control notranslate">
function() {
    return function() {
        ee_start = 1;
    }
}
                            </textarea>
                            <br>
                            <p><?php echo $text_video_instruction; ?></p>
                            <div class="video-wrapper">
                            <iframe width="512" height="288" src="https://www.youtube.com/embed/ivH0VbUaIUs" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-impression">
              <fieldset>
              <legend><?php echo $legend_impression; ?></legend>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-impression-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <input type="hidden" name="ee_tracking_impression_status" value="<?php echo isset($ee_tracking_impression_status) ? $ee_tracking_impression_status : 0; ?>"/>
                  <?php if (isset($ee_tracking_impression_status) && $ee_tracking_impression_status) { ?>
                    <a onclick="changeStatus($(this));changeDisplay($('#advanced-impression-group'));return false;" class="btn-switcher btn-status">
                      <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                    </a>
                  <?php } else { ?>
                    <a onclick="changeStatus($(this));changeDisplay($('#advanced-impression-group'));return false;" class="btn-switcher btn-status">
                      <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                    </a>
                  <?php } ?>
                </div>
              </div>
              <fieldset id="advanced-impression-group" <?php if (isset($ee_tracking_impression_status) && $ee_tracking_impression_status) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-impression-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_impression_debug" value="<?php echo isset($ee_tracking_impression_debug) ? $ee_tracking_impression_debug : 0; ?>"/>
                    <?php if (isset($ee_tracking_impression_debug) && $ee_tracking_impression_debug) { ?>
                      <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-impression-log">
                    <span data-toggle="tooltip" title="<?php echo $help_log; ?>"><?php echo $entry_log; ?></span>
                    <p>(<?php echo $text_required_debug; ?>)</p>
                  </label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_impression_log" value="<?php echo isset($ee_tracking_impression_log) ? $ee_tracking_impression_log : 0; ?>"/>
                    <?php if (isset($ee_tracking_impression_log) && $ee_tracking_impression_log) { ?>
                      <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
              </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-click">
              <fieldset>
                <legend><?php echo $legend_click; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-click-status"><?php echo $entry_status; ?></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_click_status" value="<?php echo isset($ee_tracking_click_status) ? $ee_tracking_click_status : 0; ?>"/>
                    <?php if (isset($ee_tracking_click_status) && $ee_tracking_click_status) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-click-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-click-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset id="advanced-click-group" <?php if (isset($ee_tracking_click_status) && $ee_tracking_click_status) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-click-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_click_debug" value="<?php echo isset($ee_tracking_click_debug) ? $ee_tracking_click_debug : 0; ?>"/>
                      <?php if (isset($ee_tracking_click_debug) && $ee_tracking_click_debug) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-click-log">
                      <span data-toggle="tooltip" title="<?php echo $help_log; ?>"><?php echo $entry_log; ?></span>
                      <p>(<?php echo $text_required_debug; ?>)</p>
                    </label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_click_log" value="<?php echo isset($ee_tracking_click_log) ? $ee_tracking_click_log : 0; ?>"/>
                      <?php if (isset($ee_tracking_click_log) && $ee_tracking_click_log) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-detail">
              <fieldset>
                <legend><?php echo $legend_detail; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-detail-status"><?php echo $entry_status; ?></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_detail_status" value="<?php echo isset($ee_tracking_detail_status) ? $ee_tracking_detail_status : 0; ?>"/>
                    <?php if (isset($ee_tracking_detail_status) && $ee_tracking_detail_status) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-detail-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-detail-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset id="advanced-detail-group" <?php if (isset($ee_tracking_detail_status) && $ee_tracking_detail_status) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-detail-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_detail_debug" value="<?php echo isset($ee_tracking_detail_debug) ? $ee_tracking_detail_debug : 0; ?>"/>
                      <?php if (isset($ee_tracking_detail_debug) && $ee_tracking_detail_debug) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-detail-log">
                      <span data-toggle="tooltip" title="<?php echo $help_log; ?>"><?php echo $entry_log; ?></span>
                      <p>(<?php echo $text_required_debug; ?>)</p>
                    </label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_detail_log" value="<?php echo isset($ee_tracking_detail_log) ? $ee_tracking_detail_log : 0; ?>"/>
                      <?php if (isset($ee_tracking_detail_log) && $ee_tracking_detail_log) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-cart">
              <fieldset>
                <legend><?php echo $legend_cart; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-cart-status"><?php echo $entry_status; ?></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_cart_status" value="<?php echo isset($ee_tracking_cart_status) ? $ee_tracking_cart_status : 0; ?>"/>
                    <?php if (isset($ee_tracking_cart_status) && $ee_tracking_cart_status) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-cart-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-cart-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset id="advanced-cart-group" <?php if (isset($ee_tracking_cart_status) && $ee_tracking_cart_status) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-cart-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_cart_debug" value="<?php echo isset($ee_tracking_cart_debug) ? $ee_tracking_cart_debug : 0; ?>"/>
                      <?php if (isset($ee_tracking_cart_debug) && $ee_tracking_cart_debug) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-cart-log">
                      <span data-toggle="tooltip" title="<?php echo $help_log; ?>"><?php echo $entry_log; ?></span>
                      <p>(<?php echo $text_required_debug; ?>)</p>
                    </label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_cart_log" value="<?php echo isset($ee_tracking_cart_log) ? $ee_tracking_cart_log : 0; ?>"/>
                      <?php if (isset($ee_tracking_cart_log) && $ee_tracking_cart_log) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-checkout">
              <fieldset>
                <legend><?php echo $legend_checkout; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-checkout-status"><?php echo $entry_status; ?></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_checkout_status" value="<?php echo isset($ee_tracking_checkout_status) ? $ee_tracking_checkout_status : 0; ?>"/>
                    <?php if (isset($ee_tracking_checkout_status) && $ee_tracking_checkout_status) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-checkout-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-checkout-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset id="advanced-checkout-group" <?php if (isset($ee_tracking_checkout_status) && $ee_tracking_checkout_status) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-checkout-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_checkout_debug" value="<?php echo isset($ee_tracking_checkout_debug) ? $ee_tracking_checkout_debug : 0; ?>"/>
                      <?php if (isset($ee_tracking_checkout_debug) && $ee_tracking_checkout_debug) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-checkout-log">
                      <span data-toggle="tooltip" title="<?php echo $help_log; ?>"><?php echo $entry_log; ?></span>
                      <p>(<?php echo $text_required_debug; ?>)</p>
                    </label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_checkout_log" value="<?php echo isset($ee_tracking_checkout_log) ? $ee_tracking_checkout_log : 0; ?>"/>
                      <?php if (isset($ee_tracking_checkout_log) && $ee_tracking_checkout_log) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-checkout_custom"><span data-toggle="tooltip" title="<?php echo $help_checkout_custom; ?>"><?php echo $entry_checkout_custom; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_checkout_custom" value="<?php echo isset($ee_tracking_checkout_custom) ? $ee_tracking_checkout_custom : 0; ?>"/>
                      <?php if (isset($ee_tracking_checkout_custom) && $ee_tracking_checkout_custom) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#checkout-url'));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                      <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#checkout-url'));return false;" class="btn-switcher">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                      <?php } ?>
                      <p class="text-info notranslate"><?php echo $note_checkout_custom; ?></p>
                    </div>
                  </div>
                  <div class="form-group required" id="checkout-url" <?php if (isset($ee_tracking_checkout_custom) && $ee_tracking_checkout_custom) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                    <label class="col-sm-2 control-label" for="input-checkout_url"><span data-toggle="tooltip" title="<?php echo $help_checkout_url; ?>"><?php echo $entry_checkout_url; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" id="js_version" name="ee_tracking_js_version" value="<?php echo isset($ee_tracking_js_version) ? $ee_tracking_js_version : ''; ?>"/>
                      <?php foreach ($languages as $language) { ?>
                        <div class="input-group"><span class="input-group-addon"><img src="<?php echo file_exists('language/'.$language['code'].'/'.$language['code'].'.png') ? 'language/'.$language['code'].'/'.$language['code'].'.png' : 'view/image/flags/'.$language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
                          <input type="text" name="ee_tracking_checkout_url[<?php echo $language['language_id']; ?>]" value="<?php echo isset($ee_tracking_checkout_url[$language['language_id']]) ? $ee_tracking_checkout_url[$language['language_id']] : ''; ?>" placeholder="<?php echo $entry_checkout_url; ?>" class="form-control notranslate" />
                        </div>
                        <div class="error-wrapper error_checkout_url_<?php echo $language['language_id']; ?>"></div>
                      <?php } ?>
                      <p class="text-info notranslate"><?php echo $note_checkout_url; ?></p>
                    </div>
                  </div>
                </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-transaction">
              <fieldset>
                <legend><?php echo $legend_transaction; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-transaction-status"><?php echo $entry_status; ?></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_transaction_status" value="<?php echo isset($ee_tracking_transaction_status) ? $ee_tracking_transaction_status : 0; ?>"/>
                    <?php if (isset($ee_tracking_transaction_status) && $ee_tracking_transaction_status) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-transaction-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-transaction-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset id="advanced-transaction-group" <?php if (isset($ee_tracking_transaction_status) && $ee_tracking_transaction_status) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-transaction-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_transaction_debug" value="<?php echo isset($ee_tracking_transaction_debug) ? $ee_tracking_transaction_debug : 0; ?>"/>
                      <?php if (isset($ee_tracking_transaction_debug) && $ee_tracking_transaction_debug) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-transaction-log">
                      <span data-toggle="tooltip" title="<?php echo $help_log; ?>"><?php echo $entry_log; ?></span>
                      <p>(<?php echo $text_required_debug; ?>)</p>
                    </label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_transaction_log" value="<?php echo isset($ee_tracking_transaction_log) ? $ee_tracking_transaction_log : 0; ?>"/>
                      <?php if (isset($ee_tracking_transaction_log) && $ee_tracking_transaction_log) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-order-status"><span data-toggle="tooltip" title="<?php echo $help_order_status; ?>"><?php echo $entry_order_status; ?></span></label>
                    <div class="col-sm-10">
                      <div class="well well-sm" style="height: 150px; overflow: auto;">
                        <?php foreach ($order_statuses as $order_status) { ?>
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="ee_tracking_order_status[]" value="<?php echo isset($order_status['order_status_id']) ? $order_status['order_status_id'] : ''; ?>" <?php if (isset($ee_tracking_order_status) && in_array($order_status['order_status_id'], $ee_tracking_order_status)) { ?>checked="checked"<?php } ?> />
                              <?php echo $order_status['name']; ?>
                            </label>
                          </div>
                        <?php } ?>
                      </div>
                      <div class="error-wrapper error_order_status"></div>
                    </div>
                  </div>
                </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-refund">
              <fieldset>
                <legend><?php echo $legend_refund; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-refund-status"><?php echo $entry_status; ?></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_refund_status" value="<?php echo isset($ee_tracking_refund_status) ? $ee_tracking_refund_status : 0; ?>"/>
                    <?php if (isset($ee_tracking_refund_status) && $ee_tracking_refund_status) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-refund-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-refund-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset id="advanced-refund-group" <?php if (isset($ee_tracking_refund_status) && $ee_tracking_refund_status) { ?>style="display:block"<?php } else { ?>style="display:none"<?php } ?>>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-refund-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_refund_debug" value="<?php echo isset($ee_tracking_refund_debug) ? $ee_tracking_refund_debug : 0; ?>"/>
                      <?php if (isset($ee_tracking_refund_debug) && $ee_tracking_refund_debug) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-refund-log">
                      <span data-toggle="tooltip" title="<?php echo $help_log; ?>"><?php echo $entry_log; ?></span>
                      <p>(<?php echo $text_required_debug; ?>)</p>
                    </label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_refund_log" value="<?php echo isset($ee_tracking_refund_log) ? $ee_tracking_refund_log : 0; ?>"/>
                      <?php if (isset($ee_tracking_refund_log) && $ee_tracking_refund_log) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-refund-order-status"><span data-toggle="tooltip" title="<?php echo $help_status; ?>"><?php echo $entry_order_status; ?></span></label>
                    <div class="col-sm-10">
                      <div class="well well-sm" style="height: 150px; overflow: auto;">
                        <?php foreach ($order_statuses as $order_status) { ?>
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="ee_tracking_refund_order_status[]" value="<?php echo isset($order_status['order_status_id']) ? $order_status['order_status_id'] : ''; ?>" <?php if (isset($ee_tracking_refund_order_status) && in_array($order_status['order_status_id'], $ee_tracking_refund_order_status)) { ?>checked="checked"<?php } ?> />
                              <?php echo $order_status['name']; ?>
                            </label>
                          </div>
                        <?php } ?>
                      </div>
                      <div class="error-wrapper error_refund_order_status"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-customer-refund">
                      <span data-toggle="tooltip" title="<?php echo $help_customer_refund; ?>"><?php echo $entry_customer_refund; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_customer_refund" value="<?php echo isset($ee_tracking_customer_refund) ? $ee_tracking_customer_refund : 0; ?>"/>
                      <?php if (isset($ee_tracking_customer_refund) && $ee_tracking_customer_refund) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-promotion">
              <fieldset>
                <legend><?php echo $legend_promotion; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-promotion-status"><?php echo $entry_status; ?></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_promotion_status" value="<?php echo isset($ee_tracking_promotion_status) ? $ee_tracking_promotion_status : 0; ?>"/>
                    <?php if (isset($ee_tracking_promotion_status) && $ee_tracking_promotion_status) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-promotion-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#advanced-promotion-group'));return false;" class="btn-switcher btn-status">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset id="advanced-promotion-group" <?php if (isset($ee_tracking_promotion_status) && $ee_tracking_promotion_status) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-promotion-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_promotion_debug" value="<?php echo isset($ee_tracking_promotion_debug) ? $ee_tracking_promotion_debug : 0; ?>"/>
                      <?php if (isset($ee_tracking_promotion_debug) && $ee_tracking_promotion_debug) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-debug">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-promotion-log">
                      <span data-toggle="tooltip" title="<?php echo $help_log; ?>"><?php echo $entry_log; ?></span>
                      <p>(<?php echo $text_required_debug; ?>)</p>
                    </label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_promotion_log" value="<?php echo isset($ee_tracking_promotion_log) ? $ee_tracking_promotion_log : 0; ?>"/>
                      <?php if (isset($ee_tracking_promotion_log) && $ee_tracking_promotion_log) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher btn-log">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                </fieldset>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-custom-dimension">
              <fieldset>
                <legend><?php echo $legend_custom_dimension; ?></legend>
                <table id="custom_dimension" class="table table-striped table-bordered table-hover">
                  <thead>
                  <tr>
                    <td class="text-left"><?php echo $entry_index; ?></td>
                    <td class="text-left"><?php echo $entry_object; ?></td>
                    <td class="text-left"><?php echo $entry_value; ?></td>
                    <td></td>
                  </tr>
                  </thead>
                  <tbody>
                  <?php $dimension_row = 1; ?>
                  <?php $dimension_count = 20; ?>
                  <?php if (isset($ee_tracking_custom_dimension) && is_array($ee_tracking_custom_dimension)) { ?>
                  <?php $dimension_count -= count($ee_tracking_custom_dimension); ?>
                    <?php foreach ($ee_tracking_custom_dimension as $dimension) { ?>
                      <tr id="dimension-row<?php echo $dimension_row; ?>">
                        <td class="text-left" style="width: 10%;">
                          <input type="text" name="ee_tracking_custom_dimension[<?php echo $dimension_row; ?>][index]" value="<?php echo $dimension['index']; ?>" placeholder="<?php echo $entry_index; ?>" id="input-index" class="form-control" />
                        </td>
                        <td class="text-left" style="width: 20%;">
                          <select name="ee_tracking_custom_dimension[<?php echo $dimension_row; ?>][object]" onchange="changeCdValue(<?php echo $dimension_row; ?>)" id="select-object<?php echo $dimension_row; ?>" class="form-control">
                            <option value="1" <?php if (!isset($dimension['object']) || $dimension['object'] == 1) { ?>selected="selected"<?php } ?>><?php echo $text_product; ?></option>
                            <option value="2" <?php if (isset($dimension['object']) && $dimension['object'] == 2) { ?>selected="selected"<?php } ?>><?php echo $text_order; ?></option>
                          </select>
                        </td>
                        <td class="text-right">
                          <select name="ee_tracking_custom_dimension[<?php echo $dimension_row; ?>][value]" id="select-value<?php echo $dimension_row; ?>" class="form-control">
                            <?php if (!isset($dimension['object']) || $dimension['object'] == 1) { ?>
                              <?php foreach ($product_columns as $product_column) { ?>
                              <option value="<?php echo $product_column; ?>" <?php if ($dimension['value'] == $product_column) { ?>selected="selected"<?php } ?>><?php echo $product_column; ?></option>
                              <?php } ?>
                            <?php } else { ?>
                              <?php foreach ($order_columns as $order_column) { ?>
                                <option value="<?php echo $order_column; ?>" <?php if ($dimension['value'] == $order_column) { ?>selected="selected"<?php } ?>><?php echo $order_column; ?></option>
                              <?php } ?>
                            <?php } ?>
                          </select>
                        </td>
                        <td class="text-right"><button type="button" onclick="$('#dimension-row<?php echo $dimension_row; ?>').remove();$('#dimension-count').html(parseInt($('#dimension-count').html())+1);increaseDimensionCount();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                      </tr>
                    <?php $dimension_row = $dimension_row + 1; ?>
                    <?php } ?>
                  <?php } ?>
                  </tbody>
                  <tfoot>
                  <tr>
                    <td colspan="3"><span id="dimension-count"><?php echo $dimension_count; ?></span> <?php echo $text_dimension_left; ?> <br><?php echo $note_custom_dimension; ?></td>
                    <td class="text-right"><button type="button" onclick="addDimensionRow();" data-toggle="tooltip" title="<?php echo $button_dimension_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                  </tr>
                  </tfoot>
                </table>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-filter">
              <fieldset>
                <legend><?php echo $legend_filter; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-bot_filter"><span data-toggle="tooltip" title="<?php echo $help_bot_filter; ?>"><?php echo $entry_bot_filter; ?></span></label>
                  <div class="col-sm-10">
                    <div class="text-right"><a onclick="$(this).parent().next('textarea').val('')" style="cursor: pointer;"><?php echo $button_clear; ?></a></div>
                    <textarea wrap="off" rows="2" name="ee_tracking_bot_filter" class="form-control notranslate"><?php echo isset($ee_tracking_bot_filter) ? $ee_tracking_bot_filter : ''; ?></textarea>
                    <?php echo $text_default; ?>: <span style="color: #FFA500;">bot|crawl|slurp|spider|mediapartners|google</span> <a onclick="addValue('ee_tracking_bot_filter', 'bot|crawl|slurp|spider|mediapartners|google', '|');" style="cursor: pointer;">+<?php echo $text_add_this_value; ?></a>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-ip_filter"><span data-toggle="tooltip" title="<?php echo $help_ip_filter; ?>"><?php echo $entry_ip_filter; ?></span></label>
                  <div class="col-sm-10">
                    <div class="text-right"><a onclick="$(this).parent().next('textarea').val('')" style="cursor: pointer;"><?php echo $button_clear; ?></a></div>
                    <textarea wrap="off" rows="5" name="ee_tracking_ip_filter" class="form-control notranslate"><?php echo isset($ee_tracking_ip_filter) ? $ee_tracking_ip_filter : ''; ?></textarea>
                    <?php echo $text_your_ip; ?>: <span style="color: #FFA500;"><?php echo $user_ip; ?></span> <a onclick="addValue('ee_tracking_ip_filter', '<?php echo $user_ip; ?>', '\n');" style="cursor: pointer;">+<?php echo $text_add_this_value; ?></a><br>
                    <?php echo $text_googlebot_ip; ?>: <span style="color: #FFA500;">66.249.*.*</span> <a onclick="addValue('ee_tracking_ip_filter', '66.249.*.*', '\n');" style="cursor: pointer;">+<?php echo $text_add_this_value; ?></a>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-admin_tracking"><span data-toggle="tooltip" title="<?php echo $help_admin_tracking; ?>"><?php echo $entry_admin_tracking; ?></span></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_admin_tracking" value="<?php echo isset($ee_tracking_admin_tracking) ? $ee_tracking_admin_tracking : 0; ?>"/>
                    <?php if (isset($ee_tracking_admin_tracking) && $ee_tracking_admin_tracking) { ?>
                    <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                      <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                    </a>
                    <?php } else { ?>
                    <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                      <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                    </a>
                    <?php } ?>
                  </div>
                </div>
              </fieldset>
            </div>
            <div class="tab-pane opacity-group <?php if (!isset($ee_tracking_status) || !$ee_tracking_status) { ?>show-opacity<?php } ?>" id="tab-log">
              <fieldset>
                <legend><?php echo $legend_log; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-log"><span data-toggle="tooltip" title="<?php echo $help_log; ?>"><?php echo $entry_log; ?></span></label>
                  <div class="col-sm-10">
                    <input type="hidden" name="ee_tracking_log" value="<?php echo isset($ee_tracking_log) ? $ee_tracking_log : 0; ?>"/>
                    <?php if (isset($ee_tracking_log) && $ee_tracking_log) { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#log-group'));return false;" class="btn-switcher btn-log">
                        <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                      </a>
                    <?php } else { ?>
                      <a onclick="changeStatus($(this));changeDisplay($('#log-group'));return false;" class="btn-switcher btn-log">
                        <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <fieldset id="log-group" <?php if (isset($ee_tracking_log) && $ee_tracking_log) { ?>style="display:block;"<?php } else { ?>style="display:none;"<?php } ?>>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-extended-log"><span data-toggle="tooltip" title="<?php echo $help_extended_log; ?>"><?php echo $entry_extended_log; ?></span></label>
                    <div class="col-sm-10">
                      <input type="hidden" name="ee_tracking_extended_log" value="<?php echo isset($ee_tracking_extended_log) ? $ee_tracking_extended_log : 0; ?>"/>
                      <?php if (isset($ee_tracking_extended_log) && $ee_tracking_extended_log) { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-on fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_enabled; ?>"></i>
                        </a>
                      <?php } else { ?>
                        <a onclick="changeStatus($(this));return false;" class="btn-switcher">
                          <i class="fa fa-toggle-off fa-3x" aria-hidden="true" data-toggle="tooltip" title="<?php echo $text_disabled; ?>"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-12">
                      <textarea wrap="off" rows="18" id="logs" class="form-control notranslate"><?php echo $log; ?></textarea>
                      <br>
                      <div class="text-center">
                        <a href="#" class="btn btn-info" onclick="updateLog($(this),'<?php echo $module_name; ?><?php echo $store_id; ?>.log',$('textarea[id=\'logs\']'));return false;" data-loading-text="<i class='fa fa-spinner fa-pulse'></i> <?php echo $text_loading; ?>">
                          <i class="fa fa-refresh"></i> <?php echo $button_update; ?>
                        </a>
                        <a href="#" class="btn btn-warning" onclick="clearLog($(this),'<?php echo $module_name; ?><?php echo $store_id; ?>.log',$('textarea[id=\'logs\']'));return false;" data-loading-text="<i class='fa fa-spinner fa-pulse'></i> <?php echo $text_loading; ?>">
                          <i class="fa fa-eraser"></i> <?php echo $button_clear; ?>
                        </a>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </fieldset>
            </div>
            <?php if ($store_id == 0) { ?>
            <div class="tab-pane" id="tab-help">
              <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-support-request" data-toggle="tab" onclick="loadIframe('iframe-support-request');"><i class="fa fa-ticket tab-icon" aria-hidden="true"></i> <?php echo $tab_support; ?></a></li>
                <li><a href="#tab-faq" data-toggle="tab" onclick="loadIframe('iframe-faq');"><i class="fa fa-question-circle tab-icon" aria-hidden="true"></i> <?php echo $tab_faq; ?></a></li>
                <li><a href="#tab-changelog" data-toggle="tab" onclick="loadIframe('iframe-changelog');"><i class="fa fa-code-fork tab-icon" aria-hidden="true"></i> <?php echo $tab_changelog; ?></a></li>
                <li><a href="#tab-about" data-toggle="tab" onclick="loadIframe('iframe-about');"><i class="fa fa-info-circle tab-icon" aria-hidden="true"></i> <?php echo $tab_about; ?></a></li>
                <li><a href="#tab-offline" data-toggle="tab"><i class="fa fa-power-off tab-icon" aria-hidden="true"></i> <?php echo $tab_offline; ?></a></li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="tab-support-request">
                  <iframe frameborder="0" id="iframe-support-request" data-src="<?php echo $dev_site; ?>/support-request-iframe?extension_id=<?php echo $module_id; ?>&e_version=<?php echo $module_version; ?>&opencart=<?php echo $oc_version; ?>&site=<?php echo $site_url; ?>&email=<?php echo $config_email; ?>&order_id=<?php echo isset($ee_tracking_order_id) ? $ee_tracking_order_id : ''; ?>" style="height:500px; width: 100%;"></iframe>
                </div>
                <div class="tab-pane" id="tab-faq">
                  <iframe frameborder="0" id="iframe-faq" data-src="<?php echo $dev_site; ?>/faq-iframe?extension_id=<?php echo $module_id; ?>&category_id=1&opencart=<?php echo $oc_version; ?>" style="height:400px; width: 100%;"></iframe>
                </div>
                <div class="tab-pane" id="tab-changelog">
                  <iframe frameborder="0" id="iframe-changelog" data-src="<?php echo $dev_site; ?>/changelog-iframe?extension_id=<?php echo $module_id; ?>" style="height:300px; width: 100%;"></iframe>
                </div>
                <div class="tab-pane" id="tab-about">
                  <iframe frameborder="0" id="iframe-about" data-src="<?php echo $dev_site; ?>/about-iframe?extension_id=<?php echo $module_id; ?>&e_version=<?php echo $module_version; ?>&custom=<?php echo $module_custom; ?>" style="height:300px; width: 100%;"></iframe>
                </div>
                <div class="tab-pane" id="tab-offline">
                  <div class="col-sm-6">
                    <p><?php echo $text_extension; ?>: <a href="<?php echo $oc_page; ?>" target="_blank"><?php echo $heading_title_main; ?></a> <?php if ($module_custom) { ?><span data-toggle="tooltip" title="<?php echo $text_custom; ?>">[custom-built]</span><?php } ?></p>
                    <p><?php echo $text_author; ?>: <a href="<?php echo $dev_site; ?>" target="_blank"><?php echo strtoupper($author); ?></a></p>
                    <p><?php echo $text_version; ?>: <?php echo $module_version; ?></p>
                  </div>
                  <div class="col-sm-6">
                    <p>
                      <a href="<?php echo $dev_site; ?>/support-request?extension_id=<?php echo $module_id; ?>&e_version=<?php echo $module_version; ?>&opencart=<?php echo $oc_version; ?>&site=<?php echo $site_url; ?>&email=<?php echo $config_email; ?>&order_id=<?php echo isset($ee_tracking_order_id) ? $ee_tracking_order_id : ''; ?>" target="_blank" type="button" class="btn btn-warning btn-module-info"><?php echo $text_support; ?></a>
                      <a href="<?php echo $dev_site; ?>/faq?extension_id=<?php echo $module_id; ?>&category_id=1&opencart=<?php echo $oc_version; ?>" target="_blank" type="button" class="btn btn-danger btn-module-info"><?php echo $text_faq; ?></a>
                      <a href="<?php echo $dev_site; ?>/enhanced-ecommerce-tracking-in-google-analytics" target="_blank" type="button" class="btn btn-primary btn-module-info"><?php echo $text_changelog; ?></a>
                    </p>
                    <p>
                      <a href="<?php echo $oc_page; ?>" target="_blank" type="button" class="btn btn-info btn-module-info"><?php echo $text_opencart_page; ?></a>
                      <a href="https://www.opencart.com/index.php?route=marketplace/extension&filter_member=<?php echo $author; ?>" target="_blank" type="button" class="btn btn-success btn-module-info"><?php echo $text_more_extensions; ?></a>
                    </p>
                    <p>
                      <a href="<?php echo $demo_site; ?>" target="_blank" type="button" class="btn btn-default btn-module-info"><?php echo $text_demo_site; ?></a>
                      <a href="<?php echo $demo_admin; ?>" target="_blank" type="button" class="btn btn-default btn-module-info"><?php echo $text_demo_admin; ?></a>
                    </p>
                    <p>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
    function clearLog(btn, file, element) {
      $.ajax({
        url: '<?php echo $clear_log; ?>',
        type: 'post',
        data: 'file=' + file,
        dataType: 'json',
        beforeSend: function() {
          btn.button('loading');
        },
        complete: function() {
          btn.button('reset');
        },
        success: function (json) {
          if (json.success) {
            element.val('');
            alert(json.success);
          } else if (json.error) {
            alert(json.error);
          }
          btn.button('reset');
        }
      });
    }
    function updateLog(btn, file, element) {
      $.ajax({
        url: '<?php echo $update_log; ?>',
        type: 'post',
        data: 'file=' + file,
        dataType: 'json',
        beforeSend: function() {
          btn.button('loading');
          element.val('');
        },
        complete: function() {
          btn.button('reset');
        },
        success: function (json) {
          element.val(json.log);
          btn.button('reset');
        }
      });
    }
    //--></script>
  <script type="text/javascript"><!--
    function addValue(name, value, separator) {
      if (jQuery.trim($('textarea[name=\'' + name + '\']').val()) == '') {
        $('textarea[name=\'' + name + '\']').val(value);
      } else {
        $('textarea[name=\'' + name + '\']').val($('textarea[name=\'' + name + '\']').val() + separator + value);
      }
    }
    function changeStatus(element) {
      if (parseInt(element.parent().find("input").val())) {
        element.parent().find("input").val(0);
        element.children("i").removeClass("fa-toggle-on");
        element.children("i").addClass("fa-toggle-off");
        element.children("i").css("color", "#A9A9A9");
        element.children("i").attr("data-original-title", "<?php echo $text_disabled; ?>");
      } else {
        element.parent().find("input").val(1);
        element.children("i").removeClass("fa-toggle-off");
        element.children("i").addClass("fa-toggle-on");
        element.children("i").css("color", "#32CD32");
        element.children("i").attr("data-original-title", "<?php echo $text_enabled; ?>");
      }
    }
    function changeDisplay(element) {
      element.fadeToggle();
    }
    function changeOpacity(element) {
      element.toggleClass('show-opacity');
    }
    function changeTranslator() {
      if (confirm('<?php echo $text_confirm; ?>')) {
        if ($("input[name='translator']").val()) {
          window.location.href = window.location.href.replace('&translator=1', '');
        } else {
          window.location.href = '<?php echo $action; ?>&translator=1';
        }
      }
    }
    function clickAll(btn, element) {
      element.each(function() {
        if (btn.parent().find("input").val() == $(this).parent().find('input').val()) {
          $(this).trigger("click");
        }
      });
    }
    function saveSettings(e) {
      $.ajax({
        type: 'post',
        url: '<?php echo $action; ?>',
        data: $('#form-module').serialize(),
        dataType: 'json',
        beforeSend: function() {
          e.button('loading');
          $('.hide-screen').show();
        },
        complete: function() {
         e.button('reset');
          $('.hide-screen').hide();
        },
        success: function(json) {
          $('.message-wrapper').empty();
          $('.error-wrapper').empty();
          $('div').removeClass('text-danger');
          $('.form-group').removeClass('has-error');
          if (json.success) {
            $('.message-wrapper').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $text_success; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            $('.js_position_status').empty();
            if (json.js_position) {
              $('.js_position_status').removeClass('success').removeClass('warning').removeClass('danger').addClass('success');
              $('.js_position_status').html('<i class="fa fa-check" aria-hidden="true"></i>');
              $('.js_position_status').tooltip('hide').attr('data-original-title', "<?php echo $help_success_js_position; ?>").tooltip('fixTitle');
            } else {
              if (json.js_default) {
                $('.js_position_status').removeClass('success').removeClass('warning').removeClass('danger').addClass('danger');
                $('.js_position_status').html('<i class="fa fa-times" aria-hidden="true"></i>');
                $('.js_position_status').tooltip('hide').attr('data-original-title', "<?php echo $help_danger_js_position; ?>").tooltip('fixTitle');
              } else {
                $('.js_position_status').removeClass('success').removeClass('warning').removeClass('danger').addClass('warning');
                $('.js_position_status').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>');
                $('.js_position_status').tooltip('hide').attr('data-original-title', "<?php echo $help_warning_js_position; ?>").tooltip('fixTitle');
              }
            }
            $('#js_version').val(json.js_version);
            if (json.info) {
              $('.message-wrapper').append('<div class="alert alert-warning"><i class="fa fa-info-circle"></i> ' + json.info + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            }
          } else if (json.error) {
            $('.message-wrapper').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json.error.warning + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            $.each(json.error, function(key, error) {
              $('.' + key).text(error);
              $('.' + key).addClass('text-danger');
              $('.' + key).parent().parent('.form-group').addClass('has-error');
            });
          }
          $('.hide-screen').hide();
          $(".alert").delay(6000).fadeOut();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          console.log("Error: " + errorThrown + " Status: " + textStatus);
          $('.hide-screen').hide();
        }
      });
    }
    function loadIframe(id) {
      var iframe = document.getElementById(id);
      if (!iframe.src) {
        iframe.src=$('#'+id).data('src');
      }
    }
    //--></script>
  <script type="text/javascript"><!--
    $(document).ready(function() {
      $("select[name='store_id']").change(function() {
        if (confirm('<?php echo $text_confirm; ?>')) {
          if (window.location.search.indexOf('store_id=') > -1) {
            window.location.href = window.location.href.replace('store_id=<?php echo $store_id; ?>', 'store_id='+ $(this).val());
          } else {
            window.location.href = '<?php echo $action; ?>&store_id='+ $(this).val();
          }
        } else {
          $(this).val('<?php echo $store_id; ?>');
        }
      });
    });
    //--></script>
  <script type="text/javascript"><!--
    $('.nav-tabs a:first').tab('show');
    //--></script>
  <script type="text/javascript"><!--
    var dimension_row = <?php echo $dimension_row; ?>;
    var dimension_count = <?php echo $dimension_count; ?>;
    function increaseDimensionCount() {
      dimension_count++;
      dimension_row--;
    }
    function addDimensionRow() {
      if (dimension_count) {
        html  = '<tr id="dimension-row' + dimension_row + '">';
        html += '  <td class="text-left" style="width: 10%;">';
        html += '   <input type="text" name="ee_tracking_custom_dimension[' + dimension_row + '][index]" value="' + dimension_row + '" placeholder="<?php echo $entry_index; ?>" id="input-index" class="form-control" />';
        html += '  </td>';
        html += '  <td class="text-left" style="width: 20%;">';
        html += '  <select name="ee_tracking_custom_dimension[' + dimension_row + '][object]" onchange="changeCdValue(' + dimension_row + ');" id="select-object' + dimension_row + '" class="form-control">';
        html += '  <option value="1" selected="selected"><?php echo $text_product; ?></option>';
        html += '  <option value="2"><?php echo $text_order; ?></option>';
        html += '  </select>';
        html += '  </td>';
        html += '  <td class="text-right">';
        html += '  <select name="ee_tracking_custom_dimension[' + dimension_row + '][value]" id="select-value' + dimension_row + '" class="form-control">';
        <?php foreach ($product_columns as $product_column) { ?>
        html += '  <option value="<?php echo $product_column; ?>"><?php echo $product_column; ?></option>';
        <?php } ?>
        html += '  </select>';
        html += '  </td>';
        html += '  <td class="text-right"><button type="button" onclick="$(\'#dimension-row' + dimension_row + '\').remove();dimension_row--;$(\'#dimension-count\').html(++dimension_count);" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';

        $('#custom_dimension tbody').append(html);

        $('#dimension-count').html(--dimension_count);

        dimension_row++;
      }
    }
    var product_columns = <?php echo json_encode($product_columns); ?>;
    var order_columns = <?php echo json_encode($order_columns); ?>;
    function changeCdValue(dimension_row) {
      $('#select-value' + dimension_row).find('option').remove();
      if ($('#select-object' + dimension_row).val() == 2) {
        $.each(order_columns, function(key, value) {
          $('#select-value' + dimension_row).append($("<option></option>").attr("value",value).text(value));
        });
      } else {
        $.each(product_columns, function(key, value) {
          $('#select-value' + dimension_row).append($("<option></option>").attr("value",value).text(value));
        });
      }
    }
    //--></script>
</div>
<?php echo $footer; ?>