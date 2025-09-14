<?php

/** Random names function */
function random_names($real_name = null, $raw_name = true) {

    // if the environment is production, return the real name
    if(!empty(APP_INI['environment']) && APP_INI['environment'] !== "development") {
        return $real_name;
    }

	if($raw_name) {
		return $real_name;
	}

	$names = [
		"Emmanuel Obeng", "Ama Serwaa", "Kwame Boateng", "Linda Mensah", "Yaw Antwi",
		"Sandra Owusu", "Michael Tetteh", "Akosua Afriyie", "Nana Kofi", "Joyce Ofori",
		"Felix Addo", "Esi Morrison", "Daniel Owusu", "Afia Asantewaa", "Kofi Asamoah",
		"Patience Agyemang", "Prince Amoako", "Naa Dedei", "John Mensah", "Eunice Baidoo",
		"Yaw Sarpong", "Rita Osei", "Stephen Ofori", "Deborah Konadu", "Kojo Yankson",
		"Martha Aidoo", "Bernard Koomson", "Gloria Amankwah", "Samuel Appiah", "Naana Yeboah",
		"Abena Dufie", "Clement Agyapong", "Selina Darko", "Elikem Dzramedo", "Akua Donkor",
		"Jonathan Amdahl", "Kwabena Adu", "Maame Esi", "Isaac Darkwah", "Rosemary Quaye",
		"Francis Ayensu", "Veronica Obeng", "Bright Kusi", "Cynthia Dadzie", "Fredrick Boadi",
		"Juliet Aryee", "George Mensah", "Tracy Oti", "Emmanuel Bediako", "Adwoa Boatema",
		"Kwaku Ankomah", "Beatrice Serwaa", "Lawrence Amponsah", "Comfort Owusu", "Eric Ntiamoah",
		"Anita Opare", "Nana Ama", "Yaw Adusei", "Sarah Abrefa", "Kwesi Baffour",
		"Rebecca Koomson", "Philip Asare", "Naomi Nti", "Gideon Addai", "Lucia Opoku",
		"Ato Quayson", "Mary Annan", "Kwabena Owusu", "Angela Boakye", "Albert Ofori",
		"Ruth Kwarteng", "Joe Quaye", "Juliana Addae", "Francisca Appau", "Nana Yaw",
		"Doris Asare", "Gifty Owusu", "Kojo Frimpong", "Harriet Konadu", "Raymond Adjei",
		"Salomey Agyemang", "Mavis Oti", "Peter Baffour", "Edna Yeboah", "Sampson Kusi",
		"Vivian Amoah", "Paa Kwesi", "Agnes Mensima", "David Tutu", "Esther Amponsah",
	];
	$lastname = [
		"James Smith", "Maria Garcia", "Liam Johnson", "Olivia Brown", "Noah Davis",
		"Emma Wilson", "Ava Taylor", "Isabella Anderson", "Lucas Thomas", "Mia Martinez",
		"Sophia Hernandez", "Charlotte Moore", "Benjamin Jackson", "Amelia White", "Henry Martin",
		"Alexander Thompson", "Elijah Hall", "Daniel Allen", "Sebastian Young", "Jack Scott",
		"Emily Adams", "Hannah Nelson", "Nathan Hill", "Grace Roberts", "Aiden Lewis",
		"Zoe Walker", "Chloe King", "Ethan Wright", "Luna Green", "Logan Baker",
		"Lily Gonzalez", "Harper Perez", "Samuel Carter", "Ella Rivera", "Aria Reed",
		"Matthew Murphy", "Scarlett Cox", "David Simmons", "Victoria Rogers", "Mason Cook",
		"Isla Bell", "Julian Morgan", "Levi Cooper", "Layla Bailey", "Eleanor Diaz",
		"Owen Richardson", "Gabriel Watson", "Camila Wood", "Isaac Brooks", "Sofia Bennett"
	];

	foreach($names as $name) {
		$split = explode(" ", $name);
		foreach($split as $each) {
			$firstNames[] = trim($each);
		}
	}

	foreach($lastname as $name) {
		$split = explode(" ", $name);
		foreach($split as $each) {
			$lastNames[] = trim($each);
		}
	}

	// pick and return a random name
	return $lastNames[array_rand($lastNames)]. " " . $firstNames[array_rand($firstNames)] . " " . $lastNames[array_rand($lastNames)];
}
?>