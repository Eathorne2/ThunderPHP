<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

blog_require_permission('use-blog-shortcode-generator');
set_value('thunder_blog_admin', [
    'layouts' => blog_shortcode_layouts(),
    'orders' => blog_shortcode_orders(),
    'categories' => blog_categories(),
]);
