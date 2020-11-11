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

use pocketmine\{nbt\CompoundTag, item\Item, entity\Entity};

use muqsit\invmenu\{InvMenu, InvMenuHandler};

final class NBTInspect extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener {

	private $players = [];

	private static $instance = null;

	public const UI_FORM = 0;
	public const UI_INVENTORY = 1;
	public const UI_HOTBAR = 2;

	public function onEnable() : void {
		if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if (!$this->initConfig()) {
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return;
		}
		self::$instance = $this;
	}

	private function initConfig() : bool {
		$this->saveDefaultConfig();
		foreach (($all = ($conf = $this->getConfig())->getAll()) as $k => $v) $conf->remove($k);

		$conf->set('enable-plugin', (bool)($all['enable-plugin'] ?? true));
		$conf->set('default-ui', (int)($all['default-ui'] ?? self::UI_FORM));

		$conf->save();
		$conf->reload();
		return (bool)$conf->get('enable-plugin', true);
	}

	public function playerQuitEvent(\pocketmine\event\player\PlayerQuitEvent $p) : void {
		unset($this->players[$p->getId()]);
	}

	// API BELOW

	public static function getInstance() : ?self {
		return self::$instance;
	}

	public static function inspect(CompoundTag $nbt, ?callable $onsave) : events\InspectEvent {

	}

	public static function inspectItem(Item $item) : events\InspectEvent {

	}

	public static function inspectEntity(Entity $entity) : events\InspectEvent {

	}

	public function getPlayerUsingUI(Player $player) : int {
		return (int)($this->players[$player->getId()] ?? $this->getConfig()->get('default-ui', self::UI_FORM));
	}
}