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

class Frame extends BaseInventoryUIActionClass {

	abstract protected function apply() : void {
		$inv = $this->getUIInstance()->getInvMenuInstance()->getInventory();
		foreach ([
			6,
			8,
			9,
			10,
			11,
			12,
			13,
			14,
			15,
			16,
			17,
			24,
			33,
			42,
			51
		] as $slot) $inv->setItem($slot, Item::get(Item::INVISIBLEBEDROCK));
		for ($slot=0; $slot < 54; $slot++) if ($inv->isSlotEmpty($slot)) $inv->setItem($slot, Item::get(Item::STAINED_GLASS_PANE, 8));
	}

	protected function react(InvMenuTransaction $t) : InvMenuTransactionResult {
		return $t->discard();
	}
	
}
