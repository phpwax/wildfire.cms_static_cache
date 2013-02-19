<?
class ReadOnly extends WaxModel{

  public function setup(){
    $this->define("enabled", "BooleanField");
  }


  public static function is_enabled(){
    $model = new ReadOnly;
    return $model->filter("enabled",1)->first();
  }

  public static function create_cache($target_url, $content){
    $dir_path = CACHE_DIR ."statics".$target_url;
    $file_path = $dir_path ."index.html";
    //if the path doesnt exist, make the folder
    if(!is_dir($dir_path)) mkdir($dir_path, 0777, true);
    //write the file
    file_put_contents($file_path, $content);
  }

}
?>