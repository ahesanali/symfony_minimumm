<?php

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Psr\Log\LoggerInterface;

/**
 * Render flash messages from Symfony session.
 *
 * @param Session $session
 */
function renderFlashMessages(Session $session): void
{
    $flashes = $session->getFlashBag()->all();

    foreach ($flashes as $type => $messages) {
        foreach ($messages as $message) {
            echo "<div class='alert alert-{$type}'>" . htmlspecialchars($message) . "</div>";
        }
    }
}
// This function will be used to get css,js and image paths in the view files.
function assetPath(string $path): string
{
    global $container;

    $basePath = $_SERVER['SCRIPT_NAME']; // e.g., /symfony_minimumm/index.php
    $basePath = str_replace('/index.php', '', $basePath); // remove script

    $assetMapper = $container->get(AssetMapperInterface::class);
    return $basePath.$assetMapper->getPublicPath($path);
}

// This function will be used to include layout files path in the view files.
function templatePath(): string
{
    global $container;

    $templatesDir = $container->getParameter('app.templates_dir');

    return $templatesDir;
}

// This function will be used to upload a file and get the destination folder path
function appPath(): string
{
    global $container;

    $templatesDir = $container->getParameter('kernel.project_dir');

    return $templatesDir;
}

// This function will be used to generate a URL link for a named route in the view files or in the controllers as well
function route(string $name, array $params = []): string {
    global $router;
    return $router->generate($name, $params);
}

function get_logger()
{
    global $container;
    
    $logger = $container->get(LoggerInterface::class);
    return $logger;
}
/**
     * Convert date format
     *
     * if date is 0000-00-00 00:00:00 than return same date
     * @param string $date_format
     * @param string $date_string
     * @return string
     */

	function format_date($date_format, $date_string)
	{
		$formated_date = "";
		if($date_string != "0000-00-00 00:00:00"){
			$formated_date = date($date_format, strtotime($date_string));
		}else{
			$formated_date = $date_string;
		}

		if(is_null($date_string))
			return '';
		return $formated_date;
	}

	/**
	 * Limit Text to print and append with ...
	 * @param string $text
	 * @param string $limit
	 * @return string
	 */
	function limit_text($text, $limit) {
		if (strlen ($text) > $limit) {
			$text = substr($text, 0, $limit) . '...';
		}
		return $text;
	}

	/**
	 * File Upload utility function
	 * @param string $uploaded_file_name
	 * @param string $upload_path
	 */
	function upload_image($uploaded_file_name,$upload_path)
	{
		$is_file_uploaded = false;

		if (move_uploaded_file($_FILES[$uploaded_file_name]['tmp_name'],$upload_path ))
		{
			$is_file_uploaded = true;
		}

		return $is_file_uploaded;
	}


    /**
 * @create a dropdown select
 *
 * @param string $name
 * @param array  $options
 * @param array  $attributes
 * @param string $selected   (optional)
 *
 * @return string
 */
function dropdown($name, array $options, $selected = null,  ?array $attributes = null)
{
    /*** begin the select ***/
    $dropdown = '<select name="'.$name.'"';
    /*** loop over the attributes ***/
    if(!is_null($attributes)){
        foreach ($attributes as $key => $attribute) {
            $dropdown .=  $key.'="'.$attribute.'" ';
        }
    }
    $dropdown .= ' >\n';

    $selected = $selected;
    /*** loop over the options ***/
    foreach ($options as $key => $option) {
        /*** assign a selected value ***/
        if(is_array($selected))
            $select =  in_array($key, $selected) ? ' selected' : null;
        else
            $select = $selected == $key ? ' selected' : null;

        /*** add each option to the dropdown ***/
        $dropdown .= '<option value="'.$key.'"'.$select.'>'.$option.'</option>'."\n";
    }

    /*** close the select ***/
    $dropdown .= '</select>'."\n";

    /*** and return the completed dropdown ***/
    return $dropdown;
}