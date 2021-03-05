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

use pocketmine\{level\Position,
    Player,
    inventory\InventoryHolder,
    inventory\PlayerInventory,
    nbt\tag\CompoundTag,
    nbt\tag\NamedTag,
    item\Item,
    entity\Entity,
    level\Level,
    level\format\io\BaseLevelProvider,
    command\Command,
    command\CommandSender,
    command\ConsoleCommandSender,
    tile\Tile,
    utils\TextFormat as TF,
    plugin\PluginBase};
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

use function assert;
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
		$this->getLogger()->warning('This plugin should only be use as a developer tool, there is a risk of corrupting the data or break your server by modifying the data arbitrarily! (Consider giving developer read-only permission if you decide to put this on production server)');
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
		$session->setRootTag($nbt);
		if (isset($onsave)) $session->setOnSaveCallback($onsave);
		$session->inspectCurrentTag();
		
		return self::getInstance();
	}

	public static function inspectItem(InspectSession $session, Item $item) {
		self::getInstance()->inspect($session, $item->getNamedTag(), function(NamedTag $nbt) use ($item) : void {
			if (!$nbt instanceof CompoundTag) return;
			if (!$item instanceof Item) return;
			$item->setNamedTag($nbt);
		});
		
		return self::getInstance();
	}

	public static function inspectEntity(InspectSession $session, Entity $entity) {
		self::getInstance()->inspect($session, $entity->namedtag, function(NamedTag $nbt) use ($entity) : void {
			if (!$nbt) return;
			if (!$entity instanceof Entity) return;
			$entity->namedtag = $nbt;
		});
		
		return self::getInstance();
	}

	public static function inspectLevel(InspectSession $session, Level $level) : bool {
		if (!is_a($level->getProvider(), BaseLevelProvider::class, true)) return false;
		self::getInstance()->inspect($session, $level->getProvider()->getLevelData(), function(NamedTag $nbt) use ($level) : void {
			if (!$nbt) return;
			if (!$level instanceof Level) return;
			$reflect = new \ReflectionProperty($level, 'levelData');
			$reflect->setAccessible(true);
			$reflect->setValue($reflect->class, $nbt);
			/**
			 * @todo Reload level confirmation
			 */
		});
		return true;
	}

	public static function inspectTile(InspectSession $session, Tile $tile) : bool {
	    if (($nbt = $tile->getCleanedNBT()) === null) return false;
	    self::inspect($session, $nbt, function(CompoundTag $nbt) use ($tile) : void {
	        $reflect = new \ReflectionMethod($tile, 'readSaveData');
	        $reflect->setAccessible(true);
	        $reflect->getClosure($tile)($nbt);
	        /**
             * @todo Not sure if this will work or not, think I should re-create a new tile instance and put into the chunk tile list, instead of calling readSaveData() on an tile instance that is already initialized
             * @see TIle::readSaveData()
             */
        });
		return true;
	}

	public function switchUserUI(CommandSender $user, string $ui) : bool {
	    if ($user instanceof ConsoleCommandSender) $this->consolepreferences = $ui;
		elseif ($user instanceof Player) $this->userpreferences[$user->getId()] = $ui;
		return $user instanceof ConsoleCommandSender or $user instanceof Player;
	}

	public function getUserUI(CommandSender $user) : ?string {
	    if ($user instanceof ConsoleCommandSender) return $this->consolepreferences;
		elseif ($user instanceof Player) return $this->userpreferences[$user->getId()] ?? self::UI_DEFAULT;
		return null;
	}

	public function registerUI(string $ui) : void {
		if (!is_a($ui, UIInterface::class, true)) throw new \InvalidArgumentException('Argument 1 must be a namespace of a class that implements UIInterface');
		foreach ($this->uis as $rui) {
		    assert(is_a($rui, UIInterface::class, true));
		    if (is_a($rui, UIInterface::class, true) and $ui::getName() === $rui::getName()) throw new \InvalidArgumentException('Theres is already an registered UI having the same name!');
        }
		$this->uis[] = $ui;

		if ($this->consolepreferences = null and $ui::accessibleBy(new ConsoleCommandSender)) $this->consolepreferences = $ui;
	}

	public function unregisterUI(string $ui) : bool {
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

	public function onCommand(CommandSender $p, Command $cmd, string $alias, array $args) : bool {
		if ($cmd->getName() !== 'nbtinspect') return true;
		else switch (strtolower($args[0] ?? 'help')) {
			case 'help':
				$cmdl[] = 'help' . TF::ITALIC . TF::GRAY . ' (Display NBTInspect plugin command usage)';

				if ($p->hasPermission('nbtinspect.inspect.item.read')) $cmdl[] = 'item [Inventory slot] [Entity ID]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of an item)';

				if ($p->hasPermission('nbtinspect.inspect.entity.read')) $cmdl[] = 'entity [Entity ID|Play
				er name]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of an entity or the player data of a player)';

				if ($p->hasPermission('nbtinspect.inspect.level.read')) $cmdl[] = 'level [Level folder name]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of a level by the level folder name)';

				if ($p->hasPermission('nbtinspect.inspect.tile.read')) $cmdl[] = 'tile [xyz] [Level folder name]' . TF::ITALIC . TF::GRAY . ' (Inspect the NBT data of a tile by XYZ)';

				$p->sendMessage(TF::BOLD . TF::GOLD . 'Available arguments for commands "/nbtinspect":' . ($glue = TF::RESET . "\n" . TF::WHITE . ' - ' . TF::YELLOW) . implode($glue, $cmdl ?? []));
				break;

			case 'item':
				if ($this->consolepreferences = null) {
                    $p->sendMessage(TF::BOLD . TF::RED . 'Sorry, you can only use this subcommands in-game unless a suitable UI type is registered!');
					break;
                }
				if (!$p->hasPermission('nbtinspect.inspect.item.read')) return false;
				if (!isset($args[2])) {
				   if ($p instanceof InventoryHolder) $inv = $p->getInventory();
				   else {
				       $p->sendMessage(TF::BOLD . TF::RED . 'Please enter an entity ID!');
				       break;
                   }
                } elseif (!($se = $this->getServer()->findEntity((int)$args[2])) instanceof InventoryHolder) {
				    $p->sendMessage(TF::BOLD . TF::RED . 'Entity with the ID doesn\'t exists or has no inventory!');
				    break;
                } else $inv = $se->getInventory();
				if (!isset($args[1]) and $inv instanceof PlayerInventory) $slot = $inv->getHeldItemIndex();
				else {
				    $p->sendMessage(TF::BOLD . TF::RED . 'Please enter a invalid slot number start from 0!');
				    break;
                }
				$item = $inv->getItem($slot);
				if ($item->getId() === Item::AIR) $p->sendMessage(TF::BOLD . TF::RED . 'The target slot is empty!');
				else $this->inspectItem(new InspectSession($p), $item);
                break;

            case 'entity':
                if ($this->consolepreferences = null) {
                    $p->sendMessage(TF::BOLD . TF::RED . 'Sorry, you can only use this subcommands in-game unless a suitable UI type is registered!');
					break;
                }
				if (!isset($args[1]) and $p instanceof Player) $sid = $p->getId();
				else $sid = $args[1];
				if (empty(preg_replace('/[0-9]+/i', '', $sid))) if (($entity = $this->getServer()->findEntity((int)$sid)) === null or $entity = $this->getServer()->getPlayer($sid)) $this->getPlayerData($sid, function(?CompoundTag $nbt) use ($sid, $p) : void {
					if ($nbt === null) $p->sendMessage(TF::BOLD . TF::RED . 'Player not found!');
					$this->inspect(new InspectSession($p), $nbt, function(?NamedTag $nbt) use ($sid) : void {
						$this->setPlayerData($sid, $nbt);
					});
				});
				elseif (($entity = $this->getServer()->findEntity((int)$sid)) !== null) $this->inspectEntity(new InspectSession($p), $entity);
				else $p->sendMessage(TF::BOLD . TF::RED . 'Player not found!');
				break;

			case 'level':
			    if ($this->consolepreferences = null) {
                    $p->sendMessage(TF::BOLD . TF::RED . 'Sorry, you can only use this subcommands in-game unless a suitable UI type is registered!');
					break;
                }
			    if (isset($args[1])) {
			        if (($level = $this->getServer()->getLevelByName($args[1])) === null) {
			            $p->sendMessage(TF::BOLD . TF::RED . 'Level dosen\'t exist or is not loaded!');
			            break;
                    }
                } elseif ($p instanceof Position) $level = $p->getLevel();
			    else {
			        $p->sendMessage(TF::BOLD . TF::RED . 'Please enter a level folder name!');
			        break;
                }
				if ($this->inspectLevel(new InspectSession($p), $level)) $p->sendMessage(TF::BOLD . TF::RED . 'Target level has an unsupported level provider type!');
				break;

            case 'tile':
			    if ($this->consolepreferences = null) {
                    $p->sendMessage(TF::BOLD . TF::RED . 'Sorry, you can only use this subcommands in-game unless a suitable UI type is registered!');
					break;
                }
			    if (isset($args[4])) {
			        if (($level = $this->getServer()->getLevelByName($args[4])) === null) {
			            $p->sendMessage(TF::BOLD . TF::RED . 'Level dosen\'t exist or is not loaded!');
			            break;
                    }
                } elseif ($p instanceof Position) $level = $p->getLevel();
			    else {
			        $p->sendMessage(TF::BOLD . TF::RED . 'Please enter a level folder name!');
			        break;
                }
			    if (isset($args[1]) and isset ($args[2]) and isset($args[3])) $vec = new Position((int)$args[1], (int)$args[2], (int)$args[3], $level);
			    $tile = $level->getTile($vec ?? $p->asPosition());
				if (!$this->inspectTile(new InspectSession($p), $tile)) $p->sendMessage(TF::BOLD . TF::RED . 'Failed to read tile NBT data!');
			    break;
		}
		return true;
	}
}