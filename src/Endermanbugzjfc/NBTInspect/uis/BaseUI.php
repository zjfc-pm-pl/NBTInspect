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

use Endermanbugzjfc\NBTInspect\NBTInspect;
use Endermanbugzjfc\NBTInspect\sessions\InspectSession;
use function array_values;

abstract class BaseUI implements UIInterface {

	private $session;
	protected $previous = null;

	protected function __construct(InspectSession $session) {
	    if (!static::accessibleBy($session->getSessionOwner())) throw new \InvalidArgumentException('The "' . static::getName() . '" UI is not suitable for this session owner');
		$this->session = $session;
	}

	public static function create(InspectSession $session, UIInterface $previous = null) : UIInterface {
		$self = new static($session);
		$self->previous = $previous;
		return $self;
	}

	public function getSession() : InspectSession {
		return $this->session;
	}

	public function getPreviousUI() : ?UIInterface {
		return $this->previous;
	}

	public function getNextAvailableUI() : ?string {
	    $uis = array_values(array_filter(NBTInspect::getInstance()->getAllUI(), function(string $ui) : bool {
           return $ui instanceof UIInterface and !($this instanceof ($ui::getName()));
        }));
	    foreach ($uis as $id => $ui) if ($this instanceof $ui) return array_slice($uis, $id + 1)[0] ?? null;
	    return null;
    }

}
