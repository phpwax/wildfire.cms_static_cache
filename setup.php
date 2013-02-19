<?

if(defined("CONTENT_MODEL")){
  WaxEvent::add("cms.save.success.finished", function(){
    $obj = WaxEvent::data();
    $class = get_class($obj);
    //if the class matches & readonly is enabled, then make the pages
    if($class == CONTENT_MODEL && ReadOnly::is_enabled()){
      $source_url = "http://".$_SERVER['HTTP_HOST'].$obj->permalink."?readonly=1";
      $content = file_get_contents($source_url);
      $map_model = new WildfireUrlMap;
      foreach($map_model->filter("destination_id", $obj->primval)->filter("destination_model", $class)->all() as $url) ReadOnly::create_cache($url->origin_url, $content);
    }
  });
}


?>