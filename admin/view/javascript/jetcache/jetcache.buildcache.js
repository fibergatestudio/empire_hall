jetcache.register('buildcache', (function($){
  var buildcache_array;
  var buildcaching;
  var buildcached;
  var buildcache_xhr;
  var buildcache_total;
  var config;

  var init = function(callback) {
    buildcaching = false;
    buildcache_array = [];
    buildcached = 0;
    buildcache_total = 0;
    message('');
    if (buildcache_xhr) {
      buildcache_xhr.abort();
    }
    callback();
  };

  var set_progress = function (percent) {
     if (percent < 50) {
     	$(config.progressbar_selector).css('color', '#444');
     } else {
     	$(config.progressbar_selector).css('color', '#FFF');
     }
    $(config.progressbar_selector).css('width', percent + '%');
    $(config.progress_selector).css('position', 'relative');
    $(config.progressbar_selector).html('<div style="position: absolute; margin-left: 45%;">'+ percent + '%</div>');
  }

  var message = function(message) {
    $(config.output_selector).html(message);
  }

  var buildcache_page = function() {
    if (buildcache_array.length == 0) {
      init(function() {
        set_progress(100);
        message(config.message_complete);
      });
    } else if (buildcaching) {
      var page = buildcache_array.shift();

      buildcache_xhr = $.ajax({
        url : page,
        beforeSend : function(jqXHR) {
          jqXHR.setRequestHeader(config.to_header, '1');
          set_progress(Math.ceil(buildcached * 100 / buildcache_total));
          message(config.message_processing + ': ' + page + '<br>' + config.message_processing_complete + ': ' + buildcached + '/' + buildcache_total);
        },
        complete : function() {
          buildcached++;
          buildcache_page();
          //setTimeout(buildcache_page, 1000);
        }
      });
    }
  }

  var load_stack = function() {
    $.ajax({
      url : config.array_url,
      dataType : 'json',
      type: 'POST',
      data: {
        buildcache_products: config.buildcache_with_products,
        buildcache_lang: config.buildcache_with_lang
      },
      success : function(data) {
        buildcaching = true;
        buildcache_total = data.length;
        buildcache_array = data;
      },
      complete : buildcache_page
    });
  }

  var start = function() {
	init(function() {
	  set_progress(0);
	  load_stack();
	});
  }

  var abort = function() {
	if (!buildcaching) return;

	init(function() {
	  set_progress(0);
	  message(config.message_aborted);
	});
  }

  return {
    setConfig: function(c) {
	  config = $.extend({}, config, c);
	},
	start: function() {
	  start();
	},
	abort: function() {
	  abort();
	}
  }
})(jQuery));