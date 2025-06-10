<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!\Bitrix\Main\Loader::includeModule('iblock')) return;

$arIBlocks = [];
$res = \CIBlock::GetList(['NAME' => 'ASC'], ['ACTIVE' => 'Y']);
while ($ib = $res->Fetch()) {
    $arIBlocks[$ib['ID']] = "[{$ib['ID']}] {$ib['NAME']}";
}

$arComponentParameters = [
    'PARAMETERS' => [
        'LEVELS_COUNT' => [
            'PARENT' => 'BASE',
            'NAME' => GetMessage('DEPENDENT_FIELDS_LEVELS_COUNT_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => '3',
            'REFRESH' => 'Y',
        ],
    ],
];

$count = (int)($_REQUEST['LEVELS_COUNT'] ?? $arCurrentValues['LEVELS_COUNT'] ?? 3);

for ($i = 1; $i <= $count; $i++) {
    $arComponentParameters['PARAMETERS']["LEVEL_{$i}_NAME"] = [
        'PARENT' => 'BASE',
        'NAME' => GetMessage('DEPENDENT_FILEDS_LEVEL_NAME') . " {$i}",
        'TYPE' => 'STRING',
        'DEFAULT' => "LEVEL_{$i}",
    ];
    $arComponentParameters['PARAMETERS']["LEVEL_{$i}_IBLOCK_ID"] = [
        'PARENT' => 'BASE',
        'NAME' => GetMessage('DEPENDENT_FILEDS_LEVEL_IBLOCK') . " {$i}",
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocks,
        'ADDITIONAL_VALUES' => 'Y',
    ];
    if ($i > 1) {
        $arComponentParameters['PARAMETERS']["LINK_PROP_LEVEL_{$i}"] = [
            'PARENT' => 'BASE',
            'NAME' => str_replace(
                ['#FROM#', '#TO#'],
                [$i - 1, $i],
                GetMessage('DEPENDENT_FIELDS_LINK_PROPERTY_NAME')
                ),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ];
    }
}
