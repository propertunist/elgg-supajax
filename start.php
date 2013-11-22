<?php
/************
 * supajax - converts elgg tabbed areas into ajax live reloads 
 */
 
elgg_register_event_handler('init', 'system', 'supajax_init');
   //require_once($CONFIG->path . 'firephp/FirePHP.class.php');
   require_once($CONFIG->path .'ChromePhp.php');
 
  
function supajax_init() {
  //$firephp = FirePHP::getInstance(true);
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
    'liked_content' => NULL,
    'members' => NULL,
    'groups' => NULL, 
    'activity' => NULL  
);
	$context = elgg_get_context();
	if ($context == 'co-creators')
		$context = 'members';

    if ($context == 'zones')
        $context = 'groups';
    
     if ($context == 'resonance')
        $context = 'liked_content';
    
  
    elgg_register_library('elgg:supajax', elgg_get_plugins_path() . 'supajax/lib/supajax.php');
    elgg_load_library('elgg:supajax');
    elgg_register_js("hide", elgg_get_site_url() . "mod/supajax/vendors/jquery.truncatable.js");
    elgg_register_js('jquery-haschange', elgg_get_site_url().'mod/supajax/vendors/jquery.hashchange.min.js', 'head');
    elgg_register_js('jquery-easytabs', elgg_get_site_url().'mod/supajax/vendors/jquery.easytabs.min.js', 'head');
    $ajax_js = elgg_get_simplecache_url('js', 'supajax/supajax');
    elgg_register_simplecache_view('js/supajax/supajax');
    elgg_register_js('elgg.supajax', $ajax_js); 


    
    if(array_key_exists($context,$at_contexts))
	{
	    elgg_load_js('elgg.supajax');
        if ($context =='activity')
        {
            elgg_register_js("elgg.ui.river", elgg_get_site_url() . "mod/supajax/views/default/js/ui.river.js");
            elgg_load_js('elgg.ui.river');
        }
	    ChromePhp::LOG('key exists');
		elgg_register_plugin_hook_handler('view', 'navigation/pagination', 'tabs_paginator_hook');
      	elgg_register_plugin_hook_handler("route", $context, "supajax_route_hook");
  	 	elgg_register_plugin_hook_handler('view', 'page/layouts/content/filter', 'tabs_filter_hook');
  	 	ChromePhp::LOG('after hook handlers');
	}
    
    elgg_extend_view('css/elgg', 'supajax/css');

}

function tabs_paginator_hook($hook, $type, $return, $params) {
    if (!empty($return)){
        return elgg_view('supajax/navigation/pagination', array_merge($params, array('hidden_paginator' => $return)));
    }
}

function supajax_route_hook($hook, $type, $returnvalue, $params)
{

    ChromePhp::LOG('route_hook');
    ChromePhp::LOG('route_hook; $returnvalue  =' . $returnvalue);
    //$firephp = FirePHP::getInstance(true);

	$context = elgg_get_context();
	  ChromePhp::LOG('context = ' . $context);
	$result = $returnvalue;
	$default = FALSE;
    //$page = get_input('page');
    if (is_array($returnvalue))
    {
	   $page = elgg_extract("segments", $returnvalue);
       $page_type = $page[0];   
    }
    else
        $page_type = '';

    
    $user = get_user_by_username($page[1]);
            
    if ($user)
    {
        $user_guid = $user->getGUID();
    }
    else ($user_guid = '');

	switch($context)
	{
	    case 'activity':
        {
            $offset = (int)(get_input('offset'));
            $activity_pages = array(
                    'all',
                    'owner',
                    'friends',
                    '');
            elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
            if (in_array($page_type, $activity_pages)) 
            {
                $params = supajax_get_activity_list($context, $page_type);
                $result = false;
            }
            else 
            {
                $default = true;    
            }
            break;
        }
		case 'thoughts':
		case 'videos':
		case 'links':
		case 'reference':
		case 'katalists':
		case 'pinboards':				
		case 'shouts':
		case 'file': 
		{
		    $entity_pages = array(
                'all',
                'owner',
                'friends',
                'featured',
                '');

			if (in_array($page_type, $entity_pages)) 
			{
			    
   
			     switch($page_type)
			     {
			         case "":
					 case "all":
                     case "featured":                         
					 {
					   $params = supajax_get_page_content_list($context, $page_type);
                       $result = false;
 					   break 1;
                     }
				     case "owner": 
				     {
                     
                            if (!$user_guid == elgg_get_logged_in_user_guid())
                                $params['show_tabs'] = false;
					    $params = supajax_get_page_content_list($context, $page_type, $user_guid);
                        $result = false;

						break 1;
                     }
					 case "friends":
					 {
                        if (!$user_guid == elgg_get_logged_in_user_guid())
                            $params['show_tabs'] = false;
						$params = supajax_get_page_content_list($context, $page_type, $user_guid);
                        $result = false;
						break 1;
                    }
					case "group":
					{
						$params = supajax_get_page_content_list($context, $page_type,$params, $user_guid);
                        $result = false;
						break 1;
                    }									
                    default:
                    {
                        $default = true;
                        break 1;
                    }
               	}
			}
			else 
			{
				$default = true;	
			}
			break;
		}
        case 'photos':
        {
            $photo_pages = array(
            'siteimagesall',
            'siteimagesowner',
            'siteimagesfriends',
            'all',
            'owner',
            'friends',
            'recentlyviewed',
            'recentlycommented',
            'mostviewed',
            'mostviewedtoday',
            'mostviewedthismonth',
            'mostviewedlastmonth',
            'mostviewedthisyear',
            'mostcommented',
            'mostcommentedtoday',
            'mostcommentedthismonth',
            'mostcommentedlastmonth',
            'mostcommentedthisyear',
            'tagged');
            
            if (in_array($page_type, $photo_pages)) 
            {
                $params = supajax_get_tidypics_list($page_type, $user_guid);
                $result = false;
                break 1;
            }
            else 
            {
                $default = true;    
            }            
            break;
        }
		case 'members':				
		{
            $member_pages = array(
			 'popular',
			 'newest',
			 'online',
			 '');
			
            if (in_array($page_type, $member_pages)) 
            {
			    $params = supajax_get_member_list($page_type);
                $result = false;
              }
            else 
		    {
                $default = true;	
            }
            break;
        }
        case 'zones':	 
        case 'groups': 
        {
         

            ChromePhp::LOG('1 $page_type = ' . $page_type);
       
            if (($page[0] == 'all')||($page[0] == ''))
			{
			    $page_type = get_input('filter','newest'); 	
				$group_filters = array(
				'popular',
				'newest',
				'discussion',
				'newest',
				'open',
				'closed',
				'alpha',
				'ordered');
				
				if (!in_array($page_type, $group_filters)) 
				{
					$page_type = 'newest';
				}
                ChromePhp::LOG('2 $page_type = ' . $page_type);
				$params = supajax_get_group_list($page_type);
                $result = false;
            }
			else 
			{
				$default = true;
			}
			break;
		} 			
        case 'liked_content':
        {
            if (($page[0] == 'user')||($page[0] == ''))
            {
                $page_type = get_input('filter',''); 
                if (!$page_type == 'most_liked')
                {
                    $page_type = ''; 
                }
                $params = supajax_get_liked_list($page_type);
                $result = false;
            }
            else 
            {
                $default = true;
            }
            break;
        }
		default:
		{
		    $default = true;
            break;
		}
	}
    ChromePhp::LOG('$_SERVER = ' . $_SERVER['HTTP_X_REQUESTED_WITH']);
    ChromePhp::LOG('X-requested-with = ' . get_input('X-Requested-With'));
	if($default == FALSE)
	{    // if default is true then bypass the replacement process

        if(elgg_is_xhr())
        { // if ajax has initiated the data request then output the list item directly
           // echo ("<script>alert('supajax_route_hook');</script>");
        	echo $params['content'];
        }
        else
        { // otherwise render the whole page

	    	if (isset($params['sidebar'])) 
	    	{
        		$params['sidebar'] .= elgg_view($context. '/sidebar', array('page' => $page_type));
        	} else 
        	{
	            $params['sidebar'] = elgg_view($context . '/sidebar', array('page' => $page_type));
        	}

            $body = elgg_view_layout('content', $params);

            echo elgg_view_page($params['title'], $body);  // page is drawn and standard filter is 1st called
        }  
	}   
	return $result;
}

function tabs_filter_hook($hook, $type, $return, $params)
{ //this replaces the filter with the ajax filter for relevant pages 

    ChromePhp::LOG('tabs_filter_hook');
    $page = get_input('page');
    ChromePhp::LOG('page = ' . $page);
    if(($page=="all")||($page=='')||(substr($page,0,8)=='featured')||(substr($page,0,4)=='user')||(substr($page,0,5)=='owner')||(substr($page,0,7)=='friends')||(substr($page,0,7)=='popular')||(substr($page,0,6)=='online')||(substr($page,0,6)=='newest')||(substr($page,0,13)=='siteimagesall')||(substr($page,0,15)=='siteimagesowner')||(substr($page,0,17)=='siteimagesfriends')||(substr($page,0,14)=='recentlyviewed')||(substr($page,0,17)=='recentlycommented')||(substr($page,0,10)=='mostviewed')||(substr($page,0,15)=='mostviewedtoday')||(substr($page,0,19)=='mostviewedthismonth')||(substr($page,0,19)=='mostviewedlastmonth')||(substr($page,0,18)=='mostviewedthisyear')||(substr($page,0,13)=='mostcommented')||(substr($page,0,18)=='mostcommentedtoday')||(substr($page,0,22)=='mostcommentedthismonth')||(substr($page,0,22)=='mostcommentedlastmonth')||(substr($page,0,21)=='mostcommentedthisyear')||(substr($page,0,6)=='tagged'))
    {
        ChromePhp::LOG('tabs_filter_hook - url path matched');
        elgg_load_js('jquery-haschange');
        elgg_load_js('jquery-easytabs');
        //elgg_load_js('elgg.supajax');
        elgg_load_js('hide');

        return elgg_view('page/layouts/content/ajax_filter', $params);    
    }
    else {
        return false;
    }
}