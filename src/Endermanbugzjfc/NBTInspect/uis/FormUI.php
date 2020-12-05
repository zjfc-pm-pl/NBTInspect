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

use pocketmine\utils\Utils;

use pocketmine\nbt\tag\{NamedTag, CompoundTag, ByteTag, ShortTag, IntTag, LongTag, FloatTag, DoubleTag, StringTag, ByteArrayTag, IntArrayTag};

class FormUI implements \Endermanbugzjfc\NBTInspect\uis\UIInterface {
	
	public static function getName() : string {
		return 'Form';
	}

	public static function open(NamedTag $tag, ?callable $onsave, \Endermanbugzjfc\NBTInspect\uis\UIInterface $from) {
		$tag = $tag->getOpenedTags()[0];
		
	}

	protected function openTagByType(NamedTag $tag) {
		switch (true) {
			case $tag instanceof CompoundTag:
				$tag->getPlayer()->sendForm((new forms\NestedTagInspectForm($tag))->form());
				break;

			case $tag instanceof ByteTag:
			case $tag instanceof ShortTag:
			case $tag instanceof IntTag:
			case $tag instanceof LongTag:
			case $tag instanceof FloatTag:
			case $tag instanceof DoubleTag:
			case $tag instanceof ByteArrayTag:
			case $tag instanceof IntArrayTag:
				$tag->getPlayer()->sendForm((new forms\ValueEditForm($tag))->form());
				break;

			default:
				if (isset($tag->getOpenedTags()[1])) $this->openTagByType($tag->getOpenedTags()[1]);
				break;
		}
	}
	
}
