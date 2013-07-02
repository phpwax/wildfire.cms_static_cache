<?php
class CMSAdminStaticrulesController extends AdminComponent {

  public $module_name = "staticrules";
  public $model_class = 'StaticCache';
  public $display_name = "Static Rules";
  public $dashboard = false;
  public $singular = "Static Rule";
  public $tree_layout = false;
  public $per_page = 20;
  public $filter_fields=array(
                          'text' => array('columns'=>array('origin_url', 'title'), 'partial'=>'_filters_text', 'fuzzy'=>true)
                        );
  public $operation_actions = array(
                                                  'edit'=>array('action'=>'edit', 'name'=>'Edit')
                                                );




}

?>