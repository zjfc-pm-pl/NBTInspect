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

use pocketmine\nbt\tag\{
	CompoundTag, 
	StringTag, 
	ByteTag, 
	IntTag, 
	ShortTag, 
	LongTag, 
	FloatTag,
	IntArrayTag,
	ByteArrayTag,
	ListTag
};
use pocketmine\{nbt\NBT, utils\TextFormat as TF};

use Endermanbugzjfc\InspectNBT\forms\{NumberModificationForm, BasicDisplayerForm};

use function array_unshift;

class FormTagViewer extends BaseTagViewer {
	
	public function open() {
		$tag = $this->getOpenedLayers()[0];
		switch ($type) {
			case NBT::TAG_String:
				(new Endermanbugzjfc\InspectNBT\hotbars\TextEditorHotbar());
				break;

			case NBT::TAG_Byte:
			case NBT::TAG_Short:
			case NBT::TAG_Int:
			case NBT::TAG_Long:
			case NBT::TAG_Float:
			case NBT::TAG_Double:
				(new Endermanbugzjfc\InspectNBT\forms\NumberModificationForm());
				break;

			case NBT::TAG_ByteArray:
			case NBT::TAG_IntArray:
				(new NumberModificationForm());
				break;

			case NBT::CompoundTag:
			case NBT::ListTag:
				(new BasicDisplayerForm());

				break;
			
			default:
				$this->getPlayer()->sendMessage(TF::BOLD . TF::RED . 'A NBT that has invalid tag type (' . get_class($tag) . ' | ' . $tag->getType() . ') was given!');
				break;
		}
	}
	
}
