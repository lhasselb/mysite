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
i18n::set_locale('en_US');
//i18n::set_locale('de_DE');
//i18n::set_date_format('dd.MM.YYYY');
//i18n::set_time_format('HH:mm');

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
