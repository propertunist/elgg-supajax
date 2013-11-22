<?php
/**
 * Sidebar view
 */

$current_user_guid = elgg_get_logged_in_user_guid();

$page = elgg_extract('page', $vars);
$image = elgg_extract('image', $vars);
$base = elgg_get_site_url() . 'photos/';

if ($page == 'all') // only show sidebar filter box for main listing pages
{
    $filter_box_title = elgg_echo('supajax:title:filter-box');
    $filter_box = '<div class="supajax-filter">';
    $filter_box .= elgg_view('input/radio', array(
            'align' => 'horizontal',
            'value' => array(
                  elgg_echo('item:object:album') => 'album'),
            'disabled' => false,
            'options' => array(
               elgg_echo('item:object:album') =>  'album', 
               elgg_echo('item:object:image') => 'image'),
            'name' => 'subtype',
            'id' => 'subtype'));
    $filter_box .= '<hr>';
    $filter_box .= '<label>' . elgg_echo('supajax:filter:sorting') . '</label>';
    $filter_options_values = array(
                'timing-d' => elgg_echo('supajax:sorting:timing-d'),
                'timing-a' => elgg_echo('supajax:sorting:timing-a'), 
                'comments-d' => elgg_echo('supajax:sorting:comments-d'),
                'comments-a' => elgg_echo('supajax:sorting:comments-a'),                
                'views-d' => elgg_echo('supajax:sorting:views-d'),
                'views-a' => elgg_echo('supajax:sorting:views-a')
            );
    if (elgg_is_active_plugin('likes'))
    {
        $filter_options_values['likes-d'] = elgg_echo('supajax:sorting:likes-d');
        $filter_options_values['likes-a'] = elgg_echo('supajax:sorting:likes-a');
    }
              
    $filter_box .= elgg_view('input/dropdown', array(
            'value' => array(
                 'timing-d' => elgg_echo('supajax:sorting:timing-d')),
            'options_values' => $filter_options_values,
            'name' => 'sort-type',
            'id' => 'sort-type'
            ));
            
    $filter_box .= '<hr>';
    
    $filter_box .= '<label>' . elgg_echo('supajax:filter:date_period') . '</label>';  
    $filter_box .= elgg_view('input/dropdown', array(
            'value' => array(
                'all' => elgg_echo('supajax:infinite')),
            'options_values' => array(
                'all' => elgg_echo('supajax:infinite'), 
                'day' => elgg_echo('supajax:day'),
                'week' => elgg_echo('supajax:week'),
                'month' => elgg_echo('supajax:month'),
                'year' => elgg_echo('supajax:year')),
            'name' => 'timing-range'
            ));
            
    $groups = elgg_get_entities_from_relationship(array(
        'type' => 'group',
        'relationship' => 'member',
        'relationship_guid' => $current_user_guid,
        'inverse_relationship' => false,
    ));            

    if ($groups)
    {
        $group_options = array(
            'all' => elgg_echo('supajax:sorting:all'),
            'profile' => elgg_echo('profile') . ': ' . elgg_get_logged_in_user_entity()->name,
            'all-groups' => elgg_echo('supajax:sorting:all_groups')
            );
        foreach ($groups as $group)
        {
            $group_options[$group->guid] = elgg_echo('group') . ': ' . $group->name;
        }
        $filter_box .= '<hr>';
        $filter_box .= '<label>' . elgg_echo('supajax:filter:container') . '</label>';          
        $filter_box .= elgg_view('input/dropdown', array(
        'value' => array(
            'all' => elgg_echo('supajax:all_groups')),
        'options_values' => $group_options, 
        'name' => 'containers'
        ));
    }
   /* 
    $access_types = get_read_access_array();

    $filter_box .= '<hr>';
    $filter_box .= '<label>' . elgg_echo('supajax:filter:access') . '</label>';          
    $filter_box .= elgg_view('input/dropdown', array(
    'options_values' => $access_types, 
    'name' => 'access-filter'
    ));
  */          
    if((elgg_is_logged_in()&&(elgg_get_plugin_setting('tagging', 'tidypics') == true))) // only show tagged-me checkbox if logged-in and tagging is enabled
    {
        $filter_box .= '<hr>';
     
        $filter_box .= '<label id="tagged-you-label">' . elgg_echo('supajax:filter:usertagged') . '</label>'; 
        $filter_box .= elgg_view('input/checkbox', array(
            'default' => FALSE,
            'style' => 'float:right',
            'name' => 'tagged-you',
            'id' => 'tagged-you'
        ));
     }
    
    $filter_box .= '<hr>';
    
    $filter_box .= elgg_view('output/url', array(
                'text' => elgg_echo('supajax:filter:action-button'),
                'class' => 'elgg-button elgg-button-action supajax-filter-button',
                'href' => '#',
                'id' => 'apply-this-filter',
                'style' => 'float:right'));
             $site_entity = elgg_get_site_entity();

            if (elgg_is_active_plugin('likes', $site_entity->guid))
                $likes = 'true'; 
            if (elgg_get_plugin_setting('album_comments', 'tidypics'))
                $album_comments = 'true';
            else 
                $album_comments = 'false';
            if (elgg_get_plugin_setting('view_count', 'tidypics'))
                $view_count = 'true';
            else 
                $view_count = 'false';
    $filter_box .= elgg_view('input/hidden', array(
            'ac' => $album_comments,
            'li' => $likes,
            'vc' => $view_count,
            'id' => 'fo'
            ));                
              
    $filter_box .= '</div>';
    echo elgg_view_module('aside', $filter_box_title, $filter_box);
}
if ($page == 'upload') {
        if (elgg_get_plugin_setting('quota', 'tidypics')) {
                echo elgg_view('photos/sidebar/quota', $vars);
        }
} else if (($page == 'all') || ($page == 'owner') || ($page == 'friends')) {

        if(elgg_is_active_plugin('elggx_fivestar')) {
                elgg_register_menu_item('page', array('name' => 'D10_tidypics_highestrated',
                                                      'text' => elgg_echo('tidypics:highestrated'),
                                                      'href' => $base . 'highestrated',
                                                      'section' => 'D'));
                elgg_register_menu_item('page', array('name' => 'D20_tidypics_highestvotecount',
                                                      'text' => elgg_echo('tidypics:highestvotecount'),
                                                      'href' => $base . 'highestvotecount',
                                                      'section' => 'D'));
                elgg_register_menu_item('page', array('name' => 'D30_tidypics_recentvotes',
                                                      'text' => elgg_echo('tidypics:recentlyvoted'),
                                                      'href' => $base . 'recentvotes',
                                                      'section' => 'D'));
        }

} else if ($image && $page == 'tp_view') {
        if (elgg_get_plugin_setting('exif', 'tidypics')) {
                echo elgg_view('photos/sidebar/exif', $vars);
        }

        // list of tagged members in an image (code from Tagged people plugin by Kevin Jardine)
        if (elgg_get_plugin_setting('tagging', 'tidypics')) {
                $body = elgg_list_entities_from_relationship(array(
                        'relationship' => 'phototag',
                        'relationship_guid' => $image->guid,
                        'inverse_relationship' => true,
                        'type' => 'user',
                        'limit' => 15,
                        'list_type' => 'gallery',
                        'gallery_class' => 'elgg-gallery-users',
                        'pagination' => false
                ));
                if ($body) {
                        $title = elgg_echo('tidypics_tagged_members');
                        echo elgg_view_module('aside', $title, $body);
                }
        }
}
