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

    public function ArchiveDates() {
        $list = ArrayList::create();
        $newsList = News::get();
        SS_Log::log('result '.$newsList->count(),SS_Log::WARN);
        if($newsList) {
            foreach($newsList as $news) {
                $year = $news->getYear();
                SS_Log::log('loop year='.$year,SS_Log::WARN);
                if(!$list->find('Year',$year)) {
                    $list->push(ArrayData::create(array(
                        'Year' => $year,
                        'Link' => $this->Link('date/'.$year),
                        'NewsCount' => News::get()->filterAny('NewsDate:PartialMatch', $year)->count()
                    )));
                }

            }
        }
        return $list;
    }

}

class NewsPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ('date');

    protected $newsList;

    public function init() {
        parent::init();
        $this->newsList = News::get()->filterAny(array(
            'ClassName' => 'News',
            'HomepageSectionID:GreaterThan' => '0'
        ))->sort('NewsDate DESC');
    }//init()

    public function date(SS_HTTPRequest $r) {
        $year = $r->param('ID');
        SS_Log::log('date called year ='.$year,SS_Log::WARN);
        if(!$year) return $this->httpError(404);
        $this->newsList = $this->newsList->filterAny('NewsDate:PartialMatch', $year);
        return array();
    }

    /**
     * Create a news items list
     * @return PaginatedList list containing news items
     */
    public function PaginatedLatestNews($num = 5) {
        return PaginatedList::create($this->newsList,$this->getRequest())->setPageLength($num);
    }
}
