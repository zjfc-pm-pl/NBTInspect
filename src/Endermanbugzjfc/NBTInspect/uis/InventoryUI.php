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

use pocketmine\{Player, nbt\tag\NamedTag};

class InventoryUI implements UIInterface {
	use \kim\present\traits\singleton\SingletonTrait;
	
	public function getName() : string {
		return 'Inventory';
	}

	public function open(Player $p, NamedTag $tag, ?callable $onsave, ?UIInterface $from) {
		(new InventoryInstance($p, $tag, $onsave));
	}

}
