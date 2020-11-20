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

trait API {
	
	public static function inspect(Player $p, NamedTag $nbt, ?callable $onsave) : sessions\InspectSession {
	}

	public static function inspectItem(Player $p, Item $item) : sessions\InspectSession {
	}

	public static function inspectEntity(Player $p, Entity $entity) : sessions\InspectSession {
	}

	public function switchPlayerUsingUI(Player $p, uis\UIInterface) {
		$this->players[$p->getId()] = $ui;
	}

	public function getPlayerUsingUI(Player $p) : uis\UIInterface {
		return $this->players[$p->getId()] ?? uis\defaults\DefaultFormUI::getInstance();
	}

	public function registerInspectUI(uis\UIInterface $ui) {
		foreach ($this->uis as $ui) if ($ui->getName() === $ui->getName()) throw new \InvalidArgumentException('Theres is already an UI having the same name!');
		$this->uis[] = $ui;
	}

	public function getAllUI() : array {
		return $this->uis;
	}
	
}
