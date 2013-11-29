<?php
/**
 * Main content filter
 *
 * Select between user, friends, and all content
 *
 * @uses $vars['filter_context']	Filter context: all, friends, mine
 * @uses $vars['filter_override']   HTML for overriding the default filter (override)
 * @uses $vars['context']		   Page context (override)
 * @uses $vars['show_tabs']  show the filter tabs or not
 */

ChromePhp::log('AJAX_FILTER INIT');

$show_tabs = $vars['vars']['show_tabs']; 

if ((!$show_tabs)||($show_tabs == '')||($show_tabs == NULL))
	$show_tabs = FALSE;

if (!$vars['vars']['container_guid'] == '') {
	$container_guid = $vars['vars']['container_guid'];
ChromePhp::log ('tabs-filter-ajax;vars - container_guid = ' . $vars['vars']['container_guid']);
}
else {
	$container_guid = elgg_get_logged_in_user_guid();
}
	$container = get_entity($container_guid);
if (isset($vars['vars']['context'])) {
	$context = $vars['vars']['context'];
}
else {
	$context = elgg_get_context();
}
 ChromePhp::log('context: ' . $context);
if (isset($vars['filter_override'])) {
	echo $vars['filter_override'];
	ChromePhp::log('tabs-filter-ajax; filter_override = ' . $vars['filter_override']);
	return true;
}

if ($vars['vars']['filter_context']) {
	$filter_context = $vars['vars']['filter_context'];
  ChromePhp::log('tabs-filter-ajax; filter_context 1: ' . $filter_context);
}
else {
	if (($context == 'members')||($context == 'groups'))
		$filter_context = 'newest';
	elseif ($context == 'liked_content')
		$filter_context = 'liked';
	else
	   $filter_context = 'all';
	ChromePhp::log('tabs-filter-ajax; filter_context 2: ' . $filter_context);
}

if ($container instanceof ElggGroup)
	$container_name = $container->name;
else {
	$container_name = $container->name;
}
if ($container_guid == elgg_get_logged_in_user_guid())
{
	$owner_label = elgg_echo('mine');
	$friends_label = elgg_echo('friends');
}
else
{
	$owner_label = elgg_echo('supajax:owner', array($container_name));
	$friends_label = elgg_echo('supajax:friends', array($container_name));
}
$username = $container->username;
if ($context) 
{
	switch($context)
	{
		case 'thoughts':
		case 'blog':	
			 {
			   // $username = $container->username;
				$tabs = array(
				
				'all' => array(
					'text' => elgg_echo('all'),
					'href' => (isset($vars['all_link'])) ? $vars['all_link'] : "$context/all #tabs-ajax-all",   
					'data-target' => '#tabs-ajax-all',
					'item_class' => 'tab',
					'priority' => 200,
				),
				'owner' => array(
					'text' => $owner_label,
					'href' => "$context/owner/$username #tabs-ajax-owner",
					'data-target' =>  '#tabs-ajax-owner',
					'item_class' => 'tab',
					'priority' => 300
				),
				'friends' => array(
					'text' => $friends_label,
					'href' => (isset($vars['friend_link'])) ? $vars['friend_link'] : "$context/friends/$username #tabs-ajax-friends",
					'data-target' => '#tabs-ajax-friends',
					'item_class' => 'tab',
					'priority' => 400,
				));
				if (elgg_is_active_plugin('blog_tools'))
				$tabs['featured'] =  array(
					'text' => elgg_echo('blog_tools:menu:filter:featured'),
					'href' => "$context/featured #tabs-ajax-featured",
					'data-target' => '#tabs-ajax-featured',
					'item_class' => 'tab',
					'priority' => 500,
				);		
			
				break;
			}
		case 'activity':
			{
					$tab_order = elgg_get_plugin_setting('tab_order', 'river_addon');
					if ($tab_order == 'friend_order') {
						$all_priority = 400;
						$friend_priority = 200;
					} else if ($tab_order == 'mine_order'){
						$all_priority = 500;
						$friend_priority = 400;	 
					} else {
						$all_priority = 200;
						$friend_priority = 400;	 
					}
					
		//			$username = elgg_get_logged_in_user_entity()->username;

					$tabs = array(
						'all' => array(
							'text' => elgg_echo('all'),
							'href' => (isset($vars['all_link'])) ? $vars['all_link'] : "$context/all #tabs-ajax-all",
							'data-target' => '#tabs-ajax-all',	
							'item_class' => 'tab',
							'priority' => $all_priority,
						),
						'mine' => array(
							'text' => elgg_echo('mine'),
							'href' => (isset($vars['mine_link'])) ? $vars['mine_link'] : "$context/owner/$username #tabs-ajax-owner",
							'data-target' => '#tabs-ajax-owner',   
							'item_class' => 'tab',													 
							'priority' => 300,
						),
						'friends' => array(
							'text' => elgg_echo('friends'),
							'href' => (isset($vars['friend_link'])) ? $vars['friend_link'] : "$context/friends/$username #tabs-ajax-friends",
							'data-target' => '#tabs-ajax-friends',
							'item_class' => 'tab',							
							'priority' => $friend_priority,
						),
					);
				   
				break;
			}
		case 'videos':
		case 'links':
		case 'reference':
		case 'katalists':
		case 'pinboards':
		case 'shouts':
		case 'file': {
			//$username = elgg_get_logged_in_user_entity()->username;
			
			$tabs = array(
				
				'all' => array(
					'text' => elgg_echo('all'),
					'href' => (isset($vars['all_link'])) ? $vars['all_link'] : "$context/all #tabs-ajax-all",	
					'data-target' => '#tabs-ajax-all',
					'item_class' => 'tab',
					'priority' => 200,
				),
				'owner' => array(
					'text' => $owner_label,
					'href' => "$context/owner/$username #tabs-ajax-owner",
					'data-target' =>  '#tabs-ajax-owner',
					'item_class' => 'tab',
					'priority' => 300
				),
				'friends' => array(
					'text' => elgg_echo('friends'),
					'href' => (isset($vars['friend_link'])) ? $vars['friend_link'] : "$context/friends/$username #tabs-ajax-friends",
					'data-target' => '#tabs-ajax-friends',
					'item_class' => 'tab',
					'priority' => 400,
				)
			);
		break;
		}
		case 'photos':
			{
				$tabs = array(
				'all' => array(
					'text' => elgg_echo('all'),
					'href' => (isset($vars['all_link'])) ? $vars['all_link'] : "photos/all #tabs-ajax-all",   
					'data-target' => '#tabs-ajax-all',
					'item_class' => 'tab',
					'priority' => 200,
				),
				'owner' => array(
					'text' => $owner_label,
					'href' => "photos/owner/$username #tabs-ajax-owner",
					'data-target' =>  '#tabs-ajax-owner',
					'item_class' => 'tab',
					'priority' => 300
				),
				'friends' => array(
					'text' => elgg_echo('friends'),
					'href' => (isset($vars['friend_link'])) ? $vars['friend_link'] : "photos/friends/$username #tabs-ajax-friends",
					'data-target' => '#tabs-ajax-friends',
					'item_class' => 'tab',
					'priority' => 400,
				)			  
			);
			break;  
		}
		case 'members':
		case 'co-creators':	 {
			$tabs = array(
				'newest' => array(
					'text' => elgg_echo('members:label:newest'),
					'href' => "members/newest #tabs-ajax-newest",
					'data-target' => '#tabs-ajax-newest',
					'item_class' => 'tab',				
					'priority' => 200,
					),
				'popular' => array(
					'text' => elgg_echo('members:label:popular'),
					'href' => "members/popular #tabs-ajax-popular",
					'data-target' => '#tabs-ajax-popular',			
					'item_class' => 'tab',	
					'priority' => 300,
					),
				'online' => array(
					'text' => elgg_echo('members:label:online'),
					'href' => "members/online #tabs-ajax-online",
					'data-target' => '#tabs-ajax-online',
					'item_class' => 'tab',	
					'priority' => 400,
					)
			);
			break;
		}
		case 'zones':
		case 'groups':{
			ChromePhp::log('AJAX_FILTER context = groups/zones');
					$group_tools_option_tabs = array (
						'discussion',
						'open',
						'closed',
						'alpha',
						'ordered',
						);
					$tabs = array(
						"newest" => array(
							"text" => elgg_echo("groups:newest"),
							"href" => "groups/all?filter=newest #tabs-ajax-newest",
							'data-target' => '#tabs-ajax-newest',
							'item_class' => 'tab',	
							"priority" => 200,
						),
						"popular" => array(
							"text" => elgg_echo("groups:popular"),
							"href" => "groups/all?filter=popular #tabs-ajax-popular",
							'data-target' => '#tabs-ajax-popular',
							'item_class' => 'tab',	
							"priority" => 300,
						));
				   if (elgg_is_active_plugin('group_tools'))
				   {
					   foreach ($group_tools_option_tabs as $tab_type)
					   {
						   $show_tab = false;	
						   $show_tab = elgg_get_plugin_setting("group_listing_" . $tab_type . "_available", "group_tools");
						   if ($show_tab==1)
						   {
								switch($tab_type)
								{
									case 'discussion':
										{
											 $tabs['discussion'] =  array(
												"text" => elgg_echo("groups:latestdiscussion"),
												"href" => "groups/all?filter=discussion #tabs-ajax-discussion",
												'data-target' => '#tabs-ajax-discussion',
												'item_class' => 'tab',  
												"priority" => 400); 
											 break; 
										}
									case 'open':
										{
											$tabs['open'] = array(
												"text" => elgg_echo("group_tools:groups:sorting:open"),
												"href" => "groups/all?filter=open #tabs-ajax-open",
												'data-target' => '#tabs-ajax-open',
												'item_class' => 'tab',			  
												"priority" => 500,
											   );
											break;
										}
									case 'closed':
										{
											$tabs['closed'] = array(
												"text" => elgg_echo("group_tools:groups:sorting:closed"),
												"href" => "groups/all?filter=closed #tabs-ajax-closed",
												'data-target' => '#tabs-ajax-closed',   
												'item_class' => 'tab',  
												"priority" => 600);
											break;
										}
									case 'alpha':
									{
											$tabs['alpha'] =  array(
												"text" => elgg_echo("group_tools:groups:sorting:alphabetical"),
												"href" => "groups/all?filter=alpha #tabs-ajax-alpha",
												'data-target' => '#tabs-ajax-alpha',	
												'item_class' => 'tab',
												"priority" => 700);
											break;
									}	
									case 'ordered':
									{
										$tabs['ordered'] = array(
										"text" => elgg_echo("group_tools:groups:sorting:ordered"),
										"href" => "groups/all?filter=ordered #tabs-ajax-ordered",
										'data-target' => '#tabs-ajax-ordered',		  
										"priority" => 800);
										break;
									}			   
								}
								
						  }
					  }
				   }
				   else {
					   $tabs['discussion'] =  array(
							"text" => elgg_echo("groups:latestdiscussion"),
							"href" => "groups/all?filter=discussion #tabs-ajax-discussion",
							'data-target' => '#tabs-ajax-discussion',
							'item_class' => 'tab',  
							"priority" => 400);
				   }
			break;
		}
		case 'liked_content':  {
			$tabs = array(
				'liked' => array(
					'text' => elgg_echo('liked_content:user:likes', array($container->name)),
					'href' => "liked_content/user/ #tabs-ajax-likes". elgg_get_page_owner_entity()->username,
					'data-target' => '#tabs-ajax-liked',
					'item_class' => 'tab',			  
					'priority' => 200,
					),
				'most_liked' => array(
					'text' => elgg_echo('liked_content:user:most_liked'),
					'href' => 'liked_content/user/' . elgg_get_page_owner_entity()->username . '?filter=most_liked',
					'data-target' => '#tabs-ajax-most-liked',		  
					'item_class' => 'tab',  
					'priority' => 300,
					),
			);
			break;
		}
		default: break;
	}

	if (is_array($tabs))
	{
		foreach ($tabs as $name => $tab) 
		{
				$tab['name'] = $name;
				elgg_register_menu_item('filter', $tab);
	   	}
	
		echo '<div id="easy-tabs">';

		if ($show_tabs == TRUE)
		{
			echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz tabs'));
		}
		else
		{
			echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz tabs hidden-filter'));
		}
		
		echo "<script type=\"text/javascript\" >
			  	$(document).ready(function()
			  	{
			  			
	   $('#easy-tabs')
		.bind('easytabs:ajax:beforeSend', function(e, clicked, panel){ //Fires before ajax request is made.
		 // var $this = $(clicked);

			$(\".panel-container\").css(\"background-image\", \"url('\" + elgg.config.wwwroot + \"_graphics/ajax_loader.gif')\");

		})
		
	   .bind('easytabs:after', function(e, clicked, panel){ //Fires before ajax request is made.
		   // var $this = $(clicked);
			 //  if (window.hide_comment_text)
			   hide_comment_text(panel.find(\".elgg-output\"), 1200);	  

		  //  $(\".panel-container\").css(\"background-image\", \"none!important\");
		//	console.log('easytabs:after');
		})
		.bind('easytabs:ajax:complete', function(e, clicked, panel, response, status, xhr) {
		  $(\".panel-container\").css(\"background-image\", \"none\");
	 
		  $(panel.selector).append(response);
		  
		  if (status == \"error\") {
			var msg = elgg.echo('supajax:ajax_error');
	  
			elgg.register_error(msg + xhr.status + \" \" + xhr.statusText);
		  }
		});
			  		
					//  if (window.elgg_emoticonize)
					//	elgg_emoticonize();

					$('#easy-tabs').easytabs({
						animate: true,
						animationSpeed: 600,
						defaultTab: '.elgg-menu-item-" . $filter_context . "',
						panelActiveClass: \"elgg-state-selected\",
						tabActiveClass: \"elgg-state-selected\",
						updateHash: true,
						transitionIn: 'slideDown',
						transitionOut: 'slideUp',
						cache: true
						});
					$('#easy-tabs').bind('easytabs:before', function(e, clicked, panel)
					{  
						supajax_change_page_header('" . $context . "',$(clicked).attr('data-target').split('#tabs-ajax-'),'" . $container_name . "', " . $container_guid . ");
					});				
			  	});
			  	</script>";
	}
}