<?php
class Answers {

	public $answerStatus = false;
	public $userAnswer;
	public $correctAnswer;

	// process the answer that has been submitted the user
	// as against what we have stored in the database
	public function answerMechanism($userAnswer, $correctAnswer) {

		/**
		 * @param preset the parameters
		 **/
		$this->userAnswer = $userAnswer;
		$this->correctAnswer = $correctAnswer;

		/**
		 * using the either:: keyword
		 **/
		if(strstr($this->correctAnswer, "either::")) {
			return $this->eitherAnswers();
		}
		/**
		 * using the match:: keyword
		 **/
		elseif(strstr($this->correctAnswer, "match::")) {
			return $this->matchAnswers();
		}
		/** 
		 * using the equals keyword
		 **/
		else {
			return $this->equalsAnswer();
		}

	}

	// either mechanism (can match any of the separater)
	// this section checks if an either text was found
	private function eitherAnswers() {
		// clean the words
		$this->answerString = trim(preg_replace("/either::/i", "", $this->correctAnswer));

		// using foreach loop for the text in there
		$answerExplode = explode(",", $this->answerString);
		$explodeUserAnswer = explode(",", $this->userAnswer);

		// ensure that the user has not supplied more than the options preset
		if(count($explodeUserAnswer) <= count($answerExplode)) {
			// run a loop through the results set
			foreach($answerExplode as $answerCompare) {
				foreach($explodeUserAnswer as $uAnswer) {
					if($uAnswer == $answerCompare) {
						return "correct";
						break;
					}
				}
			}
		}
        return "wrong";
	}

	// match mechanism (should be equal to the answer string)
	// this is mainly for an order of string
	private function matchAnswers() {
		// clean the words
		$this->answerString = trim(preg_replace("/match::/i", "", $this->correctAnswer));

		// using foreach loop for the text in there
		$answerExplode = explode(",", $this->answerString);
		$explodeUserAnswer = explode(",", $this->userAnswer);

		// ensure that the user has not supplied more than the options preset
		if(count($explodeUserAnswer) <= count($answerExplode)) {
			// run a loop through the results set
			if($this->answerString == $this->userAnswer) {
				return "correct";
			}
		}
        return "wrong";
	}

	// equals mechanism (should be equal to the answer string)
	private function equalsAnswer() {

		// clean the words
		if($this->correctAnswer == $this->userAnswer) {
			return "correct";
		}
        return "wrong";
	}

}