supajax for elgg 1.8
--------------------

this plugin ajaxifies various parts of elgg so that page areas are loaded dynamically as site users request the data; this increases site efficiency, speed and usability.

features:
--------

* replaces common elgg tab system so that tabs and their content load dynamically, using the easytabs jquery plugin to switch smoothly between tabs. e.g. members, groups, blogs, file, pages, bookmarks, videolist, tidypics, pinboards, the wire, liked_content & more. 
* ajax pagination is present to allow lists to grow vertically as the user clicks to load more items into the list.
* page headings update dynamically for each tab to increase awareness of the current page's context.
* tab content is cached after loading so tabs can be switched without any delay.
* the object type selector drop-down on the activity feed page triggers dynamic loads of the filtered list of content into the river.
* supports the featured tab from the blog_tools plugin if blog_tools is installed.
* adds a 'groups' tab to entity lists and the river to allow you to view the content of the current type that is in any groups you are a member of.
* hides long comments on river page - expandable with a click
* breadcrumbs no longer render for tabbed pages since they are the 1st page in the context and the main page title is sufficient
* adds a context sensitive filter box to the sidebar to allow lists to be sorted and filtered by relevant fields. e.g. tidypics lists can be switched between albums and images, can be sorted by date_created, comment count, like count & view count; the timing range of 'date_created' can be chosen and finally an additional option exists to only show items you are image-tagged in.

todo:
----
* integrate auto-update feature from the river_addon plugin for live river updates.
* integrate dynamic liking, commenting and deletion for the river from the customactivity plugin.
* modularisation of code to allow new tabs to be created - possibly via the admin interface and possibly using an anypages type of approach.
* add dropdown selector for specific groups to relevant list pages
* create admin panel to allow changing of animation properties


tech notes
----------

easy-tabs 3.2.0 needed a patch to line 190 to enable dynamic tab loading on the river page:
  $matchingPanel.not('.'+settings.panelActiveClass).hide();