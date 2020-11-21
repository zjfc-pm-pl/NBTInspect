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

	protected $players = [];
	protected $uis = [];

	private static $instance = null;

	public function onEnable() : void {
		self::$instance = $this;
		if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function playerQuitEvent(\pocketmine\event\player\PlayerQuitEvent $p) : void {
		unset($this->players[$p->getId()]);
	}

	public static function getInstance() : ?self {
		return self::$instance;
	}

	public static function inspect(Player $p, NamedTag $nbt, ?callable $onsave) : sessions\InspectSession {
	}

	public static function inspectItem(Player $p, Item $item) : sessions\InspectSession {
	}

	public static function inspectEntity(Player $p, Entity $entity) : sessions\InspectSession {
	}

	public function switchPlayerUsingUI(Player $p, uis\UIInterface $ui) : void {
		$this->players[$p->getId()] = $ui;
	}

	public function getPlayerUsingUI(Player $p) : uis\UIInterface {
		return $this->players[$p->getId()] ?? uis\defaults\DefaultFormUI::getInstance();
	}

	public function registerInspectUI(uis\UIInterface $ui) : void {
		foreach ($this->uis as $ui) if ($ui->getName() === $ui->getName()) throw new \InvalidArgumentException('Theres is already an UI having the same name!');
		$this->uis[] = $ui;
	}

	public function getAllUI() : array {
		return $this->uis;
	}
}