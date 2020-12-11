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

use pocketmine\{Player, nbt\tag\NamedTag};

use Endermanbugzjfc\NBTInspect\sessions\InspectSession;

interface UIInterface {
	
	public function getName() : string;
	public static function create(InspectSession $session) : self;

	public function preInsepct();
	public function insepct();

	public function getSession() : InspectSession;

}
