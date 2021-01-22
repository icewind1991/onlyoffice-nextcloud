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

(function ($, OC) {

    $(document).ready(function () {
        OCA.Onlyoffice = _.extend({}, OCA.Onlyoffice);
        if (!OCA.Onlyoffice.AppName) {
            OCA.Onlyoffice = {
                AppName: "onlyoffice"
            };
        }

        $("#onlyofficeAddTemplateFolder").click(function () {
            OC.dialogs.filepicker(t("onlyoffice", "Select a personal template folder"), 
            function(templateDir) {
                $("#onlyofficeTemplateFolder").val(templateDir);
            }, 
            false, 
            "httpd/unix-directory", 
            true, 
            OC.dialogs.FILEPICKER_TYPE_CHOOSE)
        });

        $("#onlyofficePersonalSave").click(function () {
            var personalTemplateDir = $("#onlyofficeTemplateFolder").val();

            $(".section-onlyoffice").addClass("icon-loading");
            $.ajax({
                method: "PUT",
                url: OC.generateUrl("apps/" + OCA.Onlyoffice.AppName + "/ajax/settings/personal"),
                data: {
                    personalTemplateDir: personalTemplateDir
                },
                success: function onSuccess(response) {
                    $(".section-onlyoffice").removeClass("icon-loading");
                    if (response) {
                        OCP.Toast.success(t(OCA.Onlyoffice.AppName, "Settings have been successfully updated"));
                    }
                }
            });
        });

        $("#onlyofficeResetTemplateFolder").click(function () {
            $("#onlyofficeTemplateFolder").val("");
        });
    });

})(jQuery, OC);