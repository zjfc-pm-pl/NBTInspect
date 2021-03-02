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

use pocketmine\{
	Player,
	nbt\tag\NamedTag,
	item\Item,
	entity\Entity,
	level\Level
};

use Endermanbugzjfc\NBTInspect\sessions\InspectSession;

interface API {
	
	/**
	 * Description
	 * @param InspectSession $session
	 * @param NamedTag $nbt
	 * @param ?\Closure $onsave A closure isntance that is compatible with <code>function(<@link NamedTag> $nbt)</code>
	 * @return mixed
	 */
	public static function inspect(InspectSession $session, NamedTag $nbt, ?\Closure $onsave);
	/**
	 * Automatically applys the NBT to the target item / entity when it has changes
	*/
	public static function inspectItem(InspectSession $session, Item $item);
	public static function inspectEntity(InspectSession $session, Entity $entity);
	public static function inspectLevel(InspectSession $session, Level $entity) : bool;

	/**
	 * @param string $ui Class name of UI that the player is using
	 * @return $this
	*/
	public function switchPlayerUI(Player $p, string $ui);
	/**
	 * @return string $ui Class name of UI that the player is using
	*/
	public function getPlayerUI(Player $p) : string;

	/**
	 * @param string $ui Class name of a class that implements UIInterface
	*/
	public function registerUI(string $ui) : void;
	/**
	 * @param string $ui Class name of a class that implements UIInterface
	 * @return bool Is duplicated registeration
	*/
	public function unregisterUI(string $ui) : bool;
	/**
	 * Get all registered UIs (Including the default UIs)
	 * @return string[] Class name of the UI
	*/
	public function getAllUI() : array;
	
}
