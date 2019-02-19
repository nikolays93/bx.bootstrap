<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\LoaderException;
use \Bitrix\Main;

class customEmptyComponent extends CBitrixComponent
{
    /** @var array */
    private $errors = array();

    /** @var array Field for ajax request data */
    private $arResponse = array(
        'errors' => array(),
        'html' => ''
    );

    function __construct($component = null)
    {
        parent::__construct($component);

        // try
        // {
        //     if(Loader::includeModule('iblock')) {
        //         throw new LoaderException("Not exists IBlock module");
        //     }
        // }
        // catch (LoaderException $exception)
        // {
        //     $this->errors[] = '<p style="color: #f00">' . $exception->getMessage() . '</p>';
        // }
    }

    function onPrepareComponentParams($arParams)
    {
        if( !isset($arParams['BLOCKS']) || !is_array($arParams['BLOCKS']) ) $arParams['BLOCKS'] = array();

        return $arParams;
    }

    public static function esc_string($s)
    {
        $s = strip_tags( (string) $s);
        $s = str_replace(array("\n", "\r"), " ", $s);
        $s = preg_replace("/\s+/", ' ', $s);
        $s = trim($s);
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
        $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s);
        $s = str_replace(" ", "-", $s);

        return $s;
    }

    private function getFile( $id )
    {
        global $APPLICATION;

        $sRealFilePath = $_SERVER["REAL_FILE_PATH"];

        // if page in SEF mode check real path
        if (strlen($sRealFilePath) > 0)
        {
            $slash_pos = strrpos($sRealFilePath, "/");
            $sFilePath = substr($sRealFilePath, 0, $slash_pos+1);
            $sFileName = substr($sRealFilePath, $slash_pos+1);
            $sFileName = substr($sFileName, 0, strlen($sFileName)-4)."_".$id.".php";
        }
        // otherwise use current
        else
        {
            $sFilePath = $APPLICATION->GetCurDir();
            $sFileName = substr($APPLICATION->GetCurPage(true), 0, strlen($APPLICATION->GetCurPage(true))-4)."_".$id.".php";
            $sFileName = substr($sFileName, strlen($sFilePath));
        }

        return array(new Main\IO\File(Main\Application::getDocumentRoot() . $sFilePath . $sFileName), $sFilePath, $sFileName);
    }

    private function IncludeAreas($bFile, $block_name = '', $sFilePath = '', $sFileName = '', $template = '')
    {
        global $APPLICATION, $USER;

        //need fm_lpa for every .php file, even with no php code inside
        $bPhpFile = (!$GLOBALS["USER"]->CanDoOperation('edit_php') && in_array(GetFileExtension($sFileName), GetScriptFileExt()));

        $bCanEdit = $USER->CanDoFileOperation('fm_edit_existent_file', array(SITE_ID, $sFilePath.$sFileName)) && (!$bPhpFile || $GLOBALS["USER"]->CanDoFileOperation('fm_lpa', array(SITE_ID, $sFilePath.$sFileName)));
        $bCanAdd = $USER->CanDoFileOperation('fm_create_new_file', array(SITE_ID, $sFilePath.$sFileName)) && (!$bPhpFile || $GLOBALS["USER"]->CanDoFileOperation('fm_lpa', array(SITE_ID, $sFilePath.$sFileName)));

        $isEdit = $bCanEdit && $bFile->isExists();

        $editor = '&site='.SITE_ID.'&back_url='.urlencode($_SERVER['REQUEST_URI']).'&templateID='.urlencode(SITE_TEMPLATE_ID);
        $editorUrl = "/bitrix/admin/public_file_edit.php?lang=".LANGUAGE_ID."&from=main.include&template=".urlencode($template)."&path=".urlencode($sFilePath.$sFileName).$editor;

        $icon = $isEdit ? 'bx-context-toolbar-edit-icon' : 'bx-context-toolbar-create-icon';
        $title = $isEdit ? 'Редактировать ' . $block_name : 'Создать ' . $block_name;

        if( !$isEdit ) $editorUrl .= "&new=Y";

        $arIcon = array(
            "URL" => 'javascript:'.$APPLICATION->GetPopupLink(
                array(
                    'URL' => $editorUrl,
                    "PARAMS" => array(
                        'width' => 770,
                        'height' => 570,
                        'resize' => true
                    )
                )
            ),
            "DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
            "ICON" => $icon,
            "TITLE" => $title,
        );

        return $arIcon;
    }

    function executeComponent()
    {
        global $APPLICATION;

        $request = Main\Context::getCurrent()->getRequest();
        $rDir  = $request->getRequestedPageDirectory();

        $areas = array();
        $this->arResult['BLOCKS'] = array();
        foreach ($this->arParams['BLOCKS'] as $i => $block)
        {
            if( empty($block) ) continue;

            $id = self::esc_string( $block );
            list($bFile, $sFilePath, $sFileName) = $this->getFile($id);

            $this->arResult['BLOCKS'][] = array(
                'ID'   => $id,
                'NAME' => $block,
                'PATH' => $bFile->isExists() ? $bFile->getPath() : '',
                'EXPANDED' => $i ? 'false' : 'true',
                'CLASS' => $i ? 'multi-collapse collapse' : 'multi-collapse collapse show'
            );

            if($APPLICATION->GetShowIncludeAreas()) {
                $areas[] = $this->IncludeAreas($bFile, $block, $sFilePath, $sFileName);
            }
        }

        // $this->arResult['errors'] = $this->errors;
        $this->includeComponentTemplate();
        $this->AddIncludeAreaIcons($areas);
    }
}