<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var customOrderComponent $component */

?>
<section class="accordion">
    <?foreach ($arResult['BLOCKS'] as $i => $block) :
        if( empty($block['PATH']) ) continue;
        ?>
        <div class="accordion__element">
            <a href="#<?= $block['ID'] ?>" class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="<?= $block['EXPANDED'] ?>"><?= $block['NAME'] ?></a>
            <div class="<?= $block['CLASS'] ?>" id="<?= $block['ID'] ?>" data-parent=".accordion">
                <? include($block['PATH']); ?>
            </div>
        </div>
    <? endforeach; ?>
</section>