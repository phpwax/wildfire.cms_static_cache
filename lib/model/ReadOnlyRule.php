<?
class ReadOnlyRule extends WaxModel{

  public function setup(){
    $this->define("regex", "CharField");
    $this->define("url", "CharField");
    $this->define("status", "BooleanField");
  }

  public static function lookup_by_url_map($model){
    $urls = array();
    $map_model = new WildfireUrlMap;
    $class = get_class($model);
    $rule = new ReadOnlyRule;

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

}
?>