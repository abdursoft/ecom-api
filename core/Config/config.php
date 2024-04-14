<?php 
/**
 * ABS MVC Framework
 *
 * @created      2023
 * @version      1.0.1
 * @author       abdursoft <support@abdursoft.com>
 * @copyright    2024 abdursoft
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
*/

//  localization 
define("LANGUAGE",'bn');

// application mood 
define('MOOD','api'); // web or api

//Set base url
define("BASE_URL",'http://localhost/medicine/'); //set root directory/domain
define("SITE_TITLE",'ABS MVC FRAMEWORK'); //site name or title
define("FAV_ICON",BASE_URL."assets/images/premium.png"); //site name or title
define('DEFAULT_KEYWORDS','Online medicine shop for best BD peoples'); //Default keywords

// Server TimeZone 
define("DB_SERVER_TIMEZONE",'Asia/Dhaka'); //set your default timezone

// Database server 
define('DATABASE_SERVER','mysql'); //supported database mysql,pgsql,mongodb,firebase

//MYSQL database credentials
define("DB",'abs_pharmacy'); //Database Name
define("HOST",'localhost'); //Database Host Name
define("USER",'root'); //Database User Name
define("PASSWORD",''); //Database Password

//POSTGRESS SQL database credentials
define("PGDB",'abs_collage'); //Database Name
define("PGHOST",'localhost'); //Database Host Name
define("PGUSER",'root'); //Database User Name
define("PGPASSWORD",''); //Database Password
define("PGPORT",''); //Database Password

// MONGODB database credentials 
define("MONDB",'new_xvoox'); //Database Name
define("MONHOST",'mongodb://localhost:27017'); //Database Host Name
define("MONUSER",'root'); //Database User Name
define("MONPASSWORD",''); //Database Password
define("MONPORT",''); //Database Password
define("MONSSL",''); //Database Password
define("MONREPLICASET",''); //Database Password
define("MONAUTHSOURCE",''); //Database Password

//Authentication 
define("TOKEN_PERIOD",20); //Expired session in minutes

//SMTP mail credentials and information
define( "MAIL_HOST", 'mail.abdursoft.com' ); //Server Or gmail SMTP HOST
define( "MAIL_PORT", 587 ); //Server SMTP Port
define( "MAIL_USERNAME", 'support@abdursoft.com' ); //User Mail Name
define( "MAIL_SUPPORT", 'support@abdursoft.com' ); //Contact Mail Name
define( "MAIL_PASSWORD", 'On!,e_zOC,,g' ); //Mail Password
define("MAIL_WEBSITE",'abdursoft.com'); //Website Name
define("MAIL_TEAM","abdursoft"); //Support Team Name
define("MAIL_CONTACT","+8801892311511"); //Contact Phone Number
define("MAIL_OWNER_ADSRESS","Mithapukur, Rangpur,BANGLADESH"); //Office Address

// payment server 
define("PAYMENT_SERVER","stripe"); //stripe,paypal,sslcommerze,amarpay,razorpay,bkash,nagad

//Bkash credentials
define('BKASH_APP_KEY',''); //Bkash App Key
define("BKASH_APP_SECRET",''); //Bkash App Secret
define("BKASH_PROXY",""); //Bkash Proxy
define("BKASH_USERNAME",''); //Bkash User Name
define("BKASH_PASSWORD",''); //Bkash Password
define("BKASH_SANDBOX",true); //True for Sandbox and false For Production

// Paypal secret and credentials 
define('PAYPAL_API_CLIENT_ID', '');  
define('PAYPAL_API_SECRET', ''); 
define('PAYPAL_SANDBOX', true); //set false for production 

// AWS Bucket Credentials 
define('AWS_KEY', '');
define('AWS_SECRET','');
define('AWS_BUCKET_INPUT','');
define('AWS_BUCKET_OUTPUT','');
define('AWS_REGION','');
define('AWS_HOST','');
define('AWS_PIPELINE','');
define('AWS_PRESET_ID','');

// set default upload server 
define('UPLOAD_SERVER','storage'); // upload servers are storage,aws

// cloudflare credentials 
define('CLOUD_REGION','');
define('CLOUD_BUCKET','');
define('CLOUD_IMAGE_BUCKET','');
define('CLOUD_VIDEO_BUCKET','');
define('CLOUD_TOKEN','');
define('CLOUD_ACCESS_KEY','');
define('CLOUDE_SECRET_KEY','');
define('CLOUD_ENDPOINT','');
define('CLOUD_BUCKET_HOST','');
define('CLOUD_TEMP_URL','');
define('CLOUD_IMAGE_URL','');
define('CLOUD_VIDEO_URL','');

// coconut api
define('COCONUT_API_KEY','');

// ffmpeg path 
define('FFMPEG','c:\ffmpeg\bin\ffmpeg.exe');
define('FFMPEGPROBE','c:\ffmpeg\bin\ffprobe.exe');

// jWT configuration 
define('JWT_ALG','HS256');
define('JWT_SECRET','ronyMe_2k23');
define('JWT_INTERVAL',10);
define('JWT_EXPAIR',500);