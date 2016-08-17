<?php

/**
 * Gallery administration system within the CMS
 *
 * @package courses
 * @subpackage admins
 */
class GalleryAdmin extends ModelAdmin
{

    private static $url_segment = 'gallerymanager';
    private static $menu_icon = 'mysite/images/gallery.png';
    private static $menu_title = 'Foto-Alben';
    private static $managed_models = array(
        'Gallery' => array('title' => 'Foto-Alben'),
        'GalleryTag' => array('title' => 'Foto-Alben-Tags')
    );
    public $showImportForm = false;
    /**
     * @config
     */
    private static $items_per_page = '20';

    /**
     *  Prepare search
     */
    public function getSearchContext()
    {
        $context = parent::getSearchContext();
        return $context;
    }

    /**
     * Get a result list
     * The results list are retrieved from SearchContext::getResults(), based on the parameters passed through the search
     * form. If no search parameters are given, the results will show every record. Results are a DataList instance, so can
     * be customized by additional SQL filters, joins.
     */
    public function getList()
    {
        // Get all including inactive
        $list = parent::getList();
        return $list;
    }

    /**
     * Alter look & feel for EditForm
     * To alter how the results are displayed (via GridField),
     * you can also overload the getEditForm() method.
     * For example, to add or remove a new component.
     */
    public function getEditForm($id = null, $fields = null)
    {

        $form = parent::getEditForm($id, $fields);
        // Hide Export and Print Button
        foreach ($form->Fields() as $field) {
            if ($field->is_a('GridField')) {
                $field->getConfig()->removeComponentsByType('GridFieldExportButton');
                $field->getConfig()->removeComponentsByType('GridFieldPrintButton');
            }
        };

        // $gridFieldName is generated from the ModelClass, eg if the Class 'ClubMember'
        // is managed by this ModelAdmin, the GridField for it will also be named 'ClubMember'
        $gridFieldName = $this->sanitiseClassName($this->modelClass);
        $gridField = $form->Fields()->fieldByName($gridFieldName);

        // Get gridfield config
        $config = $gridField->getConfig();
        /*if ($gridFieldName == 'Course') {
            $config->addComponents(new GridFieldSortableRows('SortOrder'));
        }*/
        return $form;
    }

    public function init()
    {
        parent::init();
    }

}
