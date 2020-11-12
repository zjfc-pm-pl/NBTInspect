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

use pocketmine\{Player, nbt\tag\CompoundTag, item\Item, entity\Entity};

use muqsit\invmenu\{InvMenu, InvMenuHandler};

final class NBTInspect extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener {

	private $players = [];

	private static $instance = null;

	public const UI_FORM = 0;
	public const UI_INVENTORY = 1;
	public const UI_HOTBAR = 2;
	public const UI_DEFAULT = self::UI_FORM;

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
	
	public function pluginEvent(events\NBTInspectPluginEvent $e) : void {
		if ($e->isCancelled()) return;
		switch (true) {
			case $e instanceof events\InspectEvent:
				switch ($this->getPlayerUsingUI($e->getPlayer())) {
					case self::UI_INVENTORY:
						break;
						$class = viewers\InventoryTagViewer::class;
					case self::UI_HOTBAR:
						$class = viewers\HotbarTagViewer::class;
						break;
						
					default:
						$class = viewers\FormTagViewer::class;
						break;
				}
				(new $class($e->getPlayer(), $e->getNBT(), $e->getOnSaveCallback()))->open();
				break;
				
				case $e instanceof events\DataSaveEvent:
					break;
		}
	}

	// API BELOW

	public static function getInstance() : ?self {
		return self::$instance;
	}

	public static function inspect(Player $p, NamedTag $nbt, ?callable $onsave) : events\InspectEvent {
		return (new events\InspectEvent($p, clone $nbt, $onsave)->call());
	}

	public static function inspectItem(Player $p, Item $item) : events\InspectEvent {
		return (new events\InspectEvent($p, clone $item->getNamedTag(), function(NamedTag $nbt) use ($item) : void {
			if (!($item ?? null) instanceof Item) return;
			$item->setNamedTag($nbt);
		}))->call();
	}

	public static function inspectEntity(Player $p, Entity $entity) : events\InspectEvent {
		return (new events\InspectEvent($p, clone $entity->namedtag, function(NamedTag $nbt) use ($entity) : void {
			if (!($entity ?? null) instanceof Entity) return;
			$entity->namedtag = $nbt;
		}))->call();
	}

	public function getPlayerUsingUI(Player $player) : int {
		return (int)($this->players[$player->getId()] ?? $this->getConfig()->get('default-ui', self::UI_FORM));
	}
}