<?php
class NewsPage extends Page
{
    private static $singular_name = 'News';
    private static $description = 'Seite News';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = true;
    private static $allowed_children = 'none';

    private static $db = array();
    private static $has_many = array();

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        //$labels['Linkset'] = 'Sammlung';
        return $labels;
    }

    function getCMSFields(){
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');
        return $fields;
    }

}

class NewsPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init() {
        parent::init();
    }//init()

    /**
     * Create a news items list
     * @return PaginatedList list containing news items
     */
    public function PaginatedLatestNews($num = 5) {
        $datetime = SS_DateTime::now(); // date doday
        $year = isset($_GET['year']) ? (int) $_GET['year'] : $datetime->Year();
        SS_Log::log('year='.$year,SS_Log::WARN);

        $start = isset($_GET['start']) ? (int) $_GET['start'] : 0;
        /*
        $itemsToSkip = 0;
        $itemsToReturn = 5;
        return News::Entries($itemsToSkip, $itemsToReturn);*/
        //$item->ClassName == 'Course' && $item->HomepageSectionID == 0
        $list = News::get()
        ->filterAny(array(
            'ClassName' => 'News',
            'HomepageSectionID:GreaterThan' => '0'
        ));
        //->sort('NewsDate','DESC');
        //SS_Log::log('count='.$list->count(),SS_Log::WARN);
        if ($list)
        {
            $news = PaginatedList::create($list,$this->getRequest())->setPageLength($num);
        }

        return $news;
    }
}
