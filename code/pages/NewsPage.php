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
        if($newsList) {
            foreach($newsList as $news) {
                $year = $news->getYear();
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
    protected $selectedYear;

    public function init() {
        parent::init();
        $this->newsList = News::get()->filterAny(array(
            'ClassName' => 'News',
            'HomepageSectionID:GreaterThan' => '0'
        ))->sort('NewsDate DESC');
    }//init()

    public function SelectedYear() {
        return $this->selectedYear;
    }

    public function date(SS_HTTPRequest $r) {
        $year = $r->param('ID');
        $this->selectedYear = $year;
        if(!$year) return $this->httpError(404);
        if ($year = 'all') {
            $this->newsList = News::get()->filterAny(array(
                'ClassName' => 'News',
                'HomepageSectionID:GreaterThan' => '0'
            ))->sort('NewsDate DESC');
        } else $this->newsList = $this->newsList->filterAny('NewsDate:PartialMatch', $year);
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
