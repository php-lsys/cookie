<?php
use \LSYS\Cookie;
include __DIR__."/Bootstarp.php";

Cookie::set("tt","bbcccc");


echo Cookie::get("tt");