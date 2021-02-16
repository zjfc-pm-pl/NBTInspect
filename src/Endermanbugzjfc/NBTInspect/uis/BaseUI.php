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
namespace Endermanbugzjfc\NBTInspect\uis;

use pocketmine\utils\TextFormat as TF;

use Endermanbugzjfc\NBTInspect\sessions\InspectSession;

abstract class BaseUI implements UIInterface {

	private $session;
	protected $previous = null;

	protected function __construct(InspectSession $session) {
		$this->session = $session;
	}

	public static function create(InspectSession $session, UIInterface $previous = null) : self {
		$self = new self($session);
		$self->previous = $previous;
		return $self;
	}

	public function getSession() : InspectSession {
		return $this->session;
	}

	public function getPreviousUI() : ?UIInterface {
		return $this->previous;
	}
	
}
