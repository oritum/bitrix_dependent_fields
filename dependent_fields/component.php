<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");

use Bitrix\Main\Page\Asset;

$asset = Asset::getInstance();

class LevelsProcessor {
    private $arParams;
    private $arResult;
    private $linkProps = [];

    public function __construct($arParams) {
        $this->arParams = $arParams;
        $this->arResult = [
            'LEVELS' => [],
            'FINAL_SELECTION' => []
        ];
    }

    public function process() {
        $this->prepareLinkProperties();
        $this->processLevels();
        $this->processFinalSelection();

        return $this->arResult;
    }

    private function prepareLinkProperties() {
        $levelsCount = (int)$this->arParams['LEVELS_COUNT'];

        for ($i = 2; $i <= $levelsCount; $i++) {
            $childLevel = "LEVEL_{$i}";
            $parentLevel = "LEVEL_" . ($i - 1);
            $linkProp = $this->arParams["LINK_PROP_LEVEL_{$i}"] ?? null;

            if ($linkProp) {
                $this->linkProps[$childLevel] = [
                    'PARENT_LEVEL' => $parentLevel,
                    'PROPERTY' => $linkProp,
                ];
            }
        }
    }

    private function processLevels() {
        $levelsCount = (int)$this->arParams['LEVELS_COUNT'];

        for ($i = 1; $i <= $levelsCount; $i++) {
            $levelKey = "LEVEL_{$i}";
            $iblockId = (int)$this->arParams["{$levelKey}_IBLOCK_ID"];
            $displayName = $this->arParams["{$levelKey}_NAME"] ?: $levelKey;
            $selectedId = $_GET[$levelKey] ?? null;

            $filter = ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y'];

            if (isset($this->linkProps[$levelKey])) {
                $parentLevel = $this->linkProps[$levelKey]['PARENT_LEVEL'];
                $propertyCode = $this->linkProps[$levelKey]['PROPERTY'];
                $parentSelectedIds = $_GET[$parentLevel] ?? null;

                if (empty($parentSelectedIds)) {
                    $this->arResult['LEVELS'][$levelKey] = [
                        'IBLOCK_ID' => $iblockId,
                        'NAME' => $displayName,
                        'SELECTED' => [],
                        'ELEMENTS' => [],
                    ];
                    continue;
                }

                if (!is_array($parentSelectedIds)) {
                    $parentSelectedIds = [$parentSelectedIds];
                }

                $filter["PROPERTY_{$propertyCode}"] = $parentSelectedIds;
            }

            $elements = $this->getIblockElements($filter, $this->arParams['FIELD_CODE'] ?: []);
            $this->arResult['LEVELS'][$levelKey] = [
                'IBLOCK_ID' => $iblockId,
                'NAME' => $displayName,
                'SELECTED' => $selectedId,
                'ELEMENTS' => $elements,
            ];
        }
    }

    private function getIblockElements(array $filter, array $fieldsToSelect = []) {
        $selectFields = array_merge(['ID', 'NAME'], $fieldsToSelect);

        $res = CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            $filter,
            false,
            false,
            $selectFields
        );

        $elements = [];
        while ($ob = $res->GetNextElement()) {
            $fields = $ob->GetFields();
            $fields['PROPERTIES'] = $ob->GetProperties();
            $elements[] = $fields;
        }

        return $elements;
    }

    private function processFinalSelection() {
        $levelsCount = (int)$this->arParams['LEVELS_COUNT'];
        $finalLevelKey = "LEVEL_{$levelsCount}";
        $finalSelectedIds = $_GET[$finalLevelKey] ?? [];

        if (empty($finalSelectedIds)) return;

        if (!is_array($finalSelectedIds)) {
            $finalSelectedIds = [$finalSelectedIds];
        }

        $iblockId = (int)$this->arParams["{$finalLevelKey}_IBLOCK_ID"];

        $res = CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            ['IBLOCK_ID' => $iblockId, 'ID' => $finalSelectedIds, 'ACTIVE' => 'Y'],
            false,
            false,
            ['ID', 'NAME', 'PREVIEW_TEXT']
        );

        while ($element = $res->Fetch()) {
            $this->arResult['FINAL_SELECTION'][] = $element;
        }
    }
}

$processor = new LevelsProcessor($arParams);
$arResult = $processor->process();

if ($this->InitComponentTemplate()) {
    $asset->addJs($this->__template->__folder . "/script.js");
    $asset->addString(
        '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"  rel="stylesheet">'
        );
    $asset->addString(
        '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"  integrity="sha384-ZOkYXD6+xI1swzYtTqMX03NdUu+hvTffM+mReZ3FiSV55vINNSKtvzOvYmK9Vh4C" crossorigin="anonymous"></script>'
        );
}

$this->IncludeComponentTemplate();