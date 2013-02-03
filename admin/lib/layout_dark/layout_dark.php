<?php
/**
 * phpVMS - Virtual Airline Administration Software
 * Copyright (c) 2008 Nabeel Shahzad
 * For more information, visit www.phpvms.net
 *	Forums: http://www.phpvms.net/forum
 *	Documentation: http://www.phpvms.net/docs
 *
 * phpVMS is licenced under the following license:
 *   Creative Commons Attribution Non-commercial Share Alike (by-nc-sa)
 *   View license.txt in the root, or visit http://creativecommons.org/licenses/by-nc-sa/3.0/
 *
 * @author Nabeel Shahzad
 * @copyright Copyright (c) 2008, Nabeel Shahzad
 * @link http://www.phpvms.net
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 */
 
/**
 * 
 * Layout functions file
 * 
 */


# Just because I love this movie! :D
function randquote()
{
	$quotes= array(
		"Oh, it's a big pretty white plane with red stripes, curtains in the windows and wheels and it looks like a big Tylenol",
		"Striker, listen, and you listen close: flying a plane is no different than riding a bicycle, just a lot harder to put baseball cards in the spokes",
		"It's Lieutenant Hurwitz. Severe shell-shock. Thinks he's Ethel Merman. War is hell",
		"Looks like I picked the wrong week to quit drinking",
		"Looks like I picked the wrong week to quit smoking",
		"Looks like I picked the wrong week to quit sniffing glue",
		"Looks like I picked the wrong week to quit amphetamines",
		"Roger, Roger. What's our vector, Victor?",
		"There's no reason to become alarmed, and we hope you'll enjoy the rest of your flight. By the way, is there anyone on board who knows how to fly a plane?",
		"I am serious... and don't call me Shirley.",
		"Joey, have you ever been to a Turkish prison?",
		"Shit. It's a God damn waste of time. There's no way he can land this plane",
		"No, thank you, I take it black, like my men",
		"Why, that's the Russian New Year. We can have a parade and serve hot hors d'oeuvres...",
		"The tower, the tower! Rapunzel, Rapunzel!",
		"'S'mofo butter layin' me to da' BONE! Jackin' me up... tight me!",
		"Jus' hang loose, blood. She gonna catch ya up on da' rebound on da' med side",
		"Cut me some slack, Jack! Chump don' want no help, chump don't GET da' help!",	
		"It was a rough place - the seediest dive on the wharf. Populated with every reject and cutthroat from Bombay to Calcutta. It's worse than Detroit",
		"I'm doing everything I can... and stop calling me Shirley",
		"I know but this guy has no flying experience at all. He's a menace to himself and everything else in the air... yes, birds too",
		"I haven't felt this awful since we saw that Ronald Reagan film",
		"Let's see... altitude: 21,000 feet. Speed: 520 knots. Level flight. Course: zero-niner-zero. Trim and mixture: wash, soak, rinse, spin"	
	);
	
	return 	$quotes[rand(0, count($quotes)-1)];	
}
?>