<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

if (ASTROID_JOOMLA_VERSION < 4) {
   JHtml::_('behavior.caption');
}

$app = Factory::getApplication();

$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayContent = trim(implode("\n", $results));

?>
<div class="blog<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Blog">
   <?php if ($this->params->get('show_page_heading', 1)) : ?>
      <div class="item-title">
         <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
      </div>
   <?php endif; ?>

   <?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
      <div class="item-title">
         <h2> <?php echo $this->escape($this->params->get('page_subheading')); ?>
            <?php if ($this->params->get('show_category_title')) : ?>
               <span class="subheading-category"><?php echo $this->category->title; ?></span>
            <?php endif; ?>
         </h2>
      </div>
   <?php endif; ?>

   <?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
      <?php $this->category->tagLayout = new FileLayout('joomla.content.tags'); ?>
      <?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
   <?php endif; ?>

   <?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
      <div class="category-desc clearfix">
         <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
            <img src="<?php echo $this->category->getParams()->get('image'); ?>" alt="<?php echo htmlspecialchars($this->category->getParams()->get('image_alt')); ?>" />
         <?php endif; ?>
         <?php if ($this->params->get('show_description') && $this->category->description) : ?>
            <?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
         <?php endif; ?>
      </div>
   <?php endif; ?>

   <?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
      <?php if ($this->params->get('show_no_articles', 1)) : ?>
         <p><?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?></p>
      <?php endif; ?>
   <?php endif; ?>

   <?php $leadingcount = 0; ?>
   <?php if (!empty($this->lead_items)) : ?>
      <div class="items-leading clearfix">
         <?php foreach ($this->lead_items as &$item) : ?>
            <div class="article-wraper <?php echo $this->params->get('blog_class_leading'); ?>">
               <div class="article-wraper-inner">
                  <article class="item leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?><?php echo $item->featured ? ' item-featured' : ''; ?>" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
                     <?php
                     $this->item = &$item;
                     echo $this->loadTemplate('item');
                     ?>
                  </article>
               </div>
            </div>
            <?php $leadingcount++; ?>
         <?php endforeach; ?>
      </div><!-- end items-leading -->
   <?php endif; ?>

   <?php
   $introcount = (count($this->intro_items));
   $counter = 0;
   $columns = ASTROID_JOOMLA_VERSION > 3 ? $this->params->get('num_columns', 1) : $this->columns;
   ?>

   <?php if (!empty($this->intro_items)) : ?>
      <?php foreach ($this->intro_items as $key => &$item) : ?>
         <?php $rowcount = ((int) $key % (int) $columns) + 1; ?>
         <?php if ($rowcount == 1) : ?>
            <?php $row = $counter / $columns; ?>
            <div class="items-row <?php echo 'row-' . $row; ?> row clearfix">
            <?php endif; ?>
            <div class="col-lg-<?php echo round((12 / $columns)); ?>">
               <div class="article-wraper <?php echo $this->params->get('blog_class', ''); ?>">
                  <div class="article-wraper-inner">
                     <article class="item column-<?php echo $rowcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?><?php echo $item->featured ? ' item-featured' : ''; ?>" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
                        <?php
                        $this->item = &$item;
                        echo $this->loadTemplate('item');
                        ?>
                     </article>
                  </div>
               </div>
               <?php $counter++; ?>
            </div>
            <?php if (($rowcount == $columns) or ($counter == $introcount)) : ?>
            </div>
         <?php endif; ?>
      <?php endforeach; ?>
   <?php endif; ?>

   <?php if (!empty($this->link_items)) : ?>
      <?php echo $this->loadTemplate('links'); ?>
   <?php endif; ?>

   <?php if (!empty($this->children[$this->category->id]) && $this->maxLevel != 0) : ?>
      <div class="cat-children">
         <?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
            <h3> <?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?> </h3>
         <?php endif; ?>
         <?php echo $this->loadTemplate('children'); ?> </div>
   <?php endif; ?>
   <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
      <div class="pagination-wrapper">
         <?php echo $this->pagination->getPagesLinks(); ?>
         <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
         <?php endif; ?>
      </div>
   <?php endif; ?>
</div>