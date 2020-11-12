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

use Endermanbugzjfc\NBTInspect\NBTInspect as Main;

/**
 * @allowHandle
 */

abstract class NBTInspectPluginEvent extends \pocketmine\event\plugin\PluginEvent implements \pocketmine\event\Cancellable {

	public function getPlugin() : Main {
		return Main::getInstance();
	}
	
}
