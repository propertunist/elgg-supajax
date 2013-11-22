elgg.provide('elgg.ui.river');

elgg.ui.river.init = function() {
	$('#elgg-river-selector').change(function() 
	{ // when river subtype selector changes
        var container = $('#easy-tabs');
		var selected_tab = $(container).find('a.elgg-state-selected'); // get the currently selected link in the list of tabs
		$(container).find('li.tab a').each(function(index){
			var url = $(this).attr('href');
			var hash = $(this).attr('data-target');

			if (url.indexOf('?') != -1)
				url = url.substring(0, (url.indexOf('?')));			
			if (url.indexOf('#') != -1)
				url = url.substring(0, (url.indexOf('#')-1));
			url += '?' + $('#elgg-river-selector').val();
			url += ' ' + hash;
			$(this).attr('href', url);
		});
		
		var selected_panel = $('body').find('#easy-tabs .panel-container > div.elgg-state-selected');

		$(selected_panel).load(selected_tab.attr('href'),function(response, status, xhr)
		{
              $(selected_tab).parent().data('easytabs').cached = true;
              $(container).trigger('easytabs:ajax:complete', [$('#elgg-river-selector'), selected_panel, response, status, xhr]);
        });
        $('#easy-tabs').data('easytabs').getTabs();

	});
};

elgg.register_hook_handler('init', 'system', elgg.ui.river.init);