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

// use muqsit\invmenu\{InvMenu, InvMenuHandler};

use Endermanbugzjfc\NBTInspect\{
	sessions\InspectSession,
	uis\UIInterface,
	uis\FormUI
};

use function is_a;
use function strtolower;


class NBTInspect extends PluginBase implements Listener, API{

	public const UI_DEFAULT = FormUI::class;
	
	/**
	 * @var array<int, string> Player entity runtime ID => Class name of the UI
	 */
	protected $userpreferences = [];
	
	/**
	 * @var string|null
	 */
	protected $consolepreferences = null;
	
	/**
	 * @var string[] Class name of the UIs
	 */
	protected $uis = [];

	private static $instance = null;

	public function onLoad() : void {
		self::$instance = $this;
	}

	public function onEnable() : void {
		// if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->warning('This plugin should only be use as a developer tool, there is a risk of corrupting the data or break your server by modificating the data arbitrarily! (Consider giving developer read-only permission if you decide to put this on production server)');
	}

	public function onPlayerQuit(PlayerQuitEvent $ev) : void {
		unset($this->userpreferences[$ev->getPlayer()->getId()]);
	}

	public static function getInstance() : ?self {
		return self::$instance;
	}

	/**
	 * @see API::inspect()
	 */
	public static function inspect(InspectSession $session, NamedTag $nbt, ?\Closure $onsave) {
		$s->setRootTag($nbt);
		if (isset($onsave)) $s->setOnSaveCallback($onsave);
		$s->inspectCurrentTag();
		
		return $this;
	}

	public static function inspectItem(InspectSession $session, Item $item) {
		return $this->inspect($session, $item->getNamedTag(), function(NamedTag $nbt) use ($item) : void {
			if (!$nbt) return;
			if (!$item instanceof Item) return;
			$item->setNamedTag($nbt);
		});
		
		return $this;
	}

	public static function inspectEntity(InspectSession $session, Entity $entity) {
		$this->inspect($session, $entity->namedtag, function(NamedTag $nbt) use ($entity) : void {
			if (!$nbt) return;
			if (!$entity instanceof Entity) return;
			$entity->namedtag = $nbt;
		});
		
		return $this;
	}

	public static function inspectLevel(InspectSession $session, Level $w) : bool {
		if (!is_a($w->getProvider(), BaseLevelProvider::class, true)) return false;
		$this->inspect($session, $w->getProvider()->getLevelData(), function(NamedTag $nbt) use ($w) : void {
			if (!$nbt) return;
			if (!$w instanceof Level) return;
			$reflect = new \ReflectionProperty($w, 'levelData');
			$reflect->setAccessible(true);
			$reflect->setValue($reflect->class, $nbt);
			/**
			 * @todo Reload level confirmation
			 */
		});
		return true;
	}

	public function switchUserUI(CommandSender $p, UIInterface $ui) {
		$this->userpreferences[$p->getId()] = $ui;
		return $this;
	}

	public function getUserUI(CommandSender $p) : string {
		return $this->userpreferences[$p->getId()] ?? self::UI_DEFAULT;
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
		else switch (strtolower($args[0] ?? 'help')) {
			case 'help':
				$cmdl[] = 'help' . TF::ITALIC . TF::GRAY . ' (Display NBTInspect plugin command usage)';

				if ($p->hasPermission('nbtinspect.cmd.item')) $cmdl[] = 'item [Inventory slot]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of an item)';

				if ($p->hasPermission('nbtinspect.cmd.entity')) $cmdl[] = 'entity [Entity ID|Play
				er name]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of an entity or the player data of a player)';

				if ($p->hasPermission('nbtinspect.cmd.level')) $cmdl[] = 'level [Level folder name]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of a level by the level folder name)';

				if ($p->hasPermission('nbtinspect.cmd.tile')) $cmdl[] = 'tile <xyz>' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of a tile by XYZ)';

				$p->sendMessage(TF::BOLD . TF::GOLD . 'Available arguments for commands “/nbtinspect”:' . ($glue = TF::RESET . “\n” . TF::WHITE . ' - ' . TF::YELLOW) . implode($glue, $cmdl ?? []));
				break;

			case 'item':
				if (!$p instanceof Player) {
					$p->sendMessage(TF::BOLD . TF::RED . 'Sorry, you can only use this subcommans in-game!');
					break;
				}
				if (!$p->hasPermission('nbtinspect.cmd.item')) return false;
				$item = ($inv = $p->getInventory())->getItem((int)($args[1] ?? $inv->getHeldItemIndex()));
				if ($item->getId() === Item::AIR) $p->sendMessage(TF::BOLD . TF::RED . 'The target slot is empty!');
				else $this->inspectItem($p, $item);

			case 'entity':
				if (!isset($args[1]) and $p instanceof Player) $sid = $p->getId();
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
				if (!isset($args[1]) and $p instanceof Player) $level = $p->getLevel();
				else (($level = $this->getServer()->getLevel($args[1])) === null) $p->sendMessage(TF::BOLD . TF::RED . 'Level dosen\'t exist or is not loaded!');
				if (isset($level)) $this->inspectLevel($p, $level);
				break;
		}
		return true;
	}
}