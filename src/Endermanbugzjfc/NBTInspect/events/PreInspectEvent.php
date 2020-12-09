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

class PreInspectEvent extends PluginEvent {

	// This event is called when the plugin is loading a NBT tag (For example reading the tag data from a file)

	private $player;

	public function __construct(Player $p) {
		$this->player = $p;
	}

	public function getPlayer() : Player {
		return $this->player;
	}
}
