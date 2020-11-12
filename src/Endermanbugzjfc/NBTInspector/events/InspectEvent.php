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
namespace Endermanbugzjfc\NBTInspect\events;

use pocketmine\{Player,nbt\NamedTag, utils\Utils};

class InspectEvent extends NBTInspectPluginEvent {

	private $nbt;
	private $onsave = null;
	private $player;
	
	public function __construct(Player $p, NamedTag $nbt, ?callable $onsave) : void {
		$this->player = $p;
		$this->nbt = $nbt;
		if (isset($onsave)) {
			Utils::validateCallableSignature(function(NamedTag $nbt) {}, $onsave);
			$this->onsave = $onsave;
		}
	}

	public function getNBT() : NamedTag {
		return $this->nbt;
	}

	public function getOnSaveCallback() : ?callable {
		return $this->onsave;
	}
	
	public function getPlayer() : Player {
		return $this->player;
	}
	
}
