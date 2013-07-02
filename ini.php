<?

AutoLoader::register_assets("stylesheets/wildfire.cms_static_cache",__DIR__."/assets/stylesheets/wildfire.cms_static_cache", "/*.css");
AutoLoader::register_assets("javascripts/wildfire.cms_static_cache",__DIR__."/assets/javascripts/wildfire.cms_static_cache", "/*.js");

AutoLoader::add_plugin_setup_script(__DIR__."/setup.php");
?>