<?php
class CMSAdminStaticsController extends AdminComponent {

  public $module_name = "statics";
  public $model_class = 'WildfireUrlMap';
  public $display_name = "Cache";
  public $dashboard = false;
  public $singular = "Cache";
  public $per_page = 20;
  public $tree_layout = false;


  protected function events(){
    parent::events();

    WaxEvent::add("cms.model.columns", function(){
      $obj = WaxEvent::data();
      $obj->scaffold_columns = array('title'=>true, 'origin_url'=>true);
    });

    WaxEvent::add("cms.model.init", function(){
      $obj = WaxEvent::data();
      $obj->model = new $obj->model_class($obj->model_scope);
      //add filter for active cache only
      $obj->model->filter("status",1)->filter("LENGTH(static_cache_file) > 0");

    });



  }

}

?>