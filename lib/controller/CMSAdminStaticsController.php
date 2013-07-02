<?php
class CMSAdminStaticsController extends AdminContentController {

  public $module_name = "statics";
  public $model_class = 'WildfireUrlMap';
  public $display_name = "Cache";
  public $dashboard = false;
  public $singular = "Cache";
  public $tree_layout = false;
  public $per_page = 20;
  public $filter_fields=array(
                          'text' => array('columns'=>array('origin_url', 'title'), 'partial'=>'_filters_text', 'fuzzy'=>true)
                        );
  public $operation_actions = array(
                                                  'regen'=>array('action'=>'regen', 'name'=>'Regenerate'),
                                                  'remove'=>array('action'=>'remove', 'name'=>'Remove')
                                                );


  protected function events(){
    parent::events();
    WaxEvent::clear("cms.layout.sublinks");
    WaxEvent::add("cms.layout.sublinks", function(){
      $obj = WaxEvent::data();
      $obj->quick_links = array(
          "Cache Page" => '/admin/'.$obj->module_name."/selection/"
          );
    });
    WaxEvent::clear("cms.model.columns");
    WaxEvent::add("cms.model.columns", function(){
      $obj = WaxEvent::data();
      $obj->scaffold_columns = array('title'=>true, 'origin_url'=>true, 'date_cached'=>true);
    });
    WaxEvent::clear("cms.model.init");
    WaxEvent::add("cms.model.init", function(){
      $obj = WaxEvent::data();
      $obj->model = new $obj->model_class($obj->model_scope);
      //add filter for active cache only
      $obj->model->filter("status",1)->filter("LENGTH(static_cache_file) > 0");
    });
  }



  public function _parent(){
    $this->model_class = CONTENT_MODEL;
    parent::_parent();
  }


  public function selection(){
    $class = $this->model_class = CONTENT_MODEL;
    WaxEvent::run("cms.form.setup", $this);

    $posted = Request::param($this->model->table);
    $page_id = $posted[$this->model->parent_join_field];
    if($posted && $page_id && ($obj = new $class($page_id)) && StaticCache::cacheable($obj) ){
      $source_url = "http://".$_SERVER['HTTP_HOST'].$obj->permalink."?readonly=1";
      $content = file_get_contents($source_url);
      $map_class = URL_MAP_MODEL;
      $map_model = new $map_class;
      foreach($map_model->filter("destination_id", $obj->primval)->filter("destination_model", $class)->all() as $url) StaticCache::write($url, $content);
      $this->session->add_message("<a href='$source_url' target='_blank'>Cache for $obj->title</a> has been created.");
    }elseif($posted && $page_id) $this->session->add_error("This page cannot be cached.");
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