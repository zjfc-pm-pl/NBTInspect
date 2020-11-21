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
namespace Endermanbugzjfc\NBTInspect;

use pocketmine\{Player, item\Item, entity\Entity, nbt\tag\NamedTag};

trait API {
	
	abstract public static function inspect(Player $p, NamedTag $nbt, ?callable $onsave) : ?sessions\InspectSession;
	/*
		Automatically applys the NBT to the target item / entity when it has changes
	*/
	abstract public static function inspectItem(Player $p, Item $item) : ?sessions\InspectSession;
	abstract public static function inspectEntity(Player $p, Entity $entity) : ?sessions\InspectSession;

	abstract public function switchPlayerUI(Player $p, uis\UIInterface) : events\PlayerSwitchUIEvent;
	abstract public function getPlayerUI(Player $p) : uis\UIInterface;

	abstract public function registerUI(uis\UIInterface $ui) : void;
	abstract public function unregisterUI(uis\UIInterface $ui) : bool;
	/*
		Get all registered UIs (Including the default UIs)
	*/
	abstract public function getAllUI() : array;
	
}
