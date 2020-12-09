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
namespace Endermanbugzjfc\NBTInspect\uis\defaults\forms;

use pocketmine\utils\TextFormat as TF;
use pocketmine\nbt\tag\{NamedTag, CompoundTag, ByteTag, ShortTag, IntTag, LongTag, FloatTag, DoubleTag, StringTag, ByteArrayTag, IntArrayTag};

use Endermanbugzjfc\NBTInspect\{NBTInspect as Main, Utils};

abstract class NestedTagInspectForm extends BaseForm {

	protected const TYPE = self::CUSTOM;
	
	protected function form() : \jojoe77777\FormAPI\Form {
		$f = $this->getForm();
		$s = $this->getSession();

		$f->setTitle(TF::DARK_AQUA . ($t = $s->getOpenedTags()[0])->getName() . TF::BOLD . '(' . (string)Utils::shortenTagType($t) . TF::RESET . TF::BOLD . TF::DARK_AQUA . ')');

		$f->addLabel(TF::YELLOW . 'Inspecting in: ' . TF::AQUA . implode(TF::RESET . TF::BLUE . ' >> ' . TF::AQUA, array_map(function(NamedTag $t) : string {
			return $t->getName() . TF::BOLD . '(' . Utils::shortenTagType() . $t . TF::AQUA . ')';
		}, $s->getOpenedTags(true))));

		return $f;
	}

	abstract protected function react($data = null) : void;
	
}
