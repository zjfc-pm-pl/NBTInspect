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

use pocketmine\{Player, nbt\tag\NamedTag, item\Item, entity\Entity};

use muqsit\invmenu\{InvMenu, InvMenuHandler};

final class NBTInspect extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener {

	private $players = [];

	private static $instance = null;

	public const UI_FORM = 0;
	public const UI_INVENTORY = 1;

	public function onEnable() : void {
		self::$instance = $this;
		if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function playerQuitEvent(\pocketmine\event\player\PlayerQuitEvent $p) : void {
		unset($this->players[$p->getId()]);
	}

	// API BELOW

	public static function getInstance() : ?self {
		return self::$instance;
	}

	public static function inspect(Player $p, NamedTag $nbt, ?callable $onsave) : sessions\InspectSession {
	}

	public static function inspectItem(Player $p, Item $item) : sessions\InspectSession {
	}

	public static function inspectEntity(Player $p, Entity $entity) : sessions\InspectSession {
	}
}