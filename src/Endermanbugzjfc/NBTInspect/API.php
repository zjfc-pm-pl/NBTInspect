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

use pocketmine\{Player,
	nbt\tag\NamedTag,
	item\Item,
	entity\Entity,
	level\Level
};

trait API {
	
	abstract public function inspect(Player $p, NamedTag $nbt, ?callable $onsave) : sessions\InspectSession;
	/*
		Automatically applys the NBT to the target item / entity when it has changes
	*/
	abstract public static function inspectItem(Player $p, Item $item) : sessions\InspectSession;
	abstract public static function inspectEntity(Player $p, Entity $entity) : sessions\InspectSession;
	abstract public static function inspectLevel(Player $p, Level $entity) : sessions\InspectSession;

	/*
		The UI argument / return value is the class namespace of UI that the player is using
	*/
	abstract public function switchPlayerUI(Player $p, string $ui);
	abstract public function getPlayerUI(Player $p) : string;

	/*
		Please input the namespace of a class that implements UIInterface
	*/
	abstract public function registerUI(string $ui) : void;
	abstract public function unregisterUI(string $ui) : bool;
	/*
		Get all registered UIs (Including the default UIs)
	*/
	abstract public function getAllUI() : array;
	
}
