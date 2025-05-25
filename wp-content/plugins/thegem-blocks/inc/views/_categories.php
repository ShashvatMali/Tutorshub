<?php $this->initTemplateType() ?>
<?php if (!empty($this->getCategories())): ?>
    <ul class="thegem-blocks-categories-list">
        <li>
            <a data-name="all" href="#">
                <i class="tgb-icon-presentation"></i><?= __('All', 'thegem'); ?>
                <span><?= esc_html($this->getCountTotalTemplates()) ?></span>
            </a>
        </li>
        <li>
            <a data-name="favorite" href="#">
                <i class="tgb-icon-star-outline"></i><?= __('My Favorites', 'thegem'); ?>
                <span class="favorite-cnt"><?= esc_html(count($this->getFavorites())); ?></span>
            </a>
        </li>
        <?php foreach ($this->getCategories() as $category): ?>
            <?php if (($category['name'] == 'custom-title' && $this->templateType !='title') || 
                      ($category['name'] == 'headers' && $this->templateType !='header') ||
                      ($category['name'] == 'single-product' && $this->templateType !='single-product') ||
                      ($category['name'] == 'mega-menu' && $this->templateType !='megamenu') ||
                      ($category['name'] == 'shop-categories' && $this->templateType !='product-archive') ||
                      ($category['name'] == 'blog-categories' && $this->templateType !='blog-archive') ||
                      ($category['name'] == 'search-results' && $this->templateType !='blog-archive') ||
                      ($category['name'] == 'cart' && $this->templateType !='cart') ||
                      ($category['name'] == 'checkout' && $this->templateType !='checkout') ||
                      ($category['name'] == 'purchase-summary' && $this->templateType !='checkout-thanks') ||
                      ($category['name'] == 'blog-posts' && $this->templateType !='single-post') ||
                      ($category['name'] == 'single-projects' && $this->templateType !='portfolio') ||
                      ($category['name'] == 'loop-item' && $this->templateType !='loop-item')
                  ) continue; ?>
            <li>
                <a href="#"
                   data-name="<?= esc_html($category['name']); ?>"
                   data-count-dark="<?= esc_html($category['count_dark']) ?>"
                   data-count-multicolor="<?= esc_html($category['count_multicolor']) ?>"
                >
                    <?= esc_html($category['title']); ?>
                    <span><?= esc_html($category[$this->isDarkMode() ? 'count_dark' : 'count_multicolor']); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
