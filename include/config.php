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
define("DB_NAME", "db107536_theimprint_ca");
define("DB_USERNAME", "1clk_wp_XgFwuKz");
define("DB_PASSWORD", "YzY6RL6Z");

//Database Table Values
define("TBL_CONFIG", "config");
define("TBL_PAGES", "pages");
define("TBL_USERS", "users");
define("TBL_TAGS", "tags");
define("TBL_MEDIA", "media");
