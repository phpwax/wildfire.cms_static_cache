<?
class StaticCache extends WaxModel{

  public function setup(){
    $this->define("regex", "CharField");
    $this->define("url", "CharField");
    $this->define("status", "BooleanField", array('scaffold'=>true));
  }

  public static function lookup_by_url_map($model){
    $urls = array();
    $map_model = new WildfireUrlMap;
    $class = get_class($model);
    $rule = new StaticCache;

    foreach($map_model->filter("destination_id", $model->primval)->filter("destination_model", $class)->all() as $url) $urls[] = $url->origin_url;
    //if there are no urls to check, then return false
    if(!count($urls)) return false;

    $url_string = "'".implode("','", $urls) ."'";
    $rule->sql ="
    SELECT
      count(*) as 'found'
    FROM $rule->table
    WHERE
      $rule->table.status = 1 AND
      (
        $rule->table.url IN($url_string) OR (
    ";
    foreach($urls as $url) $rule->sql .= "'$url' REGEXP `regex` OR";
    $rule->sql = trim($rule->sql, " OR").") )";

    return $rule->first()->found;

  }


  public static function cacheable($model){
    //if there is no matching rule for this item then its turned on
    return !(self::lookup_by_url_map($model));
  }

  public static function write($url_model, $content){
    if($url_model->static_cache_file) $file_path = CACHE_DIR. "statics".$url_model->static_cache_file;
    else{
      $dir_path = CACHE_DIR ."statics".$url_model->origin_url;
      //if the path doesnt exist, make the folder
      if(!is_dir($dir_path)) mkdir($dir_path, 0777, true);
      $file_path = $dir_path ."index.html";
      //save the file path
      $url_model->update_attributes(array('static_cache_file'=>str_replace(CACHE_DIR."statics", "", $file_path) ) );
    }
    //write the file
    file_put_contents($file_path, $content);
  }

}
?>