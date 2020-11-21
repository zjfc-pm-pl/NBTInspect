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

class InspectSessionOpenEvent extends PluginEvent {

	private $player;
	private $nbt;
	private $onsave = null;

	public function __construct(Player $p, NamedTag $nbt, ?callable $onsave) {
		$this->player = $p;
		$this->nbt = clone $nbt;
		$this->onsave = $onsave;
	}

	public function getNBT() : NamedTag {
		return $this->nbt;
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getOnSaveCallback() : ?callable {
		return $this->onsave;
	}
}
