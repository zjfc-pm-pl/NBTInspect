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

use Endermanbugzjfc\NBTInspect\sessions\InspectSession;

interface UIInterface {
	
	public static function getName() : string;
	public static function create(InspectSession $session, UIInterface $previous = null) : self;
	public static function accessibleBy(InspectSession $session) : bool;

	public function preInsepct();
	public function inspepct();
	public function close();

	public function getSession() : InspectSession;

	public function getPreviousUI() : ?UIInterface;

}
