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

/**
 * Add TinyMCE configuration
 * List of TinyMCE options available on
 * http://archive.tinymce.com/wiki.php/TinyMCE3x:Buttons/controls
 * See default configuration within file
 * framework/admin/_config.php
 * framework/forms/HtmlEditorConfig.php
 * plugins: contextmenu,table,emotions,paste
 */

#BASIC
HtmlEditorConfig::get("basic")->setOptions(array(
    "language" => i18n::get_tinymce_lang(),
    "body_class" => "typography",
    "friendly_name" => "basic editor",
    "priority" => 0,
    "mode" => "none",
    "editor_selector" => "htmleditor",
    "auto_resize" => true,
    "theme" => "advanced",
    "skin" => "default",
    // Remove the bottom status bar
    "theme_advanced_statusbar_location" => "none"
))->disablePlugins('contextmenu');
// Clear the default buttons
HtmlEditorConfig::get("basic")->setButtonsForLine(1);
HtmlEditorConfig::get("basic")->setButtonsForLine(2);
//HtmlEditorConfig::get("basic")->setButtonsForLine(3);
HtmlEditorConfig::get("basic")->setButtonsForLine(1,"sslink","unlink","code","pastetext");
HtmlEditorConfig::get("basic")->setOption("content_css","/mysite/css/editor.css");
HtmlEditorConfig::get('basic')->enablePlugins(array(
    'ssbuttons' => sprintf('../../../%s/tinymce_ssbuttons/editor_plugin_src.js', THIRDPARTY_DIR)
));

#LOCATION
HtmlEditorConfig::get("location")->setOptions(array(
    "language" => i18n::get_tinymce_lang(),
    "body_class" => "typography",
    "friendly_name" => "basic editor",
    "priority" => 0,
    "mode" => "none",
    "editor_selector" => "htmleditor",
    "auto_resize" => true,
    "theme" => "advanced",
    "skin" => "default",
    // Remove the bottom status bar
    "theme_advanced_statusbar_location" => "none"
))->disablePlugins('contextmenu');
// Clear the default buttons
HtmlEditorConfig::get("location")->setButtonsForLine(1);
HtmlEditorConfig::get("location")->setButtonsForLine(2);
//HtmlEditorConfig::get("basic")->setButtonsForLine(3);
HtmlEditorConfig::get("location")->setButtonsForLine(1,"sslink","unlink","code","pastetext","styleselect");
HtmlEditorConfig::get("location")->setOption("content_css","/mysite/css/location.css");
HtmlEditorConfig::get('location')->enablePlugins(array(
    'ssbuttons' => sprintf('../../../%s/tinymce_ssbuttons/editor_plugin_src.js', THIRDPARTY_DIR)
));

#CALENDAR
HtmlEditorConfig::get("calendar")->setOptions(array(
    "language" => i18n::get_tinymce_lang(),
    "body_class" => "typography",
    "friendly_name" => "basic editor",
    "priority" => 0,
    "mode" => "none",
    "editor_selector" => "htmleditor",
    "auto_resize" => true,
    "theme" => "advanced",
    "skin" => "default",
    // Remove the bottom status bar
    "theme_advanced_statusbar_location" => "none"
))->disablePlugins('contextmenu');
// Clear the default buttons
HtmlEditorConfig::get("calendar")->setButtonsForLine(1);
HtmlEditorConfig::get("calendar")->setButtonsForLine(2);
//HtmlEditorConfig::get("basic")->setButtonsForLine(3);
HtmlEditorConfig::get("calendar")->setButtonsForLine(1,"sslink","unlink","code","pastetext","styleselect");
HtmlEditorConfig::get("calendar")->setOption("content_css","/mysite/css/calendar.css");
HtmlEditorConfig::get('calendar')->enablePlugins(array(
    'ssbuttons' => sprintf('../../../%s/tinymce_ssbuttons/editor_plugin_src.js', THIRDPARTY_DIR)
));

#DEFAULT
HtmlEditorConfig::get('cms')->setOption(
        'extended_valid_elements','span'
        //'div[itemprop|itemscope|itemtype],' . 'span[itemprop]' . 'meta[itemprop|content]'
    );
HtmlEditorConfig::get("cms")->setOption("content_css","/mysite/css/editor.css");
HtmlEditorConfig::get("cms")->enablePlugins('template');
HtmlEditorConfig::get("cms")->insertButtonsAfter('tablecontrols', 'template');
//SS_Log::log(SSViewer::get_theme_folder().'/templates/helper/pdfIcon.html',SS_Log::WARN);
HtmlEditorConfig::get("cms")->setOptions(array('template_templates' => array(
    array('title' => 'PDF Icon', 'src' => SSViewer::get_theme_folder().'/templates/helper/pdfIcon.html', 'description' => 'Füge PDF Icon ein')
)));
// Add template to default tinyMCE "cms"
//HtmlEditorConfig::get('cms')->enablePlugins('template');
//HtmlEditorConfig::get('cms')->insertButtonsAfter('tablecontrols', 'template');
/*HtmlEditorConfig::get('cms')->setOptions(array('template_templates' => array(
    array('title' => 'Ein DIV', 'src' => SSViewer::get_theme_folder().'/templates/helper/test.html', 'description' => 'Füge Beispielinhalt ein')
)));*/


// Add a Google Maps shortcode
//ShortcodeParser::get('default')->register('directionsgooglemap', array('LocationPage', 'DirectionsGoogleMap'));
//ShortcodeParser::get('default')->register('existinggooglemap', array('LocationPage', 'ExistingGoogleMap'));

ShortcodeParser::get('default')->register('gallery_link', array('Gallery', 'link_shortcode_handler'));
