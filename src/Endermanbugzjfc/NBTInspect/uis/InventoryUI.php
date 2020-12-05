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

	private $inv;
	private $player;
	private $tag;
	private $onsave = null;
	private $from = null;
	
	public static function getName() : string {
		return 'Inventory';
	}

	public static function open(Player $p, NamedTag $tag, ?callable $onsave, ?UIInterface $from) : self {
		return new self($p, $tag, $onsave, $from);
	}

	public function __construct(Player $p, NamedTag $tag, ?callable $onsave, ?UIInterface $from) {
		()
	}

}
