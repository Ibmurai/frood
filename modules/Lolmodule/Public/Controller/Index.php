<?php
/**
 * LolmodulePublicControllerIndex
 * 
 * PHP version 5
 * 
 * @category Frood
 * @package  lolmodule
 * @author   Bo Thinggaard <both@fynskemedier.dk>
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    16-12-2011
 */

/**
 * LolmodulePublicControllerIndex
 * 
 * @category   Frood
 * @package    lolmodule
 * @subpackage Controller
 * @author     Bo Thinggaard <both@fynskemedier.dk>
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class LolmodulePublicControllerIndex extends LolmodulePublicController {
	/**
	 * Hello world admin action
	 * 
	 * @return void
	 */
	public function indexAction() {
		$this->doOutputDisabled();
		echo 'Hello OS public world!';
	}
}