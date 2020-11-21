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

use Endermanbugzjfc\NBTInspect\NBTInspect;

/**
 * @allowHandle
 */

abstract class PluginEvent extends \pocketmine\event\plugin\PluginEvent implements \pocketmine\event\Cancellable {

	public function __construct() {}
	
	final public function getPlugin() : \pocketmine\plugin\Plugin {
		return NBTInspect::getInstance();
	}
	
}
