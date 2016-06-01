<?php
class WorkshopPage extends Page {

    private static $singular_name = 'Workshop Page';
    private static $description = 'Seite für Workshops und Kurse';
    private static $icon = 'mysite/images/workshop.png';

    private static $db = array();

    private static $has_one = array();

}
class WorkshopPage_Controller extends Page_Controller {

}
