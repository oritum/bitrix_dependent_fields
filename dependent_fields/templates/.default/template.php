<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="container my-5">
    <div class="card shadow mx-auto col-md-6" >
        <div class="card-body">
            <form method="get" id="dependent-form">
                <?php
                $levelKeys = array_keys($arResult['LEVELS']);
                foreach ($levelKeys as $index => $levelCode):
                    $levelData = $arResult['LEVELS'][$levelCode];
                    $selectedNames = [];

                    foreach ($levelData['ELEMENTS'] as $el) {
                        if (in_array($el['ID'], $levelData['SELECTED'] ?? [])) {
                            $selectedNames[] = $el['NAME'];
                        }
                    }

                    $dropdownLabel = $selectedNames ? implode(', ', $selectedNames) : '--' . GetMessage('DEPENDENT_FIELDS_SELECT') . '--';
                    ?>
                    <div class="mb-4">
                        <label class="form-label d-block"><?= htmlspecialcharsbx($levelData['NAME']) ?></label>

                        <div class="dropdown">
                            <button
                                type="button"
                                class="btn btn-outline-secondary dropdown-toggle w-100 text-truncate text-start dropdown-toggle-manual"
                                data-dropdown-id="<?= $levelCode ?>"
                                style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"
                            >
                                <?= htmlspecialcharsbx($dropdownLabel) ?>
                            </button>

                            <ul class="dropdown-menu w-100 px-3 pb-2 manual-dropdown"
                                id="dropdown-<?= $levelCode ?>"
                                style="max-height: 300px; overflow-y: auto;"
                                data-level="<?= $index ?>">

                                <li class="d-flex justify-content-between mb-2">
                                    <button type="button" class="btn btn-sm btn-link p-0 select-all" data-level="<?= $levelCode ?>"><?=GetMessage('DEPENDENT_FIELDS_SELECT_ALL')?></button>
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 clear-all" data-level="<?= $levelCode ?>" data-level-index="<?= $index ?>"><?=GetMessage('DEPENDENT_FIELDS_CLEAR')?></button>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <?php foreach ($levelData['ELEMENTS'] as $element): 
                                    $isChecked = in_array($element['ID'], $levelData['SELECTED'] ?? []);
                                    $id = htmlspecialcharsbx($levelCode . '_' . $element['ID']);
                                ?>
                                    <li>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input keep-open"
                                                type="checkbox"
                                                name="<?= htmlspecialcharsbx($levelCode) ?>[]"
                                                id="<?= $id ?>"
                                                value="<?= $element['ID'] ?>"
                                                <?= $isChecked ? 'checked' : '' ?>
                                                data-level-index="<?= $index ?>"
                                                data-parent-level="<?= $levelCode ?>"
                                            >
                                            <label class="form-check-label" for="<?= $id ?>">
                                                <?= htmlspecialcharsbx($element['NAME']) ?>
                                            </label>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                                <li>
                                    <button type="submit" class="btn btn-primary btn-sm apply-level mt-2" data-level="<?= $levelCode ?>"><?=GetMessage('DEPENDENT_FIELDS_APPLY')?></button>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </form>
            <div class="alert alert-info mt-4">
                <h5><?=GetMessage('DEPENDENT_FIELDS_SELECTED_ITEMS')?></h5>
                <ul class="mb-0">
                    <?php foreach ($arResult['FINAL_SELECTION'] as $item): ?>
                        <li><strong><?= htmlspecialcharsbx($item['NAME']) ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>