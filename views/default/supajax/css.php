.atPaginatorWrapper .atHiddenPaginator, .hidden-filter {
	display: none!important;
}

.atPaginatorWrapper {
	height: 29px;
	margin-left: auto;
	margin-right: auto;
	padding: 7px 10px 0;
	text-align: center;
}

.panel-container
{
	 min-height:70px!important;
	 background-repeat:no-repeat!important; 
	 background-position:50% 50%!important;
	 background-image: url('<?php echo($CONFIG -> site -> url); ?>_graphics/ajax_loader.gif'); 
}

#easy-tabs ul li.active a{
	position: relative;
	top: 2px;
	background: white;
}

#easy-tabs ul li.active{
	border-color: #ccc;
	background: white;
}

a[class^='more'], a[class*=' more']{
	top: 9px;
	position: relative;
}

.supajax-no-items{margin-top:1.4em;}

.supajax-filter {
	padding-top:8px;
}

.supajax-filter .elgg-input-checkbox, .supajax-filter .elgg-input-dropdown{
	margin-right:0px!important;
}

.supajax-filter hr {
	border-color:rgba(80,80,80,0.5)!important;
}

.supajax-filter select
{
	min-width: 97px;
	max-width: 100%;
	margin-left:5px;
}
