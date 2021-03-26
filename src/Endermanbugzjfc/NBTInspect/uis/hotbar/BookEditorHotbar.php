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
	item\Item,
	utils\TextFormat as TF,
	nbt\tag\CompoundTag,
	nbt\tag\ByteTag,
	nbt\tag\ListTag
};

use function in_array;

class BookEditorHotbar extends BaseHotbar {

	// TODO: Add page system I afraid 7 books is not enough to hold byte array tag data or even string tag data
	
	public const MODE_RAW = 0;
	public const MODE_BINARY = 1;
	
	public const ACTION_EDITOR = self::ACTION_EXIT + 1;

    public function hotbar() : void {
    	parent::__construct();
        $s = $this->getUIInstance()->getSession();
        $inv = $this->getInventory();
        
        $i = Item::get(Item::WRITABLE_BOOK);
        $i->setCustomName(TF::RESET);
        $i->setNamedTagEntry(new CompoundTag('NBTInspect', [new ByteTag('action', self::ACTION_EDITOR)]));
        for ($slot=0; $slot < 7; $slot++) $inv->setItem($slot, $i, false);
        
        $i = Item::get(Item::FEATHER);
        if (count($this->getAllowedModes()) > 1) {
        	$i->setNamedTagEntry(new ListTag('ench', []));
        	$i->setCustomName();
        } else $i->setCustomName(TF::RESET . TF::BOLD . TF::GRAY . 'Switch editor view mode');
        $inv->setItem(7, $i);
        
        $inv->sendContents($s->getSessionOwner());
    }
    
    /**
     * @var int
     */
    protected $allowedmodes = [];
    
    public function allowMode(int $mode) : bool {
    	if (in_array($mode, $this->allowedmodes, true)) return false;
    	$this->allowedmodes[] = $mode;
    	return true;
    }
    
    public function isModeAllowed(int $mode) : bool {
    	return in_array($mode, $this->allowedmodes, true);
    }
    
    public function getAllowedModes() : array {
    	return $this->allowedmodes;
    }

}