<?php

namespace DeejayPoolBundle\Tests\Provider;

use DeejayPoolBundle\Entity\AvdItem;
use DeejayPoolBundle\Entity\ProviderItemInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;

/**
 * Class AvDistrictProviderMock.
 *
 * @group provider
 */
class AvDistrictProviderMock extends \DeejayPoolBundle\Provider\AvDistrictProvider
{
    private $headers = [];
    protected $debug = true;

    /**
     * Open session on digitalDjPool service.
     *
     * @return bool true if auth succes else false
     */
    public function open($login = null, $password = null, $mockFail = false)
    {
        if ($mockFail == false) {
            $mock = new MockHandler([
                new Response(200, [
                    'Set-Cookie' => '.ASPXAUTH=',
                    ], '{"msg":"","haserrors":false,"id":1204,"data":"/Videos"}'
                ),
            ]);
        } else {
            $mock = new MockHandler([
                new Response(200, [
                    'Content-Type' => 'application/json; charset=utf-8',
                    ], '{"msg":"Invalid login email and/or password.","haserrors":true,"id":0,"data":"/Videos"}'
                ),
            ]);
        }
        $handler = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handler]);
        $result = parent::open($login, $password);

        return $result;
    }

    public function getItems($page, $filter = [])
    {
        $mock = new MockHandler([
            new Response(
                200, [
                'Cache-Control' => 'private, s-maxage=0',
                'Content-Type' => 'application/json; charset=utf-8',
                'Server' => 'Microsoft-IIS/7.5',
                'X-AspNetMvc-Version' => '4.0',
                'X-AspNet-Version' => '4.0.30319',
                'X-Powered-By' => 'ASP.NET',
                'Date' => 'Sun, 30 Aug 2015 09:10:14 GMT',
                'Content-Length' => '13326',
                'Set-Cookie' => '.ASPXAUTH=',
                ], '{
    "sEcho": "1",
    "iTotalRecords": 7474,
    "iTotalDisplayRecords": 7172,
    "aaData": [
        [
            "15630",
            "What Do You Mean (Lyric Video) :: Extended :: Clean ",
            "Justin Bieber",
            "Top 40, Dance",
            "125",
            "8/28/2015",
            "1",
            "True",
            "Clean",
            "020a88b6-dbee-4dd2-bcef-29b069c6eada",
            "True",
            "",
            "0",
            "15630",
            "What Do You Mean (Lyric Video) :: Extended :: Clean "
        ],
        [
            "15631",
            "What Do You Mean (Lyric Video) :: Quick :: Clean ",
            "Justin Bieber",
            "Top 40, Dance",
            "125",
            "8/28/2015",
            "1",
            "True",
            "Clean",
            "ddb15b85-9cf3-404e-a315-59218d6bfbc2",
            "True",
            "",
            "0",
            "15631",
            "What Do You Mean (Lyric Video) :: Quick :: Clean "
        ],
        [
            "15628",
            "The Fix (Live vs Studio) :: Extended :: Clean ",
            "Nelly feat. Jeremih :: DJ Gregg R",
            "Top 40, Urban",
            "96",
            "8/28/2015",
            "2",
            "True",
            "Clean",
            "a7f53540-d7aa-4f75-b120-1eb827c20ccf",
            "True",
            "DJ Gregg R",
            "0",
            "15628",
            "The Fix (Live vs Studio) :: Extended :: Clean "
        ],
        [
            "15629",
            "The Fix (Live vs Studio) :: Extended :: Explicit ",
            "Nelly feat. Jeremih :: DJ Gregg R",
            "Top 40, Urban",
            "96",
            "8/28/2015",
            "2",
            "True",
            "Explicit",
            "236fb9c8-b93e-4100-ba7a-273ebf827c0d",
            "True",
            "DJ Gregg R",
            "94415",
            "15629",
            "The Fix (Live vs Studio) :: Extended :: Explicit "
        ],
        [
            "15627",
            "C.R.E.A.M. (Rick Wonder Twerk Remix) :: Extended :: Explicit",
            "Wu-Tang Clan :: Titan",
            "Twerk",
            "105",
            "8/28/2015",
            "2",
            "False",
            "Explicit",
            "4818c242-3bc7-4f4f-a898-1d3583982993",
            "True",
            "Titan",
            "94698",
            "15627",
            "C.R.E.A.M. (Rick Wonder Twerk Remix) :: Extended :: Explicit"
        ],
        [
            "15626",
            "Thats My Shit (E-Rock x Clayton William Twerk Remix) :: Extended :: Explicit ",
            "The Dream Ft T.I. :: Titan",
            "Twerk",
            "98",
            "8/28/2015",
            "2",
            "True",
            "Explicit",
            "0c35e071-e033-42ec-9117-d3220ab39fe5",
            "True",
            "Titan",
            "94420",
            "15626",
            "Thats My Shit (E-Rock x Clayton William Twerk Remix) :: Extended :: Explicit "
        ],
        [
            "15625",
            "When The Beat Drops Out (Dj Amira Deep House Remix) :: Quick :: Clean ",
            "Marlon Roudette :: Titan",
            "Future House, Deep House",
            "124",
            "8/28/2015",
            "2",
            "True",
            "Clean",
            "d2f10f11-6a23-4893-b0d8-81af75ae7fd4",
            "True",
            "Titan",
            "0",
            "15625",
            "When The Beat Drops Out (Dj Amira Deep House Remix) :: Quick :: Clean "
        ],
        [
            "15624",
            "When The Beat Drops Out (Calvo Deep House Remix) :: Quick :: Clean ",
            "Marlon Roudette :: Titan",
            "Future House, Deep House",
            "123",
            "8/28/2015",
            "2",
            "True",
            "Clean",
            "18683478-38d6-4ef5-82d2-2e2f2cfd6114",
            "True",
            "Titan",
            "0",
            "15624",
            "When The Beat Drops Out (Calvo Deep House Remix) :: Quick :: Clean "
        ],
        [
            "15623",
            "Get It Shawty (Gryffin Deep House Bootleg) :: Extended :: Clean",
            "Lloyd :: Titan",
            "Urban, Deep House",
            "123",
            "8/28/2015",
            "2",
            "True",
            "Clean",
            "aab4064e-5ff8-4a1d-89d9-df785f88bda6",
            "True",
            "Titan",
            "0",
            "15623",
            "Get It Shawty (Gryffin Deep House Bootleg) :: Extended :: Clean"
        ],
        [
            "15622",
            "L.A. Love (Strip & Baroud Twerk Bootleg) :: Quick :: Clean ",
            "Fergie :: Titan",
            "Twerk",
            "101",
            "8/28/2015",
            "2",
            "True",
            "Clean",
            "f3bfc753-556b-4c48-8fa2-fe479fc827bb",
            "True",
            "Titan",
            "94418",
            "15622",
            "L.A. Love (Strip & Baroud Twerk Bootleg) :: Quick :: Clean "
        ],
        [
            "15621",
            "How Deep Is Your Love (Dj Snake Remix) :: Extended :: Clean ",
            "Calvin Harris feat. Disciples :: DJ Gregg R",
                "EDM, Trap",
            "75",
            "8/27/2015",
            "2",
            "True",
            "Clean",
            "09fd643b-89ea-4782-aeb5-b52e06de49f1",
            "True",
            "DJ Gregg R",
            "0",
            "15621",
            "How Deep Is Your Love (Dj Snake Remix) :: Extended :: Clean "
        ],
        [
            "15620",
            "Heads Will Roll (Discotech Remix) :: Quick :: Clean ",
            "Yeah Yeah Yeahs :: DJ Gregg R",
            "Future House, Deep House, Dance",
            "128",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "7fd8f803-e5b0-4402-8531-8bb912ab966e",
            "True",
            "DJ Gregg R",
            "0",
            "15620",
            "Heads Will Roll (Discotech Remix) :: Quick :: Clean "
        ],
        [
            "15619",
            "Heads Will Roll (Discotech Remix) :: Extended :: Clean ",
            "Yeah Yeah Yeahs :: DJ Gregg R",
            "Future House, Deep House, Dance",
            "128",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "e7adb1ea-ef4c-454f-8e63-f70870754cb1",
            "True",
            "DJ Gregg R",
            "0",
            "15619",
            "Heads Will Roll (Discotech Remix) :: Extended :: Clean "
        ],
        [
            "15618",
            "Wobble (Deville Twerk Remix 2.0) :: Extended :: Clean ",
            "V.I.C. :: DJ Gregg R",
            "Urban, Twerk, Dance",
            "100",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "175f4d0b-01b5-4490-9676-cc106925235a",
            "True",
            "DJ Gregg R",
            "0",
            "15618",
            "Wobble (Deville Twerk Remix 2.0) :: Extended :: Clean "
        ],
        [
            "15617",
            "Summer Love (Live vs Studio) (Marc Stout Remix) :: Extended :: Clean ",
            "Justin Timberlake :: DJ Gregg R",
            "Top 40, Twerk",
            "100",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "89d97293-322d-4948-8b1f-576805eb8d9a",
            "True",
            "DJ Gregg R",
            "94416",
            "15617",
            "Summer Love (Live vs Studio) (Marc Stout Remix) :: Extended :: Clean "
        ],
        [
            "15616",
            "LYAOF (Light Your Ass On Fire) [Unofficial] :: Extended :: Explicit ",
            "Autoerotique :: DJ Gregg R",
            "Urban, Future House",
            "126",
            "8/26/2015",
            "2",
            "True",
            "Explicit",
            "f7271ea9-d146-423b-83ff-38ad69b5c107",
            "True",
            "DJ Gregg R",
            "94419",
            "15616",
            "LYAOF (Light Your Ass On Fire) [Unofficial] :: Extended :: Explicit "
        ],
        [
            "15615",
            "Like Michael Jackson (Uberjakd Remix) :: Extended :: Clean ",
            "Savage :: P Viddy",
            "EDM, Electro House",
            "128",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "f06f56f6-5c6c-4e03-b017-0948643664d0",
            "True",
            "P Viddy",
            "0",
            "15615",
            "Like Michael Jackson (Uberjakd Remix) :: Extended :: Clean "
        ],
        [
            "15614",
            "Around The World (DJ Mike D Mix) :: Extended :: Clean ",
            "Natalie La Rose feat. Fetty Wap :: P Viddy",
            "Top 40, Uptempo, Dance",
            "131",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "2979b2e2-d581-477b-9699-e8fb5a67b6c8",
            "True",
            "P Viddy",
            "0",
            "15614",
            "Around The World (DJ Mike D Mix) :: Extended :: Clean "
        ],
        [
            "15613",
            "You Know You Like It (Club Killers Redrum) :: Extended :: Clean ",
            "DJ Snake x Aluna George :: P Viddy",
            "EDM, Twerk",
            "100",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "f91e193a-bba0-4ce3-98f3-4687bc52fc92",
            "True",
            "P Viddy",
            "94417",
            "15613",
            "You Know You Like It (Club Killers Redrum) :: Extended :: Clean "
        ],
        [
            "15612",
            "Boriqua Anthem (BeatBreaker & Pat-C 2015 Bootleg) :: Extended :: Clean",
            "C and C Music Factory :: P Viddy",
            "Electro House, Old School, Latin",
            "128",
            "8/26/2015",
            "2",
            "False",
            "Clean",
            "7ab38017-3937-4906-91ed-e7a22374234a",
            "True",
            "P Viddy",
            "0",
            "15612",
            "Boriqua Anthem (BeatBreaker & Pat-C 2015 Bootleg) :: Extended :: Clean"
        ],
        [
            "15611",
            "Cha Cha (Bruce Almighty Movie Edit) :: Extended :: Explicit ",
            "D.R.A.M. :: DJ Gregg R",
            "Urban, Dance",
            "135",
            "8/26/2015",
            "2",
            "True",
            "Explicit",
            "e6311254-a96a-4b6e-b614-fe5c660f259d",
            "True",
            "DJ Gregg R",
            "0",
            "15611",
            "Cha Cha (Bruce Almighty Movie Edit) :: Extended :: Explicit "
        ],
        [
            "15610",
            "Cha Cha (Bruce Almighty Movie Edit) :: Extended :: Clean ",
            "D.R.A.M. :: DJ Gregg R",
            "Urban, Dance",
            "135",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "ad9c23fe-f8b8-4f48-a982-98604a5975bf",
            "True",
            "DJ Gregg R",
            "0",
            "15610",
            "Cha Cha (Bruce Almighty Movie Edit) :: Extended :: Clean "
        ],
        [
            "15609",
            "How Deep Is Your Love (Mixshow Edit) (Calvin Harris & R3HAB Remix) :: Extended :: Clean ",
            "Calvin Harris & Disciples :: DJ Gregg R",
            "EDM, Electro House",
            "128",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "07fd7bd0-29f7-4b3f-8098-593ba378b063",
            "True",
            "DJ Gregg R",
            "0",
            "15609",
            "How Deep Is Your Love (Mixshow Edit) (Calvin Harris & R3HAB Remix) :: Extended :: Clean "
        ],
        [
            "15608",
            "How Deep Is Your Love (Club Edit) (Calvin Harris & R3HAB Remix) :: Extended :: Clean ",
            "Calvin Harris & Disciples :: DJ Gregg R",
            "EDM, Electro House",
            "128",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "debf8761-e27e-4e5b-bd38-b120b1f792b7",
            "True",
            "DJ Gregg R",
            "0",
            "15608",
            "How Deep Is Your Love (Club Edit) (Calvin Harris & R3HAB Remix) :: Extended :: Clean "
        ],
        [
            "15607",
            "We Dem Boyz (Mastamonk 3 Percent Bootleg) :: Extended :: Explicit ",
            "Wiz Khalifa :: Titan",
            "Urban, Electro House",
            "130",
            "8/26/2015",
            "2",
            "True",
            "Explicit",
            "1dbea945-2242-4376-b743-c026eb3e9880",
            "True",
            "Titan",
            "0",
            "15607",
            "We Dem Boyz (Mastamonk 3 Percent Bootleg) :: Extended :: Explicit "
        ],
        [
            "15606",
            "Dont Look Any Further (Diggz Edit) :: Quick :: Clean ",
            "The Writers Block :: Titan",
            "Deep House, Dance",
            "121",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "aba8d519-d1e5-474c-99a0-d40025fa07ef",
            "True",
            "Titan",
            "0",
            "15606",
            "Dont Look Any Further (Diggz Edit) :: Quick :: Clean "
        ],
        [
            "15604",
            "Money In The Bank (Drank) (Meaux Green x Caked Up Twerk Bootleg) :: Extended :: Clean",
            "Lil Scrappy :: Titan",
            "Urban, Twerk",
            "104",
            "8/26/2015",
            "2",
            "False",
            "Clean",
            "c32afab2-edf3-4baa-9002-02f61c95364f",
            "True",
            "Titan",
            "0",
            "15604",
            "Money In The Bank (Drank) (Meaux Green x Caked Up Twerk Bootleg) :: Extended :: Clean"
        ],
        [
            "15603",
            "Bang Bang (G Duppy Reggae Remix) :: Extended :: Clean ",
            "Jessie J feat. Ariana Grande & Nicki Minaj :: Titan",
            "Top 40, Reggae",
            "82",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "bf32e603-1dc8-4863-8d0e-1a6dc90943ff",
            "True",
            "Titan",
            "0",
            "15603",
            "Bang Bang (G Duppy Reggae Remix) :: Extended :: Clean "
        ],
        [
            "15602",
            "Heroes (InstantParty X PartyThieves Bootleg) :: Extended :: Clean ",
            "Firebeatz & KSHMR :: Titan",
            "EDM, Trap",
            "68",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "6cd9474b-7c6e-4608-876a-09c89a2a89da",
            "True",
            "Titan",
            "0",
            "15602",
            "Heroes (InstantParty X PartyThieves Bootleg) :: Extended :: Clean "
        ],
        [
            "15601",
            "Helium (Lazy Rich & AFSHeeN Bootleg) :: Extended :: Clean ",
            "Chris Lake Ft Jereth :: Titan",
            "EDM, Electro House",
            "130",
            "8/26/2015",
            "2",
            "True",
            "Clean",
            "40e1014c-2454-42c4-9640-fdefe08755ea",
            "True",
            "Titan",
            "0",
            "15601",
            "Helium (Lazy Rich & AFSHeeN Bootleg) :: Extended :: Clean "
        ],
        [
            "15599",
            "Lane Boy :: Extended :: Clean ",
            "Twenty One Pilots",
            "Top 40",
            "80",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "611889b6-6a0c-4de9-9937-b64223a02ff6",
            "True",
            "",
            "0",
            "15599",
            "Lane Boy :: Extended :: Clean "
        ],
        [
            "15600",
            "Lane Boy :: Quick :: Clean ",
            "Twenty One Pilots",
            "Top 40",
            "80",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "957449ee-6ff1-4430-864b-05ded03bb633",
            "True",
            "",
            "0",
            "15600",
            "Lane Boy :: Quick :: Clean "
        ],
        [
            "15597",
            "Jello :: Quick :: Clean ",
            "TinaMina",
            "Twerk",
            "106",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "0fb83c3f-96e8-40e3-a160-954daff76977",
            "True",
            "",
            "0",
            "15597",
            "Jello :: Quick :: Clean "
        ],
        [
            "15595",
            "Jello :: Extended :: Clean ",
            "TinaMina",
            "Twerk",
            "106",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "7b1168e3-c110-47c6-a7c6-eefcab214414",
            "True",
            "",
            "0",
            "15595",
            "Jello :: Extended :: Clean "
        ],
        [
            "15596",
            "Jello :: Extended :: Explicit ",
            "TinaMina",
            "Twerk",
            "106",
            "8/25/2015",
            "1",
            "True",
            "Explicit",
            "90b26aca-0d70-4de3-a6e4-22731250af02",
            "True",
            "",
            "0",
            "15596",
            "Jello :: Extended :: Explicit "
        ],
        [
            "15598",
            "Jello :: Quick :: Explicit ",
            "TinaMina",
            "Twerk",
            "106",
            "8/25/2015",
            "1",
            "True",
            "Explicit",
            "7bd4809e-a0a0-4929-8446-ee9bbc666334",
            "True",
            "",
            "0",
            "15598",
            "Jello :: Quick :: Explicit "
        ],
        [
            "15593",
            "High On You :: Extended :: Clean ",
            "Sebastien feat. Hagedom",
            "Deep House, Dance",
            "120",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "16b048e5-fb72-4509-9e42-0e9a57e38da1",
            "True",
            "",
            "0",
            "15593",
            "High On You :: Extended :: Clean "
        ],
        [
            "15594",
            "High On You :: Quick :: Clean ",
            "Sebastien feat. Hagedom",
            "Deep House, Dance",
            "120",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "3f78cb9a-9a2d-40ea-b99f-1f0798bbf86f",
            "True",
            "",
            "0",
            "15594",
            "High On You :: Quick :: Clean "
        ],
        [
            "15592",
            "Lets Play (Rework 2015) :: Quick :: Explicit ",
            "Lexy and K-Paul",
            "Deep House",
            "125",
            "8/25/2015",
            "1",
            "True",
            "Explicit",
            "e0361e01-1180-424b-aaee-1e9a10218d08",
            "True",
            "",
            "0",
            "15592",
            "Lets Play (Rework 2015) :: Quick :: Explicit "
        ],
        [
            "15591",
            "Lets Play (Rework 2015) :: Extended :: Explicit ",
            "Lexy and K-Paul",
            "Deep House",
            "125",
            "8/25/2015",
            "1",
            "True",
            "Explicit",
            "0fa7f15e-ea32-4e65-83c8-0e3f8239ddb0",
            "True",
            "",
            "0",
            "15591",
            "Lets Play (Rework 2015) :: Extended :: Explicit "
        ],
        [
            "15589",
            "High By The Beach :: Quick :: Clean ",
            "Lana Del Rey",
            "Top 40, Other",
            "132",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "772289ec-9c24-412b-8273-3980076adc2a",
            "True",
            "",
            "0",
            "15589",
            "High By The Beach :: Quick :: Clean "
        ],
        [
            "15587",
            "High By The Beach :: Extended :: Clean ",
            "Lana Del Rey",
            "Top 40, Other",
            "132",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "1f5716de-3223-4eab-9de0-9870167134e3",
            "True",
            "",
            "0",
            "15587",
            "High By The Beach :: Extended :: Clean "
        ],
        [
            "15588",
            "High By The Beach :: Extended :: Explicit ",
            "Lana Del Rey",
            "Top 40, Other",
            "132",
            "8/25/2015",
            "1",
            "True",
            "Explicit",
            "7bcddb66-99d9-466e-a721-e464e95aa5b0",
            "True",
            "",
            "0",
            "15588",
            "High By The Beach :: Extended :: Explicit "
        ],
        [
            "15590",
            "High By The Beach :: Quick :: Explicit ",
            "Lana Del Rey",
            "Top 40, Other",
            "132",
            "8/25/2015",
            "1",
            "True",
            "Explicit",
            "ca45e6b3-bc08-4b47-946d-a973b535c628",
            "True",
            "",
            "0",
            "15590",
            "High By The Beach :: Quick :: Explicit "
        ],
        [
            "15584",
            "Disarm You :: Extended :: Clean ",
            "Kaskade feat. Ilsey",
            "Top 40, Prog House",
            "128",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "1c163264-017c-4806-814c-4344aa540022",
            "True",
            "",
            "0",
            "15584",
            "Disarm You :: Extended :: Clean "
        ],
        [
            "15586",
            "Disarm You :: Quick :: Clean ",
            "Kaskade feat. Ilsey",
            "EDM, Prog House",
            "128",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "2379d772-03ac-45d9-8b09-3c9a29292cc1",
            "True",
            "",
            "0",
            "15586",
            "Disarm You :: Quick :: Clean "
        ],
        [
            "15583",
            "Right Here :: Quick :: Clean ",
            "Jess Glynne",
            "Top 40, Deep House, Dance",
            "120",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "ccc896ad-01da-4f90-be29-6e6c9b1a2254",
            "True",
            "",
            "0",
            "15583",
            "Right Here :: Quick :: Clean "
        ],
        [
            "15582",
            "Right Here :: Extended :: Clean ",
            "Jess Glynne",
            "Top 40, Deep House, Dance",
            "120",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "ef5060ed-4d0e-44a8-be40-848417288ca0",
            "True",
            "",
            "0",
            "15582",
            "Right Here :: Extended :: Clean "
        ],
        [
            "15580",
            "Saving My Life :: Extended :: Clean ",
            "Gorgon City feat. ROMANS",
            "Deep House",
            "123",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "fdfb5c7b-b6ee-40f4-b1f1-099aa79d4f5a",
            "True",
            "",
            "0",
            "15580",
            "Saving My Life :: Extended :: Clean "
        ],
        [
            "15581",
            "Saving My Life :: Quick :: Clean ",
            "Gorgon City feat. ROMANS",
            "Deep House",
            "123",
            "8/25/2015",
            "1",
            "True",
            "Clean",
            "012d257c-44bf-41b1-9f7e-42494bac6b53",
            "True",
            "",
            "94699",
            "15581",
            "Saving My Life :: Quick :: Clean "
        ]
    ]
}'
            ),
        ]);
        $handler = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handler]);

        return $result = parent::getItems($page, $filter);
    }

    public function downloadItem(ProviderItemInterface $avdItem, $force = false, $mockSucces = true)
    {
        $mock = new MockHandler([
            new Response(
                200, [
                'Cache-Control' => 'private, s-maxage=0',
                'Content-Type' => 'application/json; charset=utf-8',
                'Server' => 'Microsoft-IIS/7.5',
                'X-AspNetMvc-Version' => '4.0',
                'X-AspNet-Version' => '4.0.30319',
                'X-Powered-By' => 'ASP.NET',
                'Date' => 'Sun, 30 Aug 2015 09:13:34 GMT',
                'Content-Length' => '59',
                ], '{"msg":"","haserrors":false,"id":0,"data":"XYHfVAhuVu4%3d"}'
            ),
            new Response(
                $mockSucces ? 200 : 302, [
                'Cache-Control' => 'private',
                'Content-Length' => '164556298',
                'Content-Type' => 'application/octet-stream',
                'Last-Modified' => 'Mon, 31 Aug 2015 06:18:04 GMT',
                'Accept-Ranges' => 'bytes',
                'ETag' => '-1885871426',
                'Server' => 'Microsoft-IIS/7.5',
                'Content-Disposition' => 'attachment; filename=Xxxx Yyyy_Rrrr Heee_Extended_Clean_HD.mp4',
                'X-AspNetMvc-Version' => '4.0',
                'X-AspNet-Version' => '4.0.30319',
                'X-Powered-By' => 'ASP.NET',
                'Date' => 'Sun, 30 Aug 2015 09:13:34 GMT',
                'Content-Length' => '59',
                ], '' //contentData
            ),
        ]);
        $handler = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handler]);

        return $result = parent::downloadItem($avdItem, $filter = []);
    }

    public function itemCanBeDownload(ProviderItemInterface $item)
    {
        if ($key = $this->getDownloadKey($item)) {
            $item->setDownloadLink($this->getConfValue('download_url').'?key='.$key);

            return true;
        }

        return false;
    }

    private function downloadKeyMockResponse($success = true)
    {
        return new Response(
            200, [
            'Cache-Control' => 'private, s-maxage=0',
            'Content-Type' => 'application/json; charset=utf-8',
            'Server' => 'Microsoft-IIS/7.5',
            'X-AspNetMvc-Version' => '4.0',
            'X-AspNet-Version' => '4.0.30319',
            'X-Powered-By' => 'ASP.NET',
            'Date' => 'Sun, 30 Aug 2015 09:13:34 GMT',
            'Content-Length' => '59',
            ],
            $success ?
                '{"msg":"","haserrors":false,"id":0,"data":"XYHfVAhuVu4%3d"}'
                :
                '{"msg":"You have already downloaded this video twice.  Please contact us for further assistance.","haserrors":true,"id":0,"data":""}'
            );
    }

    public function getDownloadKey(AvdItem $avdItem, $mockSucces = true)
    {
        $this->client = ProvidersTest::applyMock([$this->downloadKeyMockResponse($mockSucces)]);

        return $result = parent::getDownloadKey($avdItem);
    }

    protected function getDownloadResponse(ProviderItemInterface $item, $tempName)
    {
        $resource = fopen($tempName, 'w');
        $downloadKey = $this->getDownloadKey($item);
        $mock = new MockHandler([
              new Response(
                   200,
                  [
                    'Cache-Control' => 'private',
                    'Content-Length' => '164556298',
                    'Content-Type' => 'application/octet-stream',
                    'Last-Modified' => 'Mon, 31 Aug 2015 06:18:04 GMT',
                    'Accept-Ranges' => 'bytes',
                    'ETag' => '-1885871426',
                    'Server' => 'Microsoft-IIS/7.5',
                    'Content-Disposition' => 'attachment; filename=Xxxx Yyyy_Rrrr Heee_Extended_Clean_HD.mp4',
                    'X-AspNetMvc-Version' => '4.0',
                    'X-AspNet-Version' => '4.0.30319',
                    'X-Powered-By' => 'ASP.NET',
                    'Date' => 'Sun, 30 Aug 2015 09:13:34 GMT',
                    'Content-Length' => '59',
                  ],
                  '' //contentData
              ),
          ]);
        $handler = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handler]);

        $response = $this->client->get(
                    $this->getConfValue('download_url'), [
                    //'cookies'         => $this->cookieJar,
                    'allow_redirects' => false,
                    'debug' => $this->debug,
                    'sink' => $resource,
                    'query' => [
                        'key' => $downloadKey,
                    ],
                    'headers' => [
                        'Referer' => 'http://www.avdistrict.net/Videos',
                        'Upgrade-Insecure-Requests' => 1,
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Accept-Encoding' => 'gzip, deflate, sdch',
                        'Accept-Language' => 'fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4',
                    ],
                    ]
                );
        file_put_contents($tempName, 'very long string, very long string, very long string very long string, very long string, very long string very long string, very long string, very long string');

        return $response;
    }
}
