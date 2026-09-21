<?php

/**
 * This file is part of the ThunderPHP Framework.
 * It contains the Pager class used for general pagination
 *
 * @package ThunderPHP
 * @version 1.0.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */

namespace Core;

defined('ROOTPATH') or die("Direct script access denied");

/**
 * Pager class is used to make pagination easier
 */
class Pager
{
	/** Stores generated links */
	public array $links = [];

	/** Stores the current page number */
	public int $page_number = 1;

	/** Get this offset to use in mysql queries offset value */
	public int $offset = 0;

	/** Used to edit pagination buttons' CSS styles */
	public array $css = [
		'wrapper' => 'pager-wrapper',
		'summary' => 'pager-summary',
		'nav' => '',
		'ul' => 'pagination',
		'li' => 'page-item',
		'a' => 'page-link',
		'active' => 'active',
		'disabled' => 'disabled',
		'ellipsis' => 'page-item disabled',
		'ellipsis_inner' => 'page-link',
	];

	/** Used to edit button labels */
	public array $labels = [
		'first' => 'First',
		'prev' => 'Previous',
		'next' => 'Next',
		'last' => 'Last',
		'ellipsis' => '...',
		'summary' => 'Showing {from}-{to} of {total}',
		'empty' => 'Showing 0-0 of 0',
	];

	/** @ignore */
	protected int $limit = 10;

	/** @ignore */
	protected int $extras = 1;

	/** @ignore */
	protected int $start = 1;

	/** @ignore */
	protected int $end = 1;

	/** @ignore */
	protected int $total_rows = 0;

	/** @ignore */
	protected int $total_pages = 1;

	/** @ignore */
	protected string $page_key = 'page';

	/** @ignore */
	protected $renderer = null;

	/** @ignore */
	protected bool $summary_enabled = true;

	/** @ignore */
	protected bool $render_if_single_page = false;

	/** @ignore */
	protected bool $show_first_last = true;

	/** @ignore */
	protected bool $show_prev_next = true;

	/** @ignore */
	protected bool $use_ellipsis = true;

	/** @ignore */
	protected ?string $base_url = null;

	/**
	 * Instantiates the pager class with optional page limit.
	 * $extras is for how many page numbers to show on each side of the current page.
	 *
	 * @param int $limit How many items to show per page. The default is 10 items.
	 * @param int $extras How many page numbers to show on each side of the current page.
	 * @param int $total_rows Optional total number of rows.
	 */
	public function __construct(int $limit = 10, int $extras = 1, int $total_rows = 0)
	{
		$this->limit = $limit > 0 ? $limit : 10;
		$this->extras = $extras >= 0 ? $extras : 1;
		$this->page_number = $this->detect_page_number();

		$this->set_total_rows($total_rows);
		$this->recalculate();
		$this->build_links();
	}

	/**
	 * Set the total number of rows in the dataset.
	 */
	public function set_total_rows(int $total_rows): static
	{
		$this->total_rows = $total_rows >= 0 ? $total_rows : 0;
		$this->total_pages = max(1, (int) ceil($this->total_rows / $this->limit));

		if ($this->page_number > $this->total_pages) {
			$this->page_number = $this->total_pages;
		}

		$this->offset = ($this->page_number - 1) * $this->limit;
		$this->recalculate();
		$this->build_links();

		return $this;
	}

	/**
	 * Set a custom page query key. Default is 'page'.
	 */
	public function set_page_key(string $key): static
	{
		$key = trim($key);

		if ($key !== '') {
			$this->page_key = $key;
			$this->page_number = $this->detect_page_number();
			$this->recalculate();
			$this->build_links();
		}

		return $this;
	}

	/**
	 * Set a custom base URL.
	 * Example:
	 * ROOT.'/posts'
	 * ROOT.'/admin/users?role=admin'
	 */
	public function set_base_url(string $url): static
	{
		$url = trim($url);

		if ($url !== '') {
			$this->base_url = $url;
			$this->build_links();
		}

		return $this;
	}

	/**
	 * Get the configured base URL if any.
	 */
	public function get_base_url(): ?string
	{
		return $this->base_url;
	}

	/**
	 * Set custom button labels.
	 */
	public function set_labels(array $labels): static
	{
		$this->labels = array_merge($this->labels, $labels);
		return $this;
	}

	/**
	 * Set custom CSS classes.
	 */
	public function set_css(array $css): static
	{
		$this->css = array_merge($this->css, $css);
		return $this;
	}

	/**
	 * Set a custom renderer callback.
	 * The callback receives the pagination items and the pager instance.
	 */
	public function set_renderer(callable $callback): static
	{
		$this->renderer = $callback;
		return $this;
	}

	/**
	 * Show or hide the summary text.
	 */
	public function show_summary(bool $state = true): static
	{
		$this->summary_enabled = $state;
		return $this;
	}

	/**
	 * Check whether summary is enabled.
	 */
	public function is_summary_enabled(): bool
	{
		return $this->summary_enabled;
	}

	/**
	 * Render even if there is only one page.
	 */
	public function render_if_single_page(bool $state = true): static
	{
		$this->render_if_single_page = $state;
		return $this;
	}

	/**
	 * Show or hide first and last buttons.
	 */
	public function show_first_last(bool $state = true): static
	{
		$this->show_first_last = $state;
		return $this;
	}

	/**
	 * Show or hide previous and next buttons.
	 */
	public function show_prev_next(bool $state = true): static
	{
		$this->show_prev_next = $state;
		return $this;
	}

	/**
	 * Enable or disable ellipsis mode.
	 */
	public function use_ellipsis(bool $state = true): static
	{
		$this->use_ellipsis = $state;
		return $this;
	}

	/**
	 * Apply a built-in CSS preset.
	 */
	public function preset(string $name): static
	{
		$name = strtolower(trim($name));

		if ($name === 'bootstrap' || $name === 'bootstrap5') {
			$this->css = array_merge($this->css, [
				'wrapper' => 'pager-wrapper',
				'summary' => 'small text-muted mb-2',
				'nav' => '',
				'ul' => 'pagination',
				'li' => 'page-item',
				'a' => 'page-link',
				'active' => 'active',
				'disabled' => 'disabled',
				'ellipsis' => 'page-item disabled',
				'ellipsis_inner' => 'page-link',
			]);
		} elseif ($name === 'minimal') {
			$this->css = array_merge($this->css, [
				'wrapper' => 'pager pager-minimal',
				'summary' => 'pager-summary',
				'nav' => 'pager-nav',
				'ul' => 'pager-list',
				'li' => 'pager-item',
				'a' => 'pager-link',
				'active' => 'is-active',
				'disabled' => 'is-disabled',
				'ellipsis' => 'pager-item is-ellipsis',
				'ellipsis_inner' => 'pager-link',
			]);
		}

		return $this;
	}

	/**
	 * Get the current page number.
	 */
	public function current_page(): int
	{
		return $this->page_number;
	}

	/**
	 * Get the current offset.
	 */
	public function get_offset(): int
	{
		return $this->offset;
	}

	/**
	 * Alias for get_offset().
	 */
	public function offset(): int
	{
		return $this->get_offset();
	}

	/**
	 * Get the per-page limit.
	 */
	public function get_limit(): int
	{
		return $this->limit;
	}

	/**
	 * Alias for get_limit().
	 */
	public function limit(): int
	{
		return $this->get_limit();
	}

	/**
	 * Get total pages.
	 */
	public function get_total_pages(): int
	{
		return $this->total_pages;
	}

	/**
	 * Get total rows.
	 */
	public function get_total_rows(): int
	{
		return $this->total_rows;
	}

	/**
	 * Check if there is a previous page.
	 */
	public function has_prev(): bool
	{
		return $this->page_number > 1;
	}

	/**
	 * Check if there is a next page.
	 */
	public function has_next(): bool
	{
		return $this->page_number < $this->total_pages;
	}

	/**
	 * Get the previous page number.
	 */
	public function prev_page(): int
	{
		return max(1, $this->page_number - 1);
	}

	/**
	 * Get the next page number.
	 */
	public function next_page(): int
	{
		return min($this->total_pages, $this->page_number + 1);
	}

	/**
	 * Get the first item number currently being shown.
	 */
	public function showing_from(): int
	{
		if ($this->total_rows < 1) {
			return 0;
		}

		return $this->offset + 1;
	}

	/**
	 * Get the last item number currently being shown.
	 */
	public function showing_to(): int
	{
		if ($this->total_rows < 1) {
			return 0;
		}

		return min($this->offset + $this->limit, $this->total_rows);
	}

	/**
	 * Get the showing summary text.
	 */
	public function summary_text(): string
	{
		if ($this->total_rows < 1) {
			return $this->labels['empty'];
		}

		return str_replace(
			['{from}', '{to}', '{total}', '{page}', '{pages}'],
			[
				(string) $this->showing_from(),
				(string) $this->showing_to(),
				(string) $this->total_rows,
				(string) $this->page_number,
				(string) $this->total_pages,
			],
			$this->labels['summary']
		);
	}

	/**
	 * Check whether pager should render.
	 */
	public function should_render(): bool
	{
		if ($this->render_if_single_page) {
			return true;
		}

		return $this->total_pages > 1;
	}

	/**
	 * Generate all page items for rendering.
	 */
	public function items(): array
	{
		$items = [];
		$pages = $this->number_items();

		if ($this->show_first_last) {
			$items[] = [
				'type' => 'first',
				'label' => $this->labels['first'],
				'page' => 1,
				'url' => $this->make_url(1),
				'active' => false,
				'disabled' => !$this->has_prev(),
			];
		}

		if ($this->show_prev_next) {
			$items[] = [
				'type' => 'prev',
				'label' => $this->labels['prev'],
				'page' => $this->prev_page(),
				'url' => $this->make_url($this->prev_page()),
				'active' => false,
				'disabled' => !$this->has_prev(),
			];
		}

		foreach ($pages as $page) {
			if ($page === '...') {
				$items[] = [
					'type' => 'ellipsis',
					'label' => $this->labels['ellipsis'],
					'page' => null,
					'url' => '',
					'active' => false,
					'disabled' => true,
				];
			} else {
				$items[] = [
					'type' => 'number',
					'label' => (string) $page,
					'page' => $page,
					'url' => $this->make_url($page),
					'active' => $page === $this->page_number,
					'disabled' => false,
				];
			}
		}

		if ($this->show_prev_next) {
			$items[] = [
				'type' => 'next',
				'label' => $this->labels['next'],
				'page' => $this->next_page(),
				'url' => $this->make_url($this->next_page()),
				'active' => false,
				'disabled' => !$this->has_next(),
			];
		}

		if ($this->show_first_last) {
			$items[] = [
				'type' => 'last',
				'label' => $this->labels['last'],
				'page' => $this->total_pages,
				'url' => $this->make_url($this->total_pages),
				'active' => false,
				'disabled' => !$this->has_next(),
			];
		}

		return $items;
	}

	/**
	 * Displays the pagination buttons to the user
	 */
	public function display(): void
	{
		if (!$this->should_render()) {
			return;
		}

		$items = $this->items();

		if (is_callable($this->renderer)) {
			echo call_user_func($this->renderer, $items, $this);
			return;
		}

		$wrapper_class = !empty($this->css['wrapper']) ? ' class="' . $this->esc($this->css['wrapper']) . '"' : '';
		$nav_class = !empty($this->css['nav']) ? ' class="' . $this->esc($this->css['nav']) . '"' : '';

		?>
		<div<?=$wrapper_class?>>
			<?php if ($this->summary_enabled): ?>
				<div class="<?=$this->esc($this->css['summary'])?>"><?=$this->esc($this->summary_text())?></div>
			<?php endif; ?>

			<nav<?=$nav_class?> aria-label="Pagination">
				<ul class="<?=$this->esc($this->css['ul'])?>">
					<?php foreach ($items as $item): ?>

						<?php if ($item['type'] === 'ellipsis'): ?>
							<li class="<?=$this->esc($this->css['ellipsis'])?>">
								<span class="<?=$this->esc($this->css['ellipsis_inner'])?>"><?=$this->esc($item['label'])?></span>
							</li>
							<?php continue; ?>
						<?php endif; ?>

						<?php
							$li_classes = trim(
								$this->css['li']
								. ($item['active'] ? ' ' . $this->css['active'] : '')
								. ($item['disabled'] ? ' ' . $this->css['disabled'] : '')
							);
						?>

						<li class="<?=$this->esc($li_classes)?>">
							<?php if ($item['disabled']): ?>
								<span class="<?=$this->esc($this->css['a'])?>"><?=$this->esc($item['label'])?></span>
							<?php else: ?>
								<a
									class="<?=$this->esc($this->css['a'])?>"
									href="<?=$this->esc($item['url'])?>"
									<?=$item['active'] ? ' aria-current="page"' : ''?>
								><?=$this->esc($item['label'])?></a>
							<?php endif; ?>
						</li>

					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
		<?php
	}

	/**
	 * Return the default HTML as a string instead of outputting it.
	 */
	public function render(): string
	{
		ob_start();
		$this->display();
		return ob_get_clean();
	}

	/**
	 * Detect current page number from query string.
	 */
	protected function detect_page_number(): int
	{
		$page_number = !empty($_GET[$this->page_key]) ? (int) $_GET[$this->page_key] : 1;
		return $page_number < 1 ? 1 : $page_number;
	}

	/**
	 * Recalculate internal pagination ranges.
	 */
	protected function recalculate(): void
	{
		$this->offset = ($this->page_number - 1) * $this->limit;

		$this->start = $this->page_number - $this->extras;
		$this->end = $this->page_number + $this->extras;

		if ($this->start < 1) {
			$this->start = 1;
		}

		if ($this->end > $this->total_pages) {
			$this->end = $this->total_pages;
		}
	}

	/**
	 * Build common links.
	 */
	protected function build_links(): void
	{
		$this->links['current'] = $this->make_url($this->page_number);
		$this->links['first'] = $this->make_url(1);
		$this->links['prev'] = $this->make_url($this->prev_page());
		$this->links['next'] = $this->make_url($this->next_page());
		$this->links['last'] = $this->make_url($this->total_pages);
	}

	/**
	 * Build the visible number list, optionally with ellipsis.
	 */
	protected function number_items(): array
	{
		if ($this->total_pages <= 1) {
			return [1];
		}

		if (!$this->use_ellipsis) {
			$items = [];
			for ($x = $this->start; $x <= $this->end; $x++) {
				$items[] = $x;
			}
			return $items;
		}

		$pages = [];
		$window_start = max(1, $this->page_number - $this->extras);
		$window_end = min($this->total_pages, $this->page_number + $this->extras);

		$pages[] = 1;

		if ($window_start > 2) {
			$pages[] = '...';
		}

		for ($x = $window_start; $x <= $window_end; $x++) {
			if ($x !== 1 && $x !== $this->total_pages) {
				$pages[] = $x;
			}
		}

		if ($window_end < $this->total_pages - 1) {
			$pages[] = '...';
		}

		if ($this->total_pages > 1) {
			$pages[] = $this->total_pages;
		}

		return array_values(array_unique($pages, SORT_REGULAR));
	}

	/**
	 * Generate a URL for a specific page number.
	 */
	protected function make_url(int $page): string
	{
		$page = $page < 1 ? 1 : $page;

		if ($this->total_pages > 0 && $page > $this->total_pages) {
			$page = $this->total_pages;
		}

		if (!empty($this->base_url)) {
			return $this->build_url_from_base($page);
		}

		$params = $_GET;
		$route = $params['url'] ?? 'home';

		unset($params['url']);
		$params[$this->page_key] = $page;

		$query_string = http_build_query($params);

		$url = rtrim(ROOT, '/') . '/' . ltrim($route, '/');

		if (!empty($query_string)) {
			$url .= '?' . $query_string;
		}

		return $url;
	}

	/**
	 * Build a URL using the configured base URL.
	 */
	protected function build_url_from_base(int $page): string
	{
		$parts = parse_url($this->base_url);

		$path = $parts['path'] ?? $this->base_url;
		$query_params = [];

		if (!empty($parts['query'])) {
			parse_str($parts['query'], $query_params);
		}

		$query_params[$this->page_key] = $page;
		$query_string = http_build_query($query_params);

		$url = $path;

		if (!empty($query_string)) {
			$url .= '?' . $query_string;
		}

		if (!empty($parts['scheme']) && !empty($parts['host'])) {
			$full = $parts['scheme'] . '://';

			if (!empty($parts['user'])) {
				$full .= $parts['user'];

				if (!empty($parts['pass'])) {
					$full .= ':' . $parts['pass'];
				}

				$full .= '@';
			}

			$full .= $parts['host'];

			if (!empty($parts['port'])) {
				$full .= ':' . $parts['port'];
			}

			$full .= $url;

			return $full;
		}

		return $url;
	}

	/**
	 * Escape HTML output safely.
	 */
	protected function esc(string $value): string
	{
		return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
}