<?php
class Car {
	public $engine;
	public $wheels;
	public $is_hybrid;
	public $average_speed;
	private $variable1;
	public $price;

	function __construct ($engine = "v8", $wheels = 4, $is_hybrid = false, $average_speed = 50, $max_passengers = 4, $price = 0) {
		$this->engine = $engine;
		$this->wheels = $wheels;
		$this->is_hybrid = $is_hybrid;
		$this->average_speed = $average_speed;
		$this->max_passengers = $max_passengers;
		$this->price = $price;
	}

	public function travel ($destination = "", $distance = 0) {
		$time_needed = $distance / $this->average_speed;
		echo "Traveling to {$destination} will take you {$time_needed} hours";
	}

	public function calculate_travel_cycles ($passenger_count = 0) {
		$this->doAction();

		$count_needed = $passenger_count / $this->max_passengers;
		echo "You need to travel {$count_needed} times to ferry all passengers to moalboal";
	}

	protected function doAction () {
		echo "this is a protected method";
	}
}

class Toyota extends Car{
	public $modelName;
	public $gazoo_racing_publisher;
	public $trd_publisher;
	public $lexus_company;
	public $daihatsu_company;

	function __construct () {
		parent::__construct();
	}


	public function calculateMaintenceCost () {
		echo "calcualte daw";
		$this->doAction();
	}

	
	protected function doAction () {
		echo "this is a protected method";
	}

}

class Nissan extends Car{
	public $modelName;
	public $mitsubishi_company;
}

class Geely extends Car{
	public $modelName;
	public $mg_company;
}

class Fibonacci {
	public $arrNumbers;
	private $numLemgth;

	function __construct ($length = 0) {
		$this->numLemgth = $length;
		$this->arrNumbers = [0, 1];
	}

	public function doFibonacci () {
		for ($i = 2; $i < $this->numLemgth; $i++) {
			$this->arrNumbers[] = $this->arrNumbers[$i-1] + $this->arrNumbers[$i-2];
		}
	}
}