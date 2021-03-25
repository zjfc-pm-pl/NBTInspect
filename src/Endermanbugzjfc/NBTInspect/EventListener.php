<?php

/*

     					_________	  ______________		
     				   /        /_____|_           /
					  /--------/   /        |  _______/_____    
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
namespace Endermanbugzjfc\NBTInspect;

use pocketmine\event\{
	Listener,
	inventory\InventoryTransactionEvent,
	nbt\tag\CompoundTag,
	nbt\tag\ByteTag
};

use Endermanbugzjfc\NBTInspect\{
	uis\hotbar\BaseHotbar
};

use function array_values;

class EventListener implements Listener {
	
	public function onInventoryTransaction(InventoryTransactionEvent $ev) : void {
		$invs = $ev->getTransaction()->getInventories;
		foreach (NBTInspect::getInstance()->getHotbars() as $hotbar) if (in_array($hotbar->getInventory(), $invs, true)) $sh = $hotbar;
		if (!isset($sh)) return;
		foreach ($ev->getTransaction()->getActions as $action) {
			$in = $action->getSourceItem();
			$out = $action->getTargetItem();
			foreach ([$in, $out] as $item) {
				if (!($nbt = $item->getNamedTagEntry('NBTInspect')) instanceof CompoundTag) continue;
				if (!($nbt = $nbt->getTag('action', ByteTag::class)) instanceof ByteTag) continue;
				if ($nbt->getValue() !== BaseHotbar::ACTION_EXIT) continue;
				$sh->close();
			}
		}
	}
	
}