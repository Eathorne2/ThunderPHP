<?php namespace PluginManager; if(!defined('ROOT')) exit('No direct script access allowed');
$url = get_value('plugin_manager_marketplace_url') ?? '';
$adminRoute = (get_value()['admin_route'] ?? 'admin');
?>
<div class="pm-wrap">
    <div class="pm-head"><div><h1>Marketplace Settings</h1><p>Configure the remote JSON feed used by the marketplace.</p></div><a class="pm-btn" href="<?=ROOT?>/<?=$adminRoute?>/plugins/marketplace">Back</a></div>
    <form method="post" class="pm-panel">
        <?=csrf()?>
        <input type="hidden" name="pm_action" value="save_marketplace_settings">
        <label>Marketplace JSON URL</label>
        <input type="url" name="marketplace_url" value="<?=e($url)?>" placeholder="https://thunderphp.com/marketplace/plugins.json" class="pm-input">
        <p class="pm-muted">The JSON feed should contain a top-level <code>plugins</code> array with plugin metadata, download URLs, and optional SHA-256 checksums.</p>
        <button class="pm-btn pm-btn-primary">Save Marketplace URL</button>
    </form>
    <div class="pm-panel">
        <h2>Example Feed</h2>
<pre class="pm-readme">{
  "marketplace": "ThunderPHP Marketplace",
  "version": "1.0.0",
  "plugins": [
    {
      "id": "basic-blog",
      "name": "Basic Blog",
      "version": "1.0.0",
      "description": "A simple blog plugin.",
      "category": "Content",
      "type": "free",
      "core_requires": "^1.0.0",
      "requires": [],
      "optional": ["media-uploader"],
      "thumbnail": "https://example.com/images/basic-blog.jpg",
      "readme": "https://example.com/readmes/basic-blog.md",
      "download_url": "https://example.com/packages/basic-blog-1.0.0.zip",
      "checksum": "sha256-hash-here"
    }
  ]
}</pre>
    </div>
</div>
