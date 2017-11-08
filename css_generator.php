<?php

include("sprites.php");

$shortopt = "r::i::ri::s::p:o:c:h";
$longopt = array("recursive", "help", "output-image::", "output-style::", "padding:", "override-size:", "columns-numbers:");
$option = getopt($shortopt, $longopt);
$folder = array_pop($argv);
$option["folder"] = $folder;

function term($option)
{
	if(isset($option["h"]) || isset($option["help"]))
	{
		CSS_GENERATOR::help();
	}
	else
	{
		CSS_GENERATOR::option($option);
	}
}	

term($option);