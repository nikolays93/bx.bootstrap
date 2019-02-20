<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @global $arCurrentValues
 */

$arComponentParameters = array(
	"GROUPS" => array(
        // BASE
        // DATA_SOURCE
        // VISUAL
        // USER_CONSENT
        // URL_TEMPLATES
        // SEF_MODE
        // AJAX_SETTINGS
        // CACHE_SETTINGS
        // ADDITIONAL_SETTINGS
	),
	"PARAMETERS" => array(
        // 'CACHE_TIME' => array('DEFAULT' => 120),
        "BLOCKS" => array(
            "PARENT" => "BASE",
            "NAME" => "Блоки",
            "TYPE" => "STRING",
            "MULTIPLE" => "Y",
        ),
        "SHOW" => array(
            "PARENT" => "BASE",
            "NAME" => "Показывать блок",
            "TYPE" => "STRING",
            "DEFAULT" => "0",
        ),
	),
);
