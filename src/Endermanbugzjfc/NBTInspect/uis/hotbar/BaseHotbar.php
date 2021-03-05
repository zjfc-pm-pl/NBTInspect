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

use pocketmine\{inventory\Inventory, item\Item, nbt\tag\ByteTag, nbt\tag\CompoundTag};

use Endermanbugzjfc\NBTInspect\uis\UIInterface;

class BaseHotbar {

    /**
     * @var UIInterface
     */
    private $ui;

    /**
     * BookEditorHotbar constructor.
     * @param UIInterface $ui
     */
    public function __construct(UIInterface $ui) {
        $this->ui = $ui;
    }

    public function getUIInstance() : UIInterface {
        return $this->ui;
    }

    public function getInventory() : Inventory {
        return $this->getUIInstance()->getSession()->getSessionOwner()->getInventory();
    }

    public const ACTION_EXIT = 0;

    public function hotbar() {
        $item = Item::get(Item::INVISIBLEBEDROCK);
        $item->setCustomName(TF::GRAY . '(Click / drop to exit edit mode)');
        $item->setNamedTagEntry(new CompoundTag('NBTInspect', [new ByteTag('action', self::ACTION_EXIT)]));
        for ($slot=0; $slot <= 27; $slot++) $this->getInventory()->setItem($slot, $item, false);
    }
}