<div id="thegem-blocks" class="thegem-blocks-box">
    <div class="thegem-blocks-control-mode">
        <label class="thegem-blocks-switch thegem-blocks-switch-mode">
            <?= __('Multicolor', 'thegem') ?>
            <input name="mode" <?= ($this->isDarkMode() ? 'checked' : '') ?> id="thegem-blocks-mode-input" type="checkbox"/><span class="thegem-blocks-switch-indicator"></span>
            <?= __('Dark', 'thegem') ?>
        </label>
    </div>

    <div class="thegem-blocks-left-box">
        <div class="thegem-blocks-include-media">
            <label for="thegem-blocks-include-media-input">
                <input name="include_media" <?= ($this->isIncludeMedia() ? 'checked' : '') ?> type="checkbox" id="thegem-blocks-include-media-input"><?= __('Include Demo Media', 'thegem'); ?>
            </label>
        </div>

        <?php include '_categories.php'; ?>
    </div>

    <div class="thegem-blocks-right-box">
        <?php if (!empty($this->getTemplates())): ?>
            <div class="thegem-blocks-template-items">
            </div>
        <?php endif; ?>
    </div>
</div>
