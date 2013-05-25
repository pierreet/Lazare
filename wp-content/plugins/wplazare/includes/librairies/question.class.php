<?php
	class wplazare_question{
		public $id;
		public $role;
		public $texte;
		public $rang;
		public $status='valid';
		public $creation_date='';
		public $last_update_date='';
		
		public function __construct($id,$role, $texte, $rang) {
			$this->setData($id, $role, $texte, $rang);
		}
		
		public function setData($id,$role, $texte, $rang){
			$this->id = $id;
			$this->role = $role;
			$this->texte = $texte;
			$this->rang = $rang;
		}
	}
?>