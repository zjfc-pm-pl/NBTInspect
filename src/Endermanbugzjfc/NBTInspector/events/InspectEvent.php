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

use pocketmine\{nbt\CompoundTag, utils\Utils};

use Endermanbugzjfc\NBTInspect\NBTInspect as Main;

class InspectEvent extends NBTInspectPluginEvent {

	private $nbt;
	private $onsave = null;
	private $ui = Main::UI_DEFAULT;
	
	public function __construct(CompoundTag $nbt, ?callable $onsave, int $ui = Main::UI_DEFAULT) : void {
		$this->setNBT($nbt);
		if (isset($onsave)) {
			Utils::validateCallableSignature(function(CompoundTag $nbt) {}, $onsave);
			$this->onSave($onsave);
		}
		$this->ui = $ui;
	}

	public function getNBT() : CompoundTag {
		return $this->nbt;
	}

	public function setNBT(CompoundTag $nbt) : self {
		$this->nbt = $nbt;
		return $this;
	}

	public function onSave(callable $onsave) : self {
		$this->onsave = $onsave;
		return $this;
	}

	public function getOnSaveCallable() : ?callable {
		return $this->onsave;
	}

	public function getUIType() : int {
		return $this->ui;
	}

	public function setUIType(int $ui) : self {
		$this->ui = $ui;
		return $this;
	}
	
}
