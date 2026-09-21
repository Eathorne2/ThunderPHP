<?php

/**
 * This file is part of the ThunderPHP Framework.
 * 
 * @package ThunderPHP
 * @author Your Name <youremail@email.com>
 * @version 1.0.0
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace {NAMESPACE};
use \Core\Pager;

defined('ROOTPATH') or die("Direct script access denied");

/**
 * {CLASS_NAME} class for custom pagination styling
 */
class {CLASS_NAME} extends Pager
{

	/* add your custom css classes to the HTML*/
	public function display():void
	{
		?>
		<nav class="">
		  <ul class="pagination">
		    <li class="page-item"><a class="page-link" href="<?=$this->links['first']?>">First</a></li>

		    <?php for($x = $this->start;$x <= $this->end;$x++):?>
			    <li class="page-item <?=$x == $this->page_number ? 'active':''?>">
			    	<a class="page-link" href="<?=preg_replace("/page=[0-9]+/", "page=".$x, $this->links['current'])?>"><?=$x?></a>
			    </li>
			<?php endfor?>

		    <li class="page-item"><a class="page-link" href="<?=$this->links['next']?>">Next</a></li>
		  </ul>
		</nav>
		<?php
	}

}
