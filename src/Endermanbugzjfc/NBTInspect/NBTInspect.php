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
	level\Level,
	command\Command,
	command\CommandSender,
	utils\TextFormat as TF,
	event\Listener,
	plugin\PluginBase
};

// use muqsit\invmenu\{InvMenu, InvMenuHandler};

use function is_a;
use function strtolower;

final class NBTInspect extends PluginBase implements Listener {
	use API;

	public const UI_DEFAULT = uis\FormUI::class;

	protected $players = [];
	protected $uis = [];

	private static $instance = null;

	public function onEnable() : void {
		self::$instance = $this;
		// if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function playerQuitEvent(\pocketmine\event\player\PlayerQuitEvent $ev) : void {
		unset($this->players[$ev->getPlayer()->getId()]);
	}

	public static function getInstance() : ?self {
		return self::$instance;
	}

	public static function inspect(Player $p, NamedTag $nbt, ?callable $onsave) : sessions\InspectSession {
		$s = new sessions\InspectSession($p, $nbt, $onsave);
		$s->inspectCurrentTag();
	}

	public static function inspectItem(Player $p, Item $item) : sessions\InspectSession {
		return $this->inspect($p, $item->getNamedTag(), function(NamedTag $nbt) use ($item) : void {
			self::disclaimerScreen($p, function(Player $p, $data = false) {
				if (!$data) return;
				if (!$item instanceof Item) return;
				$item->setNamedTag($nbt);
			});
		});
	}

	public static function inspectEntity(Player $p, Entity $entity) : sessions\InspectSession {
		return $this->inspect($p, $entity->namedtag, function(NamedTag $nbt) use ($entity) : void {
			self::disclaimerScreen($p, function(Player $p, $data = false) {
				if (!$data) return;
				if (!$entity instanceof Entity) return;
				$entity->namedtag = $nbt;
			});
		});
	}

	public static function inspectLevel(Player $p, Level $w) : sessions\InspectSession {
		return $this->inspect($p, $w->getLevelData(), function(NamedTag $nbt) use ($w) : void {
			if (!$w instanceof Level) return;
			$reflect = new \ReflectionProperty($w, 'levelData');
			$reflect->setAccessible(true);
			$reflect->setValue($reflect->class, $nbt);
		});
	}

	private static function disclaimerScreen(Player $p, \closure $callback) : \jojoe77777\FormAPI\ModalForm {
		$f = \jojoe77777\FormAPI\ModalForm($callback);
		$f->addTitle(TF::BOLD . TF::BLUE . '>> ' . TF::DARK_AQUA . '!WARNING!' . TF::BLUE . ' <<');
		$f->setContent(TF::YELLOW . 'This plugin should only be use for ' . TF::BOLD . 'debugging and ' . TF::RED . 'have a chance to break your server or corrupt your world files!');
		$f->setButton1(TF::BLUE . 'Continue');
		$f->setButton2(TF::DARK_AQUA . 'Back');
		$p->sendForm($f);
	}

	public function switchPlayerUI(Player $p, uis\UIInterface $ui) {
		$this->players[$p->getId()] = $ui;
		return $this;
	}

	public function getPlayerUI(Player $p) : string {
		return $this->players[$p->getId()] ?? self::UI_DEFAULT;
	}

	public function registerUI(uis\UIInterface $ui) : void {
		if (!is_a($ui, uis\UIInterface::class, true)) throw new \InvalidArgumentException('Argument 1 must be a namespace of a class that implements UIInterface');
		foreach ($this->uis as $rui) if ($ui::getName() === $rui::getName()) throw new \InvalidArgumentException('Theres is already an registered UI having the same name!');
		$this->uis[] = $ui;
	}

	public function unregisterUI(uis\UIInterface $ui) : bool {
		if (!is_a($ui, uis\UIInterface::class, true)) throw new \InvalidArgumentException('Argument 1 must be a namespace of a class that implements UIInterface');
		foreach ($this->uis as $i => $rui) if ($rui === $ui) {
			unset($this->uis[$i]);
			return true;
		}
		return false;
	}

	public function getAllUI() : array {
		return $this->uis;
	}

	public function onCommand(CommandSender $p, Command $cmd, string $aliase, array $args) : bool {
		if ($cmd->getName() !== 'nbtinspect') return true;
		if (!$p instanceof Player) $p->sendMessage('Please use this command in-game!');
		else switch (strtolower($args[0] ?? 'help')) {
			case 'help':
				$cmdl[] = 'help' . TF::ITALIC . TF::GRAY . ' (Display NBTInspect plugin command usage)';

				if ($p->hasPermission('nbtinspect.cmd.item')) $cmdl[] = 'item' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of the item in main hand)';

				if ($p->hasPermission('nbtinspect.cmd.item')) $cmdl[] = 'entity <Entity ID>' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of an entity by the entity ID)';

				if ($p->hasPermission('nbtinspect.cmd.level')) $cmdl[] = 'level <Level folder name>' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of a loaded level by the level folder name)';

				$p->sendMessage(TF::BOLD . GOLD . 'Available arguments for commands "/nbtinspect":' . ($glue = TF::RESET . "\n" . TF::WHITE . ' - ' . TF::YELLOW) . implode($glue, $cmdl ?? []));
				break;

			case 'item':
				if (($item = $p->getInventory()->getItemInHand())->getId() === Item::AIR) $p->sendMessage(TF::BOLD . TF::RED . 'Please hold an item in your main hand to inspect!');
				else $this->inspectItem($p, $item);
				break;

			case 'entity':
				if (!isset($args[1]) or ($entity = $this->getServer()->findEntity($args[1] ?? -1)) === null) $p->sendMessage(TF::BOLD . TF::RED . 'Entity not found!');
				else $this->inspectEntity($p, $entity);
				break;

			case 'level':
				if (!isset($args[1]) or ($level = $this->getServer()->getLevel($args[1] ?? -1)) === null) $p->sendMessage(TF::BOLD . TF::RED . 'Level not dosen\'t exist or not loaded!');
				else $this->inspectLevel($p, $level);
				break;
		}
		return true;
	}
}