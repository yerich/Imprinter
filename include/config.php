<?php
/**
 * Configuration File for the Imprinter
 * 
 * This file contains the basics needed to acess the mySQL database.
 * 
 * @author rye
 * @package imprinter
 */

//Database connection settings
define("DB_HOST", $_ENV{DATABASE_SERVER});
define("DB_NAME", "imprint");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "password");

//Database Table Values
define("TBL_CONFIG", "config");
define("TBL_PAGES", "pages");
define("TBL_USERS", "users");
define("TBL_TAGS", "tags");
define("TBL_MEDIA", "media");
