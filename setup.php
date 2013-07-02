<?
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
  });
}

if(defined("CONTENT_MODEL")){

  WaxEvent::add("cms.save.success.finished", function(){
    $controller = WaxEvent::data();
    $obj = $controller->model;
    $class = get_class($obj);

    //if the class matches & readonly is enabled, then make the pages
    if($class == CONTENT_MODEL && StaticCache::is_enabled($obj)){
      $source_url = "http://".$_SERVER['HTTP_HOST'].$obj->permalink."?readonly=1";
      $content = file_get_contents($source_url);
      $map_model = new WildfireUrlMap;
      foreach($map_model->filter("destination_id", $obj->primval)->filter("destination_model", $class)->all() as $url) StaticCache::create($url->origin_url, $content);
    }
  });
}


?>