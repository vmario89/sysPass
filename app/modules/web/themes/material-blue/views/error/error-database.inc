<?php
/**
 * @var \SP\Core\UI\ThemeIcons $icons
 * @var \SP\Mvc\View\Template  $this
 */
?>
<div id="actions" align="center">
    <?php include $this->includePartial('error-list'); ?>

    <div class="buttons">
        <button id="btnBack" class="btn-back mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
            <i class="material-icons"
               title="<?php echo __('Back'); ?>"><?php echo $icons->getIconBack()->getIcon(); ?></i>
            <?php echo __('Back'); ?>
        </button>
        <a class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" href="index.php?r=install">
            <?php echo __('Install'); ?>
            <i class="material-icons"
               title="<?php echo __('Install'); ?>"><?php echo $icons->getIconPlay()->getIcon(); ?></i>
        </a>
    </div>
</div>