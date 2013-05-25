<?php
	class wplazare_user{
		private $id;
		private $email;
		private $prenom;
		private $nom;
		private $code_postal;
		private $adresse;
		private $ville;
		private $tel;
		private $date_de_naissance;
		
		public function __construct($id, $email, $prenom, $nom, $code_postal, $adresse, $ville ,$tel ,$date_de_naissance) {
			$this->setData($id, $email, $prenom, $nom, $code_postal, $adresse, $ville ,$tel ,$date_de_naissance);
		}
		
		public function setData($id, $email, $prenom, $nom, $code_postal, $adresse, $ville ,$tel ,$date_de_naissance) {
			$this->id = $id;
			$this->email = $email;
			$this->prenom = $prenom;
			$this->nom = $nom;
			$this->code_postal = $code_postal;
			$this->adresse = $adresse;
			$this->ville = $ville;
			$this->tel = $tel;
			$this->date_de_naissance = $date_de_naissance;
		}
		
		public function getId(){
			return $this->id;
		}

		public function setId($id){
			$this->id = $id;
		}

		public function getEmail(){
			return $this->email;
		}

		public function setEmail($email){
			$this->email = $email;
		}

		public function getPrenom(){
			return $this->prenom;
		}

		public function setPrenom($prenom){
			$this->prenom = $prenom;
		}

		public function getNom(){
			return $this->nom;
		}

		public function setNom($nom){
			$this->nom = $nom;
		}

		public function getCode_postal(){
			return $this->code_postal;
		}

		public function setCode_postal($code_postal){
			$this->code_postal = $code_postal;
		}

		public function getAdresse(){
			return $this->adresse;
		}

		public function setAdresse($adresse){
			$this->adresse = $adresse;
		}

		public function getVille(){
			return $this->ville;
		}

		public function setVille($ville){
			$this->ville = $ville;
		}

		public function getTel(){
			return $this->tel;
		}

		public function setTel($tel){
			$this->tel = $tel;
		}

		public function getDate_de_naissance(){
			return $this->date_de_naissance;
		}

		public function setDate_de_naissance($date_de_naissance){
			$this->date_de_naissance = $date_de_naissance;
		}		
	}