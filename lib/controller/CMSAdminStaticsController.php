<?php
class CMSAdminStaticsController extends AdminComponent {

  public $module_name = "statics";
  public $model_class = 'WildfireUrlMap';
  public $display_name = "Cache";
  public $dashboard = false;
  public $singular = "Cache";
  public $per_page = 20;
  public $tree_layout = false;
  public $filter_fields=array(
                          'text' => array('columns'=>array('origin_url', 'title'), 'partial'=>'_filters_text', 'fuzzy'=>true)
                        );
  public $operation_actions = array(
                                                  'regen'=>array('action'=>'regen', 'name'=>'Regenerate'),
                                                  'remove'=>array('action'=>'remove', 'name'=>'Remove')
                                                );


  protected function events(){
    parent::events();
    WaxEvent::add("cms.model.columns", function(){
      $obj = WaxEvent::data();
      $obj->scaffold_columns = array('title'=>true, 'origin_url'=>true, 'date_cached'=>true);
    });
    WaxEvent::add("cms.model.init", function(){
      $obj = WaxEvent::data();
      $obj->model = new $obj->model_class($obj->model_scope);
      //add filter for active cache only
      $obj->model->filter("status",1)->filter("LENGTH(static_cache_file) > 0");

    });
  }


  public function regen(){
    $class = $this->model_class;
    $url = new $class(Request::param("id"));
    if($url){
      $source_url = "http://".$_SERVER['HTTP_HOST'].$url->origin_url."?readonly=1";
      $content = file_get_contents($source_url);
      StaticCache::write($url, $content);
      $this->session->add_message("<a href='$source_url' target='_blank'>Cache for $url->origin_url</a> has been regenerated.");
    }else $this->session->add_error("Cannot find entry for that page.");
    $this->redirect_to("/".trim($this->controller,"/")."/");
  }

  public function remove(){
    $class = $this->model_class;
    $url = new $class(Request::param("id"));
    if($url){
      StaticCache::remove($url);
      $this->session->add_message("Cache for $url->origin_url has been removed.");
    }else $this->session->add_error("Cannot find entry for that page.");
    $this->redirect_to("/".trim($this->controller,"/")."/");
  }

}

?>