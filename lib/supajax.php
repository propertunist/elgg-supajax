<?php

$dbprefix = elgg_get_config("dbprefix");


function supajax_get_activity_list($context = NULL, $page_type = NULL) {
    global $CONFIG;
    // require_once($CONFIG->path . 'firephp/FirePHP.class.php');
    //$firephp = FirePHP::getInstance(true);
    $return = array();
    $options = array();
    //   $page_type = preg_replace('[\W]', '', get_input('page_type', 'all'));
    $current_user = elgg_get_logged_in_user_entity();
    if ($current_user)
        $return['container_guid'] = $current_user->getGUID();
    // $return['container_name'] = $current_user->name;

    $options['limit'] = 10;

    if (elgg_is_logged_in())
         $return['show_tabs'] = TRUE;    

    if (isset($page_type))
    {
        switch($page_type)
        {
            case 'owner':
                {
                    $return['title'] = elgg_echo('activity:title:your_activity');
                    $return['filter_context'] = 'owner';
                    $options['subject_guid'] = elgg_get_logged_in_user_guid();
                    break;
                }       
            case 'friends':
                {
                    $return['title']= elgg_echo('activity:title:your:friends');
                    $return['filter_context'] = 'friends';
                    $options['relationship_guid'] = elgg_get_logged_in_user_guid();
                    $options['relationship'] = 'friend';
                    break;
                }           
            default:
                {
                    $return['filter_context'] = 'all';
                    $return['title'] = elgg_echo('activity:title:all_activity');
                        ChromePhp::LOG('get_activity_list; page_type = ' . $return['title']);
                    break;
                }            
        }
    }
    // $options['data-options'] = htmlentities(json_encode($options), ENT_QUOTES, 'UTF-8');
    
   if(elgg_is_xhr())
   {
    $type = preg_replace('[\W]', '', get_input('type', 'all'));
    $subtype = preg_replace('[\W]', '', get_input('subtype', ''));
    if ($subtype) 
    {
        $selector = "type=$type&subtype=$subtype";
    } 
    else 
    {
        $selector = "type=$type";
    }
    
    if ($type != 'all') 
    {
        $options['type'] = $type;
        if ($subtype) 
        {
            $options['subtype'] = $subtype;
        }
    }
        $sync = get_input('sync');
        $ts = (int) get_input('time');
        if (!$ts) 
        {
            $ts = time();
        }
        // $options = get_input('options');

        $items = elgg_list_river($options);

        if ($sync == 'new') 
        {
            $options['wheres'] = array("rv.posted > {$ts}");
            $options['order_by'] = 'rv.posted asc';
            $options['limit'] = 0;
      
            if (is_array($items) && count($items) > 0) 
            {
                foreach ($items as $key => $item) 
                {
                    $id = "item-{$item->getType()}-{$item->id}";
                    $time = $item->posted;
    
                    $html = "<li id=\"$id\" class=\"elgg-item\" data-timestamp=\"$time\">";
                    $html .= elgg_view_list_item($item, $vars = array());
                    $html .= '</li>';
                
                    $output[] = $html;
                }
            }
            if ($output)
            {
                print(json_encode($output));
                exit;
            }
        }
        else
        {
            if (!$items) 
            {
                $return['content'] = '<div class="supajax-no-items">' . elgg_echo('supajax:activity:none') . '</div>';              
            } 
            else 
            {
                $return['content'] = $items;           
            }                 
        }
    }
    else
    {
        $return['class'] = 'elgg-river-layout';
        $return['filter_context'] = $page_type;

        $content = elgg_view('core/river/filter', array('selector' => $selector));
       // elgg_load_js('elgg.supajax');
            $return['content'] = $content . "
                                <div class='panel-container'>
                                    <div class='tabs' id='tabs-ajax-all'></div>
                                    <div class='tabs' id='tabs-ajax-owner'></div>
                                    <div class='tabs' id='tabs-ajax-friends'></div>
                                </div>
                             </div>
                            ";          
    }
    return $return;
}

function supajax_get_page_content_list($context = NULL, $page_type = NULL, $container_guid = NULL) {
    $at_contexts = array(
   'thoughts' => 'blog',
    'videos' => 'videolist_item',
    'photos' => 'album',
    'links' => 'bookmarks',
    'reference' => 'page_top',
    'katalists' => 'au_set',
    'pinboards' => 'au_set',        
    'shouts' => 'thewire',
    'file' => 'file',
    'members' => NULL,
    'groups' => NULL,   
    );	    
    //    $firephp = FirePHP::getInstance(true);
	$return = array();
    $options = array(
	  'type' => 'object',
	  'full_view' => false,
    );
	$current_user = elgg_get_logged_in_user_entity();
	//	$firephp->log($page_type, '$page_type = ');
        
    $container = get_entity($container_guid);
    if ($container)
    {
        if ($container instanceof ElggGroup)
            $container_name = $container->name;
        else {
            $container_name = $container->name;
        }        
    }
        
    if (elgg_is_logged_in())
        $return['show_tabs'] = TRUE;
    
	if (isset($page_type))
	{
//		$firephp->log('$page_type isset');
		switch($page_type){
            case 'featured':
            {
                $options['metadata_name_value_pairs'] = array(
                    array('name' => 'status', 'value' => 'published'),
                    array('name' => 'featured', 'value' => '0', 'operand' => " > "),
                );
                $return['filter_context'] = 'featured';
                $return['title'] = elgg_echo("blog_tools:menu:filter:featured");
                break;
            }
    		case 'owner':
    		{
			  	if ($container instanceof ElggUser) 
			  	{
				//	group_gatekeeper();
				  	$return['container_guid'] = $container_guid;
					$return['container_name'] = $container_name;
			  		$options['owner_guid'] = $container_guid;
					
					if ($current_user && ($container_guid == $current_user->getGUID())) 
					{//if the user is viewing own items
					    $return['title'] = elgg_echo($context. ':title:your_' . $context);
				        $return['filter_context'] = 'owner';
					} 
					else
					{ 
				//		if ($container instanceof ElggGroup) 
				//		{
				//			$return['filter'] = false;				
				//			$return['filter_context'] = 'group';
				//		} 
				//		else 
				//		{
							// do not show button or select a tab when viewing someone else's posts
							$return['title'] = elgg_echo($context. ':title:user_' . $context , array($container_name));
							$return['show_tabs'] = false;				
							$return['filter_context'] = 'owner';
		//				}
					}
				  	}
					else
					{
					//	system_messages(elgg_echo('supajax:container_not_found','error'));
                        echo ("<script>elgg.register_error(elgg.echo('supajax:container_not_found','error'));</script>");                     
						$return['filter_context'] = 'all';
						$return['title'] = elgg_echo($context . ':title:all_' . $context);
				  	}
					break;
				}		
			case 'friends':
				{

//                    $firephp->log ('$container_name = ' . $container_name);
                    if ($container instanceof ElggUser) 
                    {
					   if (!$friends = get_user_friends($container_guid, ELGG_ENTITIES_ANY_VALUE, 0)) { // no friends
                            $return['content'] .=  '<div class="supajax-no-items">' . elgg_echo('friends:none:you') . '</div>';
                            return $return;
                        }
                        else 
                        { //if the user has friends
                            //  group_gatekeeper();
                            //  $firephp->log ('container guid = ' . $container_guid);
                            $return['container_guid'] = $container_guid;
                            $return['container_name'] = $container_name;
                            //     $options['container_guid'] = $container_guid;
                            if ($current_user && ($container_guid == $current_user->getGUID())) {//if the user is viewing own items
                                $return['title'] = elgg_echo($context. ':title:your:friends');
                                 $return['filter_context'] = 'friends';
                            } 
                            else
                            { 
                        //      if ($container instanceof ElggGroup) 
                        //      {
                        //          $return['filter'] = false;              
                        //          $return['filter_context'] = 'group';
                        //      } 
                        //      else 
                        //      {
                                    // do not show button or select a tab when viewing someone else's posts
                                    $return['title'] = elgg_echo($context. ':title:your:friends' , array($container_name));
                                    $return['show_tabs'] = false;  
                                    $return['filter_context'] = 'friends';            
                                    //$return['filter_context'] = 'owner';
                //              }
                            }
                            foreach ($friends as $friend) {
                            $options['owner_guids'][] = $friend->getGUID();
                            }
                            
                        }
                    }
                    else
                    {
                        system_messages(elgg_echo('supajax:container_not_found','error'));
                        $return['filter_context'] = 'all';
                        $return['title'] = elgg_echo($context . ':title:all_' . $context);
                    }
                break;
                }			
		     default:
             case 'all':
             case '':
                {
                    $return['filter_context'] = 'all';
                    $return['title'] = elgg_echo($context . ':title:all_' . $context);
                    break;
                }
		}
	}
		
    $subtype = $at_contexts[$context];
    if (isset($subtype))
        $options['subtype'] = $subtype;

	if (elgg_is_xhr()){

		elgg_register_title_button();
		// show all posts for admin or users looking at their own items
		// show only published posts for other users.
		if ($context == 'blog'){
			$show_only_published = true;
			if ($current_user) {
				if (($current_user->getGUID() == $container_guid) || $current_user->isAdmin()) {
					$show_only_published = false;
				}
			}
			if ($show_only_published) {
				$options['metadata_name_value_pairs'] = array(
					array('name' => 'status', 'value' => 'published'),
				);
			}
	    }

		$list = elgg_list_entities_from_metadata($options);

		if (!$list) {
	    	$return['content'] = '<div class="supajax-no-items">' . elgg_echo('supajax:entity:none') . '</div>';               
		} 
		else 
		{
	    	$return['content'] = $list;           
	    }
	}
	else
	{
        elgg_register_title_button();
    
	        $return['content'] = "
	                            <div class='panel-container'>
	                                <div class='tabs' id='tabs-ajax-all'></div>
	                                <div class='tabs' id='tabs-ajax-owner'></div>
	   	                            <div class='tabs' id='tabs-ajax-friends'></div>
	   	                            <div class='tabs' id='tabs-ajax-featured'></div>  
	                            </div>
	                         </div>
	                        ";  		
 	}
	return $return;
}

function supajax_get_tidypics_list($page_type = NULL, $container_guid = NULL) {
    $return = array();
    $likes_metastring = get_metastring_id('likes');
    $current_user = elgg_get_logged_in_user_entity();
        
    $container = get_entity($container_guid);
    if ($container)
    {
        if ($container instanceof ElggGroup)
            $container_name = $container->name;
        else {
            $container_name = $container->name;
        }        
    }
       $current_user_guid = $current_user->getGUID();
        $offset = (int)get_input('offset', 0);
        $limit = (int)get_input('limit', 16);
        $subtype = get_input('subtype','album');
        $sort_type = get_input('sort-type','timing-d');
        $timing_range = get_input('timing-range','all');
        $tagged_you = get_input('tagged-you','0');
        $containers = get_input('containers','all');
                
        $options = array(
             'type' => 'object',
             'limit' => $limit,
             'offset' => $offset,
             'full_view' => false,
             'list_type' => 'gallery',
             'gallery_class' => 'tidypics-gallery'
            );   
 
        switch($containers)
        {
            case ((is_numeric($containers)) && ($containers > 0)):
                {   
                    $options['container_guid'] = (int)$containers;    
                    break;
                }
            case 'all-groups':
                {
                     $groups = elgg_get_entities_from_relationship(array(
                        'type' => 'group',
                        'relationship' => 'member',
                        'relationship_guid' => $current_user_guid,
                        'inverse_relationship' => false,
                    ));  
                    $group_guids = array();
                    foreach ($groups as $group)
                    {
                        $group_guids[] = $group->guid;    
                    }
                    $options['container_guids'] = $group_guids;  
                    break;
                }   
            case 'profile':
                {
                    $options['container_guid'] = $current_user_guid;
                    break;
                }
            default:
            case 'all':
                {
                    break;
                }
        }
      
        
    if (elgg_is_logged_in())
        $return['show_tabs'] = TRUE;
    
    if (isset($page_type))
    {
        switch($page_type){
            case 'owner':
            {
                if ($container instanceof ElggUser) 
                {
                //  group_gatekeeper();
                    $return['container_guid'] = $container_guid;
                    $return['container_name'] = $container_name;
                    $options['owner_guid'] = $container_guid;
                    
                    if ($current_user && ($container_guid == $current_user->getGUID())) 
                    {//if the user is viewing own items
                        $return['title'] = elgg_echo('photos:title:your_albums');
                        $return['filter_context'] = 'owner';
                    } 
                    else
                    { 
                //      if ($container instanceof ElggGroup) 
                //      {
                //          $return['filter'] = false;              
                //          $return['filter_context'] = 'group';
                //      } 
                //      else 
                //      {
                            // do not show button or select a tab when viewing someone else's posts
                        $return['title'] = elgg_echo('photos:title:user_albums' , array($container_name));
                        $return['show_tabs'] = false;               
                        $return['filter_context'] = 'owner';
        //              }
                    }
                }
                else
                {
                      system_messages(elgg_echo('supajax:container_not_found','error'));
                 //   echo ("<script>elgg.register_error(elgg.echo('supajax:container_not_found','error'));</script>");                     
                    $return['filter_context'] = 'all';
                    $return['title'] = elgg_echo('photos:title:all_albums');
                }
                break;
            }       
            case 'friends':
                {
                    if ($container instanceof ElggUser) 
                    {
                       if (!$friends = get_user_friends($container_guid, ELGG_ENTITIES_ANY_VALUE, 0)) { // no friends
                            $return['content'] .=  '<div class="supajax-no-items">' . elgg_echo('friends:none:you') . '</div>';
                            return $return;
                        }
                        else 
                        { //if the user has friends
                            //  group_gatekeeper();
                            //  $firephp->log ('container guid = ' . $container_guid);
                            $return['container_guid'] = $container_guid;
                            $return['container_name'] = $container_name;
                            //     $options['container_guid'] = $container_guid;
                            if ($current_user && ($container_guid == $current_user->getGUID())) {//if the user is viewing own items
                                $return['title'] = elgg_echo('album:title:your:friends');
                                 $return['filter_context'] = 'friends';
                            } 
                            else
                            { 
                        //      if ($container instanceof ElggGroup) 
                        //      {
                        //          $return['filter'] = false;              
                        //          $return['filter_context'] = 'group';
                        //      } 
                        //      else 
                        //      {
                                    // do not show button or select a tab when viewing someone else's posts
                                    $return['title'] = elgg_echo('album:title:user:friends' , array($container_name));
                                    $return['show_tabs'] = false;  
                                    $return['filter_context'] = 'friends';            
                                    //$return['filter_context'] = 'owner';
                //              }
                            }
                            foreach ($friends as $friend) {
                            $options['owner_guids'][] = $friend->getGUID();
                            }
                            
                        }
                    }
                    else
                    {
                        system_messages(elgg_echo('supajax:container_not_found','error'));
                        $return['filter_context'] = 'all';
                        $return['title'] = elgg_echo('photos:title:all_albums');
                    }
                break;
                }           
             default:
             case 'all':
             case '':
                {
                    $return['filter_context'] = 'all';
                    $return['title'] = elgg_echo('photos:title:all_albums');
                    break;
                }
        }
    }


    if (elgg_is_xhr())
    {
           
    
       
        if (isset($subtype))
            $options['subtype'] = $subtype;
        else
            $options['subtype'] = 'album';
        
 

        $getter = 'elgg_get_entities';
        
        switch ($sort_type) 
        {
            case 'timing-a':
                {
                    $options['order_by'] = 'time_created ASC';
                    break;
                }
            case 'comments-d':
                {
                    $options['annotation_name'] = 'generic_comment';
                    $options['calculation'] = 'count';
                    $options['order_by'] = 'annotation_calculation desc';
                    $getter = 'elgg_get_entities_from_annotation_calculation';
                    break;
                }
            case 'comments-a':
                {
                    $options['annotation_name'] = 'generic_comment';
                    $options['calculation'] = 'count';
                    $options['order_by'] = 'annotation_calculation asc';
                    $getter = 'elgg_get_entities_from_annotation_calculation';
                    break;  
                }
            case 'views-d':
                {
                    $options['annotation_name'] = 'tp_view';
                    $options['calculation'] = 'count';
                    $options['order_by'] = 'annotation_calculation desc';
                    $getter = 'elgg_get_entities_from_annotation_calculation';
                    break;
                }
            case 'views-a':
                {
                    $options['annotation_name'] = 'tp_view';
                    $options['calculation'] = 'count';
                    $options['order_by'] = 'annotation_calculation asc';
                    $getter = 'elgg_get_entities_from_annotation_calculation';
                    break;  
                } 
            case 'likes-d':
                {
                    $options['annotation_name'] = array('likes');
                    $options['order_by'] = 'likes desc';
                    $options['selects'] = array(
                        "(SELECT count(distinct l.id) FROM {$dbprefix}annotations l WHERE l.name_id = $likes_metastring AND l.entity_guid = e.guid AND l.owner_guid <> e.owner_guid) AS likes"
                    );
                    $getter = 'elgg_get_entities_from_annotations';
                    break;
                }
            case 'likes-a':
                {
                    $options['annotation_name'] = array('likes');
                    $options['order_by'] = 'likes asc';
                    $options['selects'] = array(
                        "(SELECT count(distinct l.id) FROM {$dbprefix}annotations l WHERE l.name_id = $likes_metastring AND l.entity_guid = e.guid AND l.owner_guid <> e.owner_guid) AS likes"
                    );
                    $getter = 'elgg_get_entities_from_annotations';
                    break;  
                }                 
            case 'timing-d':                
            default:
                {
                    $options['order_by'] = 'time_created DESC';
                    break;
                }
        }
        
        switch($timing_range){
            case 'day':
                {
                    $options['created_time_lower'] = strtotime('-1 day'); 
                    break;
                }
            case 'week':
                {
                    $options['created_time_lower'] = strtotime('-1 week'); 
                    break;
                }
            case 'month':
                {
                    $options['created_time_lower'] = strtotime('-1 month'); 
                    break;
                }
            case 'year':
                {
                    $options['created_time_lower'] = strtotime('-1 year'); 
                    break;
                }
            case 'all':
            default:
                {
                    $options['created_time_lower'] = '';
                    break;
                }
        }
        
        ChromePhp::LOG('tagged you = ' . $tagged_you);
        ChromePhp::LOG('sort type = ' . $sort_type);
        ChromePhp::LOG('timing period = ' . $timing_period);
        if (($tagged_you == 1)&&($subtype == 'image'))
        {
             $clauses = elgg_get_entity_relationship_where_sql('e.guid', 'phototag', $container_guid, false);
        }

        if ($clauses) 
        {
            ChromePhp::LOG('CLAUSES = ' . $clauses);
             // merge wheres to pass to get_entities()
             if (isset($options['wheres']) && !is_array($options['wheres'])) {
                $options['wheres'] = array($options['wheres']);
             } elseif (!isset($options['wheres'])) {
                $options['wheres'] = array();
             }
    
            $options['wheres'] = array_merge($options['wheres'], $clauses['wheres']);
    
            // merge joins to pass to get_entities()
             if (isset($options['joins']) && !is_array($options['joins'])) 
             {
                 $options['joins'] = array($options['joins']);
             }
             elseif (!isset($options['joins'])) 
             {
                 $options['joins'] = array();
             }
     
             $options['joins'] = array_merge( $clauses['joins'],$options['joins']);
    
             if (isset($options['selects']) && !is_array($options['selects'])) 
             {
                 $options['selects'] = array($options['selects']);
             } 
             elseif (!isset($options['selects'])) 
             { 
                 $options['selects'] = array();
             }
      
             $select = array('r.id');
      
             $options['selects'] = array_merge($options['selects'], $select);
        }
   //   echo ('<pre>');
    //  var_dump ($options);
    //  echo ('</pre>');
     // exit;

        $list = elgg_list_entities($options, $getter);
        if (!$list) {
            
            if (($tagged_you == 1)&&($subtype == 'image'))
                $return['content'] = '<div class="supajax-no-items">' . elgg_echo('tidypics:usertags_photos:nosuccess') . '</div>';
            else 
                $return['content'] = '<div class="supajax-no-items">' . elgg_echo('supajax:entities:none') . '</div>';
        } 
        else 
        {
            
            $return['content'] = $list;           
        }
    }
    else
    {
        if (elgg_is_logged_in()) 
        {
            $logged_in_guid = elgg_get_logged_in_user_guid();
            elgg_register_menu_item('title', array('name' => 'addphotos',
                                               'href' => "ajax/view/photos/selectalbum/?owner_guid=" . $logged_in_guid,
                                               'text' => elgg_echo("photos:addphotos"),
                                               'link_class' => 'elgg-button elgg-button-action elgg-lightbox'));
        }

        elgg_register_title_button('photos');
       // elgg_load_js('elgg.supajax');
    
            $return['content'] = "
                                <div class='panel-container'>
                                    <div class='tabs' id='tabs-ajax-all'></div>
                                    <div class='tabs' id='tabs-ajax-owner'></div>
                                    <div class='tabs' id='tabs-ajax-friends'></div>
                                </div>
                             </div>
                            ";          
    }
    return $return;
}


function supajax_get_member_list($page_type = '') 
{
//    $firephp = FirePHP::getInstance(true);
    $return = array();

    $return['show_tabs'] = TRUE;
            
    switch ($page_type) 
    {
        case 'popular':
        {
            $return['title']  = elgg_echo('members:title:popular');
            $return['filter_context'] = $page_type;
            break;
        }
        case 'online':
        {
            $return['title']  = elgg_echo('members:title:online');
            $return['filter_context'] = $page_type;
           break;
        }
        case '':
        case 'newest':
        default:
        {
            $return['title']  = elgg_echo('members:title:newest');
            $return['filter_context'] = 'newest';
            break;
        }
    }
                              
	if (elgg_is_xhr())
	{
		$options = array('type' => 'user', 'full_view' => false);
		switch ($page_type) 
		{
			case 'popular':
			{
				$options['relationship'] = 'friend';
				$options['inverse_relationship'] = false;
				$list = elgg_list_entities_from_relationship_count($options);
				break;
            }
			case 'online':
            {
				$list  = get_online_users();
				break;
            }
			case 'newest':
            case '':
			default:
            {
				$list = elgg_list_entities($options);
				break;
            }
		}
        if (!$list) {
            $return['content'] = '<div class="supajax-no-items">' . elgg_echo('supajax:members:none') . '</div>';               
        } 
        else 
        {
            $return['content'] = $list;           
        }
	}
	else
	{
	//    elgg_load_js('elgg.supajax');
	    $return['content'] = "
	                         <div class='panel-container'>
	                                <div class='tabs' id='tabs-ajax-newest'></div>
	                                <div class='tabs' id='tabs-ajax-popular'></div>
	                                <div class='tabs' id='tabs-ajax-online'></div>  
	                            </div>
	                         </div>
	                        ";    
	}
	return $return;
}

function supajax_get_group_list($page_type = '') 
{
    if (elgg_is_admin_logged_in())
	{
		elgg_register_title_button();
	}	
 
    $return = array();
    $return['show_tabs'] = TRUE; 
    if ($page_type)   
        $return['filter_context'] = $page_type;
    else
        $return['filter_context'] = 'newest';

    //ChromePhp::LOG('get_group_list; page_type = ' . $page_type);

    switch ($page_type) 
    {
        case 'popular':
            {
                $return['title']  = elgg_echo('groups:title:popular');
                break;
            }
        case 'discussion':
            {
                $return['title']  = elgg_echo('groups:title:discussion');
                break;
            }
        case 'open':
            {
                $return['title']  = elgg_echo('groups:title:open');
                break;
            }
        case 'closed':
            {
                $return['title']  = elgg_echo('groups:title:closed');
                break;       
            }
        case 'alpha':
            {
                $return['title']  = elgg_echo('groups:title:alpha');
                break;
            }
        case 'ordered':
            {
                $return['title']  = elgg_echo('groups:title:ordered');
                break;      
            }           
        case '':                                            
        case 'newest':
        default:
            {
                $return['title']  = elgg_echo('groups:title:newest');
                break;
            }
    }

    ChromePhp::LOG('xhr = ' . elgg_is_xhr());

	if (elgg_is_xhr())
	{
	//    echo ("<script>alert('xhr = true ; " . elgg_is_xhr() . "');</script>");
		$options = array(
			"type" => "group", 
			"full_view" => false,
		);

		$options["limit"] = 20;	
        $return['filter_context'] = $page_type;
		switch ($page_type) 
		{
			case 'popular':
                {
    				$options['relationship'] = 'member';
        			$options['inverse_relationship'] = false;
                    
        			if(!($return['content'] = elgg_list_entities_from_relationship_count($options)))
        			{
        				$return['content'] = '<div class="supajax-no-items">' . elgg_echo("groups:none") . '</div>';
        			}	
        			break;
        	   }
			case 'discussion':
				{
 					$options['type'] = 'object';
 					$options['subtype'] = 'groupforumtopic';
 					$options['order_by'] = 'e.last_action desc';
					$options['limit'] = 40;
          
					if(!($return['content'] = elgg_list_entities($options)))
					{
						$return['content'] = '<div class="supajax-no-items">' . elgg_echo("discussion:none") . '</div>';

					}	
					break;
				}
			case 'open':
			 {
                $options["metadata_name_value_pairs"] = array(
				"name" => "membership",
				"value" => ACCESS_PUBLIC
				);
				if(!($return['content'] = elgg_list_entities_from_metadata($options)))
				{
					$return['content'] = '<div class="supajax-no-items">' . elgg_echo("groups:none") . '</div>';
				}
  						
				break;
             }
			case 'closed':
                {
					$options["metadata_name_value_pairs"] = array(
						"name" => "membership",
						"value" => ACCESS_PUBLIC,
						"operand" => "<>"
					);
                   
					if(!($return['content'] = elgg_list_entities_from_metadata($options)))
					{
						$return['content'] = '<div class="supajax-no-items">' . elgg_echo("groups:none") . '</div>';
					}	
						
				break;
                }
			case 'alpha':
                {
					$options["joins"]	= array("JOIN " . $dbprefix . "groups_entity ge ON e.guid = ge.guid");
					$options["order_by"] = "ge.name ASC";
                    
					if(!($return['content'] = elgg_list_entities_from_metadata($options)))
					{
						$return['content'] = '<div class="supajax-no-items">' . elgg_echo("groups:none") . '</div>';
					}	
						
				    break;
                }
			case 'ordered':
				{
					$order_id = add_metastring("order");

					$options["pagination"] = false;
					$options["selects"] = array(
						"IFNULL((SELECT order_ms.string as order_val FROM " . $dbprefix . "metadata mo JOIN " . $dbprefix . "metastrings order_ms ON mo.value_id = order_ms.id WHERE e.guid = mo.entity_guid AND mo.name_id = " . $order_id . "), 99999) AS order_val"
					);
					
					$options["order_by"] = "CAST(order_val AS SIGNED) ASC, e.time_created DESC";
 
					if(elgg_is_admin_logged_in()){
						$options["list_class"] = "group-tools-list-ordered";
					}
					if(!($return['content'] = elgg_list_entities_from_metadata($options)))
					{
						$return['content'] = '<div class="supajax-no-items">' . elgg_echo("groups:none") . '</div>';
					}				

					break;		
				}			
            case '':											
			case 'newest':
			default:
                {
					if(!($return['content'] = elgg_list_entities($options)))
					{
						$return['content'] = '<div class="supajax-no-items">' . elgg_echo("groups:none") . '</div>';
					}	 
					break;
                }
		}
	}
	else
	{
	//    echo ("<script>alert('xhr = false ; " . elgg_is_xhr() . "');</script>");
	  //  elgg_load_js('elgg.supajax');
   
  
   
        $sidebar = elgg_view('groups/sidebar/find');
        $sidebar .= elgg_view('groups/sidebar/featured');
   
        $return['sidebar'] = $sidebar;

	    $return['content'] = "
	                         <div class='panel-container'>
	                                <div class='tabs' id='tabs-ajax-newest'></div>
	                                <div class='tabs' id='tabs-ajax-popular'></div>
	                                <div class='tabs' id='tabs-ajax-discussion'></div>
	                                <div class='tabs' id='tabs-ajax-open'></div> 
	                                <div class='tabs' id='tabs-ajax-closed'></div>
	                                <div class='tabs' id='tabs-ajax-alpha'></div> 
	                                <div class='tabs' id='tabs-ajax-ordered'></div>	                                
	                            </div>
	                         </div>
	                        ";    
	}
   // ChromePhp::LOG('content = ' . $return['content']);
	return $return;
}

function supajax_get_liked_list($page_type = '', $entity = NULL) 
{
    $likes_metastring = get_metastring_id('likes');
//    $firephp = FirePHP::getInstance(true);
    if (!$entity)
        $entity = elgg_get_logged_in_user_entity();
    $return = array();
    ChromePhp::LOG('most_liked page_type = ' . $page_type);
    $return['show_tabs'] = TRUE;
    $username = $entity->name;
    ChromePhp::LOG('most liked - username = ' . $username);
    switch ($page_type) 
    {
        case 'most_liked':
        {
            $return['title']  = elgg_echo('supajax:liked:title:user:most_liked', array($username));
            $return['filter_context'] = 'most-liked';
            break;
        }
        case '':
        default:
        {
            $return['title']  = elgg_echo('supajax:liked:title:user:liked_content', array($username));
            $return['filter_context'] = '';
            break;
        }
    }
                              
    if (elgg_is_xhr())
    {
        
        switch ($page_type) 
        {
            case 'most_liked':
            {
                $options = array(
                    'owner_guid' => $entity->guid,
                    'annotation_names' => array('likes'),
                    'selects' => array("(SELECT count(distinct l.id) FROM {$dbprefix}annotations l WHERE l.name_id = $likes_metastring AND l.entity_guid = e.guid AND l.owner_guid <> e.owner_guid) AS likes"),
                    'order_by' => 'likes DESC',
                    'full_view' => false
                    );
                $list = elgg_list_entities_from_annotations($options);
                
                break;
            }
            case '':
            default:
            {
                  $options = array(
                    'annotation_names' => array('likes'),
                    'annotation_owner_guids' => array($entity->guid),
                    'order_by' => 'maxtime DESC',
                    'full_view' => false,
                );
                $list = elgg_list_entities_from_annotations($options);
                break;
            }
        }
        if (!$list) {
            $return['content'] = '<div class="supajax-no-items">' . elgg_echo('liked_content:noresults') . '</div>';               
        } 
        else 
        {
            $return['content'] = $list;           
        }
    }
    else
    {
    //    elgg_load_js('elgg.supajax');
        $return['content'] = "
                             <div class='panel-container'>
                                    <div class='tabs' id='tabs-ajax-liked'></div>
                                    <div class='tabs' id='tabs-ajax-most-liked'></div>
                                </div>
                             </div>
                            ";    
    }
    return $return;
}
