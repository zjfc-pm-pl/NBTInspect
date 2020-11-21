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

use pocketmine\Player;

use Endermanbugzjfc\NBTInspect\uis\UIInterface;

class PlayerUISwitchEvent extends BaseEvent {
	
	private $player;
	private $ui;
	private $previous;

	public function __construct(Player $p, UIInterface $ui) {
		$this->player = $p
		$this->ui = $ui;
		$this->previous = $this->getPlugin()->getPlayerUI($p);
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getUI() : UIInterface {
		return $this->ui;
	}

	public function getPrevious() : UIInterface {
		return $this->previous;
	}
	
}
