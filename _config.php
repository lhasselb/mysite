<?php

global $project;
$project = 'mysite';

global $databaseConfig;
$databaseConfig = array(
	'type' => 'MySQLPDODatabase',
	'server' => 'localhost',
	'username' => 'root',
	'password' => 'root',
	'database' => 'jimev',
	'path' => ''
);

// Set the site locale
i18n::set_locale('de_DE');
i18n::set_date_format('dd.MM.YYYY');
i18n::set_time_format('HH:mm');

Director::set_environment_type('dev');//dev live

//head.js
Requirements::set_backend(Injector::inst()->get('HeadJsBackend'));

// Enable logging: log errors and warnings
if(getenv('OS') == "Windows_NT") {
    SS_Log::add_writer(new SS_LogFileWriter("c:\\xampp\\apache\\logs\\JimevLog.log"), SS_Log::WARN, '<=');
    ini_set("error_log", "c:\\xampp\\apache\\logs\\JimevLog.log");
} else {
    SS_Log::add_writer(new SS_LogFileWriter("/Applications/MAMP/logs/JimevLog.log"), SS_Log::WARN, '<=');
    ini_set("error_log", "/Applications/MAMP/logs/JimevLog.log");
}

// PHP related error settings
ini_set("log_errors", "On");
ini_set('display_errors', 1);
error_reporting(E_ALL);


// Add template to tinyMCE
HtmlEditorConfig::get('cms')->enablePlugins('template');
HtmlEditorConfig::get('cms')->insertButtonsAfter('tablecontrols', 'template');
HtmlEditorConfig::get('cms')->setOptions(array('template_templates' => array(
    array('title' => '10 Bereiche Accordion', 'src' => SSViewer::get_theme_folder().'/templates/helper/accordian.html', 'description' => 'Füge Beispielinhalt ein')
)));

// Add a Google Maps shortcode
ShortcodeParser::get('default')->register('googlemap', function($arguments, $address, $parser, $shortcode) {
    $iframeUrl = sprintf(
        "https://mapsengine.google.com/map/embed?mid=%s",
        urlencode($address)
    );

    $width = (isset($arguments['width']) && $arguments['width']) ? $arguments['width'] : "100%";
    $height = (isset($arguments['height']) && $arguments['height']) ? $arguments['height'] : "100%";

    return sprintf(
        '<iframe class="embedded-maps" width="%s" height="%s" src="%s" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>',
        $width,
        $height,
        $iframeUrl
    );
});
