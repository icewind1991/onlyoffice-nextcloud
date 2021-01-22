<?php
/**
 *
 * (c) Copyright Ascensio System SIA 2020
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

    script("onlyoffice", "personal");

?>

<div class="section section-onlyoffice section-onlyoffice-personal">
    <h2>ONLYOFFICE</h2>

    <h3><?php p($l->t("Personal settings")) ?></h3>

    <div id="onlyofficeTemplateSettings">
        <?php p($l->t("Document templates folder")) ?>
        <p>
            <input type="text" id="onlyofficeTemplateFolder" value="<?php p($_["templateDir"]); ?>" disabled />
            <button id="onlyofficeAddTemplateFolder"><span class="icon-folder" title="<?php p($l->t('Select a personal template folder')) ?>" data-toggle="tooltip"></span></button>
            <button id="onlyofficeResetTemplateFolder"><span  class="icon-delete" title="<?php p($l->t('Remove personal template folder')) ?>" data-toggle="tooltip"></span></button>
        </p>
        <p><em><?php p($l->t('Templates inside of this directory will be added to the template selector of ONLYOFFICE.')); ?></em></p>
        </br>
        <button id="onlyofficePersonalSave" class="button"><?php p($l->t("Save")) ?></button>
    </div>
</div>