<?php
// supajax js file 
?>
//<script type="text/javascript">

function supajax_setup_filter(){
   var name = '';
   var value = '';
   var album_comments = $('#fo').attr('ac');
   var likes = $('#fo').attr('li');
   var view_count = $('#fo').attr('vc');
   var sort_type_selector = $('#sort-type');
   
    $('input#subtype').each(function(){

        name = $(this).attr("name"); 
        if ($(this).attr("type") == "radio")
        {     
            value = $(this).attr('value') ;
  
            if((($(this)[0].checked))&&(value == 'album'))
            { 
              //alert('is album checked');
                $('input#tagged-you').attr('disabled', 'disabled');
                $('input#tagged-you, #tagged-you-label').css('opacity', '0.3');
                $('#sort-type >option').each(function(){
                //  value = $(this).attr('value') ;
                  
                    var selected_type_option = $(sort_type_selector).val();
                    $(sort_type_selector).empty();
                    $(sort_type_selector).append($('<option></option>').val('timing-d').html(elgg.echo('supajax:sorting:timing-d')));
                    $(sort_type_selector).append($('<option></option>').val('timing-a').html(elgg.echo('supajax:sorting:timing-a')));

                    if (likes == 'true')
                    {
                        $(sort_type_selector).append($('<option></option>').val('likes-d').html(elgg.echo('supajax:sorting:likes-d')));
                        $(sort_type_selector).append($('<option></option>').val('likes-a').html(elgg.echo('supajax:sorting:likes-a')));
                    } 
                    if (album_comments > 0)
                    {
                        $(sort_type_selector).append($('<option></option>').val('comments-d').html(elgg.echo('supajax:sorting:comments-d'))); 
                        $(sort_type_selector).append($('<option></option>').val('comments-a').html(elgg.echo('supajax:sorting:comments-a')));
                    } 
 
                    $(sort_type_selector).val(selected_type_option);  
                  
        
                });
            }
            else
            {
              //  alert($(this)[0].checked + '; ' + value);
                if((($(this)[0].checked))&&(value == 'image'))
                {
                    $('input#tagged-you').removeAttr('disabled');
                    $('input#tagged-you, #tagged-you-label').css('opacity', '1');
                    var selected_type_option = $(sort_type_selector).val();
                    $(sort_type_selector).empty();
                    $(sort_type_selector).append($('<option></option>').val('timing-d').html(elgg.echo('supajax:sorting:timing-d')));
                    $(sort_type_selector).append($('<option></option>').val('timing-a').html(elgg.echo('supajax:sorting:timing-a')));

                    if (likes == 'true')
                    {
                        $(sort_type_selector).append($('<option></option>').val('likes-d').html(elgg.echo('supajax:sorting:likes-d')));
                        $(sort_type_selector).append($('<option></option>').val('likes-a').html(elgg.echo('supajax:sorting:likes-a')));
                    } 
                    if (album_comments == 'true')
                    {
                        $(sort_type_selector).append($('<option></option>').val('comments-d').html(elgg.echo('supajax:sorting:comments-d'))); 
                        $(sort_type_selector).append($('<option></option>').val('comments-a').html(elgg.echo('supajax:sorting:comments-a')));
                    } 
                    if (view_count == 'true')
                    {
                        $(sort_type_selector).append($('<option></option>').val('views-d').html(elgg.echo('supajax:sorting:views-d'))); 
                        $(sort_type_selector).append($('<option></option>').val('views-a').html(elgg.echo('supajax:sorting:views-a')));
                    }   
                    $(sort_type_selector).val(selected_type_option);              
                }
            }
        }
    });
    return true;
}

function hide_comment_text(elements, timer) {
    var more_text = elgg.echo('supajax:more-text');
    var less_text = elgg.echo('supajax:less-text');
    var timeoutid = setTimeout(function(){
        $(elements).truncatable({limit:150, more: more_text, less:true, hideText: less_text});
    }, timer);
    return true;
};


function supajax_change_page_header(context, page_type, container, container_guid){
	//var page_context = $('.page-context').text().split('|');

	var address = elgg.parse_url(window.location.href, 'path').split('/');
	if (context == '')
	{
		context = address[1];

	}
	if (page_type == '')
	{
		console.log('supajax_change_page_header: no page_type');
		page_type = $('#easy-tabs').find('a.elgg-state-selected').attr('data-target').split('#tabs-ajax-');
	}
	if (container == '')
	{
	//	container = page_context[2];
		//container = address[3];

	}
    console.log ('supajax_change_page_header: context = ' + context);
   console.log ('supajax_change_page_header: container = ' + container);
	page_type = page_type[page_type.length-1];
	console.log ('supajax_change_page_header: page_type = ' + page_type);
	var title_label;
	
    var entity_pages = [
        'all',
        'owner',
        'friends',
        'featured',
        ''];
        
    var activity_pages = [
        'all',
        'owner',
        'friends',
        ''];        
        
    var member_pages = [
                'popular',
                'newest',
                'online',
                ''];        
	
    var group_pages = [
                'popular',
                'newest',
                'discussion',
                'open',
                'closed',
                'alpha',
                'ordered',
                ''];
                
    var liked_pages = [
                'liked',
                'most-liked',
                ''];                 	
	
	switch (context){
        case 'thoughts':
        case 'blogs':
        case 'videos':
        case 'photos':
        case 'links':
        case 'reference':
        case 'katalists':
        case 'pinboards':               
        case 'shouts':
        case 'file': {
                     if (jQuery.inArray(page_type, entity_pages) >= 0) {
                        switch (page_type){
                            case '':
                            case 'all':
                                        {
                                        title_label = elgg.echo(context + ':title:all_' + context); 
                                        break;
                                        }
                            case 'owner':
                                        {
                                        if (elgg.get_logged_in_user_guid() == container_guid)
                                           title_label = elgg.echo(context + ':title:your_' + context);
                                        else
                                           title_label = elgg.echo(context + ':title:user_' + context, [container]);
                                    
                                        break;
                                        }                   
                            case 'friends':
                                        {
                                        if (elgg.get_logged_in_user_guid() == container_guid)
                                            title_label = elgg.echo(context + ':title:your:friends');
                                        else
                                            title_label = elgg.echo(context + ':title:user:friends', [container]);
                                        break;
                                        }   
                            case 'featured':
                                        {
                                        title_label = elgg.echo('blog_tools:menu:filter:featured');
                                        break;
                                        }                                           
                            default:
                                break;
                             }
	                   }
	               break;
	               }
                 case 'activity': {
                     if (jQuery.inArray(page_type, activity_pages) >= 0) {
                        switch (page_type){
                            case '':
                            case 'all':
                                        {
                                        title_label = elgg.echo(context + ':title:all_' + context); 
                                        break;
                                        }
                            case 'owner':
                                        {
                                    //    if (elgg.get_logged_in_user_guid() == container_guid)
                                           title_label = elgg.echo(context + ':title:your_' + context);
                                     //   else
                                      //     title_label = elgg.echo(context + ':title:user_' + context, [container]);
                                    
                                        break;
                                        }                   
                            case 'friends':
                                        {
                                      //  if (elgg.get_logged_in_user_guid() == container_guid)
                                            title_label = elgg.echo(context + ':title:your:friends');
                                      //  else
                                      //      title_label = elgg.echo(context + ':title:user:friends', [container]);
                                        break;
                                        }   
                            case 'featured':
                                        {
                                        title_label = elgg.echo('blog_tools:menu:filter:featured');
                                        break;
                                        }                                           
                            default:
                                break;
                             }
                       }
                   break;
                   }	               
            case 'members':             
                    {
                     if (jQuery.inArray(page_type, member_pages) >= 0) {
                        switch (page_type){
                            case 'popular':
                                        {
                                        title_label = elgg.echo('members:title:popular'); 
                                        break;
                                        }
                            case '':
                            case 'newest':
                                        {
                                        title_label = elgg.echo('members:title:newest');
                                        break;
                                        }                   
                            case 'online':
                                        {
                                        title_label = elgg.echo('members:title:online');
                                        break;
                                        }   
                            default:
                                break;
                             }
                       }
                   break;
                   }   
            case 'groups':             
                    {
                     if (jQuery.inArray(page_type, group_pages) >= 0) {
                        switch (page_type){
                            case 'popular':
                                        {
                                        title_label = elgg.echo('groups:title:popular'); 
                                        break;
                                        }
                            case '':
                            case 'newest':
                                        {
                                        title_label = elgg.echo('groups:title:newest');
                                        break;
                                        }                   
                            case 'discussion':
                                        {
                                        title_label = elgg.echo('groups:title:discussion');
                                        break;
                                        }  
                            case 'open':
                                        {
                                        title_label = elgg.echo('groups:title:open'); 
                                        break;
                                        }
                            case 'closed':
                                        {
                                        title_label = elgg.echo('groups:title:closed');
                                        break;
                                        }                   
                            case 'ordered':
                                        {
                                        title_label = elgg.echo('groups:title:ordered');
                                        break;
                                        }
                            case 'alpha':
                                        {
                                        title_label = elgg.echo('groups:title:alphabetical');
                                        break;
                                        }                                                                                      
                            default:
                                break;
                             }
                       }
                   break;
                   }  
            case 'liked_content':             
                    {
                         console.log(page_type);
                     if (jQuery.inArray(page_type, liked_pages) >= 0) {
                        
                        switch (page_type){
                            case 'most-liked':
                                        {
                                        title_label = elgg.echo('supajax:liked:title:user:most_liked', [container]); 
                                        break;
                                        }
                            case 'liked':
                            case '':
                                        {
                                        title_label = elgg.echo('supajax:liked:title:user:liked_content', [container]);
                                        break;
                                        }                   
                            default:
                                break;
                             }
                       }
                   break;
                   }                     
	       default: break;
	   }
	//console.log('supajax_change_page_header: title_label = ' + title_label);
	
	$('.elgg-heading-main').html(title_label);
	return true;
}

jQuery(document).ready(function($) {
    supajax_setup_filter();
    
    $('#subtype').change(function(){
   
      supajax_setup_filter();  
    });
    
    $('.supajax-filter-button').live('click', function(event) 
    { // when supajax filter button is clicked
        var FormData = new Object;
        var filter_box = $('.supajax-filter');
        var Checked = 1;
        var Unchecked = 0;
        var url_params = '';
        var i = 0;
        
        $(filter_box).find('select, input').each(function()
        {
            // What is the tag name of this input element? We check for input or textarea.
            var TagName = $(this)[0].tagName.toLowerCase(); // (Lowecase tag name for string comparisons)
            var TagType = $(this).attr("type");    // Grab the element type
            var Name = $(this).attr("name");       // Name attribute; this is different from tag _name_
            var Value = $(this).attr("value");     // The value
            var Content = $(this).val();           // Needed for textarea, as it does not store value in 'value' attribute, like the others
            //var HTML = $(this).html();             // For textarea

            if (TagType == "text")
                FormData[Name] = Value;

           // if (TagType == "hidden")
            //    FormData[Name] = Value;

            if (TagType == "password")
                FormData[Name] = Value;

            if (TagType == "radio")
                if(($(this)[0].checked))  // You could also use -- $(this).is(":checked") 
                        FormData[Name] = Value;

            if (TagName == "textarea")
                FormData[Name] = Content;   // This is a simple example. Be careful about processing HTML -- you still need to format HTML to fit _your_ needs, whatever they may be.

            if (TagType == "checkbox")
                if ($(this)[0].checked)
                    FormData[Name] = Checked; else FormData[Name] = Unchecked;
                    
            if (TagName == 'select')
                FormData[Name] = Value;
                       
            if ((FormData[Name] != null)&&(TagType != 'hidden'))
            {
                if (i > 0)
                    url_params += '&' + Name + '=' + FormData[Name];
                else
                    url_params += Name + '=' + FormData[Name];
                
            }
            FormData[Name] = null;
            i++;
        });
        var container = $('#easy-tabs');
        var selected_tab = $(container).find('a.elgg-state-selected'); // get the currently selected link in the list of tabs
        $(container).find('li.tab a').each(function(index){
            var url = $(this).attr('href');
            var hash = $(this).attr('data-target');

            if (url.indexOf('?') != -1)
                url = url.substring(0, (url.indexOf('?')));         
            if (url.indexOf('#') != -1)
                url = url.substring(0, (url.indexOf('#')-1));
 
            url += '?' + url_params;
            url += ' ' + hash;
            $(this).attr('href', url);
        });
        
        var selected_panel = $('body').find('#easy-tabs .panel-container > div.elgg-state-selected');
//alert(selected_panel.attr('id'));
//alert(selected_tab.attr('data-target'));
//alert(selected_panel.attr('id'));
//alert(selected_tab.attr('href'));
    //    $('.supajax-filter-button').css('font-size','200%!important');
        $(selected_panel).load(selected_tab.attr('href'),function(response, status, xhr)
        {
              $(selected_tab).parent().data('easytabs').cached = true;
              $(container).trigger('easytabs:ajax:complete', [$('.supajax-filter-button'), selected_panel, response, status, xhr]);
              
        });
       //$('#easy-tabs').data('easytabs').getTabs();
       // alert(selected_tab.attr('data-target'));
      //  $('#easy-tabs').easytabs('select', selected_tab.attr('data-target'));

    });
 
    
	// pagination button    
    $('a[data-pagination]').live('click', function(event) {
        var element = $(this);
        var selected_panel = $('body').find('#easy-tabs .panel-container > div.elgg-state-selected');
        var pagination_wrapper = $(selected_panel).find('.atPaginatorWrapper');
        var wrapper_element = element.parents(pagination_wrapper);
        var hidden_paginator = $(wrapper_element,'.atHiddenPaginator');
        var next_item = hidden_paginator.find('.elgg-state-selected').next('li').not('.elgg-state-disabled');
 
        if (next_item.length == 0) {
          //  console.log('pagination button - no next item');
            return false;
        }
        
        selected_panel.find('.atAjaxLoader.hidden').removeClass('hidden');
        element.hide();
      
        var next_link = $('a', next_item);
        var next_url = next_link.attr('href');

        $.ajax({
            url: next_url,
            success: function(html_data){
            //    console.log('pagination button - ajax start');
                html_data = "<div>"+html_data+"</div>";

				if (($(html_data).children(":first").hasClass('elgg-list') == false)&&($(html_data).children(":first").hasClass('elgg-gallery') == false))
				{
					html_data = $(html_data).find('.elgg-main');
				}

                var listing = $(html_data).find('.elgg-list:first, .tidypics-gallery:first');
         
                var new_pager = $(html_data).find('.atPaginatorWrapper');
             
                if (listing.length > 0) 
                {
              //      console.log('pagination button - listing exists');
                    var append_to_panel = $(selected_panel).find("ul.elgg-list.elgg-list-entity, ul.tidypics-gallery, .elgg-list-river");
                    var new_items = listing.find('.elgg-full-comment-text .elgg-output');
                    if (window.hide_comment_text)
                        hide_comment_text(new_items, 800);
              
                 //   if (window.elgg_emoticonize)
                  //      new_items.emoticonize();                        
              
                    $(append_to_panel).append(listing.children());
                    if (elgg.infiniteeureka.set_height)
                    {
                       var name = '';
                       var value = '';
                       var class_type = null;
                        $('input#subtype').each(function(){
                             class_type = null;
                             name = $(this).attr("name"); 
                            if ($(this).attr("type") == "radio")
                            {     
                                 value = $(this).attr('value') ;
                                 
                                if((($(this)[0].checked))&&(value == 'album'))
                                    class_type = '.elgg-module-tidypics-album';
                                    
                                if((($(this)[0].checked))&&(value == 'image'))
                                    class_type = '.elgg-module-tidypics-image'; 
                                    
                                if (class_type)
                                {
                                     elgg.infiniteeureka.set_height(class_type + ' .elgg-foot');
                                     elgg.infiniteeureka.set_height(class_type + ' h3');
                                }
                                    
                            }
                       });
                    }
                 }           
                if (new_pager.length > 0) {
                //   console.log('pagination button - new_pager exists');
                    var new_hidden_paginator = new_pager.find('.elgg-state-selected').next('li').not('.elgg-state-disabled'); // find next available page
                    if (new_hidden_paginator.length == 0) { // if no page is available remove the paginator button
                  //      console.log('pagination button - new_hidden_paginator == 0');
                        $(pagination_wrapper).remove();
                    } else {
                        $(pagination_wrapper).replaceWith(new_pager); // if page is available then replace the button with a new version
                    //    console.log('pagination button - new_hidden_paginator exists');
                    }
                } else {
//                 console.log('pagination button - no new_pager');
                    $(pagination_wrapper).remove();
                }
              
            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                var msg = elgg.echo('supajax:ajax_error');
                elgg.register_error(msg + '<br/>' + err.Message);
              }
        });
      
    });
});