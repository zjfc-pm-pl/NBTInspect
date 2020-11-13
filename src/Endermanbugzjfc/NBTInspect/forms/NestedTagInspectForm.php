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
namespace Endermanbugzjfc\NBTInspect\forms;

use pocketmine\utils\TextFormat as TF;

use function implode;
use function array_map;

class NestedTagInspectForm extends BaseForm {

	protected function form() : \jojoe77777\FormAPI\Form {
		$f = $this->getForm();
		$s = $this->getSession();
		$f->setTitle(TF::DARK_AQUA . ($t = $s->getOpenedTags()[0])->getName() . TF::BOLD . '(' . (string)Utils::shortenTagType($t) . TF::RESET . TF::BOLD . TF::DARK_AQUA . ')');
		$f->setContent(TF::YELLOW . 'Inspecting in: ' . TF::AQUA . implode(TF::RESET TF::BLUE . ' >> ' . TF::AQUA, array_map(function(NamedTag $tag) : string {
			return $tag->getName() . TF::BOLD . '(' . Utils::shortenTagType() . $tag . TF::AQUA . ')';
		}, $s->getOpenedTags(true))) . "\n" . TF::RESET . TF::YELLOW . 'Tags: ' . TF::AQUA . $tag->getCount() . ' of ' . (Utils::printTagType($tag) ?? TF::BLACK . 'Mixed') . TF::AQUA . ' type');
	}
	
	protected function react();

}