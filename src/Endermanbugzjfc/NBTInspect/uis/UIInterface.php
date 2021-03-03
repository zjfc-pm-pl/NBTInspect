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

use pocketmine\command\CommandSender;

use Endermanbugzjfc\NBTInspect\sessions\InspectSession;

interface UIInterface {
	
	public static function getName() : string;
	public static function create(InspectSession $session, UIInterface $previous = null) : self;
	public static function accessibleBy(CommandSender $session) : bool;

	public function preInspect();
	public function inspect();
	public function close();

	public function getSession() : InspectSession;

	public function getPreviousUI() : ?UIInterface;

}
