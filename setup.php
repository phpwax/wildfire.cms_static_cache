<?
CMSApplication::register_module("statics", array("display_name"=>"Cache", "link"=>"/admin/statics/"));
AutoLoader::register_view_path("plugin", __DIR__."/view/");
AutoLoader::register_controller_path("plugin", __DIR__."/lib/controller/");
AutoLoader::register_controller_path("plugin", __DIR__."/resources/app/controller/");

CMSApplication::register_asset("wildfire", "js", "wildfire.cms_static_cache");
CMSApplication::register_asset("wildfire", "css", "wildfire.cms_static_cache");
AutoLoader::$plugin_array[] = array("name"=>"wildfire.cms_static_cache","dir"=>__DIR__);


//if this isnt defined, check for it
if(!defined("URL_MAP_MODEL")){
  $app = new ApplicationController(false, false);
  define("URL_MAP_MODEL", $app->cms_mapping_class);
}
//add in extra cols to the url map
if(defined("URL_MAP_MODEL")){

  WaxEvent::add(URL_MAP_MODEL.".setup", function(){

    $model = WaxEvent::data();
    $model->define("static_cache_file", "CharField");
    $model->define("date_cached", "DateTimeField", array('default'=>date("Y-m-d H:i:s"), 'output_format'=>"j F Y H:i", 'input_format'=> 'Y-m-d H:i:s'));
  });
}

if(defined("CONTENT_MODEL")){

  WaxEvent::add("cms.save.success.finished", function(){
    $controller = WaxEvent::data();
    $obj = $controller->model;
    $class = get_class($obj);

    //if the class matches & readonly is enabled, then make the pages
    if($class == CONTENT_MODEL && StaticCache::cacheable($obj)){
      $source_url = "http://".$_SERVER['HTTP_HOST'].$obj->permalink."?readonly=1";
      $content = file_get_contents($source_url);
      $map_class = URL_MAP_MODEL;
      $map_model = new $map_class;
      foreach($map_model->filter("destination_id", $obj->primval)->filter("destination_model", $class)->all() as $url) StaticCache::write($url, $content);
    }
  });
}


?>