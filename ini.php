<?

AutoLoader::register_assets("stylesheets/wildfire.cms_static_cache",__DIR__."/resources/public/stylesheets/wildfire.cms_static_cache/", "/*.css");
AutoLoader::register_assets("javascripts/wildfire.cms_static_cache",__DIR__."/resources/public/javascripts/wildfire.cms_static_cache/", "/*.js");
AutoLoader::register_assets("images/wildfire.cms_static_cache",__DIR__."/resources/public/images/wildfire.cms_static_cache/");

AutoLoader::add_plugin_setup_script(__DIR__."/setup.php");
?>