<?php

/*

     					_________	  ______________		
     				   /        /_____|_           /
					  /————/   /        |  _______/_____    
						  /   /_     ___| |_____       /
						 /   /__|    ||    ____/______/
						/   /    \   ||   |   |   
					   /__________\  | \   \  |
					       /        /   \   \ |
						  /________/     \___\|______
						                   |         \ 
							  PRODUCTION   \__________\	

							   翡翠出品 。 正宗廢品  
 
*/

declare(strict_types=1);
namespace Endermanbugzjfc\NBTInspect\uis\inventories\layout;

use pocketmine\item\Item;

use muqsit\invmenu\transaction\{InvMenuTransaction, InvMenuTransactionResult};

class OpenedTagsBar extends BaseInventoryUIActionClass {

	abstract protected function apply() : void {
		$s = $this->getUIInstance()->getSession();
		$tags = $s->getAllOpenedTags();
	}

	protected function react(InvMenuTransaction $t) : InvMenuTransactionResult {
		return $t->discard();
	}
	
}
