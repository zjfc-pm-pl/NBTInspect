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

use pocketmine\Player;

use jojoe77777\FormAPI\{Form, CustomForm, SimpleForm};

use Endermanbugzjfc\NBTInspect\uis\FormUI;

abstract class BaseForm {

	protected const CUSTOM = CustomForm::class;
	protected const SIMPLE = SimpleForm::class;

	protected const TYPE = null;

	private $ui;
	private $form;

	public function __construct(FormUI $ui) {
		$this->ui = $ui;
		$this->resetForm();
		$ui->getSession()->getSessionOwner()->sendForm($this->form());
	}

	final public function preReact(Player $p, $data = null) : void {
		$this->resetForm();
		$this->react($data);
	}
	
	abstract protected function react($data = null) : void;
	abstract protected function form() : Form;
	
	public function getUIInstance() : FormUI {
		return $this->ui;
	}

	protected function resetForm() {
		$type = self::TYPE;
		$this->form = new $type([$this, 'preReact']);
		return $this;
	}

	public function getForm() : Form {
	    return $this->form;
    }

}