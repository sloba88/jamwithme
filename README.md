Current specification, will be added somewhere else later


JamWithMe is the new start-up that aims to connect musicians with each other, or better - dating website for musicians. Goal of the website is to gather amateurs and professionals and provide social interaction as the services of managing jamming sessions, or any similar event. The main feature is search that enables finding musicians in your area based on your preferences, that could be either instruments, genres, or similar interests. The system will be "open minded" for all kinds of music or music artists.

Development:
Basic idea is to start with the responsive website with "mobile first" approach and try to implement service oriented architecture that would make easier to follow with iOS and Android native app. Maybe Phonegap?

Main features:
Registration with social networks
Generating musician profile with basic details and preferances (to be defined later)
Search or browse musicians and jams with and without map interface
Creating your own jam/group pages and promote them


Story:
Michael is a bass player from Stockholm, he just moved to Helsinki and he don't know many people in town. He is big heavy metal fan and he would like to play in a band. He has heard that there are a lot of musicians in the city but he don't know where to find them. Even when he is in mood just to jam with someone he ends up playing alone against his computer, following his favourite songs.
He heard about great new website called JamWithMe where he could browse fellow metalheads, send them personal message and arrange a jam, he is happy now :)

Creating a circle of interaction:
Our goal is to create full circle of interaction when people can move across the genres, instruments and always end on other people profiles. We will use advanced algorithms that will put in favour people that may suit your interests, across genres, instruments and across other connections. When you end up on some profile you can interact by sending a message or sending request for joining a jam.
When person gets message he will also receive email and have onsite notification, with email he will be redirected to website to answer.

User profile:
We will provide only necessary information like profile photo, biography, location, age, genres, instruments, influencers, jams, connections, photos and videos (youtube). Connections are all the people that are connected to that person through any of his current jams.



Musicians:
Idea behind this section is basically the same as Jams section, with the difference of browsing and searching only people near you. This is opposite way of searches where "Jam starters" would search the people to join their jams. You will be able to distinguish between people based on your match, but that is more of a makeup.

People providing lessions:
Musicians will be able to mark themselves as the one providing lessions and they will be displayed differently and you could filter them out. Those profiles have possibility to gather income.

Genres:
Our goal is to have our own database of all the existing genres and "smart" way of narrowing select to the one you want. Good UX practice here is "as you type" system that would only allow to select keyword that is suggested. This way we keep our database clean and if person really can't find some sub-genre he can always leave his selection on one level above. 

Instruments:
Same as genres, we will try to collect the clean database of all relevant instruments and keep it clean by using "as you type" user interface. People will not be limited to the number of instruments they are playing but we can also try to moderate this if we see someone abusing the system. One idea is to expand this section inside the profile with actual music gear that you own, but this would either request us to get all the instruments database or to let the people input what they want and could eventually create mess. We can investigate further if there is any third part API that would provide us with this kind of database.

Rating:
This may not be very popular idea but it would help filter people and distinguish between real amateurs and more serious musicians. And I don't mean that only by level of skill on instrument but also a behaviour. This idea is at lest worth considering. I imagine it as a simple text area where you would either recommend someone or not. This is something that some networks like Linkedin already have implemented. The people receiving the recommendation would be able to choose to approve it or not, that way we could keep things more positive and save ourselves from flaming comments and frustrated people living the network because someone created bunch of fake profiles and flamed them. When accepted recommendation would be integrated on receivers profile and we can also favourize people with more recommendation inside our search filters.

Database benefits:
If we get enough users this database would be unique by providing real insights of how many musicians are there in each city, what do they listen, what do they play and all their preferences. This could be very valuable information for all the instrument manufactures, and music industry in general. 

Initial marketing plan and promotion:
General idea is to focus all the marketing of the product on one city. The strength of the app comes when there are more musician inside one city better than there are a lot of musicians across the world. That city will definitely be Helsinki because it has descent music scene concentrated from entire Finland. This only means that we will try to validate idea on this area and try to get most of our physical presence here. Main source of marketing would probably be online campaigns with ads focused on the city but we will investigate all the other, more direct ways of promotion with direct marketing in relevant public places.

Here is the list of relevant places we came up so far:

Music Stores

http://www.f-musiikki.fi/
http://www.musamaailma.fi/
http://www.soitinlaine.fi/sumuCMS/sivu/etusivu_2012/kieli/fi
http://www.soundstore.fi/
http://www.millbrook.fi/
http://www.kitarapaja.com/

Record Shops

http://www.levykauppax.fi/
http://www.dlxmusic.fi/StartPage/Start.aspx
https://www.stupido.fi/shop/index,fin.php
http://www.kolumbus.fi/levykauppa/

Studios, Record labels etc.

http://www.aanitaivas.com/
http://exp.fi/
http://suomenmusiikki.fi/
http://www.seawolfstudios.com/site/?Uutiset
http://helsinkimastering.com/

Music Schools and teaching

http://www.rockway.fi/kitara/?gclid=CIG7-7DEt7wCFYHhcgodGnUAMQ
http://musiikkikouludemo.fi/?gclid=CMiI1b3Et7wCFWaQcgoduUUAvg
http://www.laulunopettaja.fi/
http://www.bandikoulu.fi/
 
Media

http://www.meteli.net/
http://www.city.fi/
http://www.soundi.fi/
http://www.inferno.fi/

Pubs and Bars (Live Shows)

http://www.barloose.com/
http://www.tavastiaklubi.fi/
http://www.kuudeslinja.com/
http://www.clubliberte.fi/content/fi/1/90/Livemusiclub.html
http://www.soffa.tv/juise/mascot/
http://www.elmu.fi/
http://www.alakerta.fi/
http://www.semifinal.fi/
http://www.virginoil.fi/
http://www.henryspub.net/helsinki/index.php
http://www.mollymalones.fi/
http://ontherocks.fi/
http://www.korjaamo.fi/fi
http://www.apolloliveclub.fi/helsinki/


Returning users:
After creating functional rounded system that would provide basic service our goal is to differ from the ads and to make people continue using our service and provide them more benefits that way. 

Further plans:
One of the plans is developing event based system where they would track events of the basic jams, or other variations of jams like guitar lessons, band rehearsals, actual live gigs.
This is where things get more complicated but this is also the system that would eventually create real money. I would suggest moving everything connected with this story to chapter II or even chapter III of our application. This is not only complicated from the developers perspective, with this we are entering some well established markets and more serious competition. 



OUTDATED
-----

Jam profile:
When person create a Jam we will automatically create a "Jam page" which will be unique to that Jam name. One person can create multiple jams, we will not limit this in beginning but we can maybe try to moderate if we see that someone is spamming this way and ruins the jam search for others. Jam page will have a styled list of all the people that are in the jam and genre that is played. This page will have an option to send request to join the jam and the "Jam starter" will have the ability to accept or deny.
Some ideas include to create event system to track the jams and to have history but that will be discussed later.

Jams:
This option will by default list all the jams that are near you with advanced algorithms that fill put on top jams that you may be interested in. Filters in the sidebar will help that you narrow the search using drop-down menus or keyword text searches.

Jams as map:
This is one of the key options that will present the search on a big map, and will point all the jams nearby.

Jam Requests:
We will try not to limit users in any way on the start. They will be able to send endless amount of requests, messages, create jams and so on. As we grow we need to track eventual abuses of this system. Idea is that "Jam starter" be some kind of mini admin who will be able to accept and deny other people into the jam. Not to complicate things we will have only one admin on the start. So there are two ways of request, from person to join the jam and from jam admin to invite a person into one of his jams. It is worth considering option to have a "private jams" in the near future because not all people want to compromise themselves and give them an option only to invite people they want. This way their jams will not appear in the search sections.

OUTDATED
-----
