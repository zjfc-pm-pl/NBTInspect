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

use pocketmine\nbt\tag\{NamedTag, CompoundTag, ByteTag, ShortTag, IntTag, LongTag, FloatTag, DoubleTag, StringTag, ByteArrayTag, IntArrayTag};

class FormUI implements \Endermanbugzjfc\NBTInspect\uis\UIInterface {
	
	public static function getName() : string {
		return 'Form';
	}

	public static function open(Player $p, NamedTag $tag, ?callable $onsave, ?UIInterface $from) : self {
		self::openTagByType($p, $tag, $from);
		return new self;
	}

	protected static function openTagByType(Player $p, NamedTag $tag, ?UIInterface $from) : bool {
		switch (true) {
			case $tag instanceof CompoundTag:
				$p->sendForm((new forms\NestedTagInspectForm($tag, $from))->form());
				return true;
				break;

			case $tag instanceof ByteTag:
			case $tag instanceof ShortTag:
			case $tag instanceof IntTag:
			case $tag instanceof LongTag:
			case $tag instanceof FloatTag:
			case $tag instanceof DoubleTag:
			case $tag instanceof ByteArrayTag:
			case $tag instanceof IntArrayTag:
				$p->sendForm((new forms\ValueEditForm($tag, $from))->form());
				return true;
				break;

			default:
				return false;
				break;
		}
	}
	
}
