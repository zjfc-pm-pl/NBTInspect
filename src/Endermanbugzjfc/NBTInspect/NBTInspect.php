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
	use API;

	public const UI_DEFAULT = uis\InventoryUI::class;

	protected $players = [];
	protected $uis = [];

	private static $instance = null;

	public function onEnable() : void {
		self::$instance = $this;
		if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function playerQuitEvent(\pocketmine\event\player\PlayerQuitEvent $ev) : void {
		unset($this->players[$ev->getPlayer()->getId()]);
	}

	public function inspectSessionOpenEvent(events\InspectSessionOpenEvent $ev) : void {
		if ($ev->isCancelled()) return;
	}

	public function playerSwitchUiEvent(events\PlayerSwitchUIEvent $ev) : void {
		if ($ev->isCancelled()) return;
		$this->players[$p->getId()] = $ev->getUI();
	}

	public static function getInstance() : ?self {
		return self::$instance;
	}

	public static function inspect(Player $p, NamedTag $nbt, ?callable $onsave) : ?sessions\InspectSession {
		($ev = new events\InspectSessionOpenEvent($p, $nbt, $onsave))->call();
		return $ev->getSession();
	}

	public static function inspectItem(Player $p, Item $item) : ?sessions\InspectSession {
		return $this->inspect($p, $item->getNamedTag(), function(NamedTag $nbt) use ($item) : void {
			if (!$item instanceof Item) return;
			$item->setNamedTag($nbt);
		});
	}

	public static function inspectEntity(Player $p, Entity $entity) : ?sessions\InspectSession {
		return $this->inspect($p, $entity->namedtag, function(NamedTag $nbt) use ($entity) : void {
			if (!$entity instanceof Entity) return;
			$entity->namedtag = $nbt;
		});
	}

	public function switchPlayerUI(Player $p, uis\UIInterface $ui) : events\PlayerSwitchUIEvent {
		($ev = new events\PlayerSwitchUIEvent($p, $ui))->call();
	}

	public function getPlayerUI(Player $p) : uis\UIInterface {
		return $this->players[$p->getId()] ?? self::UI_DEFAULT::getClass();
	}

	public function registerUI(uis\UIInterface $ui) : void {
		foreach ($this->uis as $ui) if ($ui->getName() === $ui->getName()) throw new \InvalidArgumentException('Theres is already an UI having the same name!');
		$this->uis[] = $ui;
	}

	public function unregisterUI(uis\UIInterface $ui) : bool {
		foreach ($this->uis as $i => $rui) if ($rui === $ui) {
			unset($this->uis[$i]);
			return true;
		}
		return false;
	}

	public function getAllUI() : array {
		return $this->uis;
	}
}