<?php
namespace ThunderBlog;
do_action('thunder_blog_before_page', ['post' => $post, 'author' => $author]);
?>
<article class="thunder-blog__designed-page">
    <?= blog_render_compiled_content($post, ['author' => $author, 'page' => $post]) ?>
</article>
<?php do_action('thunder_blog_after_page', ['post' => $post, 'author' => $author]); ?>
