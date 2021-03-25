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
	utils\TextFormat as TF
};

class BookEditorHotbar extends BaseHotbar {

	// TODO: Add page system I afraid 7 books is not enough to hold byte array tag data or even string tag data

    public function hotbar() : void {
    	parent::__construct();
        $s = $this->getUIInstance()->getSession();
        $inv = $this->getInventory();
        
        $i = Item::get(Item::WRITABLE_BOOK);
        $i->setCustomName(TF::RESET);
        for ($slot=0; $slot < 8; $slot++) $inv->setItem($slot, $i, false);
        $inv->sendContents($s->getSessionOwner());
    }

}