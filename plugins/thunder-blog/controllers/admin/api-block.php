<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

blog_require_any_permission(['create-blog-posts', 'edit-blog-posts', 'manage-blog-pages', 'manage-blog-designs']);
$slug = (string) get_param('slug');
$manifest = blog_block_manifest($slug);
if (!$manifest) blog_json_response(['ok' => false, 'message' => 'Block not found.'], 404);
blog_json_response(['ok' => true, 'data' => $manifest]);
