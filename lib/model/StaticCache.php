<?
class StaticCache extends WaxModel{

  public static $formats = array("html");
  public function setup(){

    $this->define("title", 'CharField', array('default'=>'Enter title', 'scaffold'=>true, 'required'=>true, 'primary_group'=>1, 'group'=>'content'));
    $this->define("regex", "CharField", array('primary_group'=>1, 'group'=>'content'));
    $this->define("url", "CharField", array('primary_group'=>1, 'group'=>'content'));
    $this->define("status", "BooleanField", array('scaffold'=>true, 'primary_group'=>1, 'group'=>'content', 'label'=>'Active'));
    parent::setup();
  }

  public function rule(){
    if($this->regex && $this->url) return "Blocks '$this->url' exactly and any matching '$this->regex'";
    else if($this->regex) return "Block any url matching '$this->regex'";
    else if($this->url) return "Blocks '$this->url' exactly.";
    return "";
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

  public static function write($url_model, $content, $format="html"){
    $file_paths = self::file_paths($url_model, $format);
    $save = $file_paths[0];
    $pos = strrpos($save, ".")+1;
    $url_model->static_cache_file = str_replace(CACHE_DIR."statics", "", substr($save, 0, $pos));
    $url_model->update_attributes(array('date_cached'=>date("Y-m-d H:i:s") ) );
    foreach($file_paths as $file_path){
      $pos = strrpos($file_path, ".");
      $dir = substr($file_path, 0, $pos);
      if(!is_dir($dir)) mkdir($dir, 0777, true);
      file_put_contents($file_path.$format, $content);
    }
  }

  public function remove($model, $format="html"){
    foreach(self::file_paths($model, $format) as $fp) if(is_readable($fp)) unlink($fp);
    $model->update_attributes(array('date_cached'=>'', 'static_cache_file'=>''));
  }

  public static function file_paths($url_model, $format="html"){
    if(constant("WILDFIRE_MULTIDOMAIN")){
      $m = new Domain;
      if($cm = $url_model->destination_model) $c = new $cm($url_model->destination_id);
      else return array();
      //fetch top item
      $top = new $cm($c->path_to_root()->rowset[0]);
      $base = str_replace($top->permalink, "/", $url_model->origin_url);
      foreach($top->domains as $row){
        $dir_path = CACHE_DIR ."statics/".ltrim($row->webaddress.$base, "/");
        if(!is_dir($dir_path)) mkdir($dir_path, 0777, true);
        $file_paths[] = rtrim($dir_path, "/") ."/.$format";
      }
    }else if($url_model->static_cache_file){
      $file_paths[] = CACHE_DIR. "statics".$url_model->static_cache_file;
    }else{
      $dir_path = CACHE_DIR ."statics".$url_model->origin_url;
      $file_paths[] = rtrim($dir_path,"/") ."/.$format";
    }
    return $file_paths;
  }

}
?>