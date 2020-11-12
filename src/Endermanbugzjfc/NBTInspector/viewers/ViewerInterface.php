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
namespace Endermanbugzjfc\NBTInspect\viewers;

use pocketmine\{Player, nbt\tag\NamedTag};

interface ViewerInterface {
	public function __construct(Player $p, NamedTag $nbt, ?callable $onsave);
	public function open();
	public function getPlayer() : Player;
	public function getNBT() : NamedTag;
	protected function getOpenedLayers() : array;
}