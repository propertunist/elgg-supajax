<?php
/**
 * Main content filter
 *
 * Select between user, friends, and all content
 *
 * @uses $vars['filter_context']  Filter context: all, friends, mine
 * @uses $vars['filter_override'] HTML for overriding the default filter (override)
 * @uses $vars['context']         Page context (override)
 */
 //$firephp = FirePHP::getInstance(true);
//$firephp->log('FILTER INIT');

    $page = get_input('page');
ChromePhp::log('FILTER INIT - ' . $page);    
  //  $firephp->log('filter hook:');
    if(($page=="all")||($page=='')||(substr($page,0,8)=='featured')||(substr($page,0,4)=='user')||(substr($page,0,5)=='owner')||(substr($page,0,7)=='friends')||(substr($page,0,7)=='popular')||(substr($page,0,6)=='online')||(substr($page,0,6)=='newest'))
    {
    //    $firephp->log('FILTER returning false');
        return false;

    }
    else {
        
    //$firephp->log('FILTER making');
    ChromePhp::log('FILTER making');

$filter_var = $vars['supajax_filter'];
//$firephp->log('tabs-filter-hook; filter_var = ' . $filter_var);
ChromePhp::log('tabs-filter; filter_var = ' . $filter_var);
if (!$vars['vars']['container_guid'] == '') {
    $container_guid = $vars['vars']['container_guid'];
  //  $firephp->log ('vars - vars - container_guid = ' . $vars['vars']['container_guid']);
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
if (isset($vars['filter_override'])) {
    echo $vars['filter_override'];
//    $firephp->log('FILTER OVERRIDE');
  //  ChromePhp::log('FILTER OVERRIDE');
    return true;
}
if (isset($vars['vars']['filter_context'])) {
    $filter_context = $vars['vars']['filter_context'];
}
else {
    if (($context == 'members')||($context == 'co-creators'))
        $filter_context = 'newest';
    else
       $filter_context = 'all';
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

//$firephp->log('vars - filter context = ' . $vars['vars']['filter_context']);
//$firephp->log('vars - context = ' . $context);
  //  echo '<span class="page-context">' . $context . '|' . $vars['vars']['filter_context'] . '|' . $container_name . '</span>';
if ($context) {
    switch($context){
        case 'thoughts':
        case 'blog':    
             {
            //$username = elgg_get_logged_in_user_entity()->username;
            $username = $container->username;
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
                if (elgg_plugin_exists('blog_tools'))
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
                if (elgg_is_logged_in() && $context) {
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
                    
                    $username = elgg_get_logged_in_user_entity()->username;
                
                    // generate a list of default tabs
                    $tabs = array(
                        'all' => array(
                            'text' => elgg_echo('all'),
                            'href' => (isset($vars['all_link'])) ? $vars['all_link'] : "$context/all #tabs-ajax-all",
                            'selected' => ($filter_context == 'all'),
                            'data-target' => '#tabs-ajax-all',                            
                            'priority' => $all_priority,
                        ),
                        'mine' => array(
                            'text' => elgg_echo('mine'),
                            'href' => (isset($vars['mine_link'])) ? $vars['mine_link'] : "$context/owner/$username #tabs-ajax-owner",
                            'selected' => ($filter_context == 'mine'),
                            'data-target' => '#tabs-ajax-owner',                            
                            'priority' => 300,
                        ),
                        'friends' => array(
                            'text' => elgg_echo('friends'),
                            'href' => (isset($vars['friend_link'])) ? $vars['friend_link'] : "$context/friends/$username #tabs-ajax-friends",
                            'selected' => ($filter_context == 'friends'),
                            'data-target' => '#tabs-ajax-friends',
                            'priority' => $friend_priority,
                        ),
                    );
                   }
                break;
            }
        case 'videos':
        case 'photos':
        case 'links':
        case 'reference':
        case 'katalists':
        case 'pinboards':
        case 'shouts':
        case 'file': {
            //$username = elgg_get_logged_in_user_entity()->username;
            $username = $container->username;
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
                ),
            );
        break;
        }
        case 'members':
        case 'co-creators':  {
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
                    ),
            );
            break;
        }
        case 'zones':
        case 'groups':{
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
                        ),
                        "discussion" => array(
                            "text" => elgg_echo("groups:latestdiscussion #tabs-ajax-discussion"),
                            "href" => "groups/all?filter=discussion",
                            'data-target' => '#tabs-ajax-discussion',
                            'item_class' => 'tab',  
                            "priority" => 400,
                        ),
                        "open" => array(
                            "text" => elgg_echo("group_tools:groups:sorting:open"),
                            "href" => "groups/all?filter=open #tabs-ajax-open",
                            'data-target' => '#tabs-ajax-open',
                            'item_class' => 'tab',              
                            "priority" => 500,
                        ),
                        "closed" => array(
                            "text" => elgg_echo("group_tools:groups:sorting:closed"),
                            "href" => "groups/all?filter=closed #tabs-ajax-closed",
                            'data-target' => '#tabs-ajax-closed',   
                            'item_class' => 'tab',  
                            "priority" => 600,
                        ),
                        "alpha" => array(
                            "text" => elgg_echo("group_tools:groups:sorting:alphabetical"),
                            "href" => "groups/all?filter=alpha #tabs-ajax-alpha",
                            'data-target' => '#tabs-ajax-alpha',    
                            'item_class' => 'tab',
                            "priority" => 700,
                        ),
                        "ordered" => array(
                            "text" => elgg_echo("group_tools:groups:sorting:ordered"),
                            "href" => "groups/all?filter=ordered #tabs-ajax-ordered",
                            'data-target' => '#tabs-ajax-ordered',          
                            "priority" => 800,
                        ),
                    );
                break;
        }
        default: break;
    }

    foreach ($tabs as $name => $tab) {

        if (($context == 'groups')){ //group_tools options
            $show_tab = false;
            $show_tab_setting = elgg_get_plugin_setting("group_listing_" . $name . "_available", "group_tools");
            if($name == "ordered"){
                if($show_tab_setting == "1"){
                    $show_tab = true;
                }
            } elseif($show_tab_setting !== "0"){
                $show_tab = true;
            }
            if($show_tab){
                $tab["name"] = $name;
                elgg_register_menu_item("filter", $tab);
            }
        }
        else
        {
            $tab['name'] = $name;
            elgg_register_menu_item('filter', $tab);
        }
    }

    echo '<div id="easy-tabs">';
   // if ($filter_var ==TRUE){
        echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz tabs'));

  
  

}
}
