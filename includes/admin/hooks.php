<div class="content">
    <form action="apply-hooks.php" method="POST" class="pure-form require-validation" enctype="application/x-www-form-urlencoded">
        <h2 class="content-subhead"><?php echo I18N::resolve("admin.section.hooks.active.name"); ?></h2>
        <div class="hook-list" id="active-hooks-list">

        </div>
        <h2 class="content-subhead"><?php echo I18N::resolve("admin.section.hooks.inactive.name"); ?></h2>
        <div class="hook-list" id="inactive-hooks-list">

        </div>
        <p class="buttons"><input type="button" id="hooks-add" class="button-standard" value="<?php echo I18N::resolve("admin.section.hooks.ui.add.name"); ?>"> <input type="submit" class="button-submit" value="<?php echo I18N::resolve("ui.button.save"); ?>"></p>
    </form>
</div>
<div id="hooks-update-objective-overlay" class="cover-box admin-cover-box">
    <div class="cover-box-inner">
        <div class="header">
            <h1 id="hooks-update-objective-overlay-title"></h1>
        </div>
        <div class="cover-box-content content pure-form">
            <div class="pure-g">
                <div class="pure-u-5-5 full-on-mobile"><p><select id="update-hook-objective">
                    <?php
                        /*
                            We'll sort the research objectives by their first respective categories.
                            Put all the research objectives into an array ($cats) of the structure
                            $cats[CATEGORY][RESEARCH OBJECTIVE][PARAMETERS ETC.]
                        */
                        $cats = array();
                        foreach (Research::OBJECTIVES as $objective => $data) {
                            // Skip unknown since it shouldn't be displayed
                            if ($objective === "unknown") continue;
                            $cats[$data["categories"][0]][$objective] = $data;
                        }

                        /*
                            After the objectives have been sorted into proper categories, we'll resolve
                            the I18N string for each research objective, one category at a time.
                        */
                        foreach ($cats as $category => $categorizedObjectives) {
                            foreach ($categorizedObjectives as $objective => $data) {
                                // Use the plural string of the objective by default
                                $i18n = I18N::resolve("objective.{$objective}.plural");
                                // Replace parameters (e.g. {%1}) with placeholders
                                for ($i = 0; $i < count($data["params"]); $i++) {
                                    $i18n = str_replace("{%".($i+1)."}", I18N::resolve("parameter.".$data["params"][$i].".placeholder"), $i18n);
                                }
                                // Now save the final localized string back into the objective
                                $categorizedObjectives[$objective]["i18n"] = $i18n;
                            }
                            /*
                                Create a group for each category of objectives, then output each of the
                                objectives within that category to the selection box.
                            */
                            echo '<optgroup label="'.I18N::resolve("category.objective.{$category}").'">';
                            foreach ($categorizedObjectives as $objective => $data) {
                                $text = I18N::resolve("objective.{$objective}.plural");
                                echo '<option value="'.$objective.'">'.$data["i18n"].'</option>';
                            }
                            echo '</optgroup>';
                        }
                    ?>
                </select></p></div>
            </div>
            <div class="research-params objective-params">
                <?php
                    foreach (Research::PARAMETERS as $param => $class) {
                        $inst = new $class();
                        if (in_array("objectives", $inst->getAvailable())) {
                            ?>
                                <div id="update-hook-objective-param-<?php echo $param; ?>-box" class="pure-g research-parameter objective-parameter">
                                    <div class="pure-u-1-3 full-on-mobile"><p><?php echo I18N::resolve("parameter.{$param}.label"); ?>:</p></div>
                                    <div class="pure-u-2-3 full-on-mobile"><p><?php echo $inst->html("update-hook-objective-param-{$param}-input", "parameter"); ?></p></div>
                                </div>
                            <?php
                        }
                    }
                ?>
                <script>
                    function getObjectiveParameter(param) {
                        switch (param) {
                            <?php
                                foreach (Research::PARAMETERS as $param => $class) {
                                    $inst = new $class();
                                    if (in_array("objectives", $inst->getAvailable())) {
                                        echo "case '{$param}':\n";
                                        echo $inst->writeJS("update-hook-objective-param-{$param}-input")."\n";
                                    }
                                }
                            ?>
                        }
                    }
                    function parseObjectiveParameter(param, data) {
                        switch (param) {
                            <?php
                                foreach (Research::PARAMETERS as $param => $class) {
                                    $inst = new $class();
                                    if (in_array("objectives", $inst->getAvailable())) {
                                        echo "case '{$param}':\n";
                                        echo $inst->parseJS("update-hook-objective-param-{$param}-input")."\n";
                                        echo "break;\n";
                                    }
                                }
                            ?>
                        }
                    }
                    $("#update-hook-objective").on("change", function() {
                        <?php
                            foreach (Research::PARAMETERS as $param => $class) {
                                $inst = new $class();
                                if (in_array("objectives", $inst->getAvailable())) {
                                    echo "$('#update-hook-objective-param-{$param}-box').hide();";
                                }
                            }
                        ?>
                        var show = objectives[$("#update-hook-objective").val()].params;
                        for (var i = 0; i < show.length; i++) {
                            $("#update-hook-objective-param-" + show[i] + "-box").show();
                        }
                    });
                </script>
            </div>
            <div class="cover-button-spacer"></div>
            <div class="pure-g">
                <div class="pure-u-1-2 right-align"><span type="button" id="update-hook-objective-cancel" class="button-standard split-button button-spaced left"><?php echo I18N::resolve("ui.button.cancel"); ?></span></div>
                <div class="pure-u-1-2"><span type="button" id="update-hook-objective-submit" class="button-submit split-button button-spaced right"><?php echo I18N::resolve("ui.button.done"); ?></span></div>
            </div>
        </div>
    </div>
</div>
<div id="hooks-update-reward-overlay" class="cover-box admin-cover-box">
    <div class="cover-box-inner">
        <div class="header">
            <h1 id="hooks-update-reward-overlay-title"></h1>
        </div>
        <div class="cover-box-content content pure-form">
            <div class="pure-g">
                <div class="pure-u-5-5 full-on-mobile"><p><select id="update-hook-reward">
                    <?php
                        /*
                            We'll sort the research rewards by their first respective categories.
                            Put all the research rewards into an array ($cats) of the structure
                            $cats[CATEGORY][RESEARCH REWARD][PARAMETERS ETC.]
                        */
                        $cats = array();
                        foreach (Research::REWARDS as $reward => $data) {
                            // Skip unknown since it shouldn't be displayed
                            if ($reward === "unknown") continue;
                            $cats[$data["categories"][0]][$reward] = $data;
                        }

                        /*
                            After the rewards have been sorted into proper categories, we'll resolve
                            the I18N string for each research reward, one category at a time.
                        */
                        foreach ($cats as $category => $categorizedRewards) {
                            foreach ($categorizedRewards as $reward => $data) {
                                // Use the plural string of the reward by default
                                $i18n = I18N::resolve("reward.{$reward}.plural");
                                // Replace parameters (e.g. {%1}) with placeholders
                                for ($i = 0; $i < count($data["params"]); $i++) {
                                    $i18n = str_replace("{%".($i+1)."}", I18N::resolve("parameter.".$data["params"][$i].".placeholder"), $i18n);
                                }
                                // Now save the final localized string back into the reward
                                $categorizedRewards[$reward]["i18n"] = $i18n;
                            }

                            /*
                                Create a group for each category of rewards, then output each of the
                                rewards within that category to the selection box.
                            */
                            echo '<optgroup label="'.I18N::resolve("category.reward.{$category}").'">';
                            foreach ($categorizedRewards as $reward => $data) {
                                $text = I18N::resolve("reward.{$reward}.plural");
                                echo '<option value="'.$reward.'">'.$data["i18n"].'</option>';
                            }
                            echo '</optgroup>';
                        }
                    ?>
                </select></p></div>
            </div>
            <div class="research-params reward-params">
                <?php
                    foreach (Research::PARAMETERS as $param => $class) {
                        $inst = new $class();
                        if (in_array("rewards", $inst->getAvailable())) {
                            ?>
                                <div id="update-hook-reward-param-<?php echo $param; ?>-box" class="pure-g research-parameter reward-parameter">
                                    <div class="pure-u-1-3 full-on-mobile"><p><?php echo I18N::resolve("parameter.{$param}.label"); ?>:</p></div>
                                    <div class="pure-u-2-3 full-on-mobile"><p><?php echo $inst->html("update-hook-reward-param-{$param}-input", "parameter"); ?></p></div>
                                </div>
                            <?php
                        }
                    }
                ?>
                <script>
                    function getRewardParameter(param) {
                        switch (param) {
                            <?php
                                foreach (Research::PARAMETERS as $param => $class) {
                                    $inst = new $class();
                                    if (in_array("rewards", $inst->getAvailable())) {
                                        echo "case '{$param}':\n";
                                        echo $inst->writeJS("update-hook-reward-param-{$param}-input")."\n";
                                    }
                                }
                            ?>
                        }
                    }
                    function parseRewardParameter(param, data) {
                        switch (param) {
                            <?php
                                foreach (Research::PARAMETERS as $param => $class) {
                                    $inst = new $class();
                                    if (in_array("rewards", $inst->getAvailable())) {
                                        echo "case '{$param}':\n";
                                        echo $inst->parseJS("update-hook-reward-param-{$param}-input")."\n";
                                        echo "break;\n";
                                    }
                                }
                            ?>
                        }
                    }
                    $("#update-hook-reward").on("change", function() {
                        <?php
                            foreach (Research::PARAMETERS as $param => $class) {
                                $inst = new $class();
                                if (in_array("rewards", $inst->getAvailable())) {
                                    echo "$('#update-hook-reward-param-{$param}-box').hide();";
                                }
                            }
                        ?>
                        var show = rewards[$("#update-hook-reward").val()].params;
                        for (var i = 0; i < show.length; i++) {
                            $("#update-hook-reward-param-" + show[i] + "-box").show();
                        }
                    });
                </script>
            </div>
            <div class="cover-button-spacer"></div>
            <div class="pure-g">
                <div class="pure-u-1-2 right-align"><span type="button" id="update-hook-reward-cancel" class="button-standard split-button button-spaced left"><?php echo I18N::resolve("ui.button.cancel"); ?></span></div>
                <div class="pure-u-1-2"><span type="button" id="update-hook-reward-submit" class="button-submit split-button button-spaced right"><?php echo I18N::resolve("ui.button.done"); ?></span></div>
            </div>
        </div>
    </div>
</div>

<div id="hooks-tg-groups-overlay" class="cover-box admin-cover-box">
    <div class="cover-box-inner">
        <div class="header">
            <h1><?php echo I18N::resolve("admin.hooks.popup.tg.select_group"); ?></h1>
        </div>
        <div class="cover-box-content content pure-form">
            <div class="pure-g">
                <div class="pure-u-1-3 full-on-mobile">
                    <p class="setting-name"><?php echo I18N::resolve("setting.hooks.tg.groups.select.name"); ?>:</p>
                </div>
                <div class="pure-u-2-3 full-on-mobile">
                    <p><select id="select-tg-group-options"></select></p>
                </div>
            </div>
            <div class="cover-button-spacer"></div>
            <div class="pure-g">
                <div class="pure-u-1-2 right-align"><span type="button" id="select-tg-group-cancel" class="button-standard split-button button-spaced left"><?php echo I18N::resolve("ui.button.cancel"); ?></span></div>
                <div class="pure-u-1-2"><span type="button" id="select-tg-group-submit" class="button-submit split-button button-spaced right"><?php echo I18N::resolve("ui.button.select"); ?></span></div>
            </div>
        </div>
    </div>
</div>

<div id="hooks-tg-groups-working" class="cover-box admin-cover-box">
    <div class="cover-box-inner tiny">
        <div class="cover-box-content">
            <div><i class="fas fa-spinner loading-spinner spinner-large"></i></div>
            <p><?php echo I18N::resolve("admin.hooks.popup.tg.searching_group"); ?></p>
        </div>
    </div>
</div>

<div id="hooks-add-overlay" class="cover-box admin-cover-box">
    <div class="cover-box-inner">
        <div class="header">
            <h1><?php echo I18N::resolve("admin.hooks.popup.add_webhook"); ?></h1>
        </div>
        <div class="cover-box-content content pure-form">
            <div class="pure-g">
                <div class="pure-u-1-3 full-on-mobile">
                    <p class="setting-name"><?php echo I18N::resolve("setting.hooks.add.type.name"); ?>:</p>
                </div>
                <div class="pure-u-2-3 full-on-mobile">
                    <p><select id="add-hook-type">
                        <option value="json"><?php echo I18N::resolve("setting.hooks.add.type.option.json"); ?></option>
                        <option value="telegram"><?php echo I18N::resolve("setting.hooks.add.type.option.telegram"); ?></option>
                    </select></p>
                </div>
            </div>
            <?php
                $presets = array();
                $path = __DIR__."/../includes/hook-presets";
                $presetdirs = array_diff(scandir($path), array('..', '.'));
                foreach ($presetdirs as $type) {
                    if (is_dir("{$path}/{$type}")) {
                        $typepresets = array_diff(scandir("{$path}/{$type}"), array('..', '.'));
                        foreach ($typepresets as $preset) {
                            $presets[$type][$preset] = file_get_contents("{$path}/{$type}/{$preset}");
                        }
                    }
                }
            ?>
            <div class="pure-g hook-add-type-conditional hook-add-type-json">
                <div class="pure-u-1-3 full-on-mobile">
                    <p class="setting-name"><?php echo I18N::resolve("setting.hooks.add.preset.name"); ?>:</p>
                </div>

                <div class="pure-u-2-3 full-on-mobile">
                    <p><select id="add-hook-json-preset">
                        <option value="none"><?php echo I18N::resolve("setting.hooks.add.preset.option.none"); ?></option>
                        <?php
                            foreach ($presets["json"] as $name => $data) {
                                echo '<option value="'.$name.'">'.$name.'</option>';
                            }
                        ?>
                    </select></p>
                </div>
            </div>
            <div class="pure-g hook-add-type-conditional hook-add-type-telegram">
                <div class="pure-u-1-3 full-on-mobile">
                    <p class="setting-name"><?php echo I18N::resolve("setting.hooks.add.preset.name"); ?>:</p>
                </div>

                <div class="pure-u-2-3 full-on-mobile">
                    <p><select id="add-hook-telegram-preset">
                        <option value="none"><?php echo I18N::resolve("setting.hooks.add.preset.option.none"); ?></option>
                        <?php
                            foreach ($presets["telegram"] as $name => $data) {
                                echo '<option value="'.$name.'">'.$name.'</option>';
                            }
                        ?>
                    </select></p>
                </div>
            </div>
            <div class="cover-button-spacer"></div>
            <div class="pure-g">
                <div class="pure-u-1-2 right-align"><span type="button" id="add-hook-cancel" class="button-standard split-button button-spaced left"><?php echo I18N::resolve("ui.button.cancel"); ?></span></div>
                <div class="pure-u-1-2"><span type="button" id="add-hook-submit" class="button-submit split-button button-spaced right"><?php echo I18N::resolve("ui.button.done"); ?></span></div>
            </div>
        </div>
    </div>
</div>

<?php
    echo IconPackOption::getScript();
?>

<script type="text/javascript" src="../js/clientside-i18n.php"></script>
<script>
    $("#hooks-add").on("click", function() {
        $("#add-hook-type").trigger("change");
        $("#hooks-add-overlay").fadeIn(150);
    });

    $("#add-hook-cancel").on("click", function() {
        $("#hooks-add-overlay").fadeOut(150);
    });

    $("#select-tg-group-cancel").on("click", function() {
        $("#hooks-tg-groups-overlay").fadeOut(150);
    });

    $("#add-hook-submit").on("click", function() {
        var type = $("#add-hook-type").val();
        switch (type) {
            case "json":
                var preset = $("#add-hook-json-preset").val();
                var body = "";

                if (preset != "none") {
                    body = presets["json"][preset];
                }

                var id = getNewID();

                var node = $(createHookNode("json", id));
                node.find(".hook-payload").val(body);
                node.find("select.hook-actions > option[value=enable]").remove();

                updateSummary(node);
                $("#active-hooks-list").append(node);
                node.find(".hook-target").trigger("input");

                viewTheme(id + "-icon-selector", document.getElementById(id + "-icon-selector").value);
                document.getElementById(id + "-icon-selector").addEventListener("change", function() {
                    viewTheme(id + "-icon-selector", document.getElementById(id + "-icon-selector").value);
                });

                break;
            case "telegram":
                var preset = $("#add-hook-telegram-preset").val();
                var body = "";

                if (preset != "none") {
                    body = presets["telegram"][preset];
                }

                var id = getNewID();

                var node = $(createHookNode("telegram", id));
                node.find(".hook-payload").val(body);
                if (preset != "none") {
                    var ext = preset.substr(preset.lastIndexOf(".") + 1);
                    node.find(".hook-tg-parse-mode").val(ext);
                }
                node.find("select.hook-actions > option[value=enable]").remove();

                updateSummary(node);
                $("#active-hooks-list").append(node);
                node.find(".hook-target").trigger("input");
                node.find(".hook-tg-parse-mode").trigger("input");

                viewTheme(id + "-icon-selector", document.getElementById(id + "-icon-selector").value);
                document.getElementById(id + "-icon-selector").addEventListener("change", function() {
                    viewTheme(id + "-icon-selector", document.getElementById(id + "-icon-selector").value);
                });

                break;
        }
        $("#hooks-add-overlay").fadeOut(150);
    });

    $("#add-hook-type").on("change", function() {
        $(".hook-add-type-conditional").hide();
        $(".hook-add-type-conditional.hook-add-type-" + $("#add-hook-type").val()).show();
    });

    function getNewID() {
        return Math.random().toString(36).substr(2, 8);
    }

    function getObjectiveFilterNode(hook) {
        var no = getNewID();
        var node = $.parseHTML('<div class="hook-filter"><span class="hook-objective-text"></span><input type="hidden" class="hook-objective-type" name="hook_' + hook + '[objective][' + no + '][type]" value="unknown"><input type="hidden" class="hook-objective-params" name="hook_' + hook + '[objective][' + no + '][params]" value="[]"><div class="hook-filter-actions"><i class="fas fa-edit hook-edit hook-objective-edit"></i> <i class="far fa-times-circle hook-delete hook-objective-delete"></i></div></div>');
        return node;
    }

    function editObjective(newObjective, caller) {
        var objective;

        if (newObjective) {
            $("#hooks-update-objective-overlay-title").text("<?php echo I18N::resolve("admin.hooks.popup.add_objective"); ?>");
            objective = {
                type: "unknown",
                params: []
            }
        } else {
            $("#hooks-update-objective-overlay-title").text("<?php echo I18N::resolve("admin.hooks.popup.edit_objective"); ?>");
            objective = {
                type: caller.parent().parent().find("input[type=hidden].hook-objective-type").val(),
                params: JSON.parse(caller.parent().parent().find("input[type=hidden].hook-objective-params").val())
            };
        }

        // Reset the report form
        $("input.parameter").val(null);
        $("select.parameter").each(function() {
            $(this)[0].selectedIndex = 0;
        });

        // Set the current research objective
        $("#update-hook-objective").val(objective.type == "unknown" ? null : objective.type);
        if (objective.type !== "unknown") {
            $("#update-hook-objective").trigger("change");
            var params = objectives[objective.type].params;
            for (var i = 0; i < params.length; i++) {
                parseObjectiveParameter(params[i], objective.params[params[i]]);
            }
        } else {
            $(".objective-parameter").hide();
        }

        $("#update-hook-objective-submit").on("click", function() {
            var objective = $("#update-hook-objective").val();
            if (objective == null) {
                alert(resolveI18N("admin.clientside.hooks.update.objective.failed.message", resolveI18N("poi.update.failed.reason.objective_null")));
                return;
            }

            var objDefinition = objectives[objective];

            var objParams = {};
            for (var i = 0; i < objDefinition.params.length; i++) {
                var paramData = getObjectiveParameter(objDefinition.params[i]);
                if (paramData == null || paramData == "") {
                    alert(resolveI18N("admin.clientside.hooks.update.objective.failed.message", resolveI18N("xhr.failed.reason.missing_fields")));
                    return;
                }
                objParams[objDefinition.params[i]] = paramData;
            }

            var node;

            if (newObjective) {
                node = $(getObjectiveFilterNode(caller.closest(".hook-instance").attr("data-hook-id")));
            } else {
                node = caller.parent().parent();
            }

            node.find("input[type=hidden].hook-objective-type").val(objective);
            node.find("input[type=hidden].hook-objective-params").val(JSON.stringify(objParams));
            node.find("span.hook-objective-text").text(resolveObjective({
                type: objective,
                params: objParams
            }));

            if (newObjective) {
                caller.parent().parent().append(node);
                node.parent().find(".hook-mode-objective").prop("disabled", false);
            }

            updateSummary(node);

            $("#update-hook-objective-submit").off();
            $("#hooks-update-objective-overlay").fadeOut(150);
        });

        $("#hooks-update-objective-overlay").fadeIn(150);
    }

    function getRewardFilterNode(hook) {
        var no = getNewID();
        var node = $.parseHTML('<div class="hook-filter"><span class="hook-reward-text"></span><input type="hidden" class="hook-reward-type" name="hook_' + hook + '[reward][' + no + '][type]" value="unknown"><input type="hidden" class="hook-reward-params" name="hook_' + hook + '[reward][' + no + '][params]" value="[]"><div class="hook-filter-actions"><i class="fas fa-edit hook-edit hook-reward-edit"></i> <i class="far fa-times-circle hook-delete hook-reward-delete"></i></div></div>');
        return node;
    }

    function editReward(newReward, caller) {
        var reward;

        if (newReward) {
            $("#hooks-update-reward-overlay-title").text("<?php echo I18N::resolve("admin.hooks.popup.add_reward"); ?>");
            reward = {
                type: "unknown",
                params: []
            }
        } else {
            $("#hooks-update-reward-overlay-title").text("<?php echo I18N::resolve("admin.hooks.popup.edit_reward"); ?>");
            reward = {
                type: caller.parent().parent().find("input[type=hidden].hook-reward-type").val(),
                params: JSON.parse(caller.parent().parent().find("input[type=hidden].hook-reward-params").val())
            };
        }

        // Reset the report form
        $("input.parameter").val(null);
        $("select.parameter").each(function() {
            $(this)[0].selectedIndex = 0;
        });

        // Set the current research reward
        $("#update-hook-reward").val(reward.type == "unknown" ? null : reward.type);
        if (reward.type !== "unknown") {
            $("#update-hook-reward").trigger("change");
            var params = rewards[reward.type].params;
            for (var i = 0; i < params.length; i++) {
                parseRewardParameter(params[i], reward.params[params[i]]);
            }
        } else {
            $(".reward-parameter").hide();
        }

        $("#update-hook-reward-submit").on("click", function() {
            var reward = $("#update-hook-reward").val();
            if (reward == null) {
                alert(resolveI18N("admin.clientside.hooks.update.reward.failed.message", resolveI18N("poi.update.failed.reason.reward_null")));
                return;
            }

            var rewDefinition = rewards[reward];

            var rewParams = {};
            for (var i = 0; i < rewDefinition.params.length; i++) {
                var paramData = getRewardParameter(rewDefinition.params[i]);
                if (paramData == null || paramData == "") {
                    alert(resolveI18N("admin.clientside.hooks.update.reward.failed.message", resolveI18N("xhr.failed.reason.missing_fields")));
                    return;
                }
                rewParams[rewDefinition.params[i]] = paramData;
            }

            var node;

            if (newReward) {
                node = $(getRewardFilterNode(caller.closest(".hook-instance").attr("data-hook-id")));
            } else {
                node = caller.parent().parent();
            }

            node.find("input[type=hidden].hook-reward-type").val(reward);
            node.find("input[type=hidden].hook-reward-params").val(JSON.stringify(rewParams));
            node.find("span.hook-reward-text").text(resolveReward({
                type: reward,
                params: rewParams
            }));

            if (newReward) {
                caller.parent().parent().append(node);
                node.parent().find(".hook-mode-reward").prop("disabled", false);
            }

            updateSummary(node);

            $("#update-hook-reward-submit").off();
            $("#hooks-update-reward-overlay").fadeOut(150);
        });

        $("#hooks-update-reward-overlay").fadeIn(150);
    }

    function encodeHTML(data) {
        return $("<div />").text(data).html();
    }

    function updateSummary(node) {
        var objText = null;
        node.closest(".hook-instance").find(".hook-filter-objectives .hook-filter").each(function() {
            var filterMode = $(this).parent().find(".hook-mode-objective").val();
            var objective = resolveObjective({
                type: $(this).find("input[type=hidden].hook-objective-type").val(),
                params: JSON.parse($(this).find("input[type=hidden].hook-objective-params").val())
            });
            var objHTML = '<span class="hook-head-objective-text">' + encodeHTML(objective) + '</span>';
            if (objText === null) {
                if (filterMode == "blacklist") {
                    objText = resolveI18N("admin.clientside.hooks.any_objective_except", objHTML);
                } else {
                    objText = objHTML;
                }
            } else {
                if (filterMode == "blacklist") {
                    objText = resolveI18N("admin.clientside.hooks.multi_and", objText, objHTML);
                } else {
                    objText = resolveI18N("admin.clientside.hooks.multi_or", objText, objHTML);
                }
            }
        });
        if (objText === null) {
            objText = '<span class="hook-head-objective-text">' + encodeHTML(resolveI18N("admin.clientside.hooks.any_objective")) + '</span>';
        }
        var rewText = null;
        node.closest(".hook-instance").find(".hook-filter-rewards .hook-filter").each(function() {
            var filterMode = $(this).parent().find(".hook-mode-reward").val();
            var reward = resolveReward({
                type: $(this).find("input[type=hidden].hook-reward-type").val(),
                params: JSON.parse($(this).find("input[type=hidden].hook-reward-params").val())
            });
            var rewHTML = '<span class="hook-head-reward-text">' + encodeHTML(reward) + '</span>';
            if (rewText === null) {
                if (filterMode == "blacklist") {
                    rewText = resolveI18N("admin.clientside.hooks.any_reward_except", rewHTML);
                } else {
                    rewText = rewHTML;
                }
            } else {
                if (filterMode == "blacklist") {
                    rewText = resolveI18N("admin.clientside.hooks.multi_and", rewText, rewHTML);
                } else {
                    rewText = resolveI18N("admin.clientside.hooks.multi_or", rewText, rewHTML);
                }
            }
        });
        if (rewText === null) {
            rewText = '<span class="hook-head-reward-text">' + encodeHTML(resolveI18N("admin.clientside.hooks.any_reward")) + '</span>';
        }
        var text = resolveI18N("poi.objective_text", objText, rewText);
        node.closest(".hook-instance").find(".hook-summary-text").html(text);
    }

    function createHookNode(type, id) {
        <?php
            $langs = I18N::getAvailableLanguagesWithNames();
            $langopts = "";
            foreach ($langs as $code => $name) {
                $langopts .= '<option value="'.$code.'">'.$name.'</option>';
            }
            $opt = new IconPackOption("setting.hooks.hook_list.icons.option.default");

            $hookSummary = '
            <span class="hook-summary-text">'.I18N::resolveArgs("poi.objective_text", '<span class="hook-head-objective-text">'.I18N::resolve("admin.clientside.hooks.any_objective").'</span>', '<span class="hook-head-reward-text">'.I18N::resolve("admin.clientside.hooks.any_reward").'</span>').'</span>';

            $hookActions = '
            <div class="pure-g">
                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.actions.name").':</p></div>
                <div class="pure-u-2-3 full-on-mobile"><p><select class="hook-actions" name="hook_{%ID%}[action]">
                    <option value="none" selected>'.I18N::resolve("setting.hooks.hook_list.actions.option.none").'</option>
                    <option value="enable">'.I18N::resolve("setting.hooks.hook_list.actions.option.enable").'</option>
                    <option value="disable">'.I18N::resolve("setting.hooks.hook_list.actions.option.disable").'</option>
                    <option value="delete">'.I18N::resolve("setting.hooks.hook_list.actions.option.delete").'</option>
                </select></p></div>
            </div>';

            $hookCommonSettings = '
            <div class="pure-g">
                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.language.name").':</p></div>
                <div class="pure-u-2-3 full-on-mobile">
                    <p><select class="hook-lang" name="hook_{%ID%}[lang]">'.$langopts.'</select></p>
                </div>
            </div>
            <div class="pure-g">
                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.icons.name").':</p></div>
                <div class="pure-u-2-3 full-on-mobile">
                    <p>'.$opt->getControl(null, "hook_{%ID%}[iconSet]", "{%ID%}-icon-selector", array("class" => "hook-icon-set")).'</p>
                </div>
            </div>
            '.$opt->getFollowingBlock(false, false).'
            <div class="pure-g">
                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.geofence.name").':</p></div>
                <div class="pure-u-2-3 full-on-mobile">
                    <p><textarea class="hook-geofence" name="hook_{%ID%}[geofence]" data-validate-as="geofence"></textarea></p>
                </div>
            </div>';

            $hookSyntaxHelp = '
            <p><a class="hook-show-help" href="#">'.I18N::resolve("admin.clientside.hooks.syntax.show").'</a></p>
            <div class="hook-syntax-help hidden-by-default">
                <div class="hook-syntax-block full-on-mobile">
                    <h3>'.I18N::resolve("admin.hooks.syntax.poi.title").'</h3>
                    '.I18N::resolveArgs("admin.hooks.syntax.poi.poi", '<code>&lt;%POI%&gt;</code>').'<br />
                    '.I18N::resolveArgs("admin.hooks.syntax.poi.lat", '<code>&lt;%LAT%&gt;</code>').'<br />
                    '.I18N::resolveArgs("admin.hooks.syntax.poi.lng", '<code>&lt;%LNG%&gt;</code>').'<br />
                    '.I18N::resolveArgs("admin.hooks.syntax.poi.coords", '<code>&lt;%COORDS%&gt;</code>').'<br />
                    '.I18N::resolveArgs("admin.hooks.syntax.poi.navurl", '<code>&lt;%NAVURL%&gt;</code>').'
                </div>
                <div class="hook-syntax-block full-on-mobile">
                    <h3>'.I18N::resolve("admin.hooks.syntax.research.title").'</h3>
                    '.I18N::resolveArgs("admin.hooks.syntax.research.objective", '<code>&lt;%OBJECTIVE%&gt;</code>').'<br />
                    '.I18N::resolveArgs("admin.hooks.syntax.research.reward", '<code>&lt;%REWARD%&gt;</code>').'<br />
                    '.I18N::resolveArgs("admin.hooks.syntax.research.reporter", '<code>&lt;%REPORTER%&gt;</code>').'<br />
                    '.I18N::resolveArgs("admin.hooks.syntax.research.time", '<code>&lt;%TIME(format)%&gt;</code>').'
                </div>
                <div class="hook-syntax-clear"></div>
                <div>
                    <h3>'.I18N::resolve("admin.hooks.syntax.icons.title").'</h3>
                    '.I18N::resolveArgs("admin.hooks.syntax.icons.objective_icon", '<code>&lt;%OBJECTIVE_ICON(format,variant)%&gt;</code>').'<br />
                    '.I18N::resolveArgs("admin.hooks.syntax.icons.reward_icon", '<code>&lt;%REWARD_ICON(format,variant)%&gt;</code>').'
                </div>
                <div>
                    <h3>'.I18N::resolve("admin.hooks.syntax.other.title").'</h3>
                    '.I18N::resolveArgs("admin.hooks.syntax.other.i18n", '<code>&lt;%I18N(token[,arg1[,arg2...]])%&gt;</code>').'<br />
                </div>
            </div>';

            $hookFilters = '
            <div class="pure-g">
                <div class="pure-u-1-2 full-on-mobile hook-filter-objectives">
                    <h2>'.I18N::resolveArgs("admin.section.hooks.objectives.name", '<a class="hook-objective-add hook-filter-add" href="#">', '</a>').'</h2>
                    <p>'.I18N::resolve("setting.hooks.hook_list.filter_mode.name").':</p>
                    <p><select class="hook-mode-objective" name="hook_{%ID%}[filterModeObjective]" disabled>
                        <option value="whitelist">'.I18N::resolve("setting.hooks.hook_list.filter_mode.objective.option.whitelist.name").'</option>
                        <option value="blacklist">'.I18N::resolve("setting.hooks.hook_list.filter_mode.objective.option.blacklist.name").'</option>
                    </select></p>
                </div>
                <div class="pure-u-1-2 full-on-mobile hook-filter-rewards">
                    <h2>'.I18N::resolveArgs("admin.section.hooks.rewards.name", '<a class="hook-reward-add hook-filter-add" href="#">', '</a>').'</h2>
                    <p>'.I18N::resolve("setting.hooks.hook_list.filter_mode.name").':</p>
                    <p><select class="hook-mode-reward" name="hook_{%ID%}[filterModeReward]" disabled>
                        <option value="whitelist">'.I18N::resolve("setting.hooks.hook_list.filter_mode.reward.option.whitelist.name").'</option>
                        <option value="blacklist">'.I18N::resolve("setting.hooks.hook_list.filter_mode.reward.option.blacklist.name").'</option>
                    </select></p>
                </div>
            </div>';
        ?>
        var html;
        if (type == "json") {
            html = <?php
                $node = '
                    <div class="hook-instance" data-hook-id="{%ID%}">
                        <div class="hook-head">
                            <span class="hook-action">'.I18N::resolve("setting.hooks.add.type.option.json").'</span> &rarr; <span class="hook-domain">'.I18N::resolve("setting.hooks.hook_list.domain.unknown").'</span><br />
                            '.$hookSummary.'
                        </div>
                        <div class="hook-body hidden-by-default">
                            <input type="hidden" name="hook_{%ID%}[type]" value="json">
                            '.$hookActions.'
                            <h2>'.I18N::resolve("admin.section.hooks.settings.name").'</h2>
                            <div class="pure-g">
                                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.webhook_url.name").':</p></div>
                                <div class="pure-u-2-3 full-on-mobile"><p><input type="text" class="hook-target" name="hook_{%ID%}[target]" data-uri-scheme="http" data-validate-as="http-uri"></p></div>
                            </div>
                            '.$hookCommonSettings.'
                            <h2>'.I18N::resolve("admin.section.hooks.body.json.name").'</h2>
                            '.$hookSyntaxHelp.'
                            <textarea class="hook-payload" name="hook_{%ID%}[body]" rows="8" data-validate-as="json"></textarea>
                            '.$hookFilters.'
                        </div>
                    </div>
                ';
                echo json_encode($node);
            ?>;
        } else if (type == "telegram") {
            html = <?php
                $node = '
                    <div class="hook-instance" data-hook-id="{%ID%}">
                        <div class="hook-head">
                            <span class="hook-action">'.I18N::resolve("setting.hooks.add.type.option.telegram").'</span> &rarr; <span class="hook-domain">'.I18N::resolve("setting.hooks.hook_list.domain.unknown").'</span><br />
                            '.$hookSummary.'
                        </div>
                        <div class="hook-body hidden-by-default">
                            <input type="hidden" name="hook_{%ID%}[type]" value="telegram">
                            '.$hookActions.'
                            <h2>'.I18N::resolve("admin.section.hooks.settings.name").'</h2>
                            <div class="pure-g">
                                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.tg.bot_token.name").':</p></div>
                                <div class="pure-u-2-3 full-on-mobile"><p><input type="text" class="hook-tg-bot-token" name="hook_{%ID%}[tg][bot_token]" data-validate-as="regex-string" data-validate-regex="^\d+:[A-Za-z\d]+$"></p></div>
                            </div>
                            <div class="pure-g">
                                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.webhook_url.name").':</p></div>
                                <div class="pure-u-2-3 full-on-mobile"><p><select class="hook-target" name="hook_{%ID%}[target]" data-uri-scheme="tg" data-validate-as="tg-uri">
                                    <optgroup label="'.I18N::resolve("setting.hooks.hook_list.tg.webhook_url.option.current").'" class="hook-target-current-group">
                                        <option value="" selected></option>
                                    </optgroup>
                                    <optgroup label="'.I18N::resolve("setting.hooks.hook_list.tg.webhook_url.option.other").'">
                                        <option value="_select">&lt; '.I18N::resolve("setting.hooks.hook_list.tg.webhook_url.option.select").' &gt;</option>
                                    </optgroup>
                                </select></p></div>
                            </div>
                            <div class="pure-g">
                                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.tg.parse_mode.name").':</p></div>
                                <div class="pure-u-2-3 full-on-mobile"><p><select class="hook-tg-parse-mode" name="hook_{%ID%}[tg][parse_mode]">
                                    <option value="text">'.I18N::resolve("setting.hooks.hook_list.tg.parse_mode.option.text").'</option>
                                    <option value="md">'.I18N::resolve("setting.hooks.hook_list.tg.parse_mode.option.md").'</option>
                                    <option value="html">'.I18N::resolve("setting.hooks.hook_list.tg.parse_mode.option.html").'</option>
                                </select></p></div>
                            </div>
                            <div class="pure-g">
                                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.tg.disable_web_page_preview.name").':</p></div>
                                <div class="pure-u-2-3 full-on-mobile"><p><label for="hook-bool-disable_web_page_preview-{%ID%}"><input type="checkbox" id="hook-bool-disable_web_page_preview-{%ID%}" class="hook-tg-disable-web-page-preview" name="hook_{%ID%}[tg][disable_web_page_preview]"> '.I18N::resolve("setting.hooks.hook_list.tg.disable_web_page_preview.label").'</label></p></div>
                            </div>
                            <div class="pure-g">
                                <div class="pure-u-1-3 full-on-mobile"><p>'.I18N::resolve("setting.hooks.hook_list.tg.disable_notification.name").':</p></div>
                                <div class="pure-u-2-3 full-on-mobile"><p><label for="hook-bool-disable_notification-{%ID%}"><input type="checkbox" id="hook-bool-disable_notification-{%ID%}" class="hook-tg-disable-notification" name="hook_{%ID%}[tg][disable_notification]"> '.I18N::resolve("setting.hooks.hook_list.tg.disable_notification.label").'</label></p></div>
                            </div>
                            '.$hookCommonSettings.'
                            <h2 class="hook-body-header">'.I18N::resolve("admin.section.hooks.body.text.name").'</h2>
                            '.$hookSyntaxHelp.'
                            <textarea class="hook-payload" name="hook_{%ID%}[body]" rows="8"></textarea>
                            '.$hookFilters.'
                        </div>
                    </div>
                ';
                echo json_encode($node);
            ?>;
        };

        html = html.split("{%ID%}").join(id);

        var node = $.parseHTML(html);
        return node;
    }

    $(".hook-list").on("click", ".hook-head", function() {
        $(this).parent().find(".hook-body").toggle();
    });

    $(".hook-list").on("input", ".hook-target", function() {
        var type = $(this).attr("data-uri-scheme");
        var url = "?";

        switch (type) {
            case "http":
                url = $(this).val().split("/");
                if (url.length >= 3) {
                    url = url[2];
                } else {
                    url = "?";
                }
                break;
            case "tg":
                url = $(this).val().split("?to=");
                if (url.length >= 2) {
                    url = url[1];
                } else {
                    url = "?";
                }
                break;
        }

        $(this).closest(".hook-instance").find(".hook-domain").text(url);
        return true;
    });

    $(".hook-list").on("change", 'select[data-uri-scheme="tg"].hook-target', function() {
        if ($(this).val() == "_select") {
            $(this)[0].selectedIndex = 0;
            $(this).trigger("input");
            var token = $(this).closest(".hook-instance").find(".hook-tg-bot-token").val();
            if (token == "") {
                alert(resolveI18N("admin.clientside.hooks.tg.xhr.groups.failed.empty_token"));
                return;
            }
            $("#select-tg-group-submit").off();
            $("#hooks-tg-groups-working").fadeIn(150);
            var hook = $(this).closest(".hook-instance");
            $.getJSON("../xhr/tg-groups.php?token=" + encodeURI(token), function(data) {
                $("#select-tg-group-options").empty();
                var isEmpty = true;
                for (var id in data.groups) {
                    if (data.groups.hasOwnProperty(id)) {
                        isEmpty = false;
                        $("#select-tg-group-options").append('<option value="tg://send?to=' + id + '">' + data.groups[id] + '</option>');
                    }
                }
                if (isEmpty) {
                    $("#hooks-tg-groups-working").fadeOut(150);
                    alert(resolveI18N("admin.clientside.hooks.tg.xhr.groups.failed.no_groups"));
                    return;
                }
                $("#select-tg-group-submit").on("click", function() {
                    var target = $("#select-tg-group-options").val();
                    hook.find(".hook-target-current-group").empty();
                    hook.find(".hook-target-current-group").append('<option value="' + target + '">' + target + '</option>');
                    hook.find(".hook-target").val(target);
                    hook.find(".hook-target").trigger("input");
                    $("#hooks-tg-groups-overlay").fadeOut(150);
                });
                $("#hooks-tg-groups-overlay").fadeIn(150);
                $("#hooks-tg-groups-working").fadeOut(150);
            }).fail(function(xhr) {
                $("#hooks-tg-groups-working").fadeOut(150);
                var data = xhr.responseJSON;
                alert(resolveI18N(data.reason));
            });
        }
    });

    $(".hook-list").on("change", '.hook-tg-parse-mode', function() {
        $(this).closest(".hook-instance").find(".hook-body-header").text(resolveI18N("admin.section.hooks.body." + $(this).val() + ".name"));
        var payload = $(this).closest(".hook-instance").find(".hook-payload");
        switch ($(this).val()) {
            case "html":
                payload.attr("data-validate-as", "html");
                break;
            default:
                payload.attr("data-validate-as", "text");
                break;
        }
        payload.trigger("input");
    });

    $(".hook-list").on("click", ".hook-show-help", function() {
        var help = $(this).closest(".hook-body").find(".hook-syntax-help");
        help.toggle();
        if (help.is(":visible")) {
            $(this).text(resolveI18N("admin.clientside.hooks.syntax.hide"));
        } else {
            $(this).text(resolveI18N("admin.clientside.hooks.syntax.show"));
        }
        return false;
    });

    $(".hook-list").on("click", ".hook-objective-add", function() {
        editObjective(true, $(this));
        return false;
    });

    $(".hook-list").on("click", ".hook-objective-edit", function() {
        editObjective(false, $(this));
    });

    $(".hook-list").on("click", ".hook-objective-delete", function() {
        var hook = $(this).closest(".hook-instance");
        $(this).closest(".hook-filter").remove();
        if (hook.find(".hook-filter-objectives .hook-filter").length == 0) {
            hook.find(".hook-mode-objective").prop("disabled", true);
        }
        updateSummary(hook);
    });

    $(".hook-list").on("click", ".hook-mode-objective", function() {
        updateSummary($(this));
    });

    $("#update-hook-objective-cancel").on("click", function() {
        $("#update-hook-objective-submit").off();
        $("#hooks-update-objective-overlay").fadeOut(150);
    });

    $(".hook-list").on("click", ".hook-reward-add", function() {
        editReward(true, $(this));
        return false;
    });

    $(".hook-list").on("click", ".hook-reward-edit", function() {
        editReward(false, $(this));
    });

    $(".hook-list").on("click", ".hook-reward-delete", function() {
        var hook = $(this).closest(".hook-instance");
        $(this).closest(".hook-filter").remove();
        if (hook.find(".hook-filter-rewards .hook-filter").length == 0) {
            hook.find(".hook-mode-reward").prop("disabled", true);
        }
        updateSummary(hook);
    });

    $(".hook-list").on("click", ".hook-mode-reward", function() {
        updateSummary($(this));
    });

    $("#update-hook-reward-cancel").on("click", function() {
        $("#update-hook-reward-submit").off();
        $("#hooks-update-reward-overlay").fadeOut(150);
    });

    var objectives = <?php echo json_encode(Research::OBJECTIVES); ?>;
    var rewards = <?php echo json_encode(Research::REWARDS); ?>;
    $(".hook-list").on("change", ".hook-actions", function() {
        if ($(this).val() == "delete") {
            $(this).css("border", "1px solid red");
            $(this).css("color", "red");
            $(this).css("margin-right", "");
        } else if ($(this).val() == "enable") {
            var color = "<?php echo Config::get("themes/color/admin"); ?>" == "dark" ? "lime" : "green";
            $(this).css("border", "1px solid " + color);
            $(this).css("color", color);
            $(this).css("margin-right", "");
        } else if ($(this).val() == "disable") {
            $(this).css("border", "1px solid darkorange");
            $(this).css("color", "darkorange");
            $(this).css("margin-right", "");
        } else {
            $(this).css("border", "");
            $(this).css("color", "");
            $(this).css("margin-right", "");
        }
    });

    var hooks = <?php
        $hooks = Config::get("webhooks");
        if ($hooks === null) $hooks = array();

        echo json_encode($hooks);
    ?>

    var presets = <?php
        echo json_encode($presets);
    ?>

    for (var i = 0; i < hooks.length; i++) {
        var hook = hooks[i];
        var node = $(createHookNode(hook.type, hook.id));

        node.find(".hook-target").val(hook.target);
        node.find(".hook-lang").val(hook.language);
        node.find(".hook-icon-set").val(hook.icons);
        node.find(".hook-payload").val(hook.body);
        node.find(".hook-mode-objective").val(hook["filter-mode"].objectives);
        node.find(".hook-mode-reward").val(hook["filter-mode"].rewards);

        if (hook.hasOwnProperty("geofence") && hook.geofence !== null) {
            var fenceStr = "";
            for (var i = 0; i < hook.geofence.length; i++) {
                fenceStr += hook.geofence[i][0] + "," + hook.geofence[i][1] + "\n";
            }
            if (fenceStr.length > 0) {
                fenceStr = fenceStr.substring(0, fenceStr.length - 1);
            }
            node.find(".hook-geofence").val(fenceStr);
        }

        if (hook.active) {
            node.find("select.hook-actions > option[value=enable]").remove();
        } else {
            node.find("select.hook-actions > option[value=disable]").remove();
        }

        if (hook.type === "telegram") {
            node.find(".hook-tg-bot-token").val(hook.options["bot-token"]);

            node.find(".hook-target-current-group").empty();
            node.find(".hook-target-current-group").append('<option value="' + hook.target + '">' + hook.target + '</option>');
            node.find(".hook-target").val(hook.target);

            node.find(".hook-tg-parse-mode").val(hook.options["parse-mode"]);
            node.find(".hook-tg-disable-web-page-preview").prop("checked", hook.options["disable-web-page-preview"]);
            node.find(".hook-tg-disable-notification").prop("checked", hook.options["disable-notification"]);
        }

        for (var j = 0; j < hook.objectives.length; j++) {
            var filter = $(getObjectiveFilterNode(hook.id));
            filter.find("input[type=hidden].hook-objective-type").val(hook.objectives[j].type);
            filter.find("input[type=hidden].hook-objective-params").val(JSON.stringify(hook.objectives[j].params));
            filter.find("span.hook-objective-text").text(resolveObjective({
                type: hook.objectives[j].type,
                params: hook.objectives[j].params
            }));
            node.find(".hook-filter-objectives").append(filter);
            filter.parent().find(".hook-mode-objective").prop("disabled", false);
        }

        for (var j = 0; j < hook.rewards.length; j++) {
            var filter = $(getRewardFilterNode(hook.id));
            filter.find("input[type=hidden].hook-reward-type").val(hook.rewards[j].type);
            filter.find("input[type=hidden].hook-reward-params").val(JSON.stringify(hook.rewards[j].params));
            filter.find("span.hook-reward-text").text(resolveReward({
                type: hook.rewards[j].type,
                params: hook.rewards[j].params
            }));
            node.find(".hook-filter-rewards").append(filter);
            filter.parent().find(".hook-mode-reward").prop("disabled", false);
        }

        updateSummary(node);
        $(hook.active ? "#active-hooks-list" : "#inactive-hooks-list").append(node);
        node.find(".hook-target").trigger("input");

        if (hook.type === "telegram") {
            node.find(".hook-tg-parse-mode").trigger("change");
        }

        viewTheme(hook.id + "-icon-selector", document.getElementById(hook.id + "-icon-selector").value);
        document.getElementById(hook.id + "-icon-selector").addEventListener("change", function() {
            viewTheme(hook.id + "-icon-selector", document.getElementById(hook.id + "-icon-selector").value);
        });
    }
</script>