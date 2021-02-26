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
	level\Level,
	level\format\io\BaseLevelProvider,
	command\Command,
	command\CommandSender,
	utils\TextFormat as TF,
	plugin\PluginBase
};
use pocketmine\event\{
	Listener,
	player\PlayerQuitEvent
};

use jojoe77777\FormAPI\ModalForm;
// use muqsit\invmenu\{InvMenu, InvMenuHandler};

use Endermanbugzjfc\NBTInspect\{
	sessions\InspectSession,
	uis\UIInterface,
	uis\FormUI
};

use function is_a;
use function strtolower;


	public const UI_DEFAULT = FormUI::class;
class NBTInspect extends PluginBase implements Listener, API{

	protected $players = [];
	protected $uis = [];

	private static $instance = null;

	public function onLoad() : void {
		self::$instance = $this;
	}

	public function onEnable() : void {
		// if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->warning('This plugin should only be use as a developer, there is a risk of corrupting the data or break your server by modificating the data arbitrarily!');
	}

	public function onPlayerQuit(PlayerQuitEvent $ev) : void {
		unset($this->players[$ev->getPlayer()->getId()]);
	}

	public static function getInstance() : ?self {
		return self::$instance;
	}

	/**
	 * @see API::inspect()
	 */
	public static function inspect(Player $p, NamedTag $nbt, ?\Closure $onsave) : InspectSession {
		$s = new InspectSession($p, $nbt, $onsave);
		$s->inspectCurrentTag();
	}

	public static function inspectItem(Player $p, Item $item) : InspectSession {
		return $this->inspect($p, $item->getNamedTag(), function(NamedTag $nbt) use ($item) : void {
			if (!$data) return;
			if (!$item instanceof Item) return;
			$item->setNamedTag($nbt);
		});
	}

	public static function inspectEntity(Player $p, Entity $entity) : InspectSession {
		return $this->inspect($p, $entity->namedtag, function(NamedTag $nbt) use ($entity) : void {
			if (!$data) return;
			if (!$entity instanceof Entity) return;
			$entity->namedtag = $nbt;
		});
	}

	public static function inspectLevel(Player $p, Level $w) : ?InspectSession {
		if (is_a($w->getProvider(), BaseLevelProvider::class, true))
		return $this->inspect($p, $w->getProvider()->getLevelData(), function(NamedTag $nbt) use ($w) : void {
			if (!$w instanceof Level) return;
			$reflect = new \ReflectionProperty($w, 'levelData');
			$reflect->setAccessible(true);
			$reflect->setValue($reflect->class, $nbt);
		});
	}

	public function switchPlayerUI(Player $p, UIInterface $ui) {
		$this->players[$p->getId()] = $ui;
		return $this;
	}

	public function getPlayerUI(Player $p) : string {
		return $this->players[$p->getId()] ?? self::UI_DEFAULT;
	}

	public function registerUI(UIInterface $ui) : void {
		if (!is_a($ui, UIInterface::class, true)) throw new \InvalidArgumentException('Argument 1 must be a namespace of a class that implements UIInterface');
		foreach ($this->uis as $rui) if ($ui::getName() === $rui::getName()) throw new \InvalidArgumentException('Theres is already an registered UI having the same name!');
		$this->uis[] = $ui;
	}

	public function unregisterUI(UIInterface $ui) : bool {
		if (!is_a($ui, UIInterface::class, true)) throw new \InvalidArgumentException('Argument 1 must be a namespace of a class that implements UIInterface');
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

				if ($p->hasPermission('nbtinspect.cmd.item')) $cmdl[] = 'item [Inventory slot]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of an item)';

				if ($p->hasPermission('nbtinspect.cmd.entity')) $cmdl[] = 'entity [Entity ID]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of an entity or the player data of a player)';

				if ($p->hasPermission('nbtinspect.cmd.level')) $cmdl[] = 'level [Level folder name]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of a level by the level folder name)';

				if ($p->hasPermission('nbtinspect.cmd.tile')) $cmdl[] = 'tile <xyz>' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of a tile by XYZ)';

				$p->sendMessage(TF::BOLD . TF::GOLD . 'Available arguments for commands "/nbtinspect":' . ($glue = TF::RESET . "\n" . TF::WHITE . ' - ' . TF::YELLOW) . implode($glue, $cmdl ?? []));
				break;

			case 'item':
				if (!$p->hasPermission('nbtinspect.cmd.item')) return false;
				if (isset($args[1])) $item = ($inv = $this->getPlayer()->getInventory())->getItem((int)$args[1]);
				else $item = $inv->getItemInHand();
				if ($item->getId() === Item::AIR) $p->sendMessage(TF::BOLD . TF::RED . 'The target slot is empty!');
				else $this->inspectItem($p, $item);
				break;

			case 'entity':
				if (!isset($args[1])) $sid = $p->getId();
				else $sid = $args[1];
				if (empty(preg_replace('/[0-9]+/i', '', $sid))) if ($entity = $this->getServer()->findEntity((int)$sid) === null or $entity = $this->getServer()->getPlayer($sid)) $this->getPlayerData($sid, function(?CompoundTag $nbt) use ($sid, $p) : void {
					if ($nbt === null) $p->sendMessage(TF::BOLD . TF::RED . 'Player not found!');
					$this->inspect($p, $nbt, function(?NamedTag $nbt) use ($sid) : void {
						$this->setPlayerData($sid, $nbt);
					});
				});
				elseif ($entity = $this->getServer()->findEntity((int)$sid) !== null) $this->inspectEntity($p, $entity);
				else $p->sendMessage(TF::BOLD . TF::RED . 'Player not found!');
				break;

			case 'level':
				if (!isset($args[1])) {
					$p->sendMessage(TF::BOLD . TF::RED . 'Please enter a level folder name!');
					break;
				}
				$slfn = $args[1];
				if ($level = $this->getServer()->getLevel($slfn) === null) {
					$this->getLevelData($slfn, function(?CompoundTag $nbt) : void {
						if ($nbt === null) $p->sendMessage(TF::BOLD . TF::RED . 'Target level file cannot be load!');
							$this->inspect($p, $nbt, function(?NamedTag $nbt) use ($slfn) : void {
							$this->setPlayerData($slfn, $nbt);
						});
					});
				}
				else $this->inspectLevel($p, $level);
				break;
		}
		return true;
	}
}