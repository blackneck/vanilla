<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::session();
include_once $this->fetchViewLocation('helper_functions', 'discussions', 'vanilla');

echo '<h1 class="H HomepageTitle">'.
    AdminCheck(NULL, array('', ' ')).
    $this->data('Title').
    '</h1>';

$Description = $this->data('Category.Description', $this->Description());
echo wrapIf(Gdn_Format::htmlFilter($Description), 'div', array('class' => 'P PageDescription'));

$this->fireEvent('AfterPageTitle');

$subtreeView = $this->fetchViewLocation('subtree', 'categories', 'vanilla', false);
if ($subtreeView) {
    include $subtreeView;
} elseif (isset($this->CategoryModel) && $this->CategoryModel instanceof CategoryModel) {
    $childCategories = $this->data('CategoryTree', []);
    $this->CategoryModel->joinRecent($childCategories);
    if ($childCategories) {
        include($this->fetchViewLocation('helper_functions', 'categories', 'vanilla'));
        if (c('Vanilla.Categories.Layout') === 'table') {
            writeCategoryTable($childCategories);
        } else {
            writeCategoryList($childCategories);
        }
    }
}

$this->fireEvent('AfterCategorySubtree');

$PagerOptions = array('Wrapper' => '<span class="PagerNub">&#160;</span><div %1$s>%2$s</div>', 'RecordCount' => $this->data('CountDiscussions'), 'CurrentRecords' => $this->data('Discussions')->numRows());
if ($this->data('_PagerUrl'))
    $PagerOptions['Url'] = $this->data('_PagerUrl');

echo '<div class="PageControls Top">';
PagerModule::write($PagerOptions);
echo Gdn_Theme::Module('NewDiscussionModule', $this->data('_NewDiscussionProperties', array('CssClass' => 'Button Action Primary')));
$this->fireEvent('PageControls');
echo '</div>';

if ($this->DiscussionData->numRows() > 0 || (isset($this->AnnounceData) && is_object($this->AnnounceData) && $this->AnnounceData->numRows() > 0)) {
    ?>
    <ul class="DataList Discussions">
        <?php include($this->fetchViewLocation('discussions', 'Discussions', 'Vanilla')); ?>
    </ul>
    <?php

    echo '<div class="PageControls Bottom">';
    PagerModule::write($PagerOptions);
    echo Gdn_Theme::Module('NewDiscussionModule', $this->data('_NewDiscussionProperties', array('CssClass' => 'Button Action Primary')));
    echo '</div>';

} else {
    ?>
    <div class="Empty"><?php echo t('No discussions were found.'); ?></div>
<?php
}
