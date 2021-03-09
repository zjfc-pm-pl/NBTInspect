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

namespace Endermanbugzjfc\NBTInspect\uis\hotbar;

use pocketmine\{
    event\Event,
    event\inventory\InventoryTransactionEvent,
    event\player\PlayerInteractEvent,
    inventory\Inventory,
    inventory\InventoryHolder,
    item\Item,
    nbt\tag\ByteTag,
    nbt\tag\CompoundTag,
    utils\TextFormat as TF};

use Endermanbugzjfc\NBTInspect\uis\UIInterface;

abstract class BaseHotbar {

    /**
     * @var UIInterface
     */
    private $ui;

    /**
     * @var Item[]
     */
    private $origininv = [];

    /**
     * BookEditorHotbar constructor.
     * @param UIInterface $ui
     */
    public function __construct(UIInterface $ui) {
        if (!$ui->getSession()->getSessionOwner() instanceof InventoryHolder) throw new \InvalidArgumentException('The session owner must be an instance of ' . InventoryHolder::class);
        $this->ui = $ui;
    }

    public function getUIInstance() : UIInterface {
        return $this->ui;
    }

    public function getInventory() : Inventory {
        $p = $this->getUIInstance()->getSession()->getSessionOwner();
        if ($p instanceof InventoryHolder) return $p->getInventory();
        else throw new \RuntimeException('Session owner is not an instance of ' . InventoryHolder::class);
    }

    public const ACTION_EXIT = 0;

    public function hotbar() {
        $this->origininv = $this->getInventory()->getContents(true);
        $item = Item::get(Item::INVISIBLEBEDROCK);
        $item->setCustomName(TF::GRAY . '(Click / drop to exit edit mode)');
        $item->setNamedTagEntry(new CompoundTag('NBTInspect', [new ByteTag('action', self::ACTION_EXIT)]));
        for ($slot=0; $slot <= 27; $slot++) $this->getInventory()->setItem($slot, $item, false);
    }

    /**
     * @param PlayerInteractEvent|InventoryTransactionEvent $ev
     */
    final public function preReact($ev) : void {
        $this->react($ev);
        $this->resetInventory();
    }

    /**
     * @param PlayerInteractEvent|InventoryTransactionEvent $ev
     */
    abstract protected function react($ev) : void;

    protected function resetInventory() : void {
        $this->getInventory()->setContents($this->origininv, false);
    }
}