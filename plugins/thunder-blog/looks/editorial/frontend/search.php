<?php

namespace ThunderBlog;

require __DIR__ . '/index.php';
do_action('thunder_blog_after_search', ['query' => $search_query ?? '', 'posts' => $posts ?? [], 'pagination' => $pagination ?? []]);
