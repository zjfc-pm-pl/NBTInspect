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

use pocketmine\{command\CommandSender, nbt\tag\NamedTag, item\Item, entity\Entity, level\Level, tile\Tile};

use Endermanbugzjfc\NBTInspect\sessions\InspectSession;

interface API {
	
	/**
	 * @param InspectSession $session
	 * @param NamedTag $nbt
	 * @param ?\Closure $onsave A closure instance that is compatible with <code>function(<@link NamedTag> $nbt)</code>
	 * @return mixed
	 */
	public static function inspect(InspectSession $session, NamedTag $nbt, ?\Closure $onsave);
	/*
	 * Auto automatically creates a callback base on the called function
	 * */
	public static function inspectItem(InspectSession $session, Item $item);
	public static function inspectEntity(InspectSession $session, Entity $entity);
	/**
	 * @return bool Whether the given level has a supported level provider or not
	 */
	public static function inspectLevel(InspectSession $session, Level $level) : bool;
	/**
	 * @return bool Whether the tile NBT data can be successfully read or not
	 */
    public static function inspectTile(InspectSession $session, Tile $tile) : bool;

	/**
	 * @param string $ui Class name of UI that the player is using
	 * @return $this
	*/
	public function switchUserUI(CommandSender $user, string $ui);
	/**
	 * @return string $ui Class name of UI that the player is using
	*/
	public function getUserUI(CommandSender $user) : ?string;

	/**
	 * @param string $ui Class name of a class that implements UIInterface
	*/
	public function registerUI(string $ui) : void;
	/**
	 * @param string $ui Class name of a class that implements UIInterface
	 * @return bool Is duplicated registration
	*/
	public function unregisterUI(string $ui) : bool;
	/**
	 * Get all registered UIs (Including the default UIs)
	 * @return string[] Class name of the UI
	*/
	public function getAllUI() : array;
	
}
