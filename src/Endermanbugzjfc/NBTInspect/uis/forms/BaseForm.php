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
namespace Endermanbugzjfc\NBTInspect\uis\forms;

use pocketmine\utils\TextFormat as TF;

use jojoe77777\FormAPI\{Form, CustomForm, SimpleForm, ModalForm};

use Endermanbugzjfc\NBTInspect\{NBTInspect, uis\UIInterface};

abstract class BaseForm {

	protected const CUSTOM = CustomForm::class;
	protected const SIMPLE = SimpleForm::class;
	protected const MODAL = ModalForm::class;

	protected const TYPE = null;

	private $ui;
	private $form;

	public function __construct(UIInterface $ui) {
		$this->ui = $ui;
		$this->resetForm();
		$ui->getSession()->getPlayer()->sendForm($this->form());
	}

	public function preReact(\pocketmine\Player $p, $data = null) : void {
		$this->resetForm();
		$this->react($react);
	}
	
	abstract protected function react($data = null) : void;
	abstract protected function form() : Form;
	
	public function getUIInstance() : UIInterface {
		return $this->ui;
	}

	protected function resetForm() {
		$type = self::TYPE;
		$this->form = new $type([$this, 'preReact']);
		return $this;
	}

}